<?php

/*

 	Service "Shop cart 2.0"
	for WhiteLion 1.0

*/

class cart extends Controller {

    private $useShipping = false;
    private $usePayments = false;
    private $useStorage = false;

    function __construct()
    {
        parent::__construct();
        if($cooperation = $this->db->getAllDataByFieldInArray('wl_aliases_cooperation', $_SESSION['alias']->id, 'alias1'))
            foreach ($cooperation as $row) {
                if($row->type == 'delivery')
                    $this->useShipping = true;
                elseif($row->type == 'payment')
                    $this->usePayments = true;
                elseif($row->type == 'storage')
                    $this->useStorage = true;
            }
        if(empty($_SESSION['cart']))
            $_SESSION['cart'] = new stdClass();
        $_SESSION['cart']->initJsStyle = true;
    }

    function _remap($method, $data = array())
    {
        if (method_exists($this, $method)) {
        	if(empty($data)) $data = null;
            return $this->$method($data);
        } else {
        	$this->index($method);
        }
    }

    public function index()
    {
        $this->wl_alias_model->setContent();
        $this->load->smodel('cart_model');

        if($id = $this->data->uri(1))
        {
            if(isset($_SESSION['user']->id))
            {
                if($cart = $this->cart_model->getById($id))
                {
                    $_SESSION['alias']->name = $this->text('Замовлення №').$id;
                    if($cart->user == $_SESSION['user']->id || $this->userCan())
                    {
                        $_SESSION['alias']->breadcrumbs = array($this->text('До всіх замовлень') => $_SESSION['alias']->alias.'/my', $this->text('Замовлення №').$id => '');

                        if($cart->products)
                            foreach ($cart->products as $product) {
                                $product->info = $this->load->function_in_alias($product->product_alias, '__get_Product', $product->product_id);
                                if($product->storage_invoice)
                                    $product->storage = $this->load->function_in_alias($product->product_alias, '__get_Invoice', array('id' => $product->storage_invoice, 'user_type' => $product->user_type));
                            }

                        if($cart->shipping_id)
                            $cart->shipping = $this->load->function_in_alias($cart->shipping_alias, '__get_delivery_info', $cart->shipping_id);

                        if($this->data->uri(2) == 'print')
                            $this->load->view('detal_view', array('cart' => $cart, 'controls' => false));
                        else
                            $this->load->page_view('detal_view', array('cart' => $cart, 'controls' => true));
                        exit;
                    }
                    else
                        $this->load->notify_view(array('errors' => $this->text('Немає прав для перегляду даного замовлення.')));
                }
                else
                    $this->load->page_404(false);
            }
            else
            {
                header('HTTP/1.0 401 Unauthorized');
                $this->load->notify_view(array('errors' => $this->text('Для перегляду замовлення спершу <a href="'.SITE_URL.'login">увійдіть</a>')));
            }
        }

        $user_type = 0;
        if(isset($_SESSION['user']->type))
            $user_type = $_SESSION['user']->type;
        $products = $this->cart_model->getProductsInCart();
        if($products)
            foreach ($products as $product) {
                $product->info = $this->load->function_in_alias($product->product_alias, '__get_Product', $product->product_id);
                if($product->storage_invoice)
                    $product->storage = $this->load->function_in_alias($product->storage_alias, '__get_Invoice', array('id' => $product->storage_invoice, 'user_type' => $user_type));
            }
        $this->load->page_view('index_view', array('products' => $products));
    }

    public function my()
    {
        if(isset($_SESSION['user']->id))
        {
            $_SESSION['alias']->name = $this->text('Мої замовлення');

            $user = $_SESSION['user']->id;
            if($id = $this->data->uri(2))
            {
                if($this->userCan() && is_numeric($id))
                    $user = $id;
                else
                    $this->load->page_404(false);
            }

            $this->load->smodel('cart_model');
            $this->load->page_view('list_view', array('carts' => $this->cart_model->getCarts(array('user' => $user))));
        }
        $this->redirect('login');
    }

    public function addProduct()
    {
        $res = array('result' => false, 'subTotal' => 0);
        if($this->data->post('productKey') && $this->data->post('quantity') != 0)
        {
            $wl_alias = $id = $storage_alias = $storage_id = 0;
            $key = explode('-', $this->data->post('productKey'));
            if(count($key) >= 2 && is_numeric($key[0]) && is_numeric($key[1]))
            {
                $wl_alias = $key[0];
                $id = $key[1];
                if(isset($key[3]) && is_numeric($key[2]) && is_numeric($key[3]))
                {
                    $storage_alias = $key[2];
                    $storage_id = $key[3];
                }
            }

            if($id)
            {
                if($product = $this->load->function_in_alias($wl_alias, '__get_Product', $id))
                {
                    $product->key = $this->data->post('productKey');
                    $product->quantity = $this->data->post('quantity');
                    $product->options = $this->data->post('options');
                    $product->storage_alias = $product->storage_invoice = 0;
                    if($storage_id)
                    {
                        if($invoice = $this->load->function_in_alias($storage_alias, '__get_Invoice', array('id' => $storage_id, 'user_type' => $_SESSION['user']->type)))
                        {
                            $product->storage_alias = $storage_alias;
                            $product->storage_invoice = $storage_id;
                            $product->price = $invoice->price_out;
                            $product->price_in = $invoice->price_in;
                            if($invoice->amount_free < $product->quantity)
                                $product->quantity = $invoice->amount_free;
                        }
                    }
                    if(isset($_SESSION['user']->id))
                    {
                        $this->load->smodel('cart_model');
                        $product->key = $this->cart_model->addProduct($product);
                    }
                    else
                        $_SESSION['cart']->products[$product->key] = $product;
                    $res['product'] = $product;
                    $res['subTotal'] = $this->cart_model->getSubTotalInCart();
                    $res['result'] = true;
                }
            }
        }
        $this->load->json($res);
    }

    public function removeProduct()
    {
        $res = array('result' => false, 'subTotal' => 0);
        if($id = $this->data->post('id'))
        {
            $this->load->smodel('cart_model');

            if(isset($_SESSION['user']->id))
            {
                if(is_numeric($id))
                {
                    if($product = $this->cart_model->getProductInfo(array('id' => $id)))
                    {
                        if($product->user == $_SESSION['user']->id)
                        {
                            if($product->cart == 0)
                            {
                                if($this->db->deleteRow($this->cart_model->table('_products'), $id))
                                {
                                    $res['result'] = true;
                                    $res['subTotal'] = $this->cart_model->getSubTotalInCart();
                                }
                                else
                                    $res['error'] = $this->text('Помилка оновлення інформації');
                            }
                            else
                                $res['error'] = $this->text('Редагувати інформацію про товар можна лише на неоформлених замовленнях!');
                        }
                        else
                            $res['error'] = $this->text('У Вас відсутній доступ до даного товару!');
                    }
                    else
                        $res['error'] = $this->text('Товар у корзині не ідентифіковано');
                }
                else
                    $res['error'] = $this->text('Товар у корзині не ідентифіковано');
            }
            elseif(isset($_SESSION['cart']->products[$id]))
            {
                unset($_SESSION['cart']->products[$id]);
                $res['result'] = true;
                $res['subTotal'] = $this->cart_model->getSubTotalInCart();
            }
            else
                $res['error'] = $this->text('Товар у корзині не ідентифіковано');
        }
        $this->load->json($res);
    }

    public function updateProduct()
    {
        $res = array('result' => false, 'subTotal' => 0);
        if($this->data->post('id') && is_numeric($this->data->post('quantity')) && $this->data->post('quantity') >= 1)
        {
            $id = $this->data->post('id');
            $quantity = $this->data->post('quantity');
            $this->load->smodel('cart_model');

            if(isset($_SESSION['user']->id))
            {
                if(is_numeric($id))
                {
                    if($product = $this->cart_model->getProductInfo(array('id' => $id)))
                    {
                        $res['quantity'] = $product->quantity;
                        if($product->user == $_SESSION['user']->id)
                        {
                            if($product->cart == 0)
                            {
                                if($product->storage_invoice)
                                {
                                    if($invoice = $this->load->function_in_alias($product->storage_alias, '__get_Invoice', array('id' => $product->storage_invoice, 'user_type' => $_SESSION['user']->type)))
                                    {
                                        if($invoice->amount_free > $quantity)
                                        {
                                            $data = array();
                                            $data['quantity'] = $data['quantity_wont'] = $quantity;
                                            if($this->db->updateRow($this->cart_model->table('_products'), $data, $id))
                                            {
                                                $res['result'] = true;
                                                $res['quantity'] = $quantity;
                                                $res['subTotal'] = $this->cart_model->getSubTotalInCart();
                                            }
                                            else
                                                $res['error'] = $this->text('Помилка оновлення інформації');
                                        }
                                        else
                                            $res['error'] = $this->text('Увага! Недостатня кількість товару на складі');
                                    }
                                    else
                                        $res['error'] = $this->text('Товар відсутній на складі');
                                }
                                else
                                {
                                    $data = array();
                                    $data['quantity'] = $data['quantity_wont'] = $quantity;
                                    if($this->db->updateRow($this->cart_model->table('_products'), $data, $id))
                                    {
                                        $res['result'] = true;
                                        $res['quantity'] = $quantity;
                                        $res['subTotal'] = $this->cart_model->getSubTotalInCart();
                                    }
                                    else
                                        $res['error'] = $this->text('Помилка оновлення інформації');
                                }
                            }
                            else
                                $res['error'] = $this->text('Редагувати інформацію про товар можна лише на неоформлених замовленнях!');
                        }
                        else
                            $res['error'] = $this->text('У Вас відсутній доступ до даного товару!');
                    }
                    else
                        $res['error'] = $this->text('Товар у корзині не ідентифіковано');
                }
                else
                    $res['error'] = $this->text('Товар у корзині не ідентифіковано');
            }
            elseif(isset($_SESSION['cart']->products[$id]))
            {
                $res['quantity'] = $_SESSION['cart']->products[$id]->quantity;
                if($_SESSION['cart']->products[$id]->storage_invoice)
                {
                    if($invoice = $this->load->function_in_alias($_SESSION['cart']->products[$id]->storage_alias, '__get_Invoice', array('id' => $_SESSION['cart']->products[$id]->storage_invoice, 'user_type' => $_SESSION['user']->type)))
                    {
                        if($invoice->amount_free > $quantity)
                        {
                            $data = array();
                            $data['quantity'] = $data['quantity_wont'] = $quantity;
                            if($this->db->updateRow($this->cart_model->table('_products'), $data, $id))
                            {
                                $res['result'] = true;
                                $res['quantity'] = $quantity;
                                $res['subTotal'] = $this->cart_model->getSubTotalInCart();
                            }
                            else
                                $res['error'] = $this->text('Помилка оновлення інформації');
                        }
                        else
                            $res['error'] = $this->text('Увага! Недостатня кількість товару на складі');
                    }
                    else
                        $res['error'] = $this->text('Товар відсутній на складі');
                }
                else
                {
                    $res['result'] = true;
                    $_SESSION['cart']->products[$id]->quantity = $res['quantity'] = $quantity;
                    $res['subTotal'] = $this->cart_model->getSubTotalInCart();
                }
            }
            else
                $res['error'] = $this->text('Товар у корзині не ідентифіковано');
        }
        $this->load->json($res);
    }

    function clientAuthentication()
    {
        if($this->data->post('email') && $this->data->post('password'))
        {
            $res = array('result' => false);

            $email = $this->data->post('email');
            $password = $this->data->post('password');

            $this->load->model('wl_user_model');
            if($this->wl_user_model->login('email', $password) || $this->wl_user_model->login('phone', $email) || $this->wl_user_model->login('phone2', $email))
            {
                $res['result'] = true;
                if($_SESSION['option']->useStorage)
                {
                    $this->recalculationProductsByUserType();
                    $res['subTotal'] = $_SESSION['cart']->subTotal;
                }
            }

            header('Content-type: application/json');
            echo json_encode($res);
            exit;
        }
    }

    private function recalculationProductsByUserType()
    {
        if($this->userIs() && $_SESSION['cart']){
            $_SESSION['cart']->subTotal = 0;
            foreach ($_SESSION['cart']->products as &$product) {
                $invoice_where = array('id' => $product['invoiceId'], 'user_type' => $_SESSION['user']->type);
                $invoice = $this->load->function_in_alias($product['storageId'], '__get_Invoice', $invoice_where);

                $product['price'] = $invoice->price_out;
                $_SESSION['cart']->subTotal += ($product['price'] * $product['quantity']);
            }
        }
    }

    function clientSignUp()
    {
        if(trim($this->data->post('name')) != '' && $this->data->post('email') && $this->data->post('password')){
            $res = array('result' => false, 'message' => '');
            $data = array();

            $data['name'] = $name = $this->data->post('name');
            $data['email'] = $email = $this->data->post('email');
            $data['password'] = $password = $this->data->post('password');
            $data['photo'] = '';

            if($email){
                $this->db->executeQuery("SELECT * FROM wl_users WHERE email = '{$email}'");

                if($this->db->numRows() > 0){
                    $res['message'] = 'Користувач з таким е-мейлом вже є';
                } else {
                    $res['result'] = true;
                }

                if($res['result'] == true){
                    $this->load->library('mail');
                    switch ($password) {
                        case '2':
                            $data['password'] = substr(hash('sha512',rand()),0,5);

                            if($this->mail->sendTemplate('password_generate', $email, array('password' => $data['password']))){
                                $res['message'] = '';
                                $res['result'] = true;
                            } else {
                                $res['message'] = 'Помилка при відправленні е-мейла';
                                $res['result'] = false;
                            }
                            break;

                        default:
                            if(strlen($password) < 5){
                                $res['message'] = 'Пароль повинен містити не меньше 5 символів';
                                $res['result'] = false;
                            }
                            break;
                    }
                }

                if($res['result'] == true){
                    $this->load->model('wl_user_model');
                    if($user = $this->wl_user_model->add($data))
                    {
                        $info['auth_id'] = $user->auth_id;
                        $info['email'] = $email;
                        $info['name'] = $name;

                        $this->mail->sendTemplate('signup/user_signup', $email, $info);
                        $this->wl_user_model->login('email', $data['password']);
                    }
                    else {
                        $res['message'] = 'Помилка при створені користувача';
                        $res['result'] = false;
                    }
                }

                header('Content-type: application/json');
                echo json_encode($res);
                exit;
            }
        }
    }

     function addInvoice()
    {
        $res = array('result' => false, 'message' => 'Помилка');
        if(isset($_SESSION['cart']) && $_SESSION['cart']->subTotal > 0)
        {
            $cart = array();
            $cart['user'] = $_SESSION['user']->id;
            $cart['status'] = 1;
            $cart['total'] = $_SESSION['cart']->subTotal;
            // $cart['currency'] = $this->load->function_in_alias('currency', '__get_Currency', 'UAH');
            $cart['date_add'] = $cart['date_edit'] = time();

            if($_SESSION['option']->useShipping){
                $cooperation_where['alias1'] = $_SESSION['alias']->id;
                $cooperation_where['type'] = 'delivery';
                $cooperation = $this->db->getAllDataByFieldInArray('wl_aliases_cooperation', $cooperation_where);
                if($cooperation)
                {
                    foreach ($cooperation as $shipping) {
                        $delivery['user'] = $_SESSION['user']->id;
                        $delivery['method'] = $_SESSION['cart']->shipping['shippingMethod'];
                        $delivery['address'] = $_SESSION['cart']->shipping['shippingAddress'];
                        $delivery['receiver'] = $_SESSION['cart']->shipping['shippingReceiver'];
                        $delivery['phone'] = $_SESSION['cart']->shipping['shippingPhone'];

                        $cart['shipping_id'] = $this->load->function_in_alias($shipping->alias2, '__set_Delivery_from_cart', $delivery);
                        $cart['shipping_alias'] = $shipping->alias2;
                        // $cart['shipping_price'] = $_SESSION['cart']->shipping['price'];

                        break;
                    }
                }
            }

            if($this->db->insertRow('s_cart', $cart)){
                $id = $this->db->getLastInsertedId();
                $_SESSION['cart']->id = $id;

                foreach ($_SESSION['cart']->products as $product) {
                    $cartProduct = array();
                    $cartProduct['cart'] = $id;
                    $cartProduct['alias'] = $product['alias'];
                    $cartProduct['storage_alias'] = isset($product['storageId']) ? $product['storageId'] : 0 ;
                    $cartProduct['storage_invoice'] = isset($product['invoiceId']) ? $product['invoiceId'] : 0 ;
                    $cartProduct['product'] = $product['productId'];
                    $cartProduct['price'] = $product['price'];
                    $cartProduct['price_in'] = $product['price_in'];
                    $cartProduct['quantity'] = $product['quantity'];
                    $cartProduct['user'] = $_SESSION['user']->id;
                    $cartProduct['date'] = time();

                    $cartProduct['additional'] = "";
                    if ($product['additional']) {
                        foreach ($product['additional'] as $key => $additional) {
                            $cartProduct['additional'] .= $key.':'.$additional.';';
                        }
                    }

                    $this->db->insertRow('s_cart_products', $cartProduct);
                }

                $where = array('field' => "phone", 'user' => "#c.user");
                $this->db->select('s_cart as c', '*', $id);
                $this->db->join('wl_users', 'name as user_name, email as user_email', '#c.user');
                $this->db->join('wl_user_info', 'value as user_phone', $where, 'user');
                $this->db->join('s_cart_status', 'name as status_name, weight', '#c.status');
                $orderInfo = $this->db->get();

                $this->db->select('s_cart_products as cp', '*', $id, 'cart');
                $where = array('alias' => "#cp.alias", 'content' => "#cp.product");
                if($_SESSION['language']) $where['language'] = $_SESSION['language'];
                $this->db->join('wl_ntkd', 'name as product_name', $where);
                $this->db->join('s_shopshowcase_products', 'article as product_article', "#cp.product");
                $orderInfo->products = $this->db->get('array');

                $info['id'] = $orderInfo->id;
                $info['status'] = $orderInfo->status;
                $info['status_name'] = $orderInfo->status_name;
                $info['comment'] = "";
                $info['date'] = date('d.m.Y H:i', $orderInfo->date_edit);
                $info['user_name'] = $orderInfo->user_name;
                $info['user_email'] = $orderInfo->user_email;
                $info['user_phone'] = $orderInfo->user_phone;
                $info['link'] = SITE_URL.'admin/'.$_SESSION['alias']->alias.'/'.$orderInfo->id;
                $info['products'] = $orderInfo->products;
                $info['productTotalPrice'] = $orderInfo->total;
                $info['shipping'] = '';

                if($_SESSION['option']->useShipping)
                {
                    $shipping = $this->load->function_in_alias($orderInfo->shipping_alias, '__get_delivery_info', $orderInfo->shipping_id);
                    if($shipping)
                        $info['shipping'] = '<h2><b>Доставка</b></h2>
                                            <b>Служба доставки:</b> '.$shipping->method_name.' <br>
                                            <b>Сайт:</b> '.$shipping->method_site.' <br>
                                            <b>Адреса:</b> '.$shipping->address;
                }

                $info['table'] = '<table align="center" border="2" cellpadding="5" cellspacing="3" width="100%" style="border-collapse: collapse;">
                    <thead><tr><th width="15%">Артикул</th><th width="60%">Продукт</th><th width="9%">Ціна</th><th width="9%">К-сть</th><th width="9%">Разом</th></tr></thead><tbody>';

                foreach($info['products'] as $product){
                    $info['table'] .=  '<tr>
                                    <td>'. $product->product_article .'</td>
                                    <td>'. $product->product_name .'</td>
                                    <td>$'. $product->price .'</td>
                                    <td>'. $product->quantity .'</td>
                                    <td>$'. $product->price * $product->quantity .'</td>
                                </tr>';
                }

                $info['table'] .= '<tr><td colspan="5" align="right">Сума: $'.$info['productTotalPrice'].'</td></tr></tbody></table>';

                $this->load->library('mail');
                $this->mail->sendTemplate('changed_cart_status', SITE_EMAIL, $info, $orderInfo->user_email, $orderInfo->user_name);

                $this->wl_alias_model->setContent();

                $res['result'] = true;
                $res['message'] = html_entity_decode($_SESSION['alias']->text, ENT_QUOTES, 'utf-8');

                if(!$_SESSION['option']->usePayments){
                    unset($_SESSION['cart']);
                }
            }
        }

        header('Content-type: application/json');
        echo json_encode($res);
        exit;
    }

     function getPrice($amount, $code)
    {
        $currencyInfo = $this->db->getQuery("SELECT * FROM s_currency WHERE code = '{$code}'");

        $price = $amount / $currencyInfo->currency;

        return number_format($price, 2);
    }

     function saveShipping()
    {
        if($this->userIs() && isset($_SESSION['cart']) && !empty($_POST)) {
            $res = array('result' => false, 'subTotal' => $_SESSION['cart']->subTotal);

            foreach ($_POST as $key => $value) {
                $_SESSION['cart']->shipping[$key] = $this->data->post($key);
            }

            $cooperation_where['alias1'] = $_SESSION['alias']->id;
            $cooperation_where['type'] = 'delivery';
            $cooperation = $this->db->getAllDataByFieldInArray('wl_aliases_cooperation', $cooperation_where);
            if($cooperation)
            {
                foreach ($cooperation as $storage) {
                    $_SESSION['cart']->shipping['method-info'] = $this->load->function_in_alias($storage->alias2, '__get_Method_info', $this->data->post('shippingMethod'));
                    // $_SESSION['cart']->shipping['price'] = 0;

                    // if($_SESSION['cart']->shipping['method-info']->price > 0)
                    // {
                    //     if($_SESSION['cart']->shipping['method-info']->price_from > 0)
                    //     {
                    //         $priceFrom = $this->getPrice($_SESSION['cart']->shipping['method-info']->price_from, $_SESSION['cart']->shipping['method-info']->price_currency_from);
                    //         if($_SESSION['cart']->subTotal < $priceFrom)
                    //         {
                    //             $_SESSION['cart']->shipping['price'] = $this->getPrice($_SESSION['cart']->shipping['method-info']->price, $_SESSION['cart']->shipping['method-info']->price_currency);
                    //         }
                    //     }
                    //     else
                    //     {
                    //         $_SESSION['cart']->shipping['price'] = $this->getPrice($_SESSION['cart']->shipping['method-info']->price, $_SESSION['cart']->shipping['method-info']->price_currency);
                    //     }
                    // }

                    if($this->data->post('shippingDefault') == 1)
                    {
                        $this->load->function_in_alias($storage->alias2, '__set_Default_from_cart');
                    }

                    break;
                }

                // $res['subTotal'] += $_SESSION['cart']->shipping['price'];
                $res['result'] = true;
            }

            header('Content-type: application/json');
            echo json_encode($res);
            exit;
        }
    }

     function loadShipping()
    {
        if($this->userIs() && isset($_SESSION['cart'])) {
            $this->load->view('shipping_view');
        }
    }

     function loadInvoice()
    {
        if($this->userIs() && isset($_SESSION['cart'])) {
            $this->load->view('invoice_view');
        }
    }

     function loadPayments()
    {
        if($this->userIs() && isset($_SESSION['cart']->id)) {
            $this->load->view('payments_view');
        }
    }

    public function checkout()
    {
        # code...
    }

    public function pay()
    {
        if(isset($_POST['method']) && is_numeric($_POST['method']))
        {
            if($_POST['method'] == 0)
            {
                $cartId = $_SESSION['cart']->id;
                unset($_SESSION['cart']);
                $this->redirect('cart/'.$cartId);
            }
            elseif($_POST['method'] > 0)
            {
                if($cart = $this->db->getAllDataById('s_cart', $_SESSION['cart']->id))
                {
                    unset($_SESSION['cart']);
                    $cart->return_url = $_SESSION['alias']->alias.'/'.$cart->id;
                    $cart->wl_alias = $_SESSION['alias']->id;

                    $this->load->function_in_alias($this->data->post('method'), '__get_Payment', $cart);
                } 
            }
        }
    }

    public function __show_btn_add_product($product)
    {
        if(!empty($product))
            $this->load->view('__btn_add_product_subview', array('product' => $product));
        else
            echo "<p>Увага! Відсутня інформація про товар! (для генерації кнопки Додати товар до корзини)";
    }

}

?>
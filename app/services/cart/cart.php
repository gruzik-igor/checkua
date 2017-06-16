<?php

/*

 	Service "Shop cart 1.0"
	for WhiteLion 1.0

*/

class cart extends Controller {

    function _remap($method, $data = array())
    {
        if (method_exists($this, $method)) {
        	if(empty($data)) $data = null;
            return $this->$method($data);
        } else {
        	$this->index($method);
        }
    }

    function index()
    {
        $this->wl_alias_model->setContent();
        $orderId = $this->data->uri(1);

        if(isset($orderId) && is_numeric($orderId) > 0 )
        {
            $where = array('field' => "phone", 'user' => "#u.id");
            $this->db->select('s_cart as c', '*', $orderId);
            $this->db->join('s_cart_status', 'name as status_name', '#c.status');
            $this->db->join('wl_users as u', 'name as user_name, email as user_email', '#c.user');
            $this->db->join('wl_user_info', 'value as user_phone', $where, 'user');
            $this->db->join('wl_user_types', 'title as user_type_name', '#u.type');
            $cartInfo = $this->db->get();

            if($cartInfo)
            {
                $_SESSION['alias']->name = 'Замовлення №'.$orderId;
                $_SESSION['alias']->breadcrumbs = array('До всіх замовлень' => 'profile/orders', 'Замовлення №'.$orderId => '');

                $cartHistory = $this->db->getQuery("SELECT h.*, s.name as status_name FROM `s_cart_history` as h LEFT JOIN `s_cart_status` as s ON h.status = s.id WHERE h.cart = $orderId ORDER BY h.date DESC", 'array');

                if($_SESSION['option']->useShipping && $cartInfo->shipping_id > 0)
                {
                    $cartInfo->shipping = $this->load->function_in_alias($cartInfo->shipping_alias, '__get_delivery_info', $cartInfo->shipping_id);
                }

                if(isset($cartInfo->user) && isset($_SESSION['user']->id) && $cartInfo->user == $_SESSION['user']->id || $this->userCan()){
                    $orderProducts = $this->getOrderProducts($orderId);

                    $this->load->page_view('orderProducts', array('orderProducts' => $orderProducts, 'cartInfo' => $cartInfo, 'cartHistory' => $cartHistory, 'controls' => true));
                    exit;
                } else $this->load->notify_view(array('errors' => 'Немає прав для перегляду даного замовлення.'));
            }
        }

        $this->load->page_view('index_view');
    }

    function productAdd()
    {
        if($this->data->post('productId') && $this->data->post('quantity') != 0 && $this->data->post('alias') ){

            if(!isset($_SESSION['cart'])){
                $_SESSION['cart'] = new stdClass();
            }

            $res = array('subTotal' => 0, 'productsCount' => 0);

            $storageId = $this->data->post('storageId');
            $invoiceId = $this->data->post('invoiceId');
            $productId = $this->data->post('productId');
            $size = $this->data->post('size') ? $this->data->post('size') : "";

            $_SESSION['cart']->products[$productId.'-'.$storageId.'-'.$invoiceId]['invoiceId'] = $res['invoiceId'] = $invoiceId;
            $_SESSION['cart']->products[$productId.'-'.$storageId.'-'.$invoiceId]['storageId'] = $res['storageId'] = $storageId;

            if($_SESSION['option']->useStorage && $this->data->post('invoiceId') && $this->data->post('storageId')){
                $user_type = isset($_SESSION['user']->type) ? $_SESSION['user']->type : 0;
                $invoice_where = array('id' => $this->data->post('invoiceId'), 'user_type' => $user_type);
                $invoice = $this->load->function_in_alias($this->data->post('storageId'), '__get_Invoice', $invoice_where);
                $_SESSION['cart']->products[$productId.'-'.$storageId.'-'.$invoiceId]['invoiceId'] = $res['invoiceId'] = $invoiceId;
                $_SESSION['cart']->products[$productId.'-'.$storageId.'-'.$invoiceId]['storageId'] = $res['storageId'] = $storageId;
            }

            $productInfo = $this->load->function_in_alias($this->data->post('alias'), '__get_Product', $productId);

            $_SESSION['cart']->products[$productId.'-'.$storageId.'-'.$invoiceId]['quantity'] = $res['quantity'] = isset($invoice) && $invoice->amount_free < $this->data->post('quantity') ? $invoice->amount_free : $this->data->post('quantity');
            $_SESSION['cart']->products[$productId.'-'.$storageId.'-'.$invoiceId]['price'] = $res['price'] = isset($invoice) ? $invoice->price_out : $productInfo->price;
            $_SESSION['cart']->products[$productId.'-'.$storageId.'-'.$invoiceId]['price_in'] = isset($invoice) ? $invoice->price_in : $productInfo->price;
            $_SESSION['cart']->products[$productId.'-'.$storageId.'-'.$invoiceId]['name'] = $res['name'] = str_replace($productInfo->article, '', $productInfo->name);
            $_SESSION['cart']->products[$productId.'-'.$storageId.'-'.$invoiceId]['m_photo'] = $res['m_photo'] = !empty($productInfo->m_photo) ? IMG_PATH.$productInfo->m_photo : false;
            $_SESSION['cart']->products[$productId.'-'.$storageId.'-'.$invoiceId]['productId'] = $res['productId'] = $productInfo->id;
            $_SESSION['cart']->products[$productId.'-'.$storageId.'-'.$invoiceId]['alias'] = $this->data->post('alias');
            $_SESSION['cart']->products[$productId.'-'.$storageId.'-'.$invoiceId]['article'] = $res['article'] = $productInfo->article;
            $_SESSION['cart']->products[$productId.'-'.$storageId.'-'.$invoiceId]['additional']['Розмір'] = $size;


            foreach ($_SESSION['cart']->products as $product) {
               $_SESSION['cart']->subTotal = $res['subTotal'] += ($product['price'] * $product['quantity']);
               $_SESSION['cart']->productsCount = $res['productsCount'] += $product['quantity'];
            }

            header('Content-type: application/json');
            echo json_encode($res);
            exit;
        }
    }

    function removeProduct()
    {
        if($this->data->post('productId')){
            $productId = $this->data->post('productId');
            $storageId = $this->data->post('storageId') ? $this->data->post('storageId') : 0;
            $invoiceId = $this->data->post('invoiceId') ? $this->data->post('invoiceId') : 0;
            unset($_SESSION['cart']->products[$productId.'-'.$storageId.'-'.$invoiceId]);

            $res = array('subTotal' => 0, 'productsCount' => 0);

            if(!empty($_SESSION['cart']->products)){
                foreach ($_SESSION['cart']->products as $product) {
                    $_SESSION['cart']->subTotal = $res['subTotal'] += ($product['price'] * $product['quantity']);
                    $_SESSION['cart']->productsCount = $res['productsCount'] += $product['quantity'];
                }
            } else {
                $_SESSION['cart']->subTotal = $_SESSION['cart']->productsCount = 0;
            }

            header('Content-type: application/json');
            echo json_encode($res);
            exit;
        }
    }

    function updateProduct()
    {
        if($this->data->post('productId') && is_numeric($this->data->post('quantity')) && $this->data->post('quantity') >= 1){
            $res = array('result' => false, 'subTotal' => 0, 'productsCount' => 0);

            $productId = $this->data->post('productId');
            $storageId = $this->data->post('storageId');
            $invoiceId = $this->data->post('invoiceId');
            $quantity = $this->data->post('quantity');

            $user_type = isset($_SESSION['user']->type) ? $_SESSION['user']->type : 0;
            $invoice_where = array('id' => $invoiceId, 'user_type' => $user_type);
            $invoice = $this->load->function_in_alias($this->data->post('storageId'), '__get_Invoice', $invoice_where);

            if(($invoice && $invoice->amount_free >= $quantity) || !$invoice && $quantity > 0){
                $_SESSION['cart']->products[$productId.'-'.$storageId.'-'.$invoiceId]['quantity'] = $res['quantity'] = $quantity;
                $res['productTotalPrice'] = $_SESSION['cart']->products[$productId.'-'.$storageId.'-'.$invoiceId]['price'] * $quantity;
                $res['result'] = true;
            } else $res['maxQuantity'] = $invoice->amount_free;

            foreach ($_SESSION['cart']->products as $product) {
                $_SESSION['cart']->subTotal = $res['subTotal'] += ($product['price'] * $product['quantity']);
                $_SESSION['cart']->productsCount = $res['productsCount'] += $product['quantity'];
            }

            header('Content-type: application/json');
            echo json_encode($res);
            exit;
        }
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

    public function addInvoice()
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

    public function getPrice($amount, $code)
    {
        $currencyInfo = $this->db->getQuery("SELECT * FROM s_currency WHERE code = '{$code}'");

        $price = $amount / $currencyInfo->currency;

        return number_format($price, 2);
    }

    public function saveShipping()
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

    public function loadShipping()
    {
        if($this->userIs() && isset($_SESSION['cart'])) {
            $this->load->view('shipping_view');
        }
    }

    public function loadInvoice()
    {
        if($this->userIs() && isset($_SESSION['cart'])) {
            $this->load->view('invoice_view');
        }
    }

    public function loadPayments()
    {
        if($this->userIs() && isset($_SESSION['cart']->id)) {
            $this->load->view('payments_view');
        }
    }

    public function order()
    {
        $orderId = $this->data->uri(2);

         if(isset($orderId) && is_numeric($orderId) > 0 ){
            $where = array('field' => "phone", 'user' => "#u.id");
            $this->db->select('s_cart as c', '*', $orderId);
            $this->db->join('s_cart_status', 'name as status_name', '#c.status');
            $this->db->join('wl_users as u', 'name as user_name, email as user_email', '#c.user');
            $this->db->join('wl_user_info', 'value as user_phone', $where, 'user');
            $this->db->join('wl_user_types', 'title as user_type_name', '#u.type');
            $cartInfo = $this->db->get();

            $cartHistory = $this->db->getQuery("SELECT h.*, s.name as status_name FROM `s_cart_history` as h LEFT JOIN `s_cart_status` as s ON h.status = s.id WHERE h.cart = $orderId ORDER BY h.date DESC", 'array');

            if($_SESSION['option']->useShipping && $cartInfo->shipping_id > 0)
            {
                $cartInfo->shipping = $this->load->function_in_alias($cartInfo->shipping_alias, '__get_delivery_info', $cartInfo->shipping_id);
            }

            if(isset($cartInfo->user) && $cartInfo->user == $_SESSION['user']->id || $this->userCan()){
                $orderProducts = $this->getOrderProducts($orderId);

                $this->load->view('orderProducts', array('orderProducts' => $orderProducts, 'cartInfo' => $cartInfo, 'cartHistory' => $cartHistory));
                exit;
            } else $this->load->notify_view(array('errors' => 'Немає прав для перегляду даного замовлення.'));
        }
    }

    public function payCash()
    {
        $cartId = $_SESSION['cart']->id;
        unset($_SESSION['cart']);
        $this->redirect('cart/'.$cartId);
    }

    public function pay()
    {
        $orderId = $this->data->uri(2);

        if(isset($orderId) && is_numeric($orderId) > 0 ){
            $where = array('field' => "phone", 'user' => "#u.id");
            $this->db->select('s_cart as c', '*', $orderId);
            $this->db->join('s_cart_status', 'name as status_name', '#c.status');
            $this->db->join('wl_users as u', 'name as user_name, email as user_email, ballance as user_ballance', '#c.user');
            $this->db->join('wl_user_info', 'value as user_phone', $where, 'user');
            $this->db->join('wl_user_types', 'title as user_type_name', '#u.type');
            $cartInfo = $this->db->get();

            if(isset($cartInfo->user) && $cartInfo->user == $_SESSION['user']->id || $this->userCan())
            {
                $this->load->page_view('pay_order_view', array('cartInfo' => $cartInfo));
                exit;
            } else $this->load->notify_view(array('errors' => 'Немає прав для перегляду даного замовлення.'));
        }
    }

    private function getOrderProducts($orderId)
    {
        $this->db->select('s_cart_products as cp', '*', $orderId, 'cart');
        $orderProducts = $this->db->get('array');

        if($orderProducts){
            foreach ($orderProducts as $product) {
                $product->info = $this->load->function_in_alias($product->alias, '__get_Product', $product->product);
            }
        }

        return $orderProducts;
    }
}

?>
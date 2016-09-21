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
        $orderId = $this->data->uri(1);

         if(isset($orderId) && is_numeric($orderId) > 0 ){
            $this->db->select('s_cart as c', '*', $orderId);
            $this->db->join('s_cart_status', 'name as status_name', '#c.status');
            $this->db->join('wl_users as u', 'name as user_name, email as user_email', '#c.user');
            $this->db->join('wl_user_info', 'phone1 as user_phone', '#u.id', 'user');
            $this->db->join('wl_user_types', 'title as user_type_name', '#u.type');
            $cartInfo = $this->db->get();

            if($cartInfo)
            {
                $cartHistory = $this->db->getQuery("SELECT h.*, s.name as status_name FROM `s_cart_history` as h LEFT JOIN `s_cart_status` as s ON h.status = s.id WHERE h.cart = $orderId ORDER BY h.date DESC", 'array');

                if($_SESSION['option']->useShipping && $cartInfo->shipping_id > 0)
                {
                    $cartInfo->shipping = $this->load->function_in_alias($cartInfo->shipping_alias, '__get_delivery_info', $cartInfo->shipping_id);
                }

                if(isset($cartInfo->user) && $cartInfo->user == $_SESSION['user']->id || $this->userCan()){
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
        if($this->data->post('productId') && $this->data->post('quantity') != 0 && $this->data->post('alias') && $this->data->post('invoiceId') && $this->data->post('storageId')){

            if(!isset($_SESSION['cart'])){
                $_SESSION['cart'] = new stdClass();
            }

            $res = array('subTotal' => 0, 'productsCount' => 0);

            $storageId = $this->data->post('storageId');
            $invoiceId = $this->data->post('invoiceId');
            $productId = $this->data->post('productId');


            if($_SESSION['option']->useStorage && $this->data->post('invoiceId') && $this->data->post('storageId')){
                $user_type = isset($_SESSION['user']->type) ? $_SESSION['user']->type : 0;
                $invoice_where = array('id' => $this->data->post('invoiceId'), 'user_type' => $user_type);
                $invoice = $this->load->function_in_alias($this->data->post('storageId'), '__get_Invoice', $invoice_where);
                $_SESSION['cart']->products[$productId.'-'.$storageId.'-'.$invoiceId]['invoiceId'] = $res['invoiceId'] = $invoiceId;
                $_SESSION['cart']->products[$productId.'-'.$storageId.'-'.$invoiceId]['storageId'] = $res['storageId'] = $storageId;
            }

            $productInfo = $this->load->function_in_alias($this->data->post('alias'), '__get_Product', $productId);

            $_SESSION['cart']->products[$productId.'-'.$storageId.'-'.$invoiceId]['quantity'] = $res['quantity'] = $invoice->amount_free < $this->data->post('quantity') ? $invoice->amount_free : $this->data->post('quantity');
            $_SESSION['cart']->products[$productId.'-'.$storageId.'-'.$invoiceId]['price'] = $res['price'] = isset($invoice) ? $invoice->price_out : $productInfo->price;
            $_SESSION['cart']->products[$productId.'-'.$storageId.'-'.$invoiceId]['price_in'] = isset($invoice) ? $invoice->price_in : $productInfo->price;
            $_SESSION['cart']->products[$productId.'-'.$storageId.'-'.$invoiceId]['name'] = $res['name'] = $productInfo->name;
            $_SESSION['cart']->products[$productId.'-'.$storageId.'-'.$invoiceId]['s_photo'] = $res['s_photo'] = !empty($productInfo->s_photo) ? IMG_PATH.$productInfo->s_photo : false;
            $_SESSION['cart']->products[$productId.'-'.$storageId.'-'.$invoiceId]['productId'] = $res['productId'] = $productInfo->id;
            $_SESSION['cart']->products[$productId.'-'.$storageId.'-'.$invoiceId]['alias'] = $this->data->post('alias');
            $_SESSION['cart']->products[$productId.'-'.$storageId.'-'.$invoiceId]['article'] = $res['article'] = $productInfo->article;


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
        if($this->data->post('productId') && $this->data->post('storageId') && $this->data->post('invoiceId')){
            $productId = $this->data->post('productId');
            $storageId = $this->data->post('storageId');
            $invoiceId = $this->data->post('invoiceId');
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
        if($this->data->post('productId') && $this->data->post('storageId') && $this->data->post('invoiceId') && is_numeric($this->data->post('quantity')) && $this->data->post('quantity') >= 1){
            $res = array('result' => false, 'subTotal' => 0, 'productsCount' => 0);

            $productId = $this->data->post('productId');
            $storageId = $this->data->post('storageId');
            $invoiceId = $this->data->post('invoiceId');
            $quantity = $this->data->post('quantity');

            $user_type = isset($_SESSION['user']->type) ? $_SESSION['user']->type : 0;
            $invoice_where = array('id' => $invoiceId, 'user_type' => $user_type);
            $invoice = $this->load->function_in_alias($this->data->post('storageId'), '__get_Invoice', $invoice_where);

            if($invoice->amount_free >= $quantity){
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

    function checkPasswordExist()
    {
        if($this->data->post('email') || $this->data->post('phone')){
            $res = array('result' => false, 'password' => false);

            $email = $this->data->post('email');
            $phone = $this->data->post('phone');
            if($email){
                $user = $this->db->getQuery("SELECT password FROM `wl_users` WHERE `email` = '{$email}'");

                $res['result'] = $user ?  true : false;
                $res['password'] = ($user && !empty($user->password)) ? true : false;
            }

            if($res['result'] == false && $phone){
                $user = $this->db->getQuery("SELECT user FROM `wl_user_info` WHERE `phone1` = '{$phone}' OR  `phone2` = '{$phone}'");

                if($user){
                    $res['result'] = true;
                    $user = $this->db->getQuery("SELECT password FROM `wl_users` WHERE `id` = '{$user->user}'");
                    $res['password'] = ($user && !empty($user->password)) ? true : false;
                }
            }

            header('Content-type: application/json');
            echo json_encode($res);
            exit;

        }
    }

    function clientAuthentication()
    {
        if($this->data->post('email') || $this->data->post('phone')){
            $res = array('result' => false);

            $email = $this->data->post('email');
            $phone = $this->data->post('phone');
            $password = $this->data->post('password');

            $this->load->model('wl_user_model');
            if($this->wl_user_model->checkUserBy('email', $email, $password) || $this->wl_user_model->checkUserBy('phone1', $phone, $password) || $this->wl_user_model->checkUserBy('phone2', $phone, $password))
            {
                $res['result'] = true;
                $this->recalculationProductsByUserType();
                $res['subTotal'] = $_SESSION['cart']->subTotal;
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
        if(trim($this->data->post('name')) != '' && ($this->data->post('email') || $this->data->post('phone'))){
            $res = array('result' => false, 'message' => '');
            $data = array();

            $data['name'] = $name = $this->data->post('name');
            $email = $this->data->post('email');
            $password = $this->data->post('password');
            $data['type'] = $_SESSION['option']->newUserType;
            $data['status'] = 1;
            $data['registered'] = time();
            $data['photo'] = 0;
            $phone = $this->data->post('phone');

            if($email || $phone){
                if($email){
                    $this->db->executeQuery("SELECT * FROM wl_users WHERE email = '{$email}'");

                    if($this->db->numRows() > 0){
                        $res['message'] = 'Користувач з таким е-мейлом вже є';
                    } else {
                        $res['result'] = true;
                    }

                }
                if($phone && $res['message'] == ''){
                    $this->db->executeQuery("SELECT * FROM `wl_user_info` WHERE `phone1` = '{$phone}' OR  `phone2` = '{$phone}'");

                    if($this->db->numRows() > 0){
                        $res['message'] = 'Користувач з таким телефоном вже є';
                        $res['result'] = false;
                    }
                    else $res['result'] = true;
                }

                if($res['result'] == true){
                    switch ($password) {
                        case '2':
                            if($email){
                                $password = substr(hash('sha512',rand()),0,5);

                                if($this->mail->sendTemplate('password_generate', $email, array('password' => $password))){
                                    $res['message'] = '';
                                    $res['result'] = true;
                                }else {
                                    $res['message'] = 'Помилка при відправленні е-мейла';
                                    $res['result'] = false;
                                }
                            } else {
                                $res['message'] = 'Введіть е-мейл для відправки паролю';
                                $res['result'] = false;
                            }
                            break;
                        case '3':
                            $password = '';
                            break;

                        default:
                            if(strlen($password) < 4){
                                $res['message'] = 'Пароль повинен містити не меньше 5 символів';
                                $res['result'] = false;
                            }
                            break;
                    }
                }
                if($res['result'] == true){
                    if($email){
                        $data['email'] = $email;
                    } else {
                        $userInfo['phone1'] = $phone;
                    }

                    if($this->db->insertRow('wl_users', $data)){
                        $id = $this->db->getLastInsertedId();
                        $_SESSION['user']->id = $id;
                        $_SESSION['user']->type = $_SESSION['option']->newUserType;

                        if($password != ''){
                            $password = sha1($email . md5($password) . SYS_PASSWORD . $id);
                            $this->db->updateRow('wl_users', array('password' => $password), $id);
                        }
                        if(isset($userInfo)){
                            $userInfo['user'] = $id;
                            $this->db->insertRow('wl_user_info', $userInfo);
                        }

                        $register['date'] = $data['registered'];
                        $register['do'] = 1;
                        $register['user'] = $id;
                        $this->db->insertRow('wl_user_register', $register);
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

                        $cart['shipping_id'] = $this->load->function_in_alias($shipping->alias2, '__set_Delivery_from_cart', $delivery);
                        $cart['shipping_alias'] = $shipping->alias2;

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

                    $this->db->insertRow('s_cart_products', $cartProduct);
                }

                $this->db->select('s_cart as c', '*', $id);
                $this->db->join('wl_users', 'name as user_name, email as user_email', '#c.user');
                $this->db->join('wl_user_info', 'phone1 as user_phone', '#c.user', 'user');
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

    public function saveShipping()
    {
        if($this->userIs() && isset($_SESSION['cart']) && !empty($_POST)) {
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
                    if($this->data->post('shippingDefault') == 1)
                    {
                        $this->load->function_in_alias($storage->alias2, '__set_Default_from_cart');
                    }
                    break;
                }
            }

            $res['result'] = true;
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
            $this->db->select('s_cart as c', '*', $orderId);
            $this->db->join('s_cart_status', 'name as status_name', '#c.status');
            $this->db->join('wl_users as u', 'name as user_name, email as user_email', '#c.user');
            $this->db->join('wl_user_info', 'phone1 as user_phone', '#u.id', 'user');
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

    public function pay()
    {
        $orderId = $this->data->uri(2);

        if(isset($orderId) && is_numeric($orderId) > 0 ){
            $this->db->select('s_cart as c', '*', $orderId);
            $this->db->join('s_cart_status', 'name as status_name', '#c.status');
            $this->db->join('wl_users as u', 'name as user_name, email as user_email, ballance as user_ballance', '#c.user');
            $this->db->join('wl_user_info', 'phone1 as user_phone', '#u.id', 'user');
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
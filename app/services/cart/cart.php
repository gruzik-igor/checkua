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
        
        $this->load->page_view('index_view');
    }

    function productAdd()
    {
        if($this->data->post('productId') && $this->data->post('alias')){

           if(!isset($_SESSION['cart'])){
                $_SESSION['cart'] = new stdClass();
           }
            
            $productId = $this->data->post('productId');

            if(!isset($_SESSION['cart']->products[$productId])){
                $res = array('subTotal' => 0, 'productsCount' => 0);
                $productInfo = $this->load->function_in_alias($this->data->post('alias'), '__get_Product', $productId);

                if($_SESSION['option']->useStorage && $this->data->post('invoiceId') && $this->data->post('storageId')){
                    $invoice = $this->load->function_in_alias($this->data->post('storageId'), '__get_Invoice', $this->data->post('invoiceId'));
                    $_SESSION['cart']->products[$productId]['storageId'] = $this->data->post('storageId');
                    $_SESSION['cart']->products[$productId]['invoiceId'] = $this->data->post('invoiceId');
                }

                $_SESSION['cart']->products[$productId]['quantity'] = isset($_SESSION['cart']->products[$productId]['quantity']) ? $_SESSION['cart']->products[$productId]['quantity'] : 1;
                $_SESSION['cart']->products[$productId]['price'] = $res['price'] = isset($invoice) ? $invoice->price_out : $productInfo->price;
                $_SESSION['cart']->products[$productId]['name'] = $res['name'] = $productInfo->name;
                $_SESSION['cart']->products[$productId]['s_photo'] = $res['s_photo'] = !empty($productInfo->s_photo) ? IMG_PATH.$productInfo->s_photo : false;
                $_SESSION['cart']->products[$productId]['productId'] = $res['productId'] = $productInfo->id;
                $_SESSION['cart']->products[$productId]['alias'] = $this->data->post('alias');
                foreach ($_SESSION['cart']->products as $product) {
                   $_SESSION['cart']->subTotal = $res['subTotal'] += ($product['price'] * $product['quantity']);
                   $_SESSION['cart']->productsCount = $res['productsCount'] += $product['quantity'];
                }
                
                header('Content-type: application/json');
                echo json_encode($res);
                exit;
            }
        }
    }

    function removeProduct()
    {
        if($this->data->post('productId')){
            $productId = $this->data->post('productId');
            unset($_SESSION['cart']->products[$productId]);

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
        if($this->data->post('productId') && $this->data->post('quantity') >= 1){
            $productId = $this->data->post('productId');

            $_SESSION['cart']->products[$productId]['quantity'] = $this->data->post('quantity');

            $res = array('subTotal' => 0, 'productsCount' => 0);

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
            $res['result'] = $this->wl_user_model->checkUserBy('email', $email, $password) || $this->wl_user_model->checkUserBy('phone1', $phone, $password) || $this->wl_user_model->checkUserBy('phone2', $phone, $password);

            header('Content-type: application/json');
            echo json_encode($res);
            exit;
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
        $cart = array();
        $res = array('result' => false, 'message' => 'Помилка');
        $cart['user'] = $_SESSION['user']->id;
        $cart['status'] = 1;
        $cart['date_add'] = time();

        if($this->db->insertRow('s_cart', $cart)){
            $id = $this->db->getLastInsertedId();

            foreach ($_SESSION['cart']->products as $product) {
                $cartProduct = array();
                $cartProduct['cart'] = $id;
                $cartProduct['alias'] = $product['alias'];
                $cartProduct['storage_alias'] = isset($product['storageId']) ? $product['storageId'] : 0 ;
                $cartProduct['storage_invoice'] = isset($product['invoiceId']) ? $product['invoiceId'] : 0 ;
                $cartProduct['product'] = $product['productId'];
                $cartProduct['price'] = $product['price'];
                $cartProduct['quantity'] = $product['quantity'];
                $cartProduct['user'] = $_SESSION['user']->id;
                $cartProduct['date'] = time();

                $this->db->insertRow('s_cart_products', $cartProduct);
                $res['result'] = true;
                $res['message'] = html_entity_decode($_SESSION['alias']->text, ENT_QUOTES, 'utf-8');
                unset($_SESSION['cart']);
            }
        }

        header('Content-type: application/json');
        echo json_encode($res);
        exit;
    }

    public function loadInvoice()
    {
        if($this->userIs() && isset($_SESSION['cart'])){
            $this->load->view('invoice_view');
        } 
    }


}

?>
<?php

class cart_admin extends Controller {

    function __construct()
    {
        parent::__construct();
        $_SESSION['option']->useStorage = 0;
        $useStorageWhere = array('alias1' => $_SESSION['alias']->id, 'type' => 'storage');
        if($cooperation = $this->db->getAllDataByFieldInArray('wl_aliases_cooperation', $useStorageWhere))
            $_SESSION['option']->useStorage = 1;
    }

    function _remap($method, $data = array())
    {
        $_SESSION['alias']->breadcrumb = array('Корзина' => '');
        $_SESSION['alias']->name = 'Корзина';
                
        if(isset($_SESSION['alias']->name))
            $_SESSION['alias']->breadcrumb = array($_SESSION['alias']->name => '');
        if (method_exists($this, $method))
        {
            if(empty($data)) $data = null;
            return $this->$method($data);
        }
        else
            $this->index($method);
    }

    function index($id)
    {
        $this->load->smodel('cart_model');

        if(is_numeric($id))
        {
            if($cart = $this->cart_model->getById($id))
            {
                $_SESSION['alias']->name .= '. Замовлення #'.$id;
                $_SESSION['alias']->breadcrumb = array($_SESSION['alias']->name => 'admin/'.$_SESSION['alias']->alias, 'Замовлення #'.$id => '');

                if($cart->products)
                    foreach ($cart->products as $product) {
                        $product->info = $this->load->function_in_alias($product->product_alias, '__get_Product', $product->product_id);
                        if($product->storage_invoice)
                            $product->storage = $this->load->function_in_alias($product->product_alias, '__get_Invoice', array('id' => $product->storage_invoice, 'user_type' => $product->user_type));
                    }

                if($cart->shipping_id)
                    $cart->shipping = $this->load->function_in_alias($cart->shipping_alias, '__get_info', $cart->shipping_id);

                $cart->payment = false;
                if($cart->payment_alias)
                    $cart->payment = $this->load->function_in_alias($cart->payment_alias, '__get_info', $cart->payment_id);
                else if($cart->payment_id)
                    $cart->payment = $this->cart_model->getPayments(array('id' => $cart->payment_id));

                $cartStatuses = $this->db->getQuery("SELECT * FROM `s_cart_status` WHERE `active` = 1 AND `weight` > (SELECT weight FROM `s_cart_status` WHERE id = $cart->status ) ORDER BY weight");

                $this->load->admin_view('detal_view', array('cart' => $cart, 'cartStatuses' => $cartStatuses));
            }
            else
                $this->load->page_404(false);
        }
        else
        {
            $carts = false;
            if(!empty($_GET['id']))
            {
                if($cart = $this->db->getAllDataById($this->cart_model->table(), $this->data->get('id')))
                    $this->load->redirect('admin/'.$_SESSION['alias']->alias.'/'.$cart->id);
            }
            else
            {
                $_SESSION['option']->paginator_per_page = 25;

                $carts = $this->cart_model->getCarts();
            }
            $this->load->admin_view('index_view', array('carts' => $carts));
        }
    }

    public function add()
    {
        $_SESSION['alias']->breadcrumb = array('Корзина' => 'admin/'.$_SESSION['alias']->alias, 'Додати покупку' => '');
        $_SESSION['alias']->name = 'Корзина. Додати покупку';
        $this->load->admin_view('add_view');
    }

    public function all()
    {
        $this->load->smodel('cart_model');
        $carts = $this->cart_model->getAllCarts();

        $this->load->admin_view('all_view', array('carts' => $carts));
    }

    private function getInvoicesByProduct($alias, $id)
    {
        $cooperation = $this->db->getAllDataByFieldInArray('wl_aliases_cooperation', $alias, 'alias1');
        $productInvoices = array();
        if($cooperation)
        {
            foreach ($cooperation as $storage) {
                if($storage->type == 'storage')
                {
                    $invoices = $this->load->function_in_alias($storage->alias2, '__get_Invoices_to_Product', $id);

                    if($invoices)
                    {
                        foreach ($invoices as $invoice) {
                            $productInvoices[] = $invoice;
                        }
                    }
                }
            }
        }

        if(empty($productInvoices)) return false;

        return $productInvoices;
    }

    public function remove()
    {
        $res = array('result' => false);
        if($this->data->post('id') && $this->data->post('totalPrice') && $this->data->post('cartId'))
        {
            $id = $this->data->post('id');
            $totalPrice = $this->data->post('totalPrice');
            $cartId = $this->data->post('cartId');
            $date_edit = time();

            $this->db->deleteRow("s_cart_products", $id);
            $this->db->executeQuery("UPDATE `s_cart` SET `total` = `total` - $totalPrice, `date_edit` = $date_edit WHERE `id` = $cartId");

            $res['result'] = true;
        }

        $this->json($res);
    }

    public function showProductInvoices()
    {
        if($this->data->post('alias') && $this->data->post('product')){
            $alias = $this->data->post('alias');
            $product = $this->data->post('product');
            $userType = $this->data->post('userType');

            $invoice_where = array('id' => $product, 'user_type' => $userType);
            $res = $this->getInvoicesByProduct($alias, $invoice_where);

            $this->json($res);
        }
    }

    public function changeProductInvoice()
    {
        $res = array('result' => false);
        if($this->data->post('id') && $this->data->post('value')){
            $value = $this->data->post('value');
            $id = $this->data->post('id');
            $price = $this->data->post('price');

            $values = explode("/", $value);
            $data['storage_invoice'] = $values[0];
            $data['storage_alias'] = $values[1];

            $date = time();

            $cartId = $this->db->getQuery("SELECT cart FROM `s_cart_products` WHERE `id` = $id")->cart;

            if($price == 'true') $data['price'] = $values[2];
            $data['price_in'] = $values[3];

            if($this->db->updateRow('s_cart_products', $data, $id)){
                if($price == 'true'){
                    $total = round($this->db->getQuery("SELECT SUM(quantity * price) as totalPrice FROM `s_cart_products` WHERE `cart` = $cartId")->totalPrice, 2);

                    $this->db->executeQuery("UPDATE `s_cart` SET `total` = {$total}, `date_edit` = $date WHERE `id` = $cartId");
                    $res['totalPrice'] = $total;
                }
                $res['result'] = true;
            }
        }

        $this->json($res);
    }

    public function changeProductQuantity()
    {
        if($this->data->post('quantity') > 0 && $this->data->post('id') && $this->data->post('cart')){
            $id = $this->data->post('id');
            $cartId = $this->data->post('cart');
            $date = time();

            $invoice = $this->load->function_in_alias($this->data->post('storageId'), '__get_Invoice', $this->data->post('invoiceId'));
            if($invoiceId)
            {
                $quantity = $invoice->amount_free >= $this->data->post('quantity') ? $this->data->post('quantity') : $invoice->amount_free;
            }
            else
            {
                $quantity = $this->data->post('quantity');
            }


            if($this->db->updateRow("s_cart_products", array('quantity' => $quantity), $id))
            {
                $total = $this->db->getQuery("SELECT SUM(quantity * price) as totalPrice FROM `s_cart_products` WHERE `cart` = $cartId")->totalPrice;

                $this->db->executeQuery("UPDATE `s_cart` SET `total` = {$total}, `date_edit` = $date WHERE `id` = $cartId");

                if(!empty($_POST['toHistory']))
                {
                    $toHistory = array();
                    $toHistory['cart'] = $cartId;
                    $toHistory['user'] = $_SESSION['user']->id;
                    $toHistory['comment'] = $this->data->post('toHistory');
                    $toHistory['comment'] .= $quantity;
                    $toHistory['date'] = time();
                    $this->db->insertRow('s_cart_history', $toHistory);
                }
            }
        }

        $this->redirect('admin/cart/'.$cartId.'#tabs-products');
    }

    public function saveToHistory($pay = null)
    {
        $data = $cartUpdate = $info = array();
        if($pay && isset($pay->cart_id))
        {
            $cartId = $data['cart'] = $pay->cart_id;
            if(isset($pay->cart_status))
                $data['status'] = $cartUpdate['status'] = $pay->cart_status;
            else
                $data['status'] = $cartUpdate['status'] = 4;
            $cartUpdate['payment_alias'] = $pay->alias;
            $cartUpdate['payment_id'] = $pay->id;
            $data['comment'] = $pay->comment;
            $data['user'] = 0;
        }
        else if(isset($_POST['cart']) && is_numeric($_POST['cart']))
        {
            $cartId = $data['cart'] = $this->data->post('cart');
            $data['status'] = $cartUpdate['status'] = $this->data->post('status') ? $this->data->post('status') : 1;
            $data['comment'] = $this->data->post('comment');
            $data['user'] = $_SESSION['user']->id;
        }
        $data['date'] = $cartUpdate['date_edit'] = time();

        if(!isset($cartId))
            return false;

        $this->load->smodel('cart_model');

        if($this->db->insertRow($this->cart_model->table('_history'), $data))
        {
            $this->db->updateRow($this->cart_model->table(), $cartUpdate, $cartId);

            if($cart = $this->cart_model->getById($cartId))
            {
                if($cart->products)
                    foreach ($cart->products as $product) {
                        $product->info = $this->load->function_in_alias($product->product_alias, '__get_Product', $product->product_id);
                        if($product->storage_invoice)
                            $product->storage = $this->load->function_in_alias($product->product_alias, '__get_Invoice', array('id' => $product->storage_invoice, 'user_type' => $product->user_type));
                    }

                if($cart->shipping_id)
                    $cart->shipping = $this->load->function_in_alias($cart->shipping_alias, '__get_delivery_info', $cart->shipping_id);

                $this->load->library('mail');

                $info['id'] = $cart->id;
                $info['action'] = $cart->action;
                $info['status'] = $cart->status;
                $info['status_name'] = $cart->status_name;
                $info['status_weight'] = $cart->status_weight;
                $info['comment'] = $data['comment'];
                $info['info'] = $cart->comment;
                $info['date'] = date('d.m.Y H:i', $cart->date_edit);
                $info['user_name'] = $cart->user_name;
                $info['user_email'] = $cart->user_email;
                $info['user_phone'] = $cart->user_phone;
                $info['link'] = SITE_URL.$_SESSION['alias']->alias.'/'.$info['id'];
                $info['pay_link'] = SITE_URL.$_SESSION['alias']->alias.'/'.$cart->id.'/pay';
                $info['admin_link'] = SITE_URL.'admin/'.$_SESSION['alias']->alias.'/'.$info['id'];
                foreach ($cart->products as $product) {
                    $product->info = $this->load->function_in_alias($product->product_alias, '__get_Product', $product->product_id);
                    $product->price = $this->cart_model->priceFormat($product->price);
                    $product->sum = $this->cart_model->priceFormat($product->price * $product->quantity);
                }
                $info['total'] = $cart->total;
                $info['total_formatted'] = $this->cart_model->priceFormat($info['total']);
                $info['products'] = $cart->products;
                $info['delivery'] = false;
                if($cart->shipping_alias && $cart->shipping_id)
                    $info['delivery'] = $this->load->function_in_alias($cart->shipping_alias, '__get_delivery_info', $cart->shipping_id);
                
                $this->mail->sendTemplate('change_status', $cart->user_email, $info);

                if($_SESSION['option']->useStorage)
                    foreach($info['products'] as $product) {
                    
                        if($product->quantity_reserved == 0 && $orderInfo->weight > 0 && $orderInfo->weight < 90)
                        {
                            $reserve = array('invoise' => $product->storage_invoice, 'amount' => $product->quantity);
                            if($this->load->function_in_alias($product->storage_alias, '__set_Reserve', $reserve))
                            {
                                $this->db->updateRow('s_cart_products', array('quantity_reserved' => $product->quantity), $product->id);
                            }
                        }
                        elseif($product->quantity_reserved > 0 && $orderInfo->weight >= 90 && $orderInfo->status != 7)
                        {
                            $reserve = array('invoise' => $product->storage_invoice, 'amount' => $product->quantity_reserved, 'reserve' => true);
                            $this->load->function_in_alias($product->storage_alias, '__set_Book', $reserve);
                        }
                        elseif($product->quantity_reserved == 0 && $orderInfo->weight >= 90 && $orderInfo->status != 7)
                        {
                            $reserve = array('invoise' => $product->storage_invoice, 'amount' => $product->quantity, 'reserve' => false);
                            $this->load->function_in_alias($product->storage_alias, '__set_Book', $reserve);
                            $this->db->updateRow('s_cart_products', array('quantity_reserved' => $product->quantity), $product->id);
                        }
                        elseif($product->quantity_reserved > 0 && $orderInfo->status == 7)
                        {
                            $reserve = array('invoise' => $product->storage_invoice, 'amount' => -$product->quantity);
                            if($this->load->function_in_alias($product->storage_alias, '__set_Reserve', $reserve))
                            {
                                $this->db->updateRow('s_cart_products', array('quantity_reserved' => $product->quantity), $product->id);
                            }
                        }
                    }
            }
        }

        if(isset($_POST['cart']))
            $this->redirect('admin/cart/'.$cartId.'#tabs-history');

        return true;
    }

    public function editComment()
    {
        $data = array();
        $res = array('result' => false);
        $id = $this->data->post('id');
        $data['comment'] = $this->data->post('comment');
        $data['user'] = $_SESSION['user']->id;
        $data['date'] = time();

        if($this->db->updateRow('s_cart_history', $data, $id)){
            $res['result'] = true;
        }
        $this->load->json($res);
    }

    public function __set_Payment($pay)
    {
        if(isset($pay->cart_id)) {
            $this->saveToHistory($pay);
        }
    }

    public function getProductByArticle()
    {
        $this->getProduct('article', $this->data->post('product'), $this->data->post('userType'), $this->data->post('userId'), $this->data->post('cartId'));
    }

    private function getProduct($key, $id, $userType, $userId, $cartId)
    {
        $where = array();
        $where['alias2'] = $_SESSION['alias']->id;
        $where['type'] = 'cart';
        if($cooperation = $this->db->getAllDataByFieldInArray('wl_aliases_cooperation', $where))
            foreach ($cooperation as $shop) {
                if($key == 'article')
                {
                    $count_products = 0;
                    $showStorages = true;

                    if($products = $this->load->function_in_alias($shop->alias1, '__get_Products', array($key => '%'.$id)))
                    {
                        foreach ($products as $product) {
                            $count = 0;

                            $invoice_where = array('id' => $product->id, 'user_type' => $userType);

                                if($showStorages)
                                {
                                    echo("<h3>Товари</h3>");
                                    echo('<div class="table-responsive"><table class="table table-condensed table-bordered">');
                                    echo("<tr>");
                                    echo("<td>Артикул</td>");
                                    echo("<td>Опис</td>");
                                    echo("<td>Ціна</td>");
                                    echo("<td></td>");
                                    echo("</tr>");
                                    $showStorages = false;
                                }

                                echo("<tr>");
                                echo("<td>{$product->article}</td>");
                                if(!empty($product->admin_photo))
                                    echo "<td><img src=".IMG_PATH. $product->admin_photo." width='90' alt=''> ".html_entity_decode($product->name)."</td>";
                                else
                                    echo("<td></td>");
                                echo("<td>{$product->price} грн</td>");
                                echo("<td><form method='post' action='".SITE_URL."admin/{$_SESSION['alias']->alias}/addProduct'><input type='hidden' value='{$userId}' name='userId'><input type='hidden' name='cartId' value='{$cartId}'><input type='hidden' name='productId' value='{$product->id}'><input type='hidden' name='price' value='{$product->price}'><button type='submit' class='btn btn-sm btn-warning'>Додати</button></form></td>");
                                echo("</tr>");
                                $count_products++;

                        }
                        echo("</table></div>");

                        return true;
                    }
                }
            }
        return false;
    }

    public function addProduct()
    {
        $data = array();
        if($this->data->post('cartId') != ''){
            $data['cart'] = $this->data->post('cartId');
        }

        $data['storage_alias'] = $this->data->post('storageId') ? $this->data->post('storageId') : 0;
        $data['storage_invoice'] = $this->data->post('invoiceId') ? $this->data->post('invoiceId') : 0;
        $data['product_id'] = $this->data->post('productId');
        $data['quantity'] = $data['quantity_wont'] = 1;
        $data['quantity_returned'] = $data['discount'] = 0;
        $data['price'] = $this->data->post('price');
        $data['price_in'] = $this->data->post('price_in') ? $this->data->post('price_in') : 0;
        $data['product_alias'] = $this->db->getQuery("SELECT wl_alias FROM `s_shopshowcase_products` WHERE `id` = {$data['product_id']} ")->wl_alias;
        $data['user'] = $this->data->post('userId') === 'false' ? $_SESSION['user']->id : $this->data->post('userId');
        $data['product_options'] = '';
        $data['date'] = time();

        $updateRow = true;
        if(!isset($data['cart']))
        {
            $data['cart'] = $this->db->insertRow('s_cart', array('user' => $data['user'], 'total' => $data['price'], 'status' => 1, 'date_add' => $data['date'], 'date_edit' => $data['date']));
            $updateRow = false;
        }

        $this->db->insertRow('s_cart_products', $data);
        if($updateRow)
            $this->db->executeQuery("UPDATE `s_cart` SET `total` = `total` + {$data['price']}, `date_edit` = {$data["date"]} WHERE `id` = {$data['cart']}");

        $this->redirect('admin/cart/'.$data['cart'].'#tabs-products');
    }

    public function findUser()
    {
        $res = array('result' => false);
        if($this->data->post('userInfo')){
            $info = $this->data->post('userInfo');

            $user = $this->db->getQuery("SELECT id,email,name,type FROM `wl_users` WHERE `name` LIKE '%{$info}%' OR `email` LIKE '%{$info}%'", 'array');
            if(!$user)
            {
              $this->db->executeQuery("SELECT user FROM `wl_user_info` WHERE `field` = 'phone' AND `value` LIKE '%{$info}%'");
                if($this->db->numRows() == 1){
                    $userId = $this->db->getRows();
                    $user = $this->db->getQuery("SELECT id,email,name,type FROM `wl_users` WHERE `id` = $userId->user", 'array');
                    $res['result'] = true;
                    $res['user'] = $user;
                }
            } else {
                $res['result'] = true;
                $res['user'] = $user;
            }
            $this->json($res);
        }
    }

    public function saveNewUser()
    {
        $res = array('result' => false, 'message' => '');
        if(trim($this->data->post('name')) != '' && ($this->data->post('email') || $this->data->post('phone')))
        {
            $data = array();

            $data['name'] = $name = $this->data->post('name');
            $data['email'] = $email = $this->data->post('email');
            $data['photo'] = 0;
            $userInfo['phone'] = $phone = $this->data->post('phone');

            if($email || $phone)
            {
                if($email)
                {
                    $this->db->executeQuery("SELECT * FROM wl_users WHERE email = '{$email}'");

                    if($this->db->numRows() > 0){
                        $res['message'] = 'Користувач з таким е-мейлом вже є';
                    } else {
                        $res['result'] = true;
                    }
                }
                if($phone && $res['message'] == '')
                {
                    $this->db->executeQuery("SELECT * FROM `wl_user_info` WHERE `field` = 'phone' AND `value` = '{$phone}'");

                    if($this->db->numRows() > 0)
                    {
                        $res['message'] = 'Користувач з таким телефоном вже є';
                        $res['result'] = false;
                    }
                    else $res['result'] = true;
                }
            }


            if($res['result'] == true)
            {
                $data['password'] = substr(hash('sha512',rand()),0,5);
                $sendTemplate = true;

                if(empty($data['email']))
                {
                    $data['email'] = $userInfo['phone'];
                    $sendTemplate = false;
                }

                $this->load->model('wl_user_model');
                if($user = $this->wl_user_model->add($data, $userInfo))
                {
                    if($sendTemplate)
                    {
                        $this->load->library('mail');
                        $this->mail->sendTemplate('password_generate', $data['email'], array('password' => $data['password']));
                    }

                    $res['id'] = $user->id;
                }
                else {
                    $res['message'] = 'Помилка при створені користувача';
                    $res['result'] = false;
                }
            }
        }

        $this->json($res);
    }

    public function save_price_format()
    {
        if($_SESSION['user']->type == 1 && $this->data->post('service'))
        {
            $price_format = array('before' => '', 'after' => '', 'round' => 2);
            $price_format['before'] = htmlspecialchars($_POST['before']);
            $price_format['after'] = htmlspecialchars($_POST['after']);
            $price_format['round'] = $this->data->post('round');
            $value = serialize($price_format);

            $where = array('alias' => $_SESSION['alias']->id, 'name' => 'price_format');
            $where['service'] = $this->data->post('service');
            if($option = $this->db->getAllDataById('wl_options', $where))
            {
                if($option->value != $value)
                    $this->db->updateRow('wl_options', array('value' => $value), $option->id);
            }
            else
            {
                $where['value'] = $value;
                $this->db->insertRow('wl_options', $where);
            }

            $_SESSION['notify'] = new stdClass();
            $_SESSION['notify']->success = 'Формат виводу ціни оновлено';
        }
        $this->redirect();
    }

    public function settings()
    {
        $this->load->smodel('cart_model');
        $uri = $this->data->uri(3);
        if(empty($uri))
        {
            $_SESSION['alias']->name .= '. Налаштування';
            $shippings = $this->cart_model->getShippings();
            $payments = $this->cart_model->getPayments();
            $this->load->admin_view('settings/index_view', array('shippings' => $shippings, 'payments' => $payments));
        }
        elseif($uri == 'shipping')
        {
            $id = $this->data->uri(4);
            $shipping = false;
            if(is_numeric($id))
            {
                $_SESSION['alias']->name .= '. Налаштування доставки #'.$id;
                $shipping = $this->cart_model->getShippings(array('id' => $id));
                if($shipping)
                    $shipping = $shipping[0];
                else
                    $this->load->page_404(false);
            }
            else if(empty($id))
                $this->redirect('admin/'.$_SESSION['alias']->alias.'/settings');
            else if($id == 'add')
                $_SESSION['alias']->name .= '. Додати просту доставку';
            else
                $this->load->page_404(false);
            $this->load->admin_view('settings/shipping_view', array('shipping' => $shipping));
        }
        elseif($uri == 'payment')
        {
            $id = $this->data->uri(4);
            $payment = false;
            if(is_numeric($id))
            {
                $_SESSION['alias']->name .= '. Налаштування оплати #'.$id;
                $payment = $this->cart_model->getPayments(array('id' => $id));
                if($payment)
                    $payment = $payment[0];
                else
                    $this->load->page_404(false);
            }
            else if(empty($id))
                $this->redirect('admin/'.$_SESSION['alias']->alias.'/settings');
            else if($id == 'add')
                $_SESSION['alias']->name .= '. Додати просту оплату';
            else
                $this->load->page_404(false);
            $this->load->admin_view('settings/payment_view', array('payment' => $payment));
        }
        else
            $this->load->page_404(false);
    }

    public function settings_change_position()
    {
        $res = array('result' => false);
        if(isset($_POST['id']) && is_numeric($_POST['position']))
        {
            $id = explode('-', $_POST['id']);
            if(count($id) == 2 && in_array($id[0], array('shipping', 'payment')) && is_numeric($id[1]))
            {
                $this->load->smodel('cart_model');
                $this->load->model('wl_position_model');

                $this->wl_position_model->table = $this->cart_model->table('_payments');
                if($id[0] == 'shipping')
                    $this->wl_position_model->table = $this->cart_model->table('_shipping');
                $newposition = $_POST['position'] + 1;
                
                if($this->wl_position_model->change($id[1], $newposition))
                    $res['result'] = true;
            }
        }
        $this->load->json($res);
    }

    public function settings_change_active()
    {
        $res = array('result' => false);
        if(isset($_POST['id']) && is_numeric($_POST['active']))
        {
            $id = explode('-', $_POST['id']);
            if(count($id) == 2 && in_array($id[0], array('shipping', 'payment')) && is_numeric($id[1]))
            {
                $this->load->smodel('cart_model');

                $table = $this->cart_model->table('_payments');
                if($id[0] == 'shipping')
                    $table = $this->cart_model->table('_shipping');

                $active = ($_POST['active'] > 0) ? 1 : 0;
                
                if($this->db->updateRow($table, array('active' => $active), $id[1]))
                    $res['result'] = true;
            }
        }
        $this->load->json($res);
    }

    public function save_shipping()
    {
        if(is_numeric($_POST['id']))
        {
            $this->load->smodel('cart_model');
            $shipping = array('wl_alias' => 0, 'active' => 0);
            $shipping['type'] = $this->data->post('type');
            $shipping['active'] = ($_POST['active'] > 0 || $_POST['id'] == 0) ? 1 : 0;
            if($_SESSION['language'])
            {
                $name = $info = array();
                foreach ($_SESSION['all_languages'] as $lang) {
                    $name[$lang] = $this->data->post('name_'.$lang);
                    $info[$lang] = $this->data->post('info_'.$lang);
                }
                $shipping['name'] = serialize($name);
                $shipping['info'] = serialize($info);
            }
            else
            {
                $shipping['name'] = $this->data->post('name');
                $shipping['info'] = $this->data->post('info');
            }

            $_SESSION['notify'] = new stdClass();
            $_SESSION['notify']->success = 'Оплату оновлено';

            if($_POST['id'] == 0)
            {
                $_SESSION['notify']->success = 'Оплату додано';
                $shipping['position'] = $this->db->getCount($this->cart_model->table('_shipping')) + 1;
                $this->db->insertRow($this->cart_model->table('_shipping'), $shipping);
            }
            else
                $this->db->updateRow($this->cart_model->table('_shipping'), $shipping, $_POST['id']);
        }
        $this->redirect('admin/'.$_SESSION['alias']->alias.'/settings');
    }

    public function delete_shipping()
    {
        if(is_numeric($_POST['id']))
        {
            $this->load->smodel('cart_model');

            if($shipping = $this->db->getAllDataById($this->cart_model->table('_shipping'), $_POST['id']))
            {
                $this->db->deleteRow($this->cart_model->table('_shipping'), $shipping->id);
                $this->db->executeQuery("UPDATE `{$this->cart_model->table('_shipping')}` SET `position` = position - 1 WHERE `position` > '{$shipping->position}'");
                if($shipping->wl_alias)
                    $this->db->deleteRow('wl_aliases_cooperation', array('alias1' => $_SESSION['alias']->id, 'alias2' => $shipping->id, 'type' => 'shipping'));
            }
        }
        $this->redirect('admin/'.$_SESSION['alias']->alias.'/settings');
    }

    public function save_payment()
    {
        if(is_numeric($_POST['id']))
        {
            $this->load->smodel('cart_model');
            $payment = array('wl_alias' => 0, 'active' => 0);
            $payment['active'] = ($_POST['active'] > 0 || $_POST['id'] == 0) ? 1 : 0;
            if($_SESSION['language'])
            {
                $name = $info = array();
                foreach ($_SESSION['all_languages'] as $lang) {
                    $name[$lang] = $this->data->post('name_'.$lang);
                    $info[$lang] = $this->data->post('info_'.$lang);
                }
                $payment['name'] = serialize($name);
                $payment['info'] = serialize($info);
            }
            else
            {
                $payment['name'] = $this->data->post('name');
                $payment['info'] = $this->data->post('info');
            }

            $_SESSION['notify'] = new stdClass();
            $_SESSION['notify']->success = 'Оплату оновлено';

            if($_POST['id'] == 0)
            {
                $_SESSION['notify']->success = 'Оплату додано';
                $payment['position'] = $this->db->getCount($this->cart_model->table('_payments')) + 1;
                $this->db->insertRow($this->cart_model->table('_payments'), $payment);
            }
            else
                $this->db->updateRow($this->cart_model->table('_payments'), $payment, $_POST['id']);
        }
        $this->redirect('admin/'.$_SESSION['alias']->alias.'/settings');
    }

    public function delete_payment()
    {
        if(is_numeric($_POST['id']))
        {
            $this->load->smodel('cart_model');

            if($payment = $this->db->getAllDataById($this->cart_model->table('_payments'), $_POST['id']))
            {
                $this->db->deleteRow($this->cart_model->table('_payments'), $payment->id);
                $this->db->executeQuery("UPDATE `{$this->cart_model->table('_payments')}` SET `position` = position - 1 WHERE `position` > '{$payment->position}'");
                if($payment->wl_alias)
                    $this->db->deleteRow('wl_aliases_cooperation', array('alias1' => $_SESSION['alias']->id, 'alias2' => $payment->id, 'type' => 'payment'));
            }
        }
        $this->redirect('admin/'.$_SESSION['alias']->alias.'/settings');
    }

    public function __tab_profile($user_id)
    {   
        if(!isset($_SESSION['option']->paginator_per_page) || $_SESSION['option']->paginator_per_page < 5)
            $_SESSION['option']->paginator_per_page = 20;
        $this->load->smodel('cart_model');
        ob_start();
        $this->load->view('admin/__tab_profile', array('orders' => $this->cart_model->getCarts(array('user' => $user_id))));
        $tab = new stdClass();
        $tab->key = $_SESSION['alias']->alias;
        $tab->name = $_SESSION['alias']->name;
        $tab->content = ob_get_contents();
        ob_end_clean();
        return $tab;
    }

}

?>
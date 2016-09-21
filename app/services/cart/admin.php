<?php

class cart extends Controller {

    function _remap($method, $data = array())
    {
        if(isset($_SESSION['alias']->name))
            $_SESSION['alias']->breadcrumb = array($_SESSION['alias']->name => '');
        if (method_exists($this, $method)) {
            if(empty($data)) $data = null;
            return $this->$method($data);
        } else {
            $this->index($method);
        }
    }

    function index($uri)
    {
        $this->load->smodel('cart_model');

        if(is_numeric($uri))
        {
            $_SESSION['alias']->breadcrumb = array($_SESSION['alias']->name => 'admin/'.$_SESSION['alias']->alias, 'Замовлення #'.$uri => '');
            $_SESSION['alias']->name .= '. Замовлення #'.$uri;

            $cartInfo = $this->cart_model->getCartInfo($uri);
            if($_SESSION['option']->useShipping && $cartInfo->shipping_id > 0)
            {
                $cartInfo->shipping = $this->load->function_in_alias($cartInfo->shipping_alias, '__get_delivery_info', $cartInfo->shipping_id);
            }

            $cartProducts = $this->getCartProducts($uri);

            $cartHistory = $this->db->getQuery("SELECT h.*, s.name as status_name, u.name as user_name FROM `s_cart_history` as h LEFT JOIN `s_cart_status` as s ON h.status = s.id LEFT JOIN `wl_users` as u ON h.user = u.id WHERE h.cart = $uri", 'array');
            $cartStatuses = $this->db->getQuery("SELECT * FROM `s_cart_status` WHERE `active` = 1 AND `weight` > (SELECT weight FROM `s_cart_status` WHERE id = $cartInfo->status )  ORDER BY weight");

            $this->load->admin_view('detal_view', array('cartInfo' => $cartInfo, 'cartProducts' => $cartProducts, 'cartHistory' => $cartHistory, 'cartStatuses' => $cartStatuses));
        } else {
            $_SESSION['option']->paginator_per_page = 25;

            $id = isset($_GET['id']) ? $_GET['id'] : 0;

            $carts = $this->cart_model->getAllCarts($id);
            $this->load->admin_view('index_view', array('carts' => $carts));
        }
    }

    public function add()
    {
        $this->load->admin_view('add_view');
    }

    public function all()
    {
        $this->load->smodel('cart_model');
        $carts = $this->cart_model->getAllCarts();

        $this->load->admin_view('all_view', array('carts' => $carts));
    }

    private function getCartProducts($id, $showInvoice = true)
    {
        $this->db->select('s_cart_products as cp', '*', $id, 'cart');
        $where = array('alias' => "#cp.alias", 'content' => "#cp.product");
        if($_SESSION['language']) $where['language'] = $_SESSION['language'];
        $this->db->join('wl_ntkd', 'name as product_name', $where);
        $this->db->join('wl_aliases', 'alias as alias_name', "#cp.alias");
        $this->db->join('s_shopshowcase_products', 'article as product_article', "#cp.product");
        $cartProducts = $this->db->get('array');

        if($showInvoice && $cartProducts){
            foreach ($cartProducts as $product) {
                if($_SESSION['option']->useStorage){
                    if($product->storage_invoice > 0 && $product->storage_alias > 0)
                    {
                        $product->invoice = $this->load->function_in_alias($product->storage_alias, '__get_Invoice', $product->storage_invoice);
                    } else {
                        $product->invoices = $this->getInvoicesByProduct($product->alias, $product->product);
                    }
                }
            }
        }

        return $cartProducts;
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
            $quantity = $invoice->amount_free >= $this->data->post('quantity') ? $this->data->post('quantity') : $invoice->amount_free;

            if($this->db->updateRow("s_cart_products", array('quantity' => $quantity), $id)){
                $total = $this->db->getQuery("SELECT SUM(quantity * price) as totalPrice FROM `s_cart_products` WHERE `cart` = $cartId")->totalPrice;

                $this->db->executeQuery("UPDATE `s_cart` SET `total` = {$total}, `date_edit` = $date WHERE `id` = $cartId");
            }
        }

        $this->redirect('admin/cart/'.$cartId.'#tabs-products');
    }

    public function saveToHistory($pay = null)
    {
        $data = $cartUpdate = $info = array();
        if($pay && isset($pay->cart_id)) {
            $cartId = $data['cart'] = $pay->cart_id;
            if(isset($pay->cart_status))
                $data['status'] = $cartUpdate['status'] = $pay->cart_status;
            else
                $data['status'] = $cartUpdate['status'] = 4;
            $cartUpdate['payment_alias'] = $pay->alias;
            $cartUpdate['payment_id'] = $pay->id;
            $data['comment'] = $pay->comment;
            $data['user'] = 0;
            unset($_SESSION['cart']);
        } else if(isset($_POST['cart'])) {
            $cartId = $data['cart'] = $this->data->post('cart');
            $data['status'] = $cartUpdate['status'] = $this->data->post('status') ? $this->data->post('status') : 1;
            $data['comment'] = $this->data->post('comment');
            $data['user'] = $_SESSION['user']->id;
        }
        $data['date'] = $cartUpdate['date_edit'] = time();

        if(!isset($cartId)) return false;
        if($this->db->insertRow('s_cart_history', $data)){
            if($this->db->updateRow('s_cart', $cartUpdate, $data['cart'])){
                $this->db->select('s_cart as c', '*', $cartId);
                $this->db->join('wl_users', 'name as user_name, email as user_email', '#c.user');
                $this->db->join('wl_user_info', 'phone1 as user_phone', '#c.user', 'user');
                $this->db->join('s_cart_status', 'name as status_name, weight', '#c.status');
                $orderInfo = $this->db->get();
                $orderInfo->products = $this->getCartProducts($cartId, false);

                if($orderInfo->weight >= 15 && $orderInfo->currency == 0)
                {
                    $currency_USD = $this->load->function_in_alias('currency', '__get_Currency', 'USD');
                    $this->db->updateRow('s_cart', array('currency' => $currency_USD), $data['cart']);
                }

                $info['id'] = $orderInfo->id;
                $info['status'] = $orderInfo->status;
                $info['status_name'] = $orderInfo->status_name;
                $info['comment'] = $data['comment'];
                $info['date'] = date('d.m.Y H:i', $orderInfo->date_edit);
                $info['user_name'] = $orderInfo->user_name;
                $info['user_email'] = $orderInfo->user_email;
                $info['user_phone'] = $orderInfo->user_phone;
                $info['link'] = SITE_URL.$_SESSION['alias']->alias.'/'.$orderInfo->id;
                $info['pay_link'] = SITE_URL.$_SESSION['alias']->alias.'/pay/'.$orderInfo->id;
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

                    if($_SESSION['option']->useStorage)
                    {
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

                $info['table'] .= '<tr><td colspan="5" align="right">Сума: $'.$info['productTotalPrice'].'</td></tr></tbody></table>';


                $this->load->library('mail');
                $this->mail->sendTemplate('changed_cart_status', $orderInfo->user_email, $info);
            }
        }

        if(isset($_POST['cart'])){
            $this->redirect('admin/cart/'.$cartId.'#tabs-history');
        }

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
        $cooperation = $this->db->getAllDataByFieldInArray('wl_aliases_cooperation', $_SESSION['alias']->id, 'alias2');
        $currency_USD = $this->load->function_in_alias('currency', '__get_Currency', 'USD');

        if($cooperation)
        {
            foreach ($cooperation as $shop) {
                if($shop->type == 'cart')
                {
                    if($key == 'article')
                    {
                        $products = $this->load->function_in_alias($shop->alias1, '__get_Products', array($key => '%'.$id));

                        $analogs = $test = array();
                        $count_products = 0;
                        $showStorages = true;

                        if($products){
                            foreach ($products as $product) {
                                $count = 0;
                                if(isset($product->options['2-analohy']) && $product->options['2-analohy']->value != ''){
                                    $productAnalogs = explode(';', $product->options['2-analohy']->value);
                                    foreach ($productAnalogs as $analog) {
                                        if(!in_array($analog, $analogs)) $analogs[] = $analog;
                                    }
                                }

                                $invoice_where = array('id' => $product->id, 'user_type' => $userType);
                                $invoices = $this->getInvoicesByProduct($product->wl_alias, $invoice_where);
                                if($invoices)
                                {
                                    foreach ($invoices as $invoice) {
                                        if($invoice->amount_free > 0) {
                                            if($showStorages)
                                            {
                                                echo("<h3>Оригінали та замінники</h3>");
                                                echo('<div class="table-responsive"><table class="table table-condensed table-bordered">');
                                                echo("<tr>");
                                                echo("<td>Бренд</td>");
                                                echo("<td>Артикул</td>");
                                                echo("<td>Опис</td>");
                                                echo("<td>Термін</td>");
                                                echo("<td>Ціна USA</td>");
                                                echo("<td>Ціна грн</td>");
                                                echo("<td>Кількість</td>");
                                                echo("<td></td>");
                                                echo("</tr>");
                                                $showStorages = false;
                                            }
                                            echo("<tr>");
                                            if($count == 0){
                                                echo "<td>". ((isset($product->options['1-vyrobnyk']) && $product->options['1-vyrobnyk']->value != '') ? nl2br($product->options['1-vyrobnyk']->value) : '')."</td>";
                                                echo("<td>{$product->article}</td>");
                                                echo "<td>".html_entity_decode($product->name)."</td>";
                                                $count++;
                                            } else echo "<td></td><td></td><td></td>";
                                            echo("<td>{$invoice->storage_time}</td>");
                                            echo("<td>\${$invoice->price_out}</td>");
                                            $invoice->price_out_uah = round($invoice->price_out * $currency_USD, 2);
                                            echo("<td>{$invoice->price_out_uah}</td>");
                                            echo("<td>{$invoice->amount_free}</td>");
                                            echo("<td><form method='post' action='".SITE_URL."admin/{$_SESSION['alias']->alias}/addProduct'><input type='hidden' value='{$userId}' name='userId'><input type='hidden' name='invoiceId' value='{$invoice->id}'><input type='hidden' name='storageId' value='{$invoice->storage}'><input type='hidden' name='cartId' value='{$cartId}'><input type='hidden' name='productId' value='{$invoice->product}' ><input type='hidden' name='price' value='{$invoice->price_out}'><input type='hidden' name='price_in' value='{$invoice->price_in}'><button type='submit' class='btn btn-sm btn-warning'>Додати</button></form></td>");
                                            echo("</tr>");
                                            $count_products++;
                                        }
                                    }
                                }
                            }
                            if(!$showStorages)
                            {
                                echo("</table></div>");
                            }
                            if($count_products == 0)
                            {
                                echo("<div class='alert alert-danger'>");
                                echo("<h4>{$product->article} {$product->name}</h4>");
                                echo("<p>Увага! Товар відсутній на складі.</p>");
                                echo("</div>");
                            }
                            if(!empty($analogs))
                            {
                                $showStorages = true;
                                foreach ($analogs as $analog) {
                                    $analog = $this->load->function_in_alias($shop->alias1, '__get_Products', array($key => '%'.$analog));
                                    if($analog)
                                    {
                                        foreach ($analog as $product) {
                                            $count = 0;
                                            if($shop->type == 'cart')
                                            {
                                                $invoice_where = array('id' => $product->id, 'user_type' => $userType);
                                                $invoices = $this->getInvoicesByProduct($product->wl_alias, $invoice_where);
                                                if($invoices)
                                                {
                                                    foreach ($invoices as $invoice) {
                                                        if($invoice->amount_free > 0) {
                                                            if($showStorages)
                                                            {
                                                                echo("<h3> Аналоги </h3>");
                                                                echo('<div class="table-responsive"><table class="table table-condensed table-bordered">');
                                                                echo("<tr>");
                                                                echo("<td>Бренд</td>");
                                                                echo("<td>Артикул</td>");
                                                                echo("<td>Опис</td>");
                                                                echo("<td>Термін</td>");
                                                                echo("<td>Ціна USA</td>");
                                                                echo("<td>Ціна грн</td>");
                                                                echo("<td>Кількість</td>");
                                                                echo("<td></td>");
                                                                echo("</tr>");
                                                                $showStorages = false;
                                                            }
                                                            echo("<tr>");
                                                            if($count == 0){
                                                                echo "<td>". ((isset($product->options['1-vyrobnyk']) && $product->options['1-vyrobnyk']->value != '') ? nl2br($product->options['1-vyrobnyk']->value) : '')."</td>";
                                                                echo("<td>{$product->article}</td>");
                                                                echo "<td>".html_entity_decode($product->name)."</td>";
                                                                $count++;
                                                            } else echo "<td></td><td></td><td></td>";
                                                            echo("<td>{$invoice->storage_time}</td>");
                                                            echo("<td>\${$invoice->price_out}</td>");
                                                            $invoice->price_out_uah = round($invoice->price_out * $currency_USD, 2);
                                                            echo("<td title='1 USD = {$currency_USD} грн'>{$invoice->price_out_uah}</td>");
                                                            echo("<td>{$invoice->amount_free}</td>");
                                                            echo("<td><form method='post' action='".SITE_URL."admin/{$_SESSION['alias']->alias}/addProduct'><input type='hidden' value='{$userId}' name='userId' /> <input type='hidden' name='invoiceId' value='{$invoice->id}' /><input type='hidden' name='storageId' value='{$invoice->storage}' /><input type='hidden' name='cartId' value='{$cartId}' /> <input type='hidden' name='productId' value='{$invoice->product}' /><input type='hidden' name='price' value='{$invoice->price_out}' /><input type='hidden' name='price_in' value='{$invoice->price_in}'> <button type='submit' class='btn btn-sm btn-warning'>Додати</button></form></td>");
                                                            echo("</tr>");
                                                            $count_products++;
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                                if(!$showStorages)
                                {
                                    echo("</table></div>");
                                }
                            }
                        }

                        if($products) return true;
                    }
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
        $data['product'] = $this->data->post('productId');
        $data['quantity'] = 1;
        $data['price'] = $this->data->post('price');
        $data['price_in'] = $this->data->post('price_in');
        $data['alias'] = $this->db->getQuery("SELECT wl_alias FROM `s_shopshowcase_products` WHERE `id` = {$data['product']} ")->wl_alias;
        $data['user'] = $this->data->post('userId') === 'false' ? $_SESSION['user']->id : $this->data->post('userId');
        $data['date'] = time();

        if(!isset($data['cart'])){
            $this->db->insertRow('s_cart', array('user' => $data['user'], 'status' => 1, 'date_add' => $data['date']));
            $data['cart'] = $this->db->getLastInsertedId();
        }

        if($this->db->insertRow('s_cart_products', $data)){
            $this->db->executeQuery("UPDATE `s_cart` SET `total` = `total` + {$data['price']}, `date_edit` = {$data["date"]} WHERE `id` = {$data['cart']}");
        }

        $this->redirect('admin/cart/'.$data['cart'].'#tabs-products');
    }

    public function findUser()
    {
        $res = array('result' => false);
        if($this->data->post('userInfo')){
            $info = $this->data->post('userInfo');

            $user = $this->db->getQuery("SELECT id,email,name,type FROM `wl_users` WHERE `name` LIKE '%{$info}%' OR `email` = '{$info}'", 'array');
            if(!$user)
            {
                $this->db->executeQuery("SELECT id FROM `wl_user_info` WHERE `phone1` LIKE '%{$info}%' OR `phone2` LIKE '%{$info}%'");
                if($this->db->numRows() == 1){
                    $userId = $this->db->getRows();
                    $user = $this->db->getQuery("SELECT id,email,name,type FROM `wl_users` WHERE `id` = $userId->id", 'array');
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
        if(trim($this->data->post('name')) != '' && ($this->data->post('email') || $this->data->post('phone'))){
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
            }


            if($res['result'] == true){
                if($email) $data['email'] = $email;
                if($phone) $userInfo['phone1'] = $phone;

                if($this->db->insertRow('wl_users', $data)){
                    $id = $res['id'] = $this->db->getLastInsertedId();

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
        }

        $this->json($res);
    }
}

?>
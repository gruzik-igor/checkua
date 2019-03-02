<?php

/*

 	Service "Shop cart 2.0"
	for WhiteLion 1.0

*/

class cart extends Controller {

    private $marketing = array();

    function __construct()
    {
        parent::__construct();
        if(empty($_SESSION['cart']))
            $_SESSION['cart'] = new stdClass();
        $_SESSION['cart']->initJsStyle = true;

        // if($cooperation = $this->db->getAllDataByFieldInArray('wl_aliases_cooperation', $_SESSION['alias']->id, 'alias1'))
        //     foreach ($cooperation as $c) {
        //         if($c->type == 'currency')
        //         {
        //             if($currency = $this->load->function_in_alias($c->alias2, '__get_Currency', 'USD'))
        //                 $_SESSION['option']->currency = $currency;
        //         }
        //         if($c->type == 'marketing')
        //             $this->marketing[] = $c->alias2;
        //     }
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
        $this->load->smodel('cart_model');

        if($id = $this->data->uri(1))
        {
            if(is_numeric($id))
            {
                if(isset($_SESSION['user']->id))
                {
                    if($cart = $this->cart_model->getById($id))
                    {
                        $this->wl_alias_model->setContent($id);
                        $_SESSION['alias']->name = $_SESSION['alias']->title = $this->text('Замовлення №').$id;
                        if($cart->user == $_SESSION['user']->id || $this->userCan())
                        {
                            $_SESSION['alias']->breadcrumbs = array($this->text('До всіх замовлень') => $_SESSION['alias']->alias.'/my', $this->text('Замовлення №').$id => '');

                            if($cart->products)
                                foreach ($cart->products as $product) {
                                    $product->info = $this->load->function_in_alias($product->product_alias, '__get_Product', $product->product_id);
                                    if($product->storage_invoice)
                                        $product->storage = $this->load->function_in_alias($product->product_alias, '__get_Invoice', array('id' => $product->storage_invoice, 'user_type' => $product->user_type));
                                }

                            $cart->shipping = $cart->payment = false;
                            if($cart->shipping_id && !empty($cart->shipping_info))
                            {
                                $cart->shipping_info = unserialize($cart->shipping_info);
                                if($cart->shipping = $this->cart_model->getShippings(array('id' => $cart->shipping_id)))
                                {
                                    $cart->shipping = $cart->shipping[0];
                                    if($_SESSION['language'])
                                    {
                                        @$name = unserialize($cart->shipping->name);
                                        if(isset($name[$_SESSION['language']]))
                                            $cart->shipping->name = $name[$_SESSION['language']];
                                        else if(is_array($name))
                                            $cart->shipping->name = array_shift($name);
                                        @$info = unserialize($cart->shipping->info);
                                        if(isset($info[$_SESSION['language']]))
                                            $cart->shipping->info = $info[$_SESSION['language']];
                                        else if(is_array($info))
                                            $cart->shipping->info = array_shift($info);
                                    }
                                    $cart->shipping->text = '';
                                    if($cart->shipping->wl_alias)
                                        $cart->shipping->text = $this->load->function_in_alias($cart->shipping->wl_alias, '__get_info', $cart->shipping_info);  
                                }
                            }
                            
                            if($cart->payment_alias && $cart->payment_id)
                                $cart->payment = $this->load->function_in_alias($cart->payment_alias, '__get_info', $cart->payment_id);
                            else if($cart->payment_id)
                            {
                                $cart->payment = $this->cart_model->getPayments(array('id' => $cart->payment_id));
                                if($cart->payment)
                                    $cart->payment = $cart->payment[0];
                            }

                            if($this->data->uri(2) == 'print')
                                $this->load->view('detal_view', array('cart' => $cart, 'controls' => false));
                            elseif($this->data->uri(2) == 'pay')
                            {
                                $cooperation_where['alias1'] = $_SESSION['alias']->id;
                                $cooperation_where['type'] = 'payment';
                                $ntkd = array('alias' => '#c.alias2', 'content' => 0);
                                if($_SESSION['language'])
                                    $ntkd['language'] = $_SESSION['language'];
                                $payments = $this->db->select('wl_aliases_cooperation as c', 'alias2 as id', $cooperation_where)
                                                        ->join('wl_ntkd', 'name, list as info', $ntkd)
                                                        ->get('array');
                                $this->load->page_view('pay_view', array('cart' => $cart, 'payments' => $payments));
                            }
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
                    $this->redirect('login');
            }
            else
                $this->load->page_404();
        }

        $this->wl_alias_model->setContent();
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
        if($this->userIs())
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
            $this->load->profile_view('list_view', array('orders' => $this->cart_model->getCarts(array('user' => $user))));
        }
        else
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
                    $product->name = html_entity_decode($product->name, ENT_QUOTES, 'utf-8');
                    $product->product_alias = $wl_alias;
                    $product->product_id = $id;
                    $product->quantity = $this->data->post('quantity');
                    $product->product_options = array();
                    if(!empty($_POST['options']) && is_array($_POST['options']))
                    {
                        $list = $changePrice = array();
                        foreach ($_POST['options'] as $option) {
                            $option = explode(':', $option, 2);
                            if(count($option) == 2 && is_numeric($option[0]))
                            {
                                if($info = $this->load->function_in_alias($wl_alias, '__get_Option_Info', $option[0]))
                                {
                                    if(!empty($info->values))
                                        foreach ($info->values as $value) {
                                            if($value->id == $option[1])
                                            {
                                                $list[$info->name] = $value->name;
                                                break;
                                            }
                                        }
                                    if(isset($info->changePrice) && $info->changePrice)
                                        $changePrice[$info->id] = $option[1];
                                }
                            }
                        }
                        if(!empty($changePrice))
                            $product->price = $this->load->function_in_alias($wl_alias, '__get_Price_With_options', array('product' => $product->product_id, 'options' => $changePrice));
                        if(!empty($list))
                        {
                            $product->product_options = $list;
                            $list = serialize($list);
                            $product->key .= '-'.md5($list);
                        }
                    }
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
                    if(isset($product->discount))
                    	$product->discount *= $product->quantity;
                    $this->load->smodel('cart_model');
                    if(isset($_SESSION['user']->id))
                        $product->key = $this->cart_model->addProduct($product);
                    else
                        $_SESSION['cart']->products[$product->key] = $product;
                    $product->priceFormat = $this->cart_model->priceFormat($product->price);
                    $openProduct = new stdClass();
                    $openProduct->key = $product->key;
                    $openProduct->priceFormat = $product->priceFormat;
                    $openProduct->quantity = $product->quantity;
                    $openProduct->admin_photo = $product->admin_photo;
                    $openProduct->link = $product->link;
                    $openProduct->name = $product->name;
                    $openProduct->product_options = '';
                    if(!empty($product->product_options))
                        foreach ($product->product_options as $key => $value) {
                            if(!empty($openProduct->product_options))
                                $openProduct->product_options .= '<br>';
                            $openProduct->product_options .= $key.': '.$value;
                        }
                    $res['product'] = $openProduct;
                    $res['subTotal'] = $this->cart_model->getSubTotalInCart(0, false);
                    $res['subTotalFormat'] = $this->cart_model->priceFormat($res['subTotal']);
                    $res['productsCountInCart'] = $this->cart_model->getProductsCountInCart();
                    $res['discountTotal'] = $this->cart_model->priceFormat($this->cart_model->discountTotal);
                    $res['result'] = true;
                }
            }
        }
        unset($_SESSION['alias-cache'][$_SESSION['alias']->id]);
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
                        ///////// -- marketing -- ////////
                        // $shopProduct = $this->db->select('s_shopshowcase_products', 'price', $product->product_id)->get();
                        // $product->discount = 0;
                        // $product->currency = $this->load->function_in_alias(17, '__get_Currency', 'USD');
                        // $product->price = $shopProduct->price * $product->currency;
                        // $product->price = round($product->price * 20) / 20;
                        // if($quantity > 1)
                        //     $product = $this->load->function_in_alias(19, '__get_Product', $product);
                        ///////// -- marketing -- ////////
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
                                                $res['priceFormat'] = $this->cart_model->priceFormat($product->price);
                                                $res['priceSumFormat'] = $this->cart_model->priceFormat($product->price * $quantity);
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
                                    $data['price'] = $product->price;
                                    $data['discount'] = $product->discount;
                                    if($this->db->updateRow($this->cart_model->table('_products'), $data, $id))
                                    {
                                        $res['result'] = true;
                                        $res['price'] = $product->price;
                                        $res['quantity'] = $quantity;
                                        $res['priceFormat'] = $this->cart_model->priceFormat($product->price);
                                        $res['priceSumFormat'] = $this->cart_model->priceFormat($product->price * $quantity);
                                        $res['subTotal'] = $this->cart_model->getSubTotalInCart();
                                        $res['discountTotal'] = $this->cart_model->priceFormat($this->cart_model->discountTotal);
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
                                $res['priceFormat'] = $_SESSION['cart']->products[$id]->priceFormat;
                                $res['priceSumFormat'] = $this->cart_model->priceFormat($_SESSION['cart']->products[$id]->price * $quantity);
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
                    ///////// -- marketing -- ////////
                    // $shopProduct = $this->db->select('s_shopshowcase_products', 'price', $_SESSION['cart']->products[$id]->product_id)->get();
                    // $_SESSION['cart']->discount = 0;
                    
                    // $_SESSION['cart']->products[$id]->currency = $this->load->function_in_alias(17, '__get_Currency', 'USD');
                    // $_SESSION['cart']->products[$id]->price = round($shopProduct->price * $_SESSION['cart']->products[$id]->currency * 20) / 20;
                    // if($quantity > 1)
                    //     $_SESSION['cart']->products[$id] = $this->load->function_in_alias(19, '__get_Product', $_SESSION['cart']->products[$id]);
                    ///////// -- marketing -- ////////
                    $_SESSION['cart']->products[$id]->priceFormat = $this->cart_model->priceFormat($_SESSION['cart']->products[$id]->price);
                    $res['priceFormat'] = $_SESSION['cart']->products[$id]->priceFormat;
                    $res['priceSumFormat'] = $this->cart_model->priceFormat($_SESSION['cart']->products[$id]->price * $quantity);
                    $res['subTotal'] = $this->cart_model->getSubTotalInCart();
                    $res['discountTotal'] = $this->cart_model->priceFormat($this->cart_model->discountTotal);
                }
            }
            else
                $res['error'] = $this->text('Товар у корзині не ідентифіковано');
        }
        $this->load->json($res);
    }

    public function login()
    {
        if($this->data->post('email') && $this->data->post('password'))
        {
            $_SESSION['notify'] = new stdClass();

            $key = 'email';
            $email_phone = $this->data->post('email');
            $password = $this->data->post('password');
            $this->load->library('validator');
            if(!$this->validator->email('email', $email_phone))
            {
                if($email_phone = $this->validator->getPhone($email_phone))
                {
                    $key = 'phone';
                    $password = $email_phone;
                }
                else
                    $key = false;
            }

            if($key)
            {
                $this->load->model('wl_user_model');
                if($this->wl_user_model->login($key, $password))
                {
                    if(!empty($_SESSION['cart']->products))
                    {
                        $this->load->smodel('cart_model');
                        foreach ($_SESSION['cart']->products as $product) {
                            $this->cart_model->addProduct($product, $_SESSION['user']->id);
                        }
                        $_SESSION['cart']->products = NULL;
                    }

                    if(date('H') > 18 || date('H') < 6)
                        $_SESSION['notify']->success = $this->text('Доброго вечора').', <strong>'.$_SESSION['user']->name.'</strong>! '.$this->text('Дякуємо що повернулися');
                    else
                        $_SESSION['notify']->success = $this->text('Доброго дня').', <strong>'.$_SESSION['user']->name.'</strong>! '.$this->text('Дякуємо що повернулися');
                }
                else
                    $_SESSION['notify']->error = $this->text('Неправильно введено email/телефон або пароль');
            }
            else
                $_SESSION['notify']->error = $this->text('Невірний формат email/номеру телефону');
        }
        $this->redirect();
    }

    public function checkEmail()
    {
        $res = array('result' => false, 'message' => '');
        if($email = $this->data->post('email'))
        {
            $this->load->model('wl_user_model');
            $user = new stdClass();
            if($this->wl_user_model->userExists($email, $user))
            {
                $res['result'] = true;
                $res['email'] = $email;
                $res['message'] = '<p>'.$this->text('Доброго дня,', 0);
                if(!empty($user->name))
                    $res['message'] .= ' <b>'.$user->name.'</b>';
                $res['message'] .= '</p><p>';
                $res['message'] .= $this->text('У магазині за Вашою email адресою <b>наявний персональний кабінет покупця</b>. <u>Ваші персональні дані - найвища цінність для нас!</u><p> Просимо вибачення за дискомфорт, та змушені просити Вас <b>ввести пароль</b>, який Ви отримали при здійсненні першої покупки <br>(знайдіть лист у Вашій електронній скринці з інформацією про першу покупку) або встановили його самостійно в процесі реєстрації. </p><p>Якщо не можете знайти/згадати пароль доступу до кабінету, пропонуємо скористатися процедурою відновлення паролю. </p><p>З повагою, адміністрація '.SITE_NAME).'</p>';
            }
        }
        if($this->data->post('ajax') == true)
            $this->load->json($res);
        else
            return $res;
    }

    public function confirm()
    {
        $this->load->smodel('cart_model');
        if($products = $this->cart_model->getProductsInCart())
        {
            $this->load->library('validator');
            $this->validator->setRules($this->text('Контактний номер'), $this->data->post('phone'), 'required|phone');
            if(!$this->userIs())
            {
                $this->validator->setRules($this->text('email'), $this->data->post('email'), 'required|email');
                $this->validator->setRules($this->text('Ім\'я Прізвище'), $this->data->post('name'), 'required|5..50');
            }

            if($this->validator->run())
            {
                $_POST['phone'] = $this->validator->getPhone($_POST['phone']);
                $new_user = $new_user_password = false;
                if(!$this->userIs())
                {
                    $check = $this->checkEmail();
                    if($check['result'])
                    {
                        $_SESSION['notify'] = new stdClass();
                        $_SESSION['notify']->error = $check['message'];
                        $this->redirect();
                    }

                    $this->load->model('wl_user_model');
                    $info = $additionall = array();
                    $info['status'] = 1;
                    $info['email'] = $this->data->post('email');
                    $info['name'] = $this->data->post('name');
                    $info['photo'] = NULL;
                    $info['password'] = $new_user_password = bin2hex(openssl_random_pseudo_bytes(4));
                    $additionall = array();
                    if(!empty($this->cart_model->additional_user_fields))
                        foreach ($this->cart_model->additional_user_fields as $key) {
                            $additionall[$key] = $this->data->post($key);
                        }
                    if($user = $this->wl_user_model->add($info, $additionall, $_SESSION['option']->newUserType, true, 'cart autoregister'))
                        $this->wl_user_model->setSession($user);
                    $new_user = true;
                }
                else
                    $this->cart_model->updateAdditionalUserFields($_SESSION['user']->id);

                if(!empty($_SESSION['cart']->products))
                    foreach ($_SESSION['cart']->products as $product) {
                        $this->cart_model->addProduct($product, $_SESSION['user']->id);
                    }

                $delivery = array('id' => 0, 'recipient' => '', 'info' => '', 'text' => '');
                if($shippingId = $this->data->post('shipping-method'))
                    if(is_numeric($shippingId))
                        if($shipping = $this->db->getAllDataById($this->cart_model->table('_shipping'), array('id' => $shippingId, 'active' => 1)))
                        {
                            $delivery['id'] = $shipping->id;
                            if($shipping->wl_alias)
                            {
                                $info = $this->load->function_in_alias($shipping->wl_alias, '__set_Shipping_from_cart');
                                if(!empty($info['info']))
                                    $delivery['info'] = $info['info'];
                                if(!empty($info['text']))
                                    $delivery['text'] = $info['text'];
                            }
                            else
                            {
                                $info = array();
                                if($city = $this->data->post('shipping-city'))
                                {
                                    $info['city'] = $city;
                                    $delivery['text'] .= $this->text('Місто').': '.$city;
                                }
                                if($department = $this->data->post('shipping-department'))
                                {
                                    $info['department'] = $department;
                                    $delivery['text'] .= ' '.$this->text('Відділення').': '.$department;
                                }
                                if($address = $this->data->post('shipping-address'))
                                {
                                    $info['address'] = $address;
                                    $delivery['text'] .= '<br>'.$this->text('Адреса').': '.$address;
                                }
                                $delivery['info'] = $info;
                            }

                            $recipient = $this->data->post('recipient');
                            $delivery['info']['phone'] = $_POST['phone'];
                            if(empty($recipient))
                                $recipient = $_SESSION['user']->name;
                            $delivery['info']['recipient'] = $recipient;
                            if($shipping->pay >= 0)
                            {
                                $delivery['pay'] = $delivery['info']['pay'] = $shipping->pay;
                                $delivery['price'] = $delivery['info']['price'] = $shipping->price;
                            }
                            $delivery['text'] .= '<br><br>'.$this->text('Отримувач').': '.$recipient.', '.$delivery['info']['phone'];
                        }

                $payment = false;
                if($payment_method = $this->data->post('payment_method'))
                    if($payment = $this->cart_model->getPayments(array('id' => $payment_method, 'active' => 1)))
                        $payment = $payment[0];

                if($cart = $this->cart_model->checkout($_SESSION['user']->id, $delivery, $payment))
                {
                    unset($_SESSION['cart']);

                    $this->load->library('mail');

                    $cart['date'] = date('d.m.Y H:i');
                    $cart['user_name'] = $_SESSION['user']->name;
                    $cart['user_email'] = $_SESSION['user']->email;
                    $cart['user_phone'] = $_POST['phone'];
                    $cart['new_user'] = $new_user;
                    if($new_user && $new_user_password)
                        $cart['password'] = $new_user_password;
                    $cart['link'] = SITE_URL.$_SESSION['alias']->alias.'/'.$cart['id'];
                    $cart['admin_link'] = SITE_URL.'admin/'.$_SESSION['alias']->alias.'/'.$cart['id'];
                    foreach ($products as $product) {
                        $product->info = $this->load->function_in_alias($product->product_alias, '__get_Product', $product->product_id);
                        $product->sum = $this->cart_model->priceFormat($product->price * $product->quantity);
                        $product->price = $this->cart_model->priceFormat($product->price);
                    }
                    $cart['total_formatted'] = $this->cart_model->priceFormat($cart['total']);
                    if($cart['discount'])
                    {
                        $sum = $cart['total'] + $cart['discount'];
                        if(!empty($delivery['price']))
                            $sum -= $delivery['price'];
                        $cart['sum_formatted'] = $this->cart_model->priceFormat($sum);
                        $cart['discount_formatted'] = $this->cart_model->priceFormat($cart['discount']);
                    }
                    $cart['products'] = $products;
                    $cart['delivery'] = $delivery['text'];
                    if(!empty($delivery['price']))
                        $cart['delivery_price'] = $this->cart_model->priceFormat($delivery['price']);
                    $cart['payment'] = $payment ? $payment->name : '';
                    
                    $this->mail->sendTemplate('checkout', $_SESSION['user']->email, $cart);
                    $this->mail->sendTemplate('checkout_manager', SITE_EMAIL, $cart);

                    if($payment && $payment->wl_alias > 0)
                    {
                        $pay = new stdClass();
                        $pay->id = $cart['id'];
                        $pay->total = $cart['total'];
                        $pay->wl_alias = $_SESSION['alias']->id;
                        $pay->return_url = $_SESSION['alias']->alias.'/'.$cart['id'];
                        
                        $this->load->function_in_alias($payment->wl_alias, '__get_Payment', $pay);
                    }
                    else 
                    {
                        $this->wl_alias_model->setContent(2);
                        $this->load->page_view('success_view', array('cart' => $cart));
                    }
                }
            }
            else
            {
                $_SESSION['notify-Cart'] = new stdClass();
                $_SESSION['notify-Cart']->error = $this->validator->getErrors();
                $this->redirect();
            }
        }
        else
            $this->redirect($_SESSION['alias']->alias);
    }

    public function checkout()
    {
        $this->load->smodel('cart_model');

        if(!empty($_SESSION['cart']->products) && $this->userIs())
        {
            foreach ($_SESSION['cart']->products as $product) {
                $this->cart_model->addProduct($product, $_SESSION['user']->id);
            }
            $_SESSION['cart']->products = NULL;

            $_SESSION['notify'] = new stdClass();
            if(date('H') > 18 || date('H') < 6)
                $_SESSION['notify']->success = $this->text('Доброго вечора').', <strong>'.$_SESSION['user']->name.'</strong>! '.$this->text('Дякуємо що повернулися');
            else
                $_SESSION['notify']->success = $this->text('Доброго дня').', <strong>'.$_SESSION['user']->name.'</strong>! '.$this->text('Дякуємо що повернулися');
        }

        if($products = $this->cart_model->getProductsInCart())
        {
            $user_type = $subTotal = $bonus = 0;
            if(isset($_SESSION['user']->type))
                $user_type = $_SESSION['user']->type;
            
            foreach ($products as $product) {
                $product->info = $this->load->function_in_alias($product->product_alias, '__get_Product', $product->product_id);
                if($product->storage_invoice)
                {
                    if($product->storage = $this->load->function_in_alias($product->storage_alias, '__get_Invoice', array('id' => $product->storage_invoice, 'user_type' => $user_type)))
                        $product->price = $product->storage->price_out;
                }
                $subTotal += $product->price * $product->quantity;
                $product->priceFormat = $this->cart_model->priceFormat($product->price);
                if(isset($product->bonus) && $product->bonus < 0 && $bonus == 0)
                    $bonus = $product->bonus;
            }

            $total = $subTotal;
            if($discount = $this->cart_model->getBonusDiscount(-$bonus, $subTotal))
                $total -= $discount;

            $showPayment = true;
            $payments = false;
            if($status = $this->db->getAllDataById($this->cart_model->table('_status'), 10, 'weight'))
                if($status->active)
                    $showPayment = true;
            if($showPayment)
                $payments = $this->cart_model->getPayments(array('active' => 1));

            $shippings = $this->cart_model->getShippings(array('active' => 1));
            $userShipping = $this->cart_model->getUserShipping();
            if($shippings && ($shippings[0]->pay >= 0 || $shippings[0]->pay > $total))
                $total += $shippings[0]->price;

            $this->wl_alias_model->setContent(1);
            $this->load->page_view('checkout_view', array('products' => $products, 'shippings' => $shippings,  'userShipping' => $userShipping, 'payments' => $payments, 'subTotal' => $this->cart_model->priceFormat($subTotal), 'total' => $this->cart_model->priceFormat($total), 'bonusCodes' => $this->cart_model->bonusCodes()));
        }
        else
            $this->redirect($_SESSION['alias']->alias);
    }

    public function coupon()
    {
        if($code = $this->data->post('code'))
        {
            $this->load->smodel('cart_model');
            if($this->cart_model->applayBonusCode($code))
            {
                $_SESSION['notify'] = new stdClass();
                $_SESSION['notify']->success = $this->text('Бонус-код застосовано!');
            }
            else
            {
                $_SESSION['notify-Cart'] = new stdClass();
                $_SESSION['notify-Cart']->error = $this->text('Бонус-код невірний або застарів');
            }
        }
        else
        {
            $_SESSION['notify-Cart'] = new stdClass();
            $_SESSION['notify-Cart']->error = $this->text('Введіть бонус-код');
        }
        $this->redirect();
    }

    public function pay()
    {
        if(isset($_POST['method']) && is_numeric($_POST['method']) && isset($_POST['cart']) && is_numeric($_POST['cart']))
        {
            if($cart = $this->db->getAllDataById('s_cart', $_POST['cart']))
            {
                $cart->return_url = $_SESSION['alias']->alias.'/'.$cart->id;
                $cart->wl_alias = $_SESSION['alias']->id;

                $this->load->function_in_alias($this->data->post('method'), '__get_Payment', $cart);
                exit;
            }
        }
        else
            $this->redirect();
    }

    public function get_Shipping_to_cart()
    {
        if($id = $this->data->post('shipping'))
        {
            $this->load->smodel('cart_model');
            $userShipping = $this->cart_model->getUserShipping();
            if($shipping = $this->cart_model->getShippings(array('id' => $id, 'active' => 1)))
                $this->load->function_in_alias($shipping[0]->wl_alias, '__get_Shipping_to_cart', $userShipping);
        }
    }

    public function __show_btn_add_product($product)
    {
        if(!empty($product))
            $this->load->view('__btn_add_product_subview', array('product' => $product));
        else
            echo "<p>Увага! Відсутня інформація про товар! (для генерації кнопки Додати товар до корзини)";
        return true;
    }

    public function __show_minicart()
    {
        $this->load->smodel('cart_model');
        $user_type = $subTotal = 0;
        if(isset($_SESSION['user']->type))
            $user_type = $_SESSION['user']->type;
        $products = $this->cart_model->getProductsInCart();
        if($products)
            foreach ($products as $product) {
                $product->info = $this->load->function_in_alias($product->product_alias, '__get_Product', $product->product_id);
                if($product->storage_invoice)
                {
                    if($product->storage = $this->load->function_in_alias($product->storage_alias, '__get_Invoice', array('id' => $product->storage_invoice, 'user_type' => $user_type)))
                        $product->price = $product->storage->price_out;
                }
                $subTotal += $product->price * $product->quantity;
                $product->priceFormat = $this->cart_model->priceFormat($product->price);
            }
        $this->load->view('__minicart_subview', array('products' => $products, 'subTotal' => $this->cart_model->priceFormat($subTotal)));
        return true;
    }

    public function __get_cart_statuses()
    {
        $this->load->smodel('cart_model');
        return $this->cart_model->getStatuses();
    }

    public function __get_user_orders($user)
    {
        $this->load->smodel('cart_model');
        return $this->cart_model->getCarts(array('user' => $user));
    }

    public function __get_Search($content)
    {
        return false;
    }

}

?>
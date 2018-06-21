<?php

class cart_model
{
	public $additional_user_fields = array('phone');

	public function table($sufix = '', $useAliasTable = false)
	{
		if($useAliasTable) return $_SESSION['service']->table.$sufix.$_SESSION['alias']->table;
		return $_SESSION['service']->table.$sufix;
	}

	public function getActionByStatus($status, $getWeight = true)
	{
		if($getWeight)
		{
			if($status = $this->db->getAllDataById($this->table('_status', $status)))
			{
				if($status->weight <= 10)
					return 'new';
				if($status->weight < 20)
					return 'paid';
				if($status->weight < 30)
					return 'delivered';
				if($status->weight >= 90)
					return 'closed';
			}
		}
		elseif(is_numeric($status))
		{
			if($status <= 10)
				return 'new';
			if($status < 20)
				return 'paid';
			if($status < 30)
				return 'delivered';
			if($status >= 90)
				return 'closed';
		}
		return false;
	}

	public function getStatuses($active = true)
	{
		if($active)
			return $this->db->getAllDataByFieldInArray($this->table('_status', 1, 'active'));
		else
			return $this->db->getAllData($this->table('_status'));
	}

	public function getCarts($where = false)
	{
		if($where)
	    	$this->db->select($this->table().' as c', '*', $where);
	    else
	    	$this->db->select($this->table().' as c');
		$this->db->join($this->table('_status'), 'name as status_name, color as status_color', '#c.status');
		$this->db->join('wl_users as u', 'name as user_name, email as user_email, type as user_type, alias as user_alias', '#c.user');
		$this->db->join('wl_user_types', 'title as user_type_name', '#u.type');
		if(!empty($this->additional_user_fields))
			foreach ($this->additional_user_fields as $key => $field) {
				$this->db->join('wl_user_info as ui_'.$key, 'value as user_'.$field, array('field' => $field, 'user' => "#c.user"));
			}
		$this->db->group('id', 'c');
		$this->db->order('date_add DESC', 'c');

		if(isset($_SESSION['option']->paginator_per_page) && $_SESSION['option']->paginator_per_page > 0)
		{
			$start = 0;
			if(isset($_GET['page']) && is_numeric($_GET['page']) && $_GET['page'] > 1) {
				$start = ($_GET['page'] - 1) * $_SESSION['option']->paginator_per_page;
			}
			$this->db->limit($start, $_SESSION['option']->paginator_per_page);
		}

		$carts = $this->db->get('array', false);
		if($carts)
			$_SESSION['option']->paginator_total = $this->db->get('count');
		else
		{
			$_SESSION['option']->paginator_total = 0;
			$this->db->clear();
		}

		return $carts;
	}

	public function getById($id, $allInfo = true)
	{
		if(is_numeric($id) && $id > 0)
		{
			$this->db->select($this->table().' as c', '*', $id);
			$where = array('field' => "phone", 'user' => "#c.user");
			$this->db->join($this->table('_status'), 'name as status_name, weight as status_weight', '#c.status');
			$this->db->join('wl_users as u', 'name as user_name, email as user_email, type as user_type, alias as user_alias', '#c.user');
			$this->db->join('wl_user_types', 'title as user_type_name', '#u.type');
			if(!empty($this->additional_user_fields))
				foreach ($this->additional_user_fields as $key => $field) {
					$this->db->join('wl_user_info as ui_'.$key, 'value as user_'.$field, array('field' => $field, 'user' => "#c.user"));
				}
			$this->db->limit(1);

			if($cart = $this->db->get())
			{
				if($allInfo)
				{
					$cart->products = $this->db->getAllDataByFieldInArray($this->table('_products'), $cart->id, 'cart');
					$cart->action = $this->getActionByStatus($cart->status_weight, false);
					$cart->history = $this->db->select($this->table('_history') .' as h', '*', $cart->id, 'cart')
												->join($this->table('_status'), 'name as status_name', '#h.status')
												->join('wl_users', 'name as user_name', '#h.user')
												->order('date ASC')
												->get('array');
				}
				return $cart;
			}
		}
		return false;
	}

	public function getProductsInCart($user = 0)
	{
		if(isset($_SESSION['user']->id))
		{
			$where_cp = array();
			$where_cp['user'] = ($user == 0) ? $_SESSION['user']->id : $user;
			$where_cp['cart'] = 0;
			if($products = $this->db->getAllDataByFieldInArray($this->table('_products'), $where_cp))
			{
				foreach ($products as $product) {
					$product->key = $product->id;
					$product->product_options = unserialize($product->product_options);
				}
				return $products;
			}
		}
		elseif(isset($_SESSION['cart']->products))
			return $_SESSION['cart']->products;
		return false;
	}

	public function getSubTotalInCart($user = 0, $priceFormat = true)
	{
		$subTotal = 0;
		if($products = $this->getProductsInCart($user))
			foreach ($products as $product) {
				$subTotal += $product->price * $product->quantity;
			}
		if($priceFormat)
			return $this->priceFormat($subTotal);
		return $subTotal;
	}

	public function getProductInfo($where = array())
	{
		if(!empty($where))
			return $this->db->select($this->table('_products') .' as p', '*', $where)
							->join($this->table(), 'status, shipping_alias, shipping_id, payment_alias, payment_id, total, comment, date_add, date_edit', '#p.cart')
							->get();
		return false;
	}

	public function addProduct($product, $user = 0, $cart = 0)
	{
		$cart_product = array('cart' => $cart);
		$cart_product['user'] = ($user == 0) ? $_SESSION['user']->id : $user;
		$cart_product['product_alias'] = $product->wl_alias;
		$cart_product['product_id'] = $product->id;
		if(empty($product->product_options))
			$cart_product['product_options'] = '';
		else
			$cart_product['product_options'] = serialize($product->product_options);
		$cart_product['storage_alias'] = $product->storage_alias;
		$cart_product['storage_invoice'] = $product->storage_invoice;
		if($inCart = $this->db->getAllDataById($this->table('_products'), $cart_product))
		{
			$update = array();
			$update['price'] = $product->price;
			if($product->storage_invoice && isset($product->price_in))
				$update['price_in'] = $product->price_in;
			else
				$update['price_in'] = $product->price;
			$update['quantity'] = $cart_product['quantity_wont'] = $product->quantity;
			$update['discount'] = (isset($price->discount)) ? $product->discount : 0;
			$update['date'] = time();
			$this->db->updateRow($this->table('_products'), $update, $inCart->id);

			return $inCart->id;
		}
		else
		{
			$cart_product['price'] = $product->price;
			if($product->storage_invoice && isset($product->price_in))
				$cart_product['price_in'] = $product->price_in;
			else
				$cart_product['price_in'] = $product->price;
			$cart_product['quantity'] = $cart_product['quantity_wont'] = $product->quantity;
			$cart_product['quantity_returned'] = 0;
			$cart_product['discount'] = (isset($price->discount)) ? $product->discount : 0;
			$cart_product['date'] = time();

			return $this->db->insertRow($this->table('_products'), $cart_product);
		}
	}

	public function checkout($user, $delivery = array())
	{
		$cart = array();
		$cart['user'] = $user;
		$cart['status'] = 1;
		$cart['shipping_alias'] = (isset($delivery['shipping_alias'])) ? $delivery['shipping_alias'] : 0;
		$cart['shipping_id'] = (isset($delivery['shipping_id'])) ? $delivery['shipping_id'] : 0;
		$cart['payment_alias'] = $this->data->post('payment_method');
		$cart['payment_id'] = 0;
		$cart['total'] = $this->getSubTotalInCart($user, false);
		$cart['comment'] = $this->data->post('comment');
		$cart['date_add'] = $cart['date_edit'] = time();
		$cart_id = $this->db->insertRow($this->table(), $cart);
		$cart['id'] = $cart_id;

		$where = array('user' => $user, 'cart' => 0);
		$this->db->updateRow($this->table('_products'), array('cart' => $cart_id), $where);

		return $cart;
	}

	public function updateAdditionalUserFields($user_id)
	{
		if(!empty($this->additional_user_fields))
		{
			$exist = array();
			if($infos = $this->db->getAllDataByFieldInArray('wl_user_info', $user_id, 'user'))
				foreach ($infos as $info) {
					if(isset($exist[$info->field]))
						$exist[$info->field][] = $info->value;
					else
						$exist[$info->field] = array($info->value);
				}
			foreach ($this->additional_user_fields as $key) {
                if($value = $this->data->post($key))
                {
                	if(isset($exist[$key]))
                	{
                		if(!in_array($value, $exist[$key]))
                		{
                			$data = array('user' => $user_id, 'date' => time());
							$data['field'] = $key;
							$data['value'] = $value;
                			$this->db->insertRow('wl_user_info', $data);
                		}
                	}
                	else
                	{
                		$data = array('user' => $user_id, 'date' => time());
						$data['field'] = $key;
						$data['value'] = $value;
	        			$this->db->insertRow('wl_user_info', $data);
                	}
                }
            }
		}
		
		$user = $this->db->select('wl_users as u', 'name', $user_id)->get();
		if(empty($user->name) && isset($_POST['receiver']))
            $this->db->updateRow('wl_users', array('name' => $this->data->post('receiver')), $_SESSION['user']->id);
        
        return true;
	}

	public function getPaymentName($payment_id)
	{
		if($payment_id > 0)
		{
			$where = array('content' => 0);
	        $where['alias'] = $payment_id;
	        if($_SESSION['language'])
	            $where['language'] = $_SESSION['language'];
	        if($payment = $this->db->getAllDataById('wl_ntkd', $where))
	            return $payment->name;
	    }
	    else
	    {
	    	if($payment = $this->db->getAllDataById($this->table('_payment_simple'), -$payment_id))
	            return $payment->name;
	    }
	    return false;
	}

	public function priceFormat($price)
	{
		if(!is_array($_SESSION['option']->price_format) && !empty($_SESSION['option']->price_format))
			$_SESSION['option']->price_format = unserialize($_SESSION['option']->price_format);

		$before = $after = '';
		$round = 2;
		if(isset($_SESSION['option']->price_format['before']))
			$before = $_SESSION['option']->price_format['before'];
		if(isset($_SESSION['option']->price_format['after']))
			$after = $_SESSION['option']->price_format['after'];
		if(isset($_SESSION['option']->price_format['round']))
			$round = $_SESSION['option']->price_format['round'];

		$text = $before . round($price, $round) . $after;
		return $text;
	}

}

?>
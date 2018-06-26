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

	public function getShippings($where = array())
	{
		$where_ntkd = array('alias' => '#s.wl_alias', 'content' => 0);
		if($_SESSION['language'])
			$where_ntkd['language'] = $_SESSION['language'];
		$this->db->select($this->table('_shipping').' as s', '*', $where)
				->join('wl_aliases', 'alias', '#s.wl_alias')
				->join('wl_ntkd', 'name as shipping_name, list as shipping_info', $where_ntkd)
				->order('position');
		if($shippings = $this->db->get('array'))
		{
			$shippings_ids = array();
			foreach ($shippings as $shipping) {
				if($shipping->wl_alias > 0)
				{
					if(!in_array($shipping->wl_alias, $shippings_ids))
        				$shippings_ids[] = $shipping->wl_alias;
					$shipping->name = $shipping->shipping_name;
					$shipping->info = $shipping->shipping_info;
					unset($shipping->shipping_name, $shipping->shipping_info);
				}
				else if($_SESSION['language'] && empty($where['id']))
				{
					@$name = unserialize($shipping->name);
					if(isset($name[$_SESSION['language']]))
						$shipping->name = $name[$_SESSION['language']];
					else if(is_array($name))
						$shipping->name = array_shift($name);
					@$info = unserialize($shipping->info);
					if(isset($info[$_SESSION['language']]))
						$shipping->info = $info[$_SESSION['language']];
					else if(is_array($info))
						$shipping->info = array_shift($info);
				}
			}
			if(empty($where))
			{
				$cooperation_where = array();
				$cooperation_where['alias1'] = $_SESSION['alias']->id;
				$cooperation_where['type'] = 'shipping';
		        if($cooperation = $this->db->getAllDataByFieldInArray('wl_aliases_cooperation', $cooperation_where))
		        	foreach ($cooperation as $shipping) {
		        		if (!in_array($shipping->alias2, $shippings_ids)) {
		        			$s = new stdClass();
		        			$insert = array();
		        			$insert['wl_alias'] = $s->wl_alias = $where_ntkd['alias'] = $shippings_ids[] = $shipping->alias2;
		        			$insert['active'] = $s->active = $insert['type'] = $s->type = 0;
		        			$insert['position'] = $s->position = count($shippings) + 1;
		        			$insert['name'] = $s->name = $insert['info'] = $s->info = '';
		        			$s->id = $this->db->insertRow($this->table('_shipping'));
		        			if($ntkd = $this->db->getAllDataById('wl_ntkd', $where_ntkd))
		        			{
		        				$s->name = $ntkd->name;
		        				$s->info = $ntkd->list;
		        			}
		        			$shippings[] = $s;
		        		}
		        	}
	        }
	        return $shippings;
	    }
        else if(empty($where))
		{
			$cooperation_where = $shippings = array();
			$cooperation_where['alias1'] = $_SESSION['alias']->id;
			$cooperation_where['type'] = 'shipping';
	        if($cooperation = $this->db->getAllDataByFieldInArray('wl_aliases_cooperation', $cooperation_where))
	        	foreach ($cooperation as $shipping) {
        			$s = new stdClass();
        			$insert = array();
        			$insert['wl_alias'] = $s->wl_alias = $where_ntkd['alias'] = $shipping->alias2;
        			$insert['active'] = $s->active = $insert['type'] = $s->type = 0;
        			$insert['position'] = $s->position = count($shippings) + 1;
        			$insert['name'] = $s->name = $insert['info'] = $s->info = 0;
        			$s->id = $this->db->insertRow($this->table('_shipping'));
        			if($ntkd = $this->db->getAllDataById('wl_ntkd', $where_ntkd))
        			{
        				$s->name = $ntkd->name;
        				$s->info = $ntkd->list;
        			}
        			$shippings[] = $s;
	        	}
	        if(!empty($shippings))
	        	return $shippings;
        }
		return false;
	}

	public function getPayments($where=array())
	{
		$where_ntkd = array('alias' => '#p.wl_alias', 'content' => 0);
		if($_SESSION['language'])
			$where_ntkd['language'] = $_SESSION['language'];
		$this->db->select($this->table('_payments').' as p', '*', $where)
				->join('wl_aliases', 'alias', '#p.wl_alias')
				->join('wl_ntkd', 'name as payment_name, list as payment_info', $where_ntkd)
				->order('position');
		if($payments = $this->db->get('array'))
		{
			$payments_ids = array();
			foreach ($payments as $pay) {
				if($pay->wl_alias > 0)
				{
					if(!in_array($pay->wl_alias, $payments_ids))
        				$payments_ids[] = $pay->wl_alias;
					$pay->name = $pay->payment_name;
					$pay->info = $pay->payment_info;
					unset($pay->payment_name, $pay->payment_info);
				}
				else if($_SESSION['language'] && empty($where['id']))
				{
					@$name = unserialize($pay->name);
					if(isset($name[$_SESSION['language']]))
						$pay->name = $name[$_SESSION['language']];
					else if(is_array($name))
						$pay->name = array_shift($name);
					@$info = unserialize($pay->info);
					if(isset($info[$_SESSION['language']]))
						$pay->info = $info[$_SESSION['language']];
					else if(is_array($info))
						$pay->info = array_shift($info);
				}
			}
			if(empty($where))
			{
				$cooperation_where = array();
				$cooperation_where['alias1'] = $_SESSION['alias']->id;
				$cooperation_where['type'] = 'payment';
		        if($cooperation = $this->db->getAllDataByFieldInArray('wl_aliases_cooperation', $cooperation_where))
		        	foreach ($cooperation as $pay) {
		        		if (!in_array($pay->alias2, $payments_ids)) {
		        			$s = new stdClass();
		        			$insert = array();
		        			$insert['wl_alias'] = $s->wl_alias = $where_ntkd['alias'] = $payments_ids[] = $pay->alias2;
		        			$insert['active'] = $s->active = 0;
		        			$insert['position'] = $s->position = count($payments) + 1;
		        			$insert['name'] = $s->name = $insert['info'] = $s->info = '';
		        			$s->id = $this->db->insertRow($this->table('_payments'));
		        			if($ntkd = $this->db->getAllDataById('wl_ntkd', $where_ntkd))
		        			{
		        				$s->name = $ntkd->name;
		        				$s->info = $ntkd->list;
		        			}
		        			$payments[] = $s;
		        		}
		        	}
	        }
	        return $payments;
	    }
        else if(empty($where))
		{
			$cooperation_where = $payments = array();
			$cooperation_where['alias1'] = $_SESSION['alias']->id;
			$cooperation_where['type'] = 'payment';
	        if($cooperation = $this->db->getAllDataByFieldInArray('wl_aliases_cooperation', $cooperation_where))
	        	foreach ($cooperation as $pay) {
        			$s = new stdClass();
        			$insert = array();
        			$insert['wl_alias'] = $s->wl_alias = $where_ntkd['alias'] = $pay->alias2;
        			$insert['active'] = $s->active = 0;
        			$insert['position'] = $s->position = count($payments) + 1;
        			$insert['name'] = $s->name = $insert['info'] = $s->info = '';
        			$s->id = $this->db->insertRow($this->table('_payments'));
        			if($ntkd = $this->db->getAllDataById('wl_ntkd', $where_ntkd))
        			{
        				$s->name = $ntkd->name;
        				$s->info = $ntkd->list;
        			}
        			$payments[] = $s;
	        	}
	        if(!empty($payments))
	        	return $payments;
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
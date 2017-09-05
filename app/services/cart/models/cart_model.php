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
		$this->db->order('date_add DESC', 'c');

		if(isset($_SESSION['option']->paginator_per_page) && $_SESSION['option']->paginator_per_page > 0)
		{
			$start = 0;
			if(isset($_GET['page']) && is_numeric($_GET['page']) && $_GET['page'] > 1) {
				$start = ($_GET['page'] - 1) * $_SESSION['option']->paginator_per_page;
			}
			$_SESSION['option']->paginator_total = $this->db->getCount($this->table(), $_SESSION['alias']->id);

			$this->db->limit($start, $_SESSION['option']->paginator_per_page);
		}

		$carts = $this->db->get('array', false);
		$_SESSION['option']->paginator_total = $this->db->get('count');

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

			if($cart = $this->db->get('single'))
			{
				if($allInfo)
				{
					$cart->products = $this->db->getAllDataByFieldInArray($this->table('_products'), $cart->id, 'cart');
					$cart->action = $this->getActionByStatus($cart->status_weight, false);
					$cart->history = $this->db->select($this->table('_history') .' as h', '*', $cart->id, 'cart')
												->join($this->table('_status'), 'name as status_name', '#h.status')
												->order('date DESC')
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
			$user = ($user == 0) ? $_SESSION['user']->id : $user;
			return $this->db->getAllDataByFieldInArray($this->table('_products'), $user, 'user');
		}
		elseif(isset($_SESSION['cart']->products))
			return $_SESSION['cart']->products;
		return false;
	}

	public function addProduct($product, $user = 0, $cart = 0)
	{
		$cart_product = array('cart' => $cart, 'quantity_returned' => 0);
		$cart_product['user'] = ($user == 0) ? $_SESSION['user']->id : $user;
		$cart_product['product_alias'] = $product->wl_alias;
		$cart_product['product_id'] = $product->id;
		$cart_product['storage_alias'] = $product->storage_alias;
		$cart_product['storage_invoice'] = $product->storage_invoice;
		$cart_product['price'] = $product->price;
		if($product->storage_invoice && isset($product->price_in))
			$cart_product['price_in'] = $product->price_in;
		else
			$cart_product['price_in'] = $product->price;
		$cart_product['quantity'] = $cart_product['quantity_wont'] = $product->quantity;
		$cart_product['discount'] = (isset($price->discount)) ? $product->discount : 0;
		$cart_product['date'] = time();

		return $this->db->insertRow($this->table('_products'), $cart_product);
	}


}

?>
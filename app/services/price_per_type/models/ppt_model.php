<?php 

class ppt_model
{

	public function table($sufix = '', $useAliasTable = false)
	{
		if($useAliasTable) return $_SESSION['service']->table.$sufix.$_SESSION['alias']->table;
		return $_SESSION['service']->table.$sufix;
	}
	
	public function getProduct($product)
	{
		$where = array();
		if(isset($product->wl_alias))
		{
			$where['product_alias'] = $product->wl_alias;
			$where['product_id'] = $product->id;
		}
		else if(isset($product->product_alias))
		{
			$where['product_alias'] = $product->product_alias;
			$where['product_id'] = $product->product_id;
		}
		// $where['user_type'] = $this->data->post('type_id');
		if(!empty($where))
		{
			$product->marketing = false;
			$product->price_before = $product->price;
			$product->discount = 0;
			if($marketing = $this->db->getAllDataById($this->table('_product'), $where))
			{
				$product->marketing = $marketing;
				if($marketing->change_price == '+')
					$product->price += $marketing->price;
				if($marketing->change_price == '*')
					$product->price *= $marketing->price;
				$product->discount = ($product->price_before - $product->price) * $product->quantity;
			}
			else
			{
				$data = array();
				$data['shop_alias'] = $product->product_alias;
				// $data['user_type'] = $this->data->post('type_id');
				if($marketing = $this->db->getAllDataById($this->table(), $data))
				{
					if($marketing->change_price == '+')
						$product->price += $marketing->price;
					if($marketing->change_price == '*')
						$product->price *= $marketing->price;
					$product->discount = ($product->price_before - $product->price) * $product->quantity;
				}
			}
		}
		return $product;
	}

	public function getProducts($products, $currency, $all)
	{
		if(is_array($products) && is_object($products[0]))
		{
			$where = array();
			if(isset($products[0]->wl_alias))
			{
				$where['product_alias'] = $products[0]->wl_alias;
				if(!$all)
				{
					$where['product_id'] = array();
					foreach ($products as $product) {
						$where['product_id'][] = $product->id;
					}
				}
			}
			else if(isset($products[0]->product_alias))
			{
				$where['product_alias'] = $products[0]->product_alias;
				if(!$all)
				{
					$where['product_id'] = array();
					foreach ($products as $product) {
						$where['product_id'][] = $product->product_id;
					}
				}
			}
			if(!empty($where))
			{
				if($marketings = $this->db->getAllDataByFieldInArray($this->table(), $where))
					foreach ($products as $product) {
						foreach ($marketings as $marketing) {
							if($marketing->product_id == $product->id)
							{
								$prices = unserialize($marketing->price);
								ksort ($prices);
								if($currency && is_numeric($currency) && $currency > 0)
									foreach ($prices as $from => &$price)
										$price *= $currency;
								else if(!empty($product->currency))
									foreach ($prices as $from => &$price)
										$price *= $product->currency;
								$product->prices = $prices;
								break;
							}
						}
					}
			}
		}
		return $products;
	}

	public function saveForShop()
	{
		$data = $update = array();
		$data['shop_alias'] = $this->data->post('shop_id');
		$data['user_type'] = $this->data->post('type_id');
		$update['change_price'] = $this->data->post('change_price');
		$update['price'] = $this->data->post('price');
		if($row = $this->db->getAllDataById($this->table(), $data))
			$this->db->updateRow($this->table(), $update, $row->id);
		else
			$this->db->insertRow($this->table(), array_merge($data, $update));
		return true;
	}

	public function saveForProduct()
	{
		$data = $update = array();
		$data['product_alias'] = $this->data->post('shop_id');
		$data['product_id'] = $this->data->post('product_id');
		$data['user_type'] = $this->data->post('type_id');
		$update['change_price'] = $this->data->post('change_price');
		$update['price'] = $this->data->post('price');
		if($row = $this->db->getAllDataById($this->table('_product'), $data))
			$this->db->updateRow($this->table('_product'), $update, $row->id);
		else
			$this->db->insertRow($this->table('_product'), array_merge($data, $update));
		return true;
	}

	public function deleteForProduct()
	{
		$data = array();
		$data['product_alias'] = $this->data->post('shop_id');
		$data['product_id'] = $this->data->post('product_id');
		$data['user_type'] = $this->data->post('type_id');
		$this->db->deleteRow($this->table('_product'), $data);
		return true;
	}

	public function delete($id)
	{
		if($this->db->deleteRow($this->table(), $id))
			return true;
		return false;
	}

}

?>
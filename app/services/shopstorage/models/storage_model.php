<?php 

class storage_model
{

	public function table($sufix = '', $useAliasTable = false)
	{
		if($useAliasTable) return $_SESSION['service']->table.$sufix.$_SESSION['alias']->table;
		return $_SESSION['service']->table.$sufix;
	}
	
	public function getStorage($id = 0)
	{
		if($id == 0) $id = $_SESSION['alias']->id;
		$this->db->select($this->table().' as s', '*', $id);
		$this->db->join('wl_users', 'name as user_name', '#s.user_add');
		$this->db->join('wl_ntkd', 'name, list as time', array('alias' => $_SESSION['alias']->id, 'content' => 0));
		$storage =  $this->db->get('single');
		if($storage)
		{
			if($_SESSION['option']->markUpByUserTypes)
			{
				if($markups = $this->db->getAllDataByFieldInArray($this->table('_markup'), $storage->id, 'storage'))
				{
					$storage->markup = array();
					foreach ($markups as $markup) {
						$storage->markup[$markup->user_type] = $markup->markup;
					}
				}
			}
		}
		return $storage;
	}

	public function getProducts($id, $user_type = 0)
	{
		if($user_type == 1)
			$user_type = 2;
		$where['storage'] = $_SESSION['alias']->id;
		if($id > 0) $where['product'] = $id;
		$this->db->select($this->table('_products'), '*', $where);
		if(isset($_SESSION['option']->paginator_per_page) && $_SESSION['option']->paginator_per_page > 0)
		{
			$start = 0;
			if(isset($_GET['page']) && is_numeric($_GET['page']) && $_GET['page'] > 1) {
				$start = ($_GET['page'] - 1) * $_SESSION['option']->paginator_per_page;
			}
			$_SESSION['option']->paginator_total = $this->db->getCount($this->table('_products'), $_SESSION['alias']->id, 'storage');

			$this->db->limit($start, $_SESSION['option']->paginator_per_page);
		}
		$this->db->join('wl_ntkd', 'name as storage_name, list as storage_time', array('alias' => $_SESSION['alias']->id, 'content' => 0));
		if($_SESSION['option']->markUpByUserTypes)
			$this->db->join($this->table('_markup'), 'markup', array('storage' => $_SESSION['alias']->id, 'user_type' => $user_type));
		$invoises = $this->db->get('array', false);
		$_SESSION['option']->paginator_total = $this->db->get('count');
		if($invoises && $user_type >= 0 && $_SESSION['option']->markUpByUserTypes)
		{
			foreach ($invoises as $invoise) {
				if($invoise->price_out != 0)
				{
					$price_out = unserialize($invoise->price_out);
					if(isset($price_out[$user_type]))
						$invoise->price_out = $price_out[$user_type];
					else
						$invoise->price_out = end($price_out);
				}
				else
				{
					$invoise->price_out = $invoise->price_in;
					if($invoise->markup > 0)
						$invoise->price_out = round($invoise->price_in * ($invoise->markup + 100) / 100, 2);
				}
				$invoise->amount_free = $invoise->amount - $invoise->amount_reserved;
			}
		}
		elseif($invoises)
		{
			foreach ($invoises as $invoise) {
				$invoise->amount_free = $invoise->amount - $invoise->amount_reserved;
			}
		}
		return $invoises;
	}

	public function getInvoice($id, $user_type = 0)
	{
		$this->db->select($this->table('_products').' as p', '*', $id);
		$this->db->join('wl_ntkd', 'name as storage_name, list as storage_time', array('alias' => $_SESSION['alias']->id, 'content' => 0));
		$this->db->join('wl_users as u1', 'name as manager_add_name', '#p.manager_add');
		$this->db->join('wl_users as u2', 'name as manager_edit_name', '#p.manager_edit');
		$invoise = $this->db->get('single');
		if($user_type >= 0 && $invoise && $_SESSION['option']->markUpByUserTypes)
		{
			if($user_type == 1)
				$user_type = 2;
			if($invoise->price_out != 0)
			{
				$price_out = unserialize($invoise->price_out);
				if(isset($price_out[$user_type]))
					$invoise->price_out = $price_out[$user_type];
				else
					$invoise->price_out = end($price_out);
			}
			else
			{
				$invoise->price_out = $invoise->price_in;
				if($user_type != 1)
					if($markup = $this->db->getAllDataById($this->table('_markup'), array('storage' => $invoise->storage, 'user_type' => $user_type)))
						$invoise->price_out = round($invoise->price_in * ($markup->markup + 100) / 100, 2);
			}
		}
		if($invoise)
		{
			$invoise->amount_free = $invoise->amount - $invoise->amount_reserved;
		}
		else
		{
			$invoise = $this->db->select('wl_ntkd', 'name as storage_name, list as storage_time', array('alias' => $_SESSION['alias']->id, 'content' => 0))->get('single');
		}
		return $invoise;
	}

	public function save($id = 0)
	{
		$data = array();
		$data['price_in'] = $this->data->post('price_in');

		if($this->data->post('optionPrice_out_old') != $this->data->post('optionPrice_out_new'))
		{
			if($this->data->post('optionPrice_out_new') == 1)
			{
				$price_out = array();
				if($markups = $this->db->getAllDataByFieldInArray($this->table('_markup'), $_SESSION['alias']->id, 'storage'))
				{
					foreach ($markups as $markup) {
						$price_out[$markup->user_type] = $data['price_in'] * ($markup->markup + 100) / 100;
					}
				}
				$data['price_out'] = serialize($price_out);
			}
			else
				$data['price_out'] = 0;
		}
		else
		{
			if($this->data->post('optionPrice_out_old'))
			{
				if($_SESSION['option']->markUpByUserTypes)
				{
					$price_out = array();
					foreach ($_POST as $key => $value) {
						$key = explode('-', $key);
						if($key[0] == 'price_out' && isset($key[1]) && is_numeric($key[1]))
						{
							$key = $key[1];
							$price_out[$key] = $value;
						}
					}
					$data['price_out'] = serialize($price_out);
				}
				else
					$data['price_out'] = $this->data->post('price_out');
			}
			else
				$data['price_out'] = 0;
		}
		
		$data['amount'] = $this->data->post('amount');
		$data['amount_reserved'] = 0;
		if($this->data->post('amount_reserved')) $data['amount_reserved'] = $this->data->post('amount_reserved');
		$data['date_in'] = 0;
		if($this->data->post('date_in'))
		{
			$date = explode('.', $this->data->post('date_in'));
			$date = mktime(0,0,0, $date[1], $date[0], $date[2]);
			if(is_numeric($date))
	            $data['date_in'] = $date;
	    }
        $data['manager_edit'] = $_SESSION['user']->id;
        $data['date_add'] = $data['date_edit'] = time();

       	if($id == 0)
		{
			$data['storage'] = $_SESSION['alias']->id;
			$data['product'] = $this->data->post('product-id');
			$data['currency_in'] = $data['currency_out'] = $data['date_out'] = 0;
			$data['manager_add'] = $_SESSION['user']->id;
			$data['date_add'] = $data['date_out'] = time();
			if($this->db->insertRow($this->table('_products'), $data))
				return $this->db->getLastInsertedId();
		}
		elseif($id > 0)
		{
			if($this->db->updateRow($this->table('_products'), $data, $id))
				return true;
		}
        return false;
	}

	public function delete($id)
	{
		if($this->db->deleteRow($this->table('_products'), $id)) return true;
		return false;
	}

	public function setReserve($data)
	{
		if(isset($data['invoise']) && isset($data['amount']))
		{
			$invoise = $this->db->getAllDataById($this->table('_products'), $data['invoise']);
			if($invoise)
			{
				$amount['amount_reserved'] = $invoise->amount_reserved + $data['amount'];
				$this->db->updateRow($this->table('_products'), $amount, $invoise->id);
				return true;
			}
		}
		return false;
	}

	public function setBook($data)
	{
		if(isset($data['invoise']) && isset($data['amount']))
		{
			$invoise = $this->db->getAllDataById($this->table('_products'), $data['invoise']);
			if($invoise)
			{
				$amount['amount'] = $invoise->amount - $data['amount'];
				if(isset($data['reserve']) && $data['reserve']) $amount['amount_reserved'] = $invoise->amount_reserved - $data['amount'];
				$this->db->updateRow($this->table('_products'), $amount, $invoise->id);
				return true;
			}
		}
		return false;
	}

}

?>
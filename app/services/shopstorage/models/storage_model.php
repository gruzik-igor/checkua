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
		$storage =  $this->db->get('single');
		if($storage)
		{
			if($_SESSION['option']->markUpByUserTypes)
			{
				$markups = $this->db->getAllDataByFieldInArray($this->table('_markup'), $storage->id, 'storage');
				if($markups)
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

	public function getProducts($id = 0)
	{
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
		$invoises = $this->db->get('array');
		if($invoises && $_SESSION['option']->markUpByUserTypes)
		{
			foreach ($invoises as $invoise) {
				$price_out = unserialize($invoise->price_out);
				$invoise->price_out = $price_out[$_SESSION['user']->type];
			}
		}
		return $invoises;
	}

	public function getProduct($id, $all_info = false)
	{
		$this->db->select($this->table('_products').' as p', '*', $id);
		$this->db->join('wl_ntkd', 'name as storage_name, list as storage_time', array('alias' => $_SESSION['alias']->id, 'content' => 0));
		$this->db->join('wl_users as u1', 'name as manager_add_name', '#p.manager_add');
		$this->db->join('wl_users as u2', 'name as manager_edit_name', '#p.manager_edit');
		$invoise = $this->db->get('single');
		if(!$all_info && $invoise && $_SESSION['option']->markUpByUserTypes)
		{
			$price_out = unserialize($invoise->price_out);
			$invoise->price_out = $price_out[$_SESSION['user']->type];
		}
		return $invoise;
	}

	public function save($id = 0)
	{
		$data = array();
		$data['price_in'] = $this->data->post('price_in');
		if($_SESSION['option']->markUpByUserTypes)
		{
			$price_out = array();
			foreach ($_POST as $key => $value) {
				$key = explode('-', $key);
				if($key[0] == 'price_out' && isset($key[1]) && is_numeric($key[1]) && $key[1] > 0)
				{
					$key = $key[1];
					$price_out[$key] = $value;
				}
			}
			$data['price_out'] = serialize($price_out);
		}
		else
		{
			$data['price_out'] = $this->data->post('price_out');
		}
		
		$data['amount'] = $this->data->post('amount');
		$data['date_in'] = 0;
		$date = explode('.', $this->data->post('date_in'));
		$date = mktime(0,0,0, $date[1], $date[0], $date[2]);
		if(is_numeric($date)) {
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
			$data['date_add'] = time();
			if($this->db->insertRow($this->table('_products'), $data)) return $this->db->getLastInsertedId();
		}
		elseif($id > 0)
		{
			if($this->db->updateRow($this->table('_products'), $data, $id)) return true;
		}
        return false;
	}

	public function delete($id)
	{
		if($this->db->deleteRow($this->table('_products'), $id)) return true;
		return false;
	}

}

?>
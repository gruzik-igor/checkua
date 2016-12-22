<?php

class cart_model
{
	public function table($sufix = '', $useAliasTable = false)
	{
		if($useAliasTable) return $_SESSION['service']->table.$sufix.$_SESSION['alias']->table;
		return $_SESSION['service']->table.$sufix;
	}

	public function getAllCarts($id = 0)
	{
		$where = array();
		if($id > 0) $where['id'] = $id;

		$this->db->select($this->table().' as c', '*, 1c as s1c', $where);
		if(isset($_SESSION['option']->paginator_per_page) && $_SESSION['option']->paginator_per_page > 0)
		{
			$start = 0;
			if(isset($_GET['page']) && is_numeric($_GET['page']) && $_GET['page'] > 1) {
				$start = ($_GET['page'] - 1) * $_SESSION['option']->paginator_per_page;
			}
			$_SESSION['option']->paginator_total = $this->db->getCount($this->table(), $_SESSION['alias']->id);

			$this->db->limit($start, $_SESSION['option']->paginator_per_page);
		}
		$this->db->join($this->table('_status'), 'name as status_name', '#c.status');
		$this->db->join('wl_users', 'name as user_name', '#c.user');
		$this->db->join('wl_user_info', 'phone1 as user_phone, phone2 as user_phone2', '#c.user', 'user');
		$this->db->order('date_add DESC');

		$carts =  $this->db->get('array', false);
		$_SESSION['option']->paginator_total = $this->db->get('count');

		return $carts;
	}

	public function getCartInfo($id)
	{
		if($id > 0) $where['id'] = $id;

		$this->db->select($this->table().' as c', '*, 1c as s1c', $where);
		$this->db->join($this->table('_status'), 'name as status_name, weight as status_weight', '#c.status');
		$this->db->join('wl_users as u', 'name as user_name, email as user_email, type as user_type', '#c.user');
		$this->db->join('wl_user_info', 'phone1 as user_phone, phone2 as user_phone2', '#c.user', 'user');
		$this->db->join('wl_user_types', 'title as user_type_name', '#u.type');

		$cartInfo =  $this->db->get('single');

		return $cartInfo;
	}


}

?>
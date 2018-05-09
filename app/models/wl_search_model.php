<?php

class wl_search_model {

	public function get($all = false)
	{
		// $where['alias'] = array(8, 9, 11, 12);
		if($by = $this->data->get('by'))
			$where['keywords'] = '%'.$by;
		if($keywords = $this->data->get('keywords'))
			$where['keywords'] = '%'.$keywords;
		if($_SESSION['language'])
		{
			if(!$all)
				$where['language'] = $_SESSION['language'];
			if(in_array($this->data->get('language'), $_SESSION['all_languages']))
				$where['language'] = $this->data->get('language');
		}
		if($alias = $this->data->get('alias'))
		{
			if($alias = $this->db->getAllDataById('wl_aliases', $alias, 'alias'))
				$where['alias'] = $alias->id;
		}
		if($wl_aliases = $this->db->getAllDataByFieldInArray('wl_aliases', array('service' => '>0')))
			foreach ($wl_aliases as $alias) {
				$where['alias'][] = $alias->id;
			}
		$this->db->select('wl_ntkd', 'alias as alias_id, content, name, list', $where);
		$this->db->join('wl_aliases', 'alias, table, service', '#wl_ntkd.alias');
		if($sort = $this->data->get('sort'))
		{
			if($sort == 'name_up')
				$this->db->order('name ASC');
			else
				$this->db->order('name DESC');
		}
		else
		{
			$this->db->order('content DESC');
		}
		if(isset($_SESSION['option']->paginator_per_page) && $_SESSION['option']->paginator_per_page > 0)
		{
			$start = 0;
			if(isset($_GET['per_page']) && is_numeric($_GET['per_page']) && $_GET['per_page'] > 0)
				$_SESSION['option']->paginator_per_page = $_GET['per_page'];
			if(isset($_GET['page']) && is_numeric($_GET['page']) && $_GET['page'] > 1)
				$start = ($_GET['page'] - 1) * $_SESSION['option']->paginator_per_page;
			$this->db->limit($start, $_SESSION['option']->paginator_per_page);
		}

		return $this->db->get('array');
	}

	public function getImage($alias, $content, $folder = '', $prefix = 'admin_')
	{
		$where = array();
		$where['alias'] = $alias;
		$where['content'] = $content;
		$this->db->select('wl_images', '*', $where);
		$this->db->order('position ASC');
		$this->db->limit(1);
		if($image = $this->db->get())
			return $folder.'/'.$content.'/'.$prefix.$image->file_name;
		return false;
	}

}

?>
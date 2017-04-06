<?php 

class wl_search_model {

	public function get($by, $all = false)
	{
		$where['name'] = '%'.$by;
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
		$this->db->select('wl_ntkd', 'alias as alias_id, content, name, list, text', $where);
		$this->db->join('wl_aliases', 'alias, table, service', '#wl_ntkd.alias');
		if($sort = $this->data->get('sort'))
		{
			if($sort == 'name_up')
				$this->db->order('name ASC');
			else
				$this->db->order('name DESC');
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
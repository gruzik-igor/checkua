<?php 

class wl_search_model {

	public function get($by, $language_all = false)
	{
		$where['name'] = '%'.$by;
		if(!$language_all && $_SESSION['language']) $where['language'] = $_SESSION['language'];
		$this->db->select('wl_ntkd', 'alias as alias_id, content, name, list, text', $where);
		$this->db->join('wl_aliases', 'alias, table, service', '#wl_ntkd.alias');
		return $this->db->get('array');
	}

	public function getImage($alias, $content, $folder = '')
	{
		$where = array();
		$where['alias'] = $alias;
		$where['content'] = $content;
		$this->db->select('wl_images', '*', $where);
		$this->db->order('main DESC');
		$this->db->limit(1);
		if($image = $this->db->get())
			return $folder.'/'.$content.'/admin_'.$image->file_name;
		return false;
	}

}

?>
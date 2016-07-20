<?php

class static_page_model
{
	public function table($sufix = '')
	{
		return $_SESSION['service']->table.$sufix;
	}

	public function get($id = 0)
	{
		if($id == 0) $id = $_SESSION['alias']->id;
		$this->db->select($_SESSION['service']->table, '*', $id);
		$this->db->join('wl_users as a', 'name as author_add_name', '#author_add');
		$this->db->join('wl_users as e', 'name as author_edit_name', '#author_edit');
		$page = $this->db->get('single');
		if($page)
		{
			$this->db->select($this->table('_photos'), '*', $id, 'alias');
			$this->db->join('wl_users', 'name as user_name', '#user');
			$page->photos = $this->db->get('array');
			if(!empty($page->photos))
			{
				$sizes = $this->db->getAllDataByFieldInArray('wl_images_sizes', $_SESSION['alias']->id, 'alias');
				foreach ($page->photos as $photo) {
					if($sizes){
						foreach ($sizes as $resize) if($resize->active == 1){
							$resize_name = $resize->prefix.'_photo';
							$photo->$resize_name = $_SESSION['option']->folder.'/'.$resize->prefix.'_'.$photo->name;
						}
					}
					$photo->photo = $_SESSION['option']->folder.'/'.$photo->name;
					$page->photo = $_SESSION['option']->folder.'/'.$photo->name;
				}
			}

			$page->videos = $this->db->getAllDataByFieldInArray('wl_video', array('alias' => $_SESSION['alias']->id, 'content' => 0));
		}
		return $page;
	}

}

?>
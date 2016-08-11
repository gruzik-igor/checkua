<?php 

class wl_cache_model
{

	public $page = false;
	
	public function get($link = 'main')
	{
		$this->page = $this->db->getAllDataById('wl_sitemap', $link, 'link');
	}

	public function get_from_statistic($alias, $content)
	{
		# code...
	}
}
?>
<?php

class install{

	public $service = null;
	
	public $name = "static_pages";
	public $title = "Статичні сторінки";
	public $description = "";
	public $group = "page";
	public $table_service = "s_static_page";
	public $table_alias = "";
	public $multi_alias = 1;
	public $order_alias = 10;
	public $admin_ico = 'fa-newspaper-o';
	public $version = "2";

	public $options = array('resize' => 1, 'folder' => 'static_page');
	public $options_admin = array ();
	public $sub_menu = array();

	public $seo_name = "Статична сторінка";
	public $seo_title = "Статична сторінка";
	public $seo_description = "";
	public $seo_keywords = "";

	function alias($alias = 0, $table = '')
	{
		if($alias == 0) return false;

		$page['id'] = $alias;
		$page['author_add'] = $_SESSION['user']->id;
		$page['date_add'] = time();
		$page['author_edit'] = $_SESSION['user']->id;
		$page['date_edit'] = time();
		$this->db->insertRow($this->table_service, $page);

		if($this->options['resize'] > 0){
			$query = "INSERT INTO `wl_images_sizes` (`id`, `alias`, `active`, `name`, `prefix`, `type`, `height`, `width`) VALUES
												 ( NULL, {$alias}, 1, 'Оригінал', '', 1, 1500, 1500),
												 ( NULL, {$alias}, 1, 'Preview', 's', 2, 200, 200);";
			$this->db->executeQuery($query);
		}

		if(isset($this->options['folder']) && $this->options['folder'] != ''){
			$path = IMG_PATH.$this->options['folder'];
			if(strlen(IMG_PATH) > strlen(SITE_URL)) $path = substr($path, strlen(SITE_URL));
			if(!is_dir($path)) mkdir($path, 0777);
		}

		return true;
	}

	public function alias_delete($alias = 0, $table = '')
	{
		$this->db->deleteRow($this->table_service, $alias);
		$this->db->deleteRow($this->table_service . '_photos', $alias, 'alias');

		$path = IMG_PATH.$this->options['folder'];
		if(strlen(IMG_PATH) > strlen(SITE_URL)) $path = substr($path, strlen(SITE_URL));
		if(is_dir($path)) $this->removeDirectory($path);
			
		return true;
	}

	public function setOption($option, $value, $table = '')
	{
		return true;
	}

	function install_go(){
		$query = "CREATE TABLE IF NOT EXISTS `{$this->table_service}` (
					  `id` int(11) NOT NULL,
					  `photo` text NOT NULL,
					  `author_add` int(11) NOT NULL,
					  `date_add` int(11) NOT NULL,
					  `author_edit` int(11) NOT NULL,
					  `date_edit` int(11) NOT NULL,
					  PRIMARY KEY (`id`)
					) ENGINE=InnoDB  DEFAULT CHARSET=utf8;";
		$this->db->executeQuery($query);

		$query = "CREATE TABLE IF NOT EXISTS `{$this->table_service}_photos` (
					  `id` int(11) NOT NULL AUTO_INCREMENT,
					  `alias` int(11) NOT NULL,
					  `name` text NOT NULL,
					  `date` int(11) NOT NULL,
					  `user` int(11) NOT NULL,
					  `title` text NOT NULL,
					  `main` int(11) NOT NULL,
					  PRIMARY KEY (`id`)
					) ENGINE=InnoDB  DEFAULT CHARSET=utf8;";
		$this->db->executeQuery($query);

		return true;
	}

	public function uninstall($service = 0)
	{
		if(isset($_POST['content']) && $_POST['content'] == 1){
			if(isset($this->options['folder']) && $this->options['folder'] != ''){
				$path = IMG_PATH.$this->options['folder'];
				if(strlen(IMG_PATH) > strlen(SITE_URL)) $path = substr($path, strlen(SITE_URL));
				if(is_dir($path)) $this->removeDirectory($path);
			}
		}
		$this->db->executeQuery("DROP TABLE IF EXISTS {$this->table_service}");
		$this->db->executeQuery("DROP TABLE IF EXISTS {$this->table_service}_photos");
		return true;
	}

	private function removeDirectory($dir) {
	    if ($objs = glob($dir."/*")) {
	       foreach($objs as $obj) {
	         is_dir($obj) ? $this->removeDirectory($obj) : unlink($obj);
	       }
	    }
	    rmdir($dir);
	}
	
}

?>
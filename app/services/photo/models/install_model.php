<?php

class install{

	public $service = null;
	
	public $name = "photo";
	public $title = "Фотоальбом";
	public $description = "Фотоальбом з підтримкою альбомів. Мультимовний.";
	public $table_service = "s_photos";
	public $table_alias = "";
	public $version = "1.2";

	public $options = array('resize' => 1, 'folder' => 'photo', 'canAdd' => 2);

	public $seo_name = "Фотоальбом";
	public $seo_title = "Фотоальбом";
	public $seo_description = "";
	public $seo_keywords = "";

	function alias($alias = 0, $table = '')
	{
		if($alias == 0) return false;

		$query = "CREATE TABLE IF NOT EXISTS `{$this->table_service}{$table}` (
					  `id` int(11) NOT NULL AUTO_INCREMENT,
					  `album` int(11) NOT NULL,
					  `user` int(11) NOT NULL,
					  `name` text NOT NULL,
					  `date` int(11) NOT NULL,
					  PRIMARY KEY (`id`),
					  UNIQUE KEY `id` (`id`)
					) ENGINE=InnoDB  DEFAULT CHARSET=utf8;";
		$this->db->executeQuery($query);

		$query = "CREATE TABLE IF NOT EXISTS `{$this->table_service}_albums{$table}` (
					  `id` int(11) NOT NULL AUTO_INCREMENT,
					  `link` text NOT NULL,
					  `photo` int(11) NOT NULL,
					  `active` tinyint(1) NOT NULL,
					  `position` int(11) NOT NULL,
					  `user` int(11) NOT NULL,
					  `date` int(11) NOT NULL,
					  PRIMARY KEY (`id`),
					  UNIQUE KEY `id` (`id`)
					) ENGINE=InnoDB  DEFAULT CHARSET=utf8;";
		$this->db->executeQuery($query);

		if($this->options['resize'] > 0){
			$query = "CREATE TABLE IF NOT EXISTS `{$this->table_service}_photo_size` (
					  `id` int(11) NOT NULL AUTO_INCREMENT,
					  `alias` int(11) NOT NULL,
					  `active` tinyint(1) NOT NULL,
					  `name` text NOT NULL,
					  `prefix` varchar(2) NOT NULL,
					  `type` tinyint(1) NOT NULL COMMENT '1 resize, 2 preview',
					  `height` int(11) NOT NULL,
					  `width` int(11) NOT NULL,
					  PRIMARY KEY (`id`),
					  UNIQUE KEY `id` (`id`)
					) ENGINE=InnoDB  DEFAULT CHARSET=utf8;";
			$this->db->executeQuery($query);

			$query = "INSERT INTO `{$this->table_service}_photo_size` (`id`, `alias`, `active`, `name`, `prefix`, `type`, `height`, `width`) VALUES
												 ( NULL, {$alias}, 1, 'Оригінал', '', 1, 1500, 1500),
												 ( NULL, {$alias}, 1, 'Preview', 's', 2, 200, 200);";
			$this->db->executeQuery($query);
		}

		$path = IMG_PATH.$this->options['folder'];
		if(strlen(IMG_PATH) > strlen(SITE_URL)) $path = substr($path, strlen(SITE_URL));
		if(!is_dir($path)) mkdir($path, 0777);

		return true;
	}

	public function setOption($option, $value, $table = '')
	{
		$this->options[$option] = $value;

		if ($option == 'resize' AND $value > 0) {
			$query = "CREATE TABLE IF NOT EXISTS `{$this->table_service}_photo_size` (
					  `id` int(11) NOT NULL AUTO_INCREMENT,
					  `alias` int(11) NOT NULL,
					  `active` tinyint(1) NOT NULL,
					  `name` text NOT NULL,
					  `prefix` varchar(2) NOT NULL,
					  `type` tinyint(1) NOT NULL COMMENT '1 resize, 2 preview',
					  `height` int(11) NOT NULL,
					  `width` int(11) NOT NULL,
					  PRIMARY KEY (`id`),
					  UNIQUE KEY `id` (`id`)
					) ENGINE=InnoDB  DEFAULT CHARSET=utf8;";
			$this->db->executeQuery($query);
		}
	}

	function install_go(){

		if($this->options['resize'] > 0){
			$query = "CREATE TABLE IF NOT EXISTS `{$this->table_service}_photo_size` (
					  `id` int(11) NOT NULL AUTO_INCREMENT,
					  `alias` int(11) NOT NULL,
					  `active` tinyint(1) NOT NULL,
					  `name` text NOT NULL,
					  `prefix` varchar(2) NOT NULL,
					  `type` tinyint(1) NOT NULL COMMENT '1 resize, 2 preview',
					  `height` int(11) NOT NULL,
					  `width` int(11) NOT NULL,
					  PRIMARY KEY (`id`),
					  UNIQUE KEY `id` (`id`)
					) ENGINE=InnoDB  DEFAULT CHARSET=utf8;";
			$this->db->executeQuery($query);
		}

		return true;
	}

	public function uninstall($alias = 0)
	{
		if(isset($_POST['content']) && $_POST['content'] == 1){
			$path = IMG_PATH.$this->options['folder'];
			if(strlen(IMG_PATH) > strlen(SITE_URL)) $path = substr($path, strlen(SITE_URL));
			if(is_dir($path)) $this->removeDirectory($path);

			$this->db->executeQuery("DROP TABLE IF EXISTS {$this->table_service}_photo_size");
		}
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
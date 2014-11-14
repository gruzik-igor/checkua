<?php

class install{

	public $service = null;
	
	public $name = "library";
	public $title = "Бібліотека статей";
	public $description = "Бібліотека статей із підтримкою категорій. Мультимовна.";
	public $table_service = "s_library";
	public $table_alias = "";
	public $version = "1.0";

	public $options = array('useCategories' => 1, 'articleMultiCategory' => 0, 'resize' => 0, 'folder' => 'library', 'canAdd' => 2);

	public $seo_name = "Бібліотека";
	public $seo_title = "Бібліотека";
	public $seo_description = "";
	public $seo_keywords = "";

	function alias($alias = 0, $table = '')
	{
		if($alias == 0) return false;

		$query = "CREATE TABLE IF NOT EXISTS `{$this->table_service}{$table}` (
					  `id` int(11) NOT NULL AUTO_INCREMENT,
					  `category` int(11) NOT NULL,
					  `link` text NOT NULL,
					  `position` int(11) NOT NULL,
					  `photo` int(11) NOT NULL,
					  `active` tinyint(1) NOT NULL,
					  `user` int(11) NOT NULL,
					  `date` int(11) NOT NULL,
					  PRIMARY KEY (`id`),
					  UNIQUE KEY `id` (`id`)
					) ENGINE=InnoDB  DEFAULT CHARSET=utf8;";
		$this->db->executeQuery($query);

		if($this->options['useCategories'] > 0){
			$query = "CREATE TABLE IF NOT EXISTS `{$this->table_service}_categories{$table}` (
						  `id` int(11) NOT NULL AUTO_INCREMENT,
						  `link` text NOT NULL,
						  `position` int(11) NOT NULL,
						  `photo` int(11) NOT NULL,
						  `active` tinyint(1) NOT NULL,
						  `user` int(11) NOT NULL,
						  `date` int(11) NOT NULL,
						  PRIMARY KEY (`id`),
						  UNIQUE KEY `id` (`id`)
						) ENGINE=InnoDB  DEFAULT CHARSET=utf8;";
			$this->db->executeQuery($query);

			if($this->options['articleMultiCategory'] > 0){
				$query = "CREATE TABLE IF NOT EXISTS `{$this->table_service}_article_category{$table}` (
						  `article` int(11) NOT NULL,
						  `category` int(11) NOT NULL
						) ENGINE=InnoDB  DEFAULT CHARSET=utf8;";
				$this->db->executeQuery($query);
			}
		}

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

		$path = IMG_PATH.$this->options['folder'].'/categories';
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
		if ($option == 'useCategories' AND $value > 0) {
			$query = "CREATE TABLE IF NOT EXISTS `{$this->table_service}_categories{$table}` (
						  `id` int(11) NOT NULL AUTO_INCREMENT,
						  `link` text NOT NULL,
						  `position` int(11) NOT NULL,
						  `photo` int(11) NOT NULL,
						  `active` tinyint(1) NOT NULL,
						  `user` int(11) NOT NULL,
						  `date` int(11) NOT NULL,
						  PRIMARY KEY (`id`),
						  UNIQUE KEY `id` (`id`)
						) ENGINE=InnoDB  DEFAULT CHARSET=utf8;";
			$this->db->executeQuery($query);
		}
		if($option == 'articleMultiCategory' AND $value > 0){
			$query = "CREATE TABLE IF NOT EXISTS `{$this->table_service}_article_category{$table}` (
					  `article` int(11) NOT NULL,
					  `category` int(11) NOT NULL
					) ENGINE=InnoDB  DEFAULT CHARSET=utf8;";
			$this->db->executeQuery($query);
			$categories = $this->db->getCount($this->table_service.'_article_category'.$table);
			if($categories == 0){
				$articles = $this->db->getAllData($this->table_service.$table);
				if($articles){
					foreach ($articles as $article) {
						$this->db->insertRow($this->table_service.'_article_category'.$table, array('article' => $article->id, 'category' => $article->category));
					}
				}
			}
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
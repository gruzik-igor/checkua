<?php

class install{

	public $service = null;
	
	public $name = "shopshowcase";
	public $title = "Магазин-вітрина";
	public $description = "Перелік товарів з підтримкою властифостей та фотогалереї БЕЗ можливості їх замовити та оплатити. Мультимовна.";
	public $table_service = "s_shopshowcase";
	public $table_alias = "";
	public $multi_alias = 1;
	public $admin_ico = 'list';
	public $version = "2.0";

	public $options = array('useGroups' => 1, 'useOptions' => 1, 'ProductMultiGroup' => 0, 'resize' => 1, 'folder' => 'shopshowcase', 'canAdd' => 2);

	public $seo_name = "Магазин-вітрина";
	public $seo_title = "Магазин-вітрина";
	public $seo_description = "";
	public $seo_keywords = "";

	function alias($alias = 0, $table = '')
	{
		if($alias == 0) return false;

		$query = "CREATE TABLE IF NOT EXISTS `{$this->table_service}_products{$table}` (
					  `id` int(11) NOT NULL AUTO_INCREMENT,
					  `group` int(11) NOT NULL,
					  `link` text NOT NULL,
					  `price` int(11) NOT NULL,
					  `availability` tinyint(1) NOT NULL,
					  `position` int(11) NOT NULL,
					  `photo` text NOT NULL,
					  `active` tinyint(1) NOT NULL,
					  `user` int(11) NOT NULL,
					  `date_add` int(11) NOT NULL,
					  `date_edit` int(11) NOT NULL,
					  PRIMARY KEY (`id`),
					  UNIQUE KEY `id` (`id`)
					) ENGINE=InnoDB  DEFAULT CHARSET=utf8;";
		$this->db->executeQuery($query);

		$query = "CREATE TABLE IF NOT EXISTS `{$this->table_service}_product_photos{$table}` (
					  `id` int(11) NOT NULL AUTO_INCREMENT,
					  `product` int(11) NOT NULL,
					  `name` text NOT NULL
					  `date` int(11) NOT NULL,
					  `user` int(11) NOT NULL,
					  `title` text NOT NULL,
					  `main` int(11) NOT NULL,
					  PRIMARY KEY (`id`)
					) ENGINE=InnoDB  DEFAULT CHARSET=utf8;";
		$this->db->executeQuery($query);

		$query = "CREATE TABLE IF NOT EXISTS `{$this->table_service}_group_options{$table}` (
					  `id` int(11) NOT NULL AUTO_INCREMENT,
					  `group` int(11) NOT NULL,
					  `link` text NOT NULL,
					  `position` int(11) NOT NULL,
					  `type` int(11) NOT NULL,
					  `filter` tinyint(1) NOT NULL,
					  `active` tinyint(1) NOT NULL,
					  PRIMARY KEY (`id`),
					  UNIQUE KEY `id` (`id`)
					) ENGINE=InnoDB  DEFAULT CHARSET=utf8;";
		$this->db->executeQuery($query);

		$query = "CREATE TABLE IF NOT EXISTS `{$this->table_service}_product_options{$table}` (
					  `id` int(11) NOT NULL AUTO_INCREMENT,
					  `product` int(11) NOT NULL,
					  `option` int(11) NOT NULL,
					  `language` varchar(2) NOT NULL
					  `value` text NOT NULL,
					  PRIMARY KEY (`id`),
					  UNIQUE KEY `id` (`id`)
					) ENGINE=InnoDB  DEFAULT CHARSET=utf8;";
		$this->db->executeQuery($query);

		$query = "CREATE TABLE IF NOT EXISTS `{$this->table_service}_options_name{$table}` (
					  `id` int(11) NOT NULL AUTO_INCREMENT,
					  `option` int(11) NOT NULL,
					  `language` varchar(2) NOT NULL,
					  `name` text NOT NULL,
					  `sufix` text NOT NULL,
					  PRIMARY KEY (`id`)
					) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
		$this->db->executeQuery($query);

		if($this->options['useGroups'] > 0){
			$query = "CREATE TABLE IF NOT EXISTS `{$this->table_service}_groups{$table}` (
						  `id` int(11) NOT NULL AUTO_INCREMENT,
						  `link` text NOT NULL,
						  `parent` int(11),
						  `position` int(11) NOT NULL,
						  `photo` int(11) NOT NULL,
						  `active` tinyint(1) NOT NULL,
						  `user` int(11) NOT NULL,
						  `date` int(11) NOT NULL,
						  PRIMARY KEY (`id`),
						  UNIQUE KEY `id` (`id`)
						) ENGINE=InnoDB  DEFAULT CHARSET=utf8;";
			$this->db->executeQuery($query);

			if($this->options['ProductMultiGroup'] > 0){
				$query = "CREATE TABLE IF NOT EXISTS `{$this->table_service}_product_group{$table}` (
						  `product` int(11) NOT NULL,
						  `group` int(11) NOT NULL
						) ENGINE=InnoDB  DEFAULT CHARSET=utf8;";
				$this->db->executeQuery($query);
			}
		}

		$query = "CREATE TABLE IF NOT EXISTS `{$this->table_service}_availability` (
					  `id` int(11) NOT NULL AUTO_INCREMENT,
					  `color` text NOT NULL,
					  `active` tinyint(1) NOT NULL DEFAULT '1',
					  `position` int(11) NOT NULL,
					  PRIMARY KEY (`id`)
					) ENGINE=InnoDB  DEFAULT CHARSET=cp1251 AUTO_INCREMENT=4 ;";
		$this->db->executeQuery($query);

		$query = " INSERT INTO `{$this->table_service}_availability` (`id`, `color`, `active`, `position`) VALUES
						(1, 'rgb(2, 204, 2)', 1, 1),
						(2, 'rgb(255, 163, 0)', 1, 2),
						(3, 'red', 1, 3);";
		$this->db->executeQuery($query);

		$query = "CREATE TABLE IF NOT EXISTS `s_shopshowcase_availability_name` (
					  `id` int(11) NOT NULL AUTO_INCREMENT,
					  `availability` int(11) NOT NULL,
					  `language` varchar(2) NOT NULL,
					  `name` text NOT NULL,
					  PRIMARY KEY (`id`)
					) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;";
		$this->db->executeQuery($query);
		
		$query = " INSERT INTO `s_shopshowcase_availability_name` (`id`, `availability`, `language`, `name`) VALUES
						(1, 1, '', 'В наявності'),
						(2, 2, '', 'Очікується'),
						(3, 3, '', 'Немає');";
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

		$path = IMG_PATH.$this->options['folder'].'/groups';
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
		if ($option == 'useGroups' AND $value > 0) {
			$query = "CREATE TABLE IF NOT EXISTS `{$this->table_service}_groups{$table}` (
						  `id` int(11) NOT NULL AUTO_INCREMENT,
						  `link` text NOT NULL,
						  `parent` int(11),
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
		if($option == 'ProductMultiGroup' AND $value > 0){
			$query = "CREATE TABLE IF NOT EXISTS `{$this->table_service}_product_group{$table}` (
						  `product` int(11) NOT NULL,
						  `group` int(11) NOT NULL
						) ENGINE=InnoDB  DEFAULT CHARSET=utf8;";
			$this->db->executeQuery($query);
			$groups = $this->db->getCount($this->table_service.'_product_group'.$table);
			if($groups == 0){
				$products = $this->db->getAllData($this->table_service.$table);
				if($products){
					foreach ($products as $product) {
						$this->db->insertRow($this->table_service.'_product_group'.$table, array('product' => $product->id, 'group' => $product->group));
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
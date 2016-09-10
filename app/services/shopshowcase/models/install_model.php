<?php

class install
{
	public $service = null;
	
	public $name = "shopshowcase";
	public $title = "Магазин-вітрина";
	public $description = "Перелік товарів з підтримкою властифостей та фотогалереї БЕЗ можливості їх замовити та оплатити. Мультимовна.";
	public $group = "shop";
	public $table_service = "s_shopshowcase";
	public $multi_alias = 1;
	public $order_alias = 100;
	public $admin_ico = 'fa-qrcode';
	public $version = "2.2";

	public $options = array('ProductUseArticle' => 0, 'useGroups' => 1, 'ProductMultiGroup' => 0, 'useAvailability' => 0, 'folder' => 'shopshowcase', 'productOrder' => 'position DESC', 'groupOrder' => 'position ASC');
	public $options_type = array('ProductUseArticle' => 'bool', 'useGroups' => 'bool', 'ProductMultiGroup' => 'bool', 'useAvailability' => 'bool', 'folder' => 'text', 'productOrder' => 'text', 'groupOrder' => 'text');
	public $options_title = array('ProductUseArticle' => 'Використання зовнішнього артикулу', 'useGroups' => 'Наявність груп', 'ProductMultiGroup' => 'Мультигрупи (1 товар більше ніж 1 група)', 'useAvailability' => 'Використання наявності товару', 'folder' => 'Папка для зображень', 'productOrder' => 'Сортування товарів', 'groupOrder' => 'Сортування груп');
	public $options_admin = array (
					'word:products_to_all' => 'товарів',
					'word:product_to' => 'До товару',
					'word:product_to_delete' => 'товару',
					'word:product' => 'товар',
					'word:products' => 'товари',
					'word:product_add' => 'Додати товар',
					'word:groups_to_all' => 'груп',
					'word:groups_to_delete' => 'групу',
					'word:group' => 'група',
					'word:group_add' => 'Додати групу товарів',
					'word:options_to_all' => 'параметрів',
					'word:option' => 'параметр товару',
					'word:option_add' => 'Додати параметр товару'
				);
	public $sub_menu = array("add" => "Додати товар", "all" => "До всіх товарів", "groups" => "Групи", "options" => "Властивості");

	public $seo_name = "Магазин-вітрина";
	public $seo_title = "Магазин-вітрина";
	public $seo_description = "";
	public $seo_keywords = "";

	function alias($alias = 0, $table = '')
	{
		if($alias == 0) return false;

		if($this->options['useGroups'] > 0)
		{
			$query = "CREATE TABLE IF NOT EXISTS `{$this->table_service}_groups` (
						  `id` int(11) NOT NULL AUTO_INCREMENT,
						  `wl_alias` int(11) NOT NULL,
						  `alias` text NOT NULL,
						  `parent` int(11),
						  `position` int(11) NOT NULL,
						  `photo` text NOT NULL,
						  `active` tinyint(1) NOT NULL,
						  `author_add` int(11) NOT NULL,
						  `date_add` int(11) NOT NULL,
						  `author_edit` int(11) NOT NULL,
						  `date_edit` int(11) NOT NULL,
						  PRIMARY KEY (`id`),
						  UNIQUE KEY `id` (`id`)
						) ENGINE=InnoDB  DEFAULT CHARSET=utf8;";
			$this->db->executeQuery($query);

			if($this->options['ProductMultiGroup'] > 0)
			{
				$query = "CREATE TABLE IF NOT EXISTS `{$this->table_service}_product_group` (
						  `product` int(11) NOT NULL,
						  `group` int(11) NOT NULL
						) ENGINE=InnoDB  DEFAULT CHARSET=utf8;";
				$this->db->executeQuery($query);
			}
		}

		if($this->options['useAvailability'] > 0)
		{
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

			$query = "CREATE TABLE IF NOT EXISTS `{$this->table_service}_availability_name` (
						  `id` int(11) NOT NULL AUTO_INCREMENT,
						  `availability` int(11) NOT NULL,
						  `language` varchar(2) NOT NULL,
						  `name` text NOT NULL,
						  PRIMARY KEY (`id`)
						) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;";
			$this->db->executeQuery($query);
			
			$query = " INSERT INTO `{$this->table_service}_availability_name` (`id`, `availability`, `language`, `name`) VALUES
							(1, 1, '', 'В наявності'),
							(2, 2, '', 'Очікується'),
							(3, 3, '', 'Немає');";
			$this->db->executeQuery($query);
		}

		$query = "INSERT INTO `wl_images_sizes` (`id`, `alias`, `active`, `name`, `prefix`, `type`, `height`, `width`) VALUES
											 ( NULL, {$alias}, 1, 'Оригінал', '', 1, 1500, 1500),
											 ( NULL, {$alias}, 1, 'Preview', 's', 2, 200, 200);";
		$this->db->executeQuery($query);

		$path = IMG_PATH.$this->options['folder'];
		if(strlen(IMG_PATH) > strlen(SITE_URL)) $path = substr($path, strlen(SITE_URL));
		if(!is_dir($path)) mkdir($path, 0777);

		$path = IMG_PATH.$this->options['folder'].'/groups';
		if(strlen(IMG_PATH) > strlen(SITE_URL)) $path = substr($path, strlen(SITE_URL));
		if(!is_dir($path)) mkdir($path, 0777);

		return true;
	}

	public function alias_delete($alias = 0, $table = '')
	{
		if($alias > 0) {
			$products = $this->db->getAllDataByFieldInArray($this->table_service.'_products', $alias, 'wl_alias');
			if(!empty($products))
			{
				$this->db->deleteRow($this->table_service.'_products', $alias, 'wl_alias');
				$this->db->deleteRow($this->table_service.'_groups', $alias, 'wl_alias');

				foreach ($products as $product) {
					$this->db->executeQuery("DELETE FROM `wl_images` WHERE `alias` = {$alias} AND `content` = {$product->id}");
					$this->db->deleteRow($this->table_service.'_product_group', $product->id, 'product');
				}

				$options = $this->db->getAllDataByFieldInArray($this->table_service.'_options', $alias, 'wl_alias');
				if(!empty($options))
				{
					$this->db->deleteRow($this->table_service.'_options', $alias, 'wl_alias');

					foreach ($options as $option) {
						$this->db->deleteRow($this->table_service.'_product_options', $option->id, 'option');
						$this->db->deleteRow($this->table_service.'_options_name', $option->id, 'option');
					}
				}
			}

			$groups = $this->db->getAllDataByFieldInArray($this->table_service.'_groups', $alias, 'wl_alias');
			if(!empty($groups))
			{
				$this->db->deleteRow($this->table_service.'_groups', $alias, 'wl_alias');
			}
			
			$this->db->deleteRow($this->table_service.'_product_options', $alias, 'shop');
		}

		$path = IMG_PATH.$this->options['folder'];
		if(strlen(IMG_PATH) > strlen(SITE_URL)) $path = substr($path, strlen(SITE_URL));
		if(is_dir($path)) $this->removeDirectory($path);

		return true;
	}

	public function setOption($option, $value, $alias, $table = '')
	{
		$this->options[$option] = $value;

		if ($option == 'useGroups' AND $value > 0)
		{
			$query = "CREATE TABLE IF NOT EXISTS `{$this->table_service}_groups` (
						  `id` int(11) NOT NULL AUTO_INCREMENT,
						  `wl_alias` int(11) NOT NULL,
						  `alias` text NOT NULL,
						  `parent` int(11),
						  `position` int(11) NOT NULL,
						  `photo` int(11) NOT NULL,
						  `active` tinyint(1) NOT NULL,
						  `author_add` int(11) NOT NULL,
						  `date_add` int(11) NOT NULL,
						  `author_edit` int(11) NOT NULL,
						  `date_edit` int(11) NOT NULL,
						  PRIMARY KEY (`id`),
						  UNIQUE KEY `id` (`id`)
						) ENGINE=InnoDB  DEFAULT CHARSET=utf8;";
			$this->db->executeQuery($query);
		}
		if($option == 'ProductMultiGroup' AND $value > 0)
		{
			$query = "CREATE TABLE IF NOT EXISTS `{$this->table_service}_product_group` (
						  `product` int(11) NOT NULL,
						  `group` int(11) NOT NULL
						) ENGINE=InnoDB  DEFAULT CHARSET=utf8;";
			$this->db->executeQuery($query);

			$products = $this->db->getAllDataByFieldInArray($this->table_service.'_products', $alias, 'wl_alias');
			if($products)
			{
				$list = array();
				foreach ($products as $product) {
					$list[] = $product->id;
				}

				$count = $this->db->getCount($this->table_service.'_product_group', array('product' => $list));
				if($count > 0)
				{
					foreach ($products as $product) {
						$this->db->insertRow($this->table_service.'_product_group'.$table, array('product' => $product->id, 'group' => $product->group));
					}
				}
			}
		}
		if($option == 'useAvailability' AND $value > 0)
		{
			$query = "CREATE TABLE IF NOT EXISTS `{$this->table_service}_availability` (
						  `id` int(11) NOT NULL AUTO_INCREMENT,
						  `color` text NOT NULL,
						  `active` tinyint(1) NOT NULL DEFAULT '1',
						  `position` int(11) NOT NULL,
						  PRIMARY KEY (`id`)
						) ENGINE=InnoDB  DEFAULT CHARSET=cp1251 AUTO_INCREMENT=4 ;";
			$this->db->executeQuery($query);

			$query = "CREATE TABLE IF NOT EXISTS `{$this->table_service}_availability_name` (
						  `id` int(11) NOT NULL AUTO_INCREMENT,
						  `availability` int(11) NOT NULL,
						  `language` varchar(2) NOT NULL,
						  `name` text NOT NULL,
						  PRIMARY KEY (`id`)
						) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;";
			$this->db->executeQuery($query);

			$availability = $this->db->getAllData($this->table_service.'_availability');
			if(empty($availability))
			{
				$query = " INSERT INTO `{$this->table_service}_availability` (`id`, `color`, `active`, `position`) VALUES
								(1, 'rgb(2, 204, 2)', 1, 1),
								(2, 'rgb(255, 163, 0)', 1, 2),
								(3, 'red', 1, 3);";
				$this->db->executeQuery($query);
				
				$query = " INSERT INTO `{$this->table_service}_availability_name` (`id`, `availability`, `language`, `name`) VALUES
								(1, 1, '', 'В наявності'),
								(2, 2, '', 'Очікується'),
								(3, 3, '', 'Немає');";
				$this->db->executeQuery($query);
			}
		}
	}

	public function install_go()
	{
		$query = "CREATE TABLE IF NOT EXISTS `{$this->table_service}_products` (
					  `id` int(11) NOT NULL AUTO_INCREMENT,
					  `wl_alias` int(11) NOT NULL,
					  `article` text NOT NULL,
					  `alias` text NOT NULL,
					  `group` int(11) NOT NULL,
					  `price` float unsigned NOT NULL,
					  `currency` tinyint(2) NOT NULL,
					  `availability` tinyint(1) NOT NULL,
					  `photo` text NOT NULL,
					  `active` tinyint(1) NOT NULL,
					  `position` int(11) NOT NULL,
					  `author_add` int(11) NOT NULL,
					  `date_add` int(11) NOT NULL,
					  `author_edit` int(11) NOT NULL,
					  `date_edit` int(11) NOT NULL,
					  PRIMARY KEY (`id`),
					  UNIQUE KEY `id` (`id`)
					) ENGINE=InnoDB  DEFAULT CHARSET=utf8;";
		$this->db->executeQuery($query);

		$query = "CREATE TABLE IF NOT EXISTS `{$this->table_service}_options` (
					  `id` int(11) NOT NULL AUTO_INCREMENT,
					  `wl_alias` int(11) NOT NULL,
					  `group` int(11) NOT NULL,
					  `alias` text NOT NULL,
					  `position` int(11) NOT NULL,
					  `type` int(11) NOT NULL,
					  `filter` tinyint(1) NOT NULL,
					  `active` tinyint(1) NOT NULL,
					  PRIMARY KEY (`id`),
					  UNIQUE KEY `id` (`id`)
					) ENGINE=InnoDB  DEFAULT CHARSET=utf8;";
		$this->db->executeQuery($query);

		$query = "CREATE TABLE IF NOT EXISTS `{$this->table_service}_options_name` (
					  `id` int(11) NOT NULL AUTO_INCREMENT,
					  `option` int(11) NOT NULL,
					  `language` varchar(2) NOT NULL,
					  `name` text NOT NULL,
					  `sufix` text NOT NULL,
					  PRIMARY KEY (`id`),
					  UNIQUE KEY `id` (`id`)
					) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
		$this->db->executeQuery($query);

		$query = "CREATE TABLE IF NOT EXISTS `{$this->table_service}_product_options` (
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  `product` int(11) NOT NULL,
			  `option` int(11) NOT NULL,
			  `language` varchar(2) NOT NULL,
			  `value` text NOT NULL,
			  PRIMARY KEY (`id`),
			  UNIQUE KEY `id` (`id`)
			) ENGINE=InnoDB  DEFAULT CHARSET=utf8;";
		$this->db->executeQuery($query);

		return true;
	}

	public function uninstall($service = 0)
	{
		if(isset($_POST['content']) && $_POST['content'] == 1){
			$path = IMG_PATH.$this->options['folder'];
			if(strlen(IMG_PATH) > strlen(SITE_URL)) $path = substr($path, strlen(SITE_URL));
			if(is_dir($path)) $this->removeDirectory($path);

			$this->db->executeQuery("DROP TABLE IF EXISTS {$this->table_service}_products");
			$this->db->executeQuery("DROP TABLE IF EXISTS {$this->table_service}_options");
			$this->db->executeQuery("DROP TABLE IF EXISTS {$this->table_service}_options_name");
			$this->db->executeQuery("DROP TABLE IF EXISTS {$this->table_service}_product_options");
			$this->db->executeQuery("DROP TABLE IF EXISTS {$this->table_service}s_shopshowcase_product_group");
			$this->db->executeQuery("DROP TABLE IF EXISTS {$this->table_service}_services");
			$this->db->executeQuery("DROP TABLE IF EXISTS {$this->table_service}_groups");
			$this->db->executeQuery("DROP TABLE IF EXISTS {$this->table_service}_availability");
			$this->db->executeQuery("DROP TABLE IF EXISTS {$this->table_service}_availability_name");
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
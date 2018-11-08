<?php

class install
{
	public $service = null;
	
	public $name = "shopshowcase";
	public $title = "Магазин-вітрина";
	public $description = "Перелік товарів з підтримкою властифостей та фотогалереї БЕЗ можливості їх замовити та оплатити. Мультимовна.";
	public $group = "shop";
	public $table_service = "s_shopshowcase";
	public $table_alias = "";
	public $multi_alias = 1;
	public $order_alias = 100;
	public $admin_ico = 'fa-qrcode';
	public $version = "2.6";

	public $options = array('ProductUseArticle' => 0, 'useGroups' => 1, 'ProductMultiGroup' => 0, 'useAvailability' => 0, 'searchHistory' => 1, 'useMarkUp' => 0, 'folder' => 'shopshowcase', 'productOrder' => 'position DESC', 'groupOrder' => 'position ASC', 'prom' => 0);
	public $options_type = array('ProductUseArticle' => 'bool', 'useGroups' => 'bool', 'ProductMultiGroup' => 'bool', 'useAvailability' => 'bool', 'searchHistory' => 'bool', 'useMarkUp' => 'bool', 'folder' => 'text', 'productOrder' => 'text', 'groupOrder' => 'text', 'prom' => 'bool');
	public $options_title = array('ProductUseArticle' => 'Використання зовнішнього артикулу', 'useGroups' => 'Наявність груп', 'ProductMultiGroup' => 'Мультигрупи (1 товар більше ніж 1 група)', 'useAvailability' => 'Використання наявності товару', 'searchHistory' => 'Зберігати історію пошуку користувачів', 'useMarkUp' => 'Використовувати націнку', 'folder' => 'Папка для зображень', 'productOrder' => 'Сортування товарів', 'groupOrder' => 'Сортування груп', 'prom' => 'Вигрузка детально prom.ua');
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
					'word:options_to_all' => 'властивостей',
					'word:option' => 'властивість товару',
					'word:option_add' => 'Додати властивість товару'
				);
	public $sub_menu = array("add" => "Додати товар", "all" => "До всіх товарів", "groups" => "Групи", "options" => "Властивості");
	public $sub_menu_access = array("add" => 2, "all" => 2, "groups" => 2, "options" => 1);

	function alias($alias = 0, $table = '')
	{
		if($alias == 0) return false;

		if($this->options['useGroups'] > 0)
		{
			$query = "CREATE TABLE IF NOT EXISTS `{$this->table_service}_groups` (
						  `id` int(11) NOT NULL AUTO_INCREMENT,
						  `wl_alias` int(11) NOT NULL,
						  `alias` text NULL,
						  `parent` int(11),
						  `position` int(11) NULL,
						  `active` tinyint(1) NULL,
						  `author_add` int(11) NOT NULL,
						  `date_add` int(11) NOT NULL,
						  `author_edit` int(11) NOT NULL,
						  `date_edit` int(11) NOT NULL,";
			if($this->options['prom'] > 0)
				$query .= "`prom_id` int(11) NOT NULL,";
			$query .= "	  PRIMARY KEY (`id`),
						  UNIQUE KEY `id` (`id`),
						  KEY `wl_alias` (`wl_alias`),
						  KEY `parent` (`parent`),
						  KEY `position` (`position`),
						  KEY `active` (`active`)
						) ENGINE=InnoDB  DEFAULT CHARSET=utf8;";
			$this->db->executeQuery($query);

			if($this->options['ProductMultiGroup'] > 0)
			{
				$query = "CREATE TABLE IF NOT EXISTS `{$this->table_service}_product_group` (
						  `id` int(11) NOT NULL AUTO_INCREMENT,
						  `product` int(11) NOT NULL,
						  `group` int(11) NOT NULL,
						  `position` int(11) NULL,
						  `active` tinyint(1) NULL,
						  PRIMARY KEY (`id`),
						  KEY `product` (`product`, `group`)
						) ENGINE=InnoDB  DEFAULT CHARSET=utf8;";
				$this->db->executeQuery($query);
			}
		}

		if($this->options['useAvailability'] > 0)
		{
			$query = "CREATE TABLE IF NOT EXISTS `{$this->table_service}_availability` (
						  `id` int(11) NOT NULL AUTO_INCREMENT,
						  `color` text NULL,
						  `active` tinyint(1) NULL DEFAULT '1',
						  `position` int(11) NULL,
						  PRIMARY KEY (`id`)
						) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;";
			$this->db->executeQuery($query);

			$query = " INSERT INTO `{$this->table_service}_availability` (`id`, `color`, `active`, `position`) VALUES
							(1, 'rgb(2, 204, 2)', 1, 1),
							(2, 'rgb(255, 163, 0)', 1, 2),
							(3, 'red', 1, 3);";
			$this->db->executeQuery($query);

			$query = "CREATE TABLE IF NOT EXISTS `{$this->table_service}_availability_name` (
						  `id` int(11) NOT NULL AUTO_INCREMENT,
						  `availability` int(11) NOT NULL,
						  `language` varchar(2) DEFAULT '',
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

		if($this->options['searchHistory'] > 0)
		{
			$query = "CREATE TABLE IF NOT EXISTS `{$this->table_service}_search_history` (
						  `id` int(11) NOT NULL AUTO_INCREMENT,
						  `product_id` int(11) NOT NULL,
						  `product_article` text NULL,
						  `user` int(11) NOT NULL,
						  `date` int(11) NOT NULL,
						  `last_view` int(11) NOT NULL,
						  `count_per_day` int(11) NULL,
						  PRIMARY KEY (`id`),
						  KEY `product` (`product_id`,`user`,`date`)
						) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";
			$this->db->executeQuery($query);
		}

		if($this->options['useMarkUp'] > 0)
		{
			$query = "CREATE TABLE IF NOT EXISTS `{$this->table_service}_markup` (
						  `id` int(11) NOT NULL AUTO_INCREMENT,
						  `from` float NOT NULL DEFAULT '0',
						  `to` float NOT NULL DEFAULT '0',
						  `value` float NOT NULL DEFAULT '0',
						  PRIMARY KEY (`id`)
						) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";
			$this->db->executeQuery($query);
		}

		$preview = array();
		$preview['alias'] = $alias;
		$preview['active'] = 1;
		$preview['name'] = 'Відображення у корзині';
		$preview['prefix'] = 'cart';
		$preview['type'] = 2;
		$preview['width'] = $preview['height'] = 200;
		$preview['quality'] = 80;
		$this->db->insertRow('wl_images_sizes', $preview);

		return true;
	}

	public function alias_delete($alias = 0, $table = '', $uninstall_service = false)
	{
		if($alias > 0 && !$uninstall_service) 
		{
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
				$this->db->deleteRow($this->table_service.'_groups', $alias, 'wl_alias');

			$this->db->deleteRow($this->table_service.'_product_options', $alias, 'shop');
		}
		return true;
	}

	public function setOption($option, $value, $alias, $table = '')
	{
		$this->options[$option] = $value;

		if ($option == 'prom' AND $value > 0)
		{
			if($columns = $this->db->getQuery("SHOW COLUMNS FROM `{$this->table_service}_groups`"))
			{
				$go = true;
				foreach ($columns as $column) {
					if($column->Field == 'prom_id')
						$go = false;
				}
				if($go)
					$this->db->executeQuery("ALTER TABLE `{$this->table_service}_groups` ADD `prom_id` INT NULL");
			}
		}
		if ($option == 'useGroups' AND $value > 0)
		{
			$query = "CREATE TABLE IF NOT EXISTS `{$this->table_service}_groups` (
						  `id` int(11) NOT NULL AUTO_INCREMENT,
						  `wl_alias` int(11) NULL,
						  `alias` text NULL,
						  `parent` int(11),
						  `position` int(11) NULL,
						  `active` tinyint(1) NULL,
						  `author_add` int(11) NOT NULL,
						  `date_add` int(11) NOT NULL,
						  `author_edit` int(11) NOT NULL,
						  `date_edit` int(11) NOT NULL,
						  PRIMARY KEY (`id`),
						  UNIQUE KEY `id` (`id`),
						  KEY `wl_alias` (`wl_alias`),
						  KEY `parent` (`parent`),
						  KEY `position` (`position`),
						  KEY `active` (`active`)
						) ENGINE=InnoDB  DEFAULT CHARSET=utf8;";
			$this->db->executeQuery($query);
		}
		if($option == 'ProductMultiGroup' AND $value > 0)
		{
			$query = "CREATE TABLE IF NOT EXISTS `{$this->table_service}_product_group` (
						  `id` int(11) NOT NULL AUTO_INCREMENT,
						  `product` int(11) NOT NULL,
						  `group` int(11) NOT NULL,
						  `position` int(11) NULL,
						  `active` tinyint(1) NULL,
						  PRIMARY KEY (`id`),
						  KEY `product` (`product`, `group`)
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
						  `color` text NULL,
						  `active` tinyint(1) NULL DEFAULT '1',
						  `position` int(11) NULL,
						  PRIMARY KEY (`id`)
						) ENGINE=InnoDB  DEFAULT CHARSET=cp1251 AUTO_INCREMENT=4 ;";
			$this->db->executeQuery($query);

			$query = "CREATE TABLE IF NOT EXISTS `{$this->table_service}_availability_name` (
						  `id` int(11) NOT NULL AUTO_INCREMENT,
						  `availability` int(11) NULL,
						  `language` varchar(2) DEFAULT '',
						  `name` text NULL,
						  PRIMARY KEY (`id`),
						  KEY `availability` (`availability`)
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
		if($option == 'searchHistory' AND $value > 0)
		{
			$query = "CREATE TABLE IF NOT EXISTS `{$this->table_service}_search_history` (
						  `id` int(11) NOT NULL AUTO_INCREMENT,
						  `product_id` int(11) NOT NULL,
						  `product_article` text NULL,
						  `user` int(11) NOT NULL,
						  `date` int(11) NOT NULL,
						  `last_view` int(11) NOT NULL,
						  `count_per_day` int(11) NULL,
						  PRIMARY KEY (`id`),
						  KEY `product` (`product_id`,`user`,`date`)
						) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";
			$this->db->executeQuery($query);
		}

		if($option == 'useMarkUp' AND $value > 0)
		{
			$query = "CREATE TABLE IF NOT EXISTS `{$this->table_service}_markup` (
						  `id` int(11) NOT NULL AUTO_INCREMENT,
						  `from` float NOT NULL DEFAULT '0',
						  `to` float NOT NULL DEFAULT '0',
						  `value` float NOT NULL DEFAULT '0',
						  PRIMARY KEY (`id`)
						) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";
			$this->db->executeQuery($query);
		}
	}

	public function install_go()
	{
		$query = "CREATE TABLE IF NOT EXISTS `{$this->table_service}_products` (
					  `id` int(11) NOT NULL AUTO_INCREMENT,
					  `wl_alias` int(11) NOT NULL,
					  `article` text NULL,
					  `alias` text NULL,
					  `group` int(11) NULL,
					  `price` float unsigned NULL,
					  `old_price` float unsigned NULL,
					  `currency` tinyint(2) NULL,
					  `availability` tinyint(1) NULL,
					  `active` tinyint(1) NULL,
					  `position` int(11) NULL,
					  `author_add` int(11) NOT NULL,
					  `date_add` int(11) NOT NULL,
					  `author_edit` int(11) NOT NULL,
					  `date_edit` int(11) NOT NULL,
					  PRIMARY KEY (`id`),
					  UNIQUE KEY `id` (`id`),
					  KEY `wl_alias` (`wl_alias`),
					  KEY `group` (`group`),
					  KEY `active` (`active`),
					  KEY `position` (`position`)
					) ENGINE=InnoDB  DEFAULT CHARSET=utf8;";
		$this->db->executeQuery($query);

		$query = "CREATE TABLE IF NOT EXISTS `{$this->table_service}_options` (
					  `id` int(11) NOT NULL AUTO_INCREMENT,
					  `wl_alias` int(11) NOT NULL,
					  `group` int(11) NULL,
					  `alias` text NULL,
					  `photo` text NULL,
					  `position` int(11) NULL,
					  `type` int(11) NULL,
					  `main` tinyint(1)  NULL DEFAULT '0',
					  `changePrice` text NULL,
					  `filter` tinyint(1) NULL,
					  `toCart` tinyint(1) NULL,
					  `active` tinyint(1) NULL,
					  PRIMARY KEY (`id`),
					  UNIQUE KEY `id` (`id`),
					  KEY `wl_alias` (`wl_alias`),
					  KEY `group` (`group`),
					  KEY `active` (`active`),
					  KEY `position` (`position`)
					) ENGINE=InnoDB  DEFAULT CHARSET=utf8;";
		$this->db->executeQuery($query);

		$query = "CREATE TABLE IF NOT EXISTS `{$this->table_service}_options_name` (
					  `id` int(11) NOT NULL AUTO_INCREMENT,
					  `option` int(11) NOT NULL,
					  `language` varchar(2) DEFAULT '',
					  `name` text NULL,
					  `sufix` text NULL,
					  PRIMARY KEY (`id`),
					  UNIQUE KEY `id` (`id`),
					  KEY `option` (`option`)
					) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
		$this->db->executeQuery($query);

		$query = "CREATE TABLE IF NOT EXISTS `{$this->table_service}_product_options` (
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  `product` int(11) NOT NULL,
			  `option` int(11) NOT NULL,
			  `language` varchar(2) DEFAULT '',
			  `value` text NULL,
			  `changePrice` text NULL,
			  PRIMARY KEY (`id`),
			  UNIQUE KEY `id` (`id`),
			  KEY `option` (`product`, `option`)
			) ENGINE=InnoDB  DEFAULT CHARSET=utf8;";
		$this->db->executeQuery($query);

		$query = "CREATE TABLE IF NOT EXISTS `{$this->table_service}_products_similar` (
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  `product` int(11) NOT NULL,
			  `group` int(11) NOT NULL,
			  PRIMARY KEY (`id`),
			  KEY `product` (`product`, `group`)
			) ENGINE=InnoDB  DEFAULT CHARSET=utf8;";
		$this->db->executeQuery($query);

		return true;
	}

	public function uninstall($service = 0)
	{
		if(isset($_POST['content']) && $_POST['content'] == 1)
		{
			$this->db->executeQuery("DROP TABLE IF EXISTS {$this->table_service}_products");
			$this->db->executeQuery("DROP TABLE IF EXISTS {$this->table_service}_options");
			$this->db->executeQuery("DROP TABLE IF EXISTS {$this->table_service}_options_name");
			$this->db->executeQuery("DROP TABLE IF EXISTS {$this->table_service}_product_options");
			$this->db->executeQuery("DROP TABLE IF EXISTS {$this->table_service}_product_group");
			$this->db->executeQuery("DROP TABLE IF EXISTS {$this->table_service}_services");
			$this->db->executeQuery("DROP TABLE IF EXISTS {$this->table_service}_groups");
			$this->db->executeQuery("DROP TABLE IF EXISTS {$this->table_service}_availability");
			$this->db->executeQuery("DROP TABLE IF EXISTS {$this->table_service}_availability_name");
			$this->db->executeQuery("DROP TABLE IF EXISTS {$this->table_service}_search_history");
			$this->db->executeQuery("DROP TABLE IF EXISTS {$this->table_service}_markup");
		}
	}
	
}

?>
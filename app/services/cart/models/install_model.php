<?php

class install
{
	public $service = null;

	public $name = "cart";
	public $title = "Корзина";
	public $description = "Корзина для shopshowcase";
	public $group = "cart";
	public $table_service = "s_cart";
	public $multi_alias = 0;
	public $order_alias = 200;
	public $admin_sidebar = 1;
	public $admin_ico = 'fa-shopping-cart';
	public $version = "2.1";

	public $options = array('useCheckBox' => 0, 'newUserType' => 4, 'price_format' => '');
	public $options_type = array('useCheckBox' => 'bool', 'usePassword' => 'bool', 'newUserType' => 'number', 'price_format' => false);
	public $options_title = array('useCheckBox' => 'Використовувати галочки', 'newUserType' => 'ID типу нового користувача');
	public $options_admin = array (
					'word:products_to_all' => 'товарів',
					'word:product_to' => 'До товару',
					'word:product_to_delete' => 'товару',
					'word:product' => 'товар',
					'word:products' => 'товари'
				);
	public $sub_menu = array();

	public $cooperation_index = array('shopshowcase' => 2);
	public $cooperation_types = array('cart' => 'Корзина');
	public $cooperation_service = array('shopshowcase' => 'cart');

	public function alias($alias = 0, $table = '')
	{
		if($alias == 0)
			return false;

		$checkout = array();
		$checkout['alias'] = $alias;
		$checkout['content'] = 1;
		$checkout['name'] = 'Checkout';
		$checkout['language'] = $checkout['title'] = $checkout['description'] = $checkout['keywords'] = $checkout['text'] = $checkout['list'] = $checkout['meta'] = NULL;
		if($_SESSION['language'])
			foreach ($_SESSION['all_languages'] as $language) {
				$checkout['language'] = $language;
				$this->db->insertRow('wl_ntkd', $checkout);
			}
		else
			$this->db->insertRow('wl_ntkd', $checkout);

		$checkout = array();
		$checkout['alias'] = $alias;
		$checkout['content'] = 2;
		$checkout['name'] = $this->title;
		$checkout['text'] = '<p>Дякуємо за замовлення. Очікуйте дзвінка менеджера</p>';
		$checkout['language'] = $checkout['title'] = $checkout['description'] = $checkout['keywords'] = $checkout['list'] = $checkout['meta'] = NULL;
		if($_SESSION['language'])
			foreach ($_SESSION['all_languages'] as $language) {
				$checkout['language'] = $language;
				$this->db->insertRow('wl_ntkd', $checkout);
			}
		else
			$this->db->insertRow('wl_ntkd', $checkout);

		$alias1 = -1;
		if($actions = $this->db->getAllDataByFieldInArray('wl_aliases_cooperation', array('alias1' => '<0', 'type' => '__tab_profile'), 'alias1'))
			$alias1 = $actions[0]->alias1 - 1;
		$this->db->insertRow('wl_aliases_cooperation', array('alias1' => $alias1, 'alias2' => $alias, 'type' => '__tab_profile'));

		// $alias1 = -1;
		// if($actions = $this->db->getAllDataByFieldInArray('wl_aliases_cooperation', array('alias1' => '<0', 'type' => '__link_profile'), 'alias1'))
		// 	$alias1 = $actions[0]->alias1 - 1;
		// $this->db->insertRow('wl_aliases_cooperation', array('alias1' => $alias1, 'alias2' => $alias, 'type' => '__link_profile'));

		return true;
	}

	public function alias_delete($alias = 0, $table = '')
	{
		return true;
	}

	public function setOption($option, $value, $alias, $table = '')
	{
		return true;
	}

	public function install_go()
	{
		$query = "CREATE TABLE IF NOT EXISTS `{$this->table_service}` (
					  `id` int(11) NOT NULL AUTO_INCREMENT,
					  `user` int(11) NOT NULL,
					  `status` int(2) NOT NULL,
					  `shipping_id` int(11) NULL,
					  `shipping_info` text NULL,
					  `payment_alias` int(11) NULL,
					  `payment_id` int(11) NULL,
					  `total` float UNSIGNED NOT NULL,
					  `bonus` int(11) NULL,
					  `discount` float UNSIGNED NULL,
					  `comment` text NULL,
					  `date_add` int(11) NOT NULL,
					  `date_edit` int(11) NOT NULL,
					  PRIMARY KEY (`id`),
					  UNIQUE KEY `id` (`id`)
					) ENGINE=InnoDB  DEFAULT CHARSET=utf8;";
		$this->db->executeQuery($query);

		$query = "CREATE TABLE IF NOT EXISTS `{$this->table_service}_products` (
					  `id` int(11) NOT NULL AUTO_INCREMENT,
					  `cart` int(11) NULL,
					  `user` int(11) NOT NULL,
					  `product_alias` int(11) NOT NULL,
					  `product_id` int(11) NOT NULL,
					  `product_options` text NULL,
					  `storage_alias` int(11) NULL,
					  `storage_invoice` int(11) NULL,
					  `price` float UNSIGNED NOT NULL,
					  `price_in` float UNSIGNED NULL,
					  `quantity` int(11) NOT NULL,
					  `quantity_wont` int(11) NOT NULL,
					  `quantity_returned` int(11) NULL,
					  `discount` float UNSIGNED NULL,
					  `bonus` int(11) NULL,
					  `date` int(11) NOT NULL,
					  PRIMARY KEY (`id`),
					  UNIQUE KEY `id` (`id`)
					) ENGINE=InnoDB  DEFAULT CHARSET=utf8;";
		$this->db->executeQuery($query);

		$query = "CREATE TABLE IF NOT EXISTS `{$this->table_service}_status` (
					  `id` int(11) NOT NULL AUTO_INCREMENT,
					  `name` text NOT NULL,
					  `color` text NULL,
					  `active` tinyint(1) NOT NULL,
					  `weight` tinyint(2) NOT NULL,
					  PRIMARY KEY (`id`),
					  UNIQUE KEY `id` (`id`)
					) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
		$this->db->executeQuery($query);

		$query = "INSERT INTO `{$this->table_service}_status` (`name`, `color`, `active`, `weight`) VALUES
											 ('Нове НЕ оплачене', 'warning', 1, 0),
											 ('Підтверджене/очікує оплати', 'success', 0, 9),
											 ('Нове оплачене', 'warning', 1, 10),
											 ('Оплачене', 'danger', 1, 11),
											 ('Відправлено', 'primary', 1, 20),
											 ('Закрите', 'default', 1, 98),
											 ('Скасоване', 'default', 1, 99);";
		$this->db->executeQuery($query);

		$query = "CREATE TABLE IF NOT EXISTS `{$this->table_service}_payments` (
					  `id` int(11) NOT NULL AUTO_INCREMENT,
					  `wl_alias` int(11) NULL,
					  `active` tinyint(1) NULL,
					  `position` int(11) NULL,
					  `name` text NOT NULL,
					  `info` text,
					  PRIMARY KEY (`id`),
					  KEY `active` (`active`)
					) ENGINE=InnoDB  DEFAULT CHARSET=utf8;";
		$this->db->executeQuery($query);

		$query = "INSERT INTO `{$this->table_service}_payments` (`id`, `wl_alias`, `active`, `position`, `name`, `info`) VALUES
											(1, 0, 1, 1, 'Готівкою при отриманні', 'Оплата готівкою при доставці/отриманні товару.'),
											(2, 0, 0, 2, 'Оплатити на рахунок за реквізитами', 'Реквізити оплати отримаєте листом на електронну скриньку');";
		$this->db->executeQuery($query);

		$query = "CREATE TABLE IF NOT EXISTS `{$this->table_service}_shipping` (
					  `id` int(11) NOT NULL AUTO_INCREMENT,
					  `wl_alias` int(11) NULL,
					  `active` tinyint(1) NULL,
					  `position` int(11) NULL,
					  `type` tinyint(1) NULL,
					  `name` text NOT NULL,
					  `info` text,
					  `pay` float NULL,
					  `price` float NULL,
					  PRIMARY KEY (`id`),
					  KEY `active` (`active`)
					) ENGINE=InnoDB  DEFAULT CHARSET=utf8;";
		$this->db->executeQuery($query);

		$query = "CREATE TABLE IF NOT EXISTS `{$this->table_service}_history` (
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  `cart` int(11) NOT NULL,
			  `status` int(11) NOT NULL,
			  `user` int(11) NOT NULL,
			  `comment` text NULL,
			  `date` int(11) NOT NULL,
			  PRIMARY KEY (`id`),
			  UNIQUE KEY `id` (`id`)
			) ENGINE=InnoDB  DEFAULT CHARSET=utf8;";
		$this->db->executeQuery($query);

		$query = "CREATE TABLE `{$this->table_service}_bonus` (
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  `status` tinyint(1) NOT NULL,
			  `code` varchar(12) NOT NULL,
			  `count_do` int(11) NOT NULL,
			  `from` int(11) NOT NULL,
			  `to` int(11) NOT NULL,
			  `discount_type` tinyint(1) NOT NULL,
			  `discount` float NOT NULL,
			  `discount_max` float NOT NULL,
			  `order_min` float NOT NULL,
			  `info` text NOT NULL,
			  `manager` int(11) NOT NULL,
			  `date` int(11) NOT NULL,
			  PRIMARY KEY (`id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;";
		$this->db->executeQuery($query);

		return true;
	}

	public function uninstall($service = 0)
	{
		if(isset($_POST['content']) && $_POST['content'] == 1){
			$this->db->executeQuery("DROP TABLE IF EXISTS {$this->table_service}");
			$this->db->executeQuery("DROP TABLE IF EXISTS {$this->table_service}_products");
			$this->db->executeQuery("DROP TABLE IF EXISTS {$this->table_service}_status");
			$this->db->executeQuery("DROP TABLE IF EXISTS {$this->table_service}_history");
		}
	}

}

?>
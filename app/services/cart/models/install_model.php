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
	public $admin_ico = 'fa-shopping-cart';
	public $version = "1.0";

	public $options = array('usePassword' => 1, 'newUserType' => 4);
	public $options_type = array('usePassword' => 'bool', 'newUserType' => 'number');
	public $options_title = array('usePassword' => 'Пароль обов"язковий при ідентифікації', 'newUserType' => 'ID типу нового користувача');
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
					  `shipping_alias` int(11) NULL,
					  `shipping_id` int(11) NULL,
					  `payment_alias` int(11) NULL,
					  `payment_id` int(11) NULL,
					  `total` float UNSIGNED NOT NULL,
					  `comment` text NULL,
					  `date_add` int(11) NOT NULL,
					  `date_edit` int(11) NOT NULL,
					  PRIMARY KEY (`id`),
					  UNIQUE KEY `id` (`id`)
					) ENGINE=InnoDB  DEFAULT CHARSET=utf8;";
		$this->db->executeQuery($query);

		$query = "CREATE TABLE IF NOT EXISTS `{$this->table_service}_products` (
					  `id` int(11) NOT NULL AUTO_INCREMENT,
					  `cart` int(11) NOT NULL,
					  `alias` int(11) NOT NULL,
					  `storage_alias` int(11) NULL,
					  `storage_invoice` int(11) NULL,
					  `product` int(11) NOT NULL,
					  `price` float UNSIGNED NOT NULL,
					  `price_in` float UNSIGNED NULL,
					  `currency` int(11) NULL,
					  `quantity` int(11) NOT NULL,
					  `quantity_reserved` int(11) NULL,
					  `quantity_returned` int(11) NULL,
					  `discount` float UNSIGNED NULL,
					  `user` int(11) NOT NULL,
					  `date` int(11) NOT NULL,
					  PRIMARY KEY (`id`),
					  UNIQUE KEY `id` (`id`)
					) ENGINE=InnoDB  DEFAULT CHARSET=utf8;";
		$this->db->executeQuery($query);

		$query = "CREATE TABLE IF NOT EXISTS `{$this->table_service}_status` (
					  `id` int(11) NOT NULL AUTO_INCREMENT,
					  `name` text NOT NULL,
					  `active` tinyint(1) NOT NULL,
					  `weight` tinyint(2) NOT NULL,
					  PRIMARY KEY (`id`),
					  UNIQUE KEY `id` (`id`)
					) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
		$this->db->executeQuery($query);

		$query = "INSERT INTO `{$this->table_service}_status` (`name`, `active`, `weight`) VALUES
											 ('Нова', 1, 0),
											 ('Підтверджено/очікує оплати', 1, 10),
											 ('Оплачено', 1, 15),
											 ('Нова,оплачено', 1, 15),
											 ('Відправлено', 1, 20),
											 ('Закрито', 1, 99),
											 ('Скасовано', 1, 99);";
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
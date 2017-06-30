<?php

class install
{
	public $service = null;

	public $name = "delivery";
	public $title = "Доставка";
	public $description = "";
	public $group = "shop";
	public $table_service = "s_delivery";
	public $multi_alias = 0;
	public $order_alias = 1;
	public $admin_ico = 'fa-car';
	public $version = "1.0";

	public $options = array();
	public $options_type = array();
	public $options_title = array();
	public $options_admin = array ();
	public $sub_menu = array();

	public $cooperation_index = array('cart' => 2);
	public $cooperation_types = array('delivery' => 'Доставка');
	public $cooperation_service = array('cart' => 'delivery');


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
		$this->options[$option] = $value;

		return true;
	}

	public function install_go()
	{
		$query = "CREATE TABLE IF NOT EXISTS `{$this->table_service}_carts` (
					  `id` int(11) NOT NULL AUTO_INCREMENT,
					  `user` int(11) NOT NULL,
					  `method` tinyint(2) NOT NULL,
					  `address` text NOT NULL,
					  `receiver` text NOT NULL,
					  `phone` text NOT NULL,
					  `comment` text NULL,
					  PRIMARY KEY (`id`)
					) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1";
		$this->db->executeQuery($query);

		$query = "CREATE TABLE IF NOT EXISTS `{$this->table_service}_users` (
					  `id` int(11) NOT NULL AUTO_INCREMENT,
					  `user` int(11) NOT NULL,
					  `method` tinyint(2) NOT NULL,
					  `address` text NOT NULL,
					  `receiver` text NOT NULL,
					  `phone` text NOT NULL,
					  PRIMARY KEY (`id`)
					) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1";
		$this->db->executeQuery($query);

		$query = "CREATE TABLE IF NOT EXISTS `{$this->table_service}_methods` (
					  `id` int(11) NOT NULL AUTO_INCREMENT,
					  `active` tinyint(1) NOT NULL,
					  `name` text NOT NULL,
					  `info` text NOT NULL,
					  `placeholder` text NOT NULL,
					  `site` text NOT NULL,
					  `date_add` int(11) NOT NULL,
					  PRIMARY KEY (`id`)
					) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1";
		$this->db->executeQuery($query);

		return true;
	}

	public function uninstall($service = 0)
	{
		return true;
	}

}

?>
<?php

class install{

	public $service = null;
	
	public $name = "faq";
	public $title = "FAQ";
	public $description = "Перелік типових питань-відповідей. Мультимовний.";
	public $group = "page";
	public $table_service = "s_faq";
	public $table_alias = "";
	public $multi_alias = 1;
	public $order_alias = 25;
	public $admin_ico = 'fa-question';
	public $version = "1.1";

	public $options = array('useGroups' => 1);
	public $options_type = array('useGroups' => 'bool');
	public $options_title = array('useGroups' => 'Наявність груп');
	public $options_admin = array ();
	public $sub_menu = array("add" => "Додати питання", "all" => "До всіх питань", "groups" => "Групи");

	public function alias($alias = 0, $table = '')
	{
		if($alias == 0) return false;

		$query = "CREATE TABLE IF NOT EXISTS `{$this->table_service}_questions` (
					  `id` int(11) NOT NULL AUTO_INCREMENT,
					  `alias` text,
					  `group` int(11) NOT NULL,
					  `position` int(11) NOT NULL,
					  `active` tinyint(1) NOT NULL,
					  `author_add` int(11) NOT NULL,
					  `date_add` int(11) NOT NULL,
					  `author_edit` int(11) NOT NULL,
					  `date_edit` int(11) NOT NULL,
					  PRIMARY KEY (`id`),
					  UNIQUE KEY `id` (`id`)
					) ENGINE=InnoDB  DEFAULT CHARSET=utf8;";
		$this->db->executeQuery($query);

		if($this->options['useGroups'] > 0)
		{
			$query = "CREATE TABLE IF NOT EXISTS `{$this->table_service}_groups` (
						  `id` int(11) NOT NULL AUTO_INCREMENT,
						  `alias` text,
						  `position` int(11) NOT NULL,
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

		return true;
	}

	public function alias_delete($alias = 0, $table = '')
	{
		$this->db->executeQuery("DROP TABLE IF EXISTS {$this->table_service}_questions");
		$this->db->executeQuery("DROP TABLE IF EXISTS {$this->table_service}_groups");

		return true;
	}

	public function setOption($option, $value, $table = '')
	{
		$this->options[$option] = $value;

		if ($option == 'useGroups' AND $value > 0) {
			$query = "CREATE TABLE IF NOT EXISTS `{$this->table_service}_groups` (
						  `id` int(11) NOT NULL AUTO_INCREMENT,
						  `position` int(11) NOT NULL,
						  `active` tinyint(1) NOT NULL,
						  `author` int(11) NOT NULL,
						  `date_add` int(11) NOT NULL,
						  `date_edit` int(11) NOT NULL,
						  PRIMARY KEY (`id`),
						  UNIQUE KEY `id` (`id`)
						) ENGINE=InnoDB  DEFAULT CHARSET=utf8;";
			$this->db->executeQuery($query);
		}
	}

	public function install_go(){
		return true;
	}

	public function uninstall($service = 0)
	{
		return true;
	}
	
}

?>
<?php

class install
{
	public $service = null;
	
	public $name = "library";
	public $title = "Бібліотека статей (Блог)";
	public $description = "Бібліотека статей із підтримкою категорій. Мультимовна.";
	public $group = "page";
	public $table_service = "s_library";
	public $table_alias = "";
	public $multi_alias = 1;
	public $order_alias = 60;
	public $admin_ico = 'fa-book';
	public $version = "2.5";

	public $options = array('useGroups' => 1, 'articleMultiGroup' => 0, 'useAvailability' => 0, 'folder' => 'library', 'articleOrder' => 'position DESC', 'groupOrder' => 'position ASC');
	public $options_type = array('useGroups' => 'bool', 'articleMultiGroup' => 'bool', 'useAvailability' => 'bool', 'folder' => 'text', 'articleOrder' => 'text', 'groupOrder' => 'text');
	public $options_title = array('useGroups' => 'Наявність груп', 'articleMultiGroup' => 'Мультигрупи (1 стаття більше ніж 1 група)', 'useAvailability' => 'Використання доступності', 'folder' => 'Папка для зображень/аудіо', 'articleOrder' => 'Сортування товарів', 'groupOrder' => 'Сортування груп');
	public $options_admin = array (
					'word:articles_to_all' => 'статтей',
					'word:article_to' => 'До статті',
					'word:article_to_delete' => 'статтю',
					'word:article' => 'стаття',
					'word:articles' => 'статті',
					'word:article_add' => 'Додати статтю',
					'word:groups_to_all' => 'груп',
					'word:groups_to_delete' => 'групу',
					'word:group' => 'група',
					'word:group_add' => 'Додати групу статтей'
				);
	public $sub_menu = array("add" => "Додати статтю", "all" => "До всіх статтей", "groups" => "Групи");
	public $sub_menu_access = array("add" => 2, "all" => 2, "groups" => 2);

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

			if($this->options['articleMultiGroup'] > 0)
			{
				$query = "CREATE TABLE IF NOT EXISTS `{$this->table_service}_article_group` (
						  `article` int(11) NOT NULL,
						  `group` int(11) NOT NULL
						) ENGINE=InnoDB  DEFAULT CHARSET=utf8;";
				$this->db->executeQuery($query);
			}
		}

		$path = IMG_PATH.$this->options['folder'].'/groups';
		$path = substr($path, strlen(SITE_URL));
		if(!is_dir($path))
			mkdir($path, 0777);

		return true;
	}

	public function alias_delete($alias = 0, $table = '')
	{
		$articles = $this->db->getAllDataByFieldInArray($this->table_service.'_articles', $alias, 'wl_alias');
		if(!empty($articles))
		{
			$this->db->deleteRow($this->table_service.'_articles', $alias, 'wl_alias');
			$this->db->deleteRow($this->table_service.'_groups', $alias, 'wl_alias');

			foreach ($articles as $article) {
				$this->db->deleteRow($this->table_service.'_article_group', $article->id, 'article');
			}
		}

		$groups = $this->db->getAllDataByFieldInArray($this->table_service.'_groups', $alias, 'wl_alias');
		if(!empty($groups))
			$this->db->deleteRow($this->table_service.'_groups', $alias, 'wl_alias');

		$path = IMG_PATH.$this->options['folder'].'/groups';
		$path = substr($path, strlen(SITE_URL));
		if(is_dir($path))
			$this->removeDirectory($path);

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
		if($option == 'articleMultiGroup' AND $value > 0)
		{
			$query = "CREATE TABLE IF NOT EXISTS `{$this->table_service}_article_group` (
						  `article` int(11) NOT NULL,
						  `group` int(11) NOT NULL
						) ENGINE=InnoDB  DEFAULT CHARSET=utf8;";
			$this->db->executeQuery($query);

			$articles = $this->db->getAllDataByFieldInArray($this->table_service.'_articles', $alias, 'wl_alias');
			if($articles)
			{
				$list = array();
				foreach ($articles as $article) {
					$list[] = $article->id;
				}

				$count = $this->db->getCount($this->table_service.'_article_group', array('article' => $list));
				if($count > 0)
				{
					foreach ($articles as $article) {
						$this->db->insertRow($this->table_service.'_article_group'.$table, array('article' => $article->id, 'group' => $article->group));
					}
				}
			}
		}
	}

	public function install_go()
	{
		$query = "CREATE TABLE IF NOT EXISTS `{$this->table_service}_articles` (
					  `id` int(11) NOT NULL AUTO_INCREMENT,
					  `wl_alias` int(11) NOT NULL,
					  `alias` text NOT NULL,
					  `group` int(11) NOT NULL,
					  `availability` int(11) NOT NULL,
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

		return true;
	}

	public function uninstall($service = 0)
	{
		if(isset($_POST['content']) && $_POST['content'] == 1)
		{
			$this->db->executeQuery("DROP TABLE IF EXISTS {$this->table_service}_articles");
			$this->db->executeQuery("DROP TABLE IF EXISTS {$this->table_service}_article_group");
			$this->db->executeQuery("DROP TABLE IF EXISTS {$this->table_service}_groups");
		}
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
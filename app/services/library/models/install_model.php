<?php

class install{

	public $service = null;
	
	public $name = "library";
	public $title = "Бібліотека статей";
	public $description = "Бібліотека статей із підтримкою категорій. Мультимовна.";
	public $group = "page";
	public $table_service = "s_library";
	public $table_alias = "";
	public $multi_alias = 1;
	public $order_alias = 60;
	public $admin_ico = 'fa-book';
	public $version = "2.2.1";

	public $options = array('useGroups' => 1, 'ArticleMultiGroup' => 0, 'resize' => 1, 'folder' => 'library', 'idExplodeLink' => '-');
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

	public $seo_name = "Бібліотека статей";
	public $seo_title = "Бібліотека статей";
	public $seo_description = "";
	public $seo_keywords = "";

	function alias($alias = 0, $table = '')
	{
		if($alias == 0) return false;

		$query = "CREATE TABLE IF NOT EXISTS `{$this->table_service}_articles{$table}` (
					  `id` int(11) NOT NULL AUTO_INCREMENT,
					  `group` int(11) NOT NULL,
					  `alias` text NOT NULL,
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

		$query = "CREATE TABLE IF NOT EXISTS `{$this->table_service}_article_photos{$table}` (
					  `id` int(11) NOT NULL AUTO_INCREMENT,
					  `article` int(11) NOT NULL,
					  `name` text NOT NULL,
					  `date` int(11) NOT NULL,
					  `user` int(11) NOT NULL,
					  `title` text NOT NULL,
					  `main` int(11) NOT NULL,
					  PRIMARY KEY (`id`)
					) ENGINE=InnoDB  DEFAULT CHARSET=utf8;";
		$this->db->executeQuery($query);

		if($this->options['useGroups'] > 0){
			$query = "CREATE TABLE IF NOT EXISTS `{$this->table_service}_groups{$table}` (
						  `id` int(11) NOT NULL AUTO_INCREMENT,
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

			if($this->options['ArticleMultiGroup'] > 0){
				$query = "CREATE TABLE IF NOT EXISTS `{$this->table_service}_article_group{$table}` (
						  `article` int(11) NOT NULL,
						  `group` int(11) NOT NULL
						) ENGINE=InnoDB  DEFAULT CHARSET=utf8;";
				$this->db->executeQuery($query);
			}
		}

		if($this->options['resize'] > 0){
			$query = "INSERT INTO `wl_images_sizes` (`id`, `alias`, `active`, `name`, `prefix`, `type`, `height`, `width`) VALUES
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

	public function alias_delete($alias = 0, $table = '')
	{
		$this->db->executeQuery("DROP TABLE IF EXISTS {$this->table_service}_articles{$table}");
		$this->db->executeQuery("DROP TABLE IF EXISTS {$this->table_service}_article_photos{$table}");
		$this->db->executeQuery("DROP TABLE IF EXISTS {$this->table_service}_groups{$table}");
		$this->db->executeQuery("DROP TABLE IF EXISTS {$this->table_service}_article_group{$table}");
		if($alias > 0) @$this->db->deleteRow('wl_images_sizes', $alias, 'alias');

		$path = IMG_PATH.$this->options['folder'];
		if(strlen(IMG_PATH) > strlen(SITE_URL)) $path = substr($path, strlen(SITE_URL));
		if(is_dir($path)) $this->removeDirectory($path);

		return true;
	}

	public function setOption($option, $value, $table = '')
	{
		$this->options[$option] = $value;

		if ($option == 'useGroups' AND $value > 0) {
			$query = "CREATE TABLE IF NOT EXISTS `{$this->table_service}_groups{$table}` (
						  `id` int(11) NOT NULL AUTO_INCREMENT,
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
		if($option == 'ArticleMultiGroup' AND $value > 0){
			$query = "CREATE TABLE IF NOT EXISTS `{$this->table_service}_article_group{$table}` (
						  `article` int(11) NOT NULL,
						  `group` int(11) NOT NULL
						) ENGINE=InnoDB  DEFAULT CHARSET=utf8;";
			$this->db->executeQuery($query);
			$groups = $this->db->getCount($this->table_service.'_article_group'.$table);
			if($groups == 0){
				$articles = $this->db->getAllData($this->table_service.$table);
				if($articles){
					foreach ($articles as $article) {
						$this->db->insertRow($this->table_service.'_article_group'.$table, array('article' => $article->id, 'group' => $article->group));
					}
				}
			}
		}
	}

	function install_go(){
		return true;
	}

	public function uninstall($service = 0)
	{
		if(isset($_POST['content']) && $_POST['content'] == 1){
			$path = IMG_PATH.$this->options['folder'];
			if(strlen(IMG_PATH) > strlen(SITE_URL)) $path = substr($path, strlen(SITE_URL));
			if(is_dir($path)) $this->removeDirectory($path);
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
<?php

class articles_model {

	public function table($sufix = '')
	{
		return $_SESSION['service']->table.$sufix.$_SESSION['alias']->table;
	}
	
	function getArticles($category = 0, $active = true){
		$where = '';
		if($active) $where = "WHERE a.active = '1'";
		if($category > 0 && $_SESSION['option']->useCategories > 0){
			if($_SESSION['option']->articleMultiCategory == 0){
				if($where == '') $where = "WHERE a.category = '{$category}'";
				else $where .= " AND a.category = '{$category}'";
			} else {
				$articles = $this->db->getAllDataByFieldInArray($this->table('_article_category'), $category, 'category');
				if($articles) {
					if($where == '') $where = "WHERE a.id IN (";
					else $where .= " AND a.id IN (";
					foreach ($articles as $id) {
						$where .= "'{$id}', ";
					}
					$where = substr($where, 0, -2);
					$where .= ")";
				} else return null;
			}
		}
		$limit = '';
		if(isset($_SESSION['option']->PerPage) && $_SESSION['option']->PerPage > 0){
			$start = 0;
			if(isset($_GET['page']) && is_numeric($_GET['page']) && $_GET['page'] > 1){
				$start = ($_GET['page'] - 1) * $_SESSION['option']->PerPage;
			}
			$limit = "LIMIT {$start}, {$_SESSION['option']->PerPage}";
		}
		//ORDER BY a.position DESC
		$this->db->executeQuery("SELECT a.*, u.name as user_name FROM `{$this->table()}` as a LEFT JOIN wl_users as u ON u.id = user {$where} {$limit}");
        if($this->db->numRows() > 0){
            $articles = $this->db->getRows('array');

            $this->db->executeQuery("SELECT count(*) as count FROM `{$this->table()}` as a {$where}");
			$_SESSION['option']->count_all_articles = $this->db->getRows()->count;

			$where = '';
            if($_SESSION['language']) $where = "AND `language` = '{$_SESSION['language']}'";
            foreach ($articles as $article) {
            	$this->db->executeQuery("SELECT `name`, `text` FROM `wl_ntkd` WHERE `alias` = '{$_SESSION['alias']->id}' AND `content` = '{$article->id}' {$where}");
            	if($this->db->numRows() == 1){
            		$ntkd = $this->db->getRows();
            		$article->name = $ntkd->name;
            		$article->text = $ntkd->text;
            	}
            }

			return $articles;
		}
		return null;
	}
	
	function getArticleById($id){
		$this->db->executeQuery("SELECT a.*, u.name as user_name FROM {$this->table()} as a LEFT JOIN wl_users as u ON u.id = a.user WHERE a.id = $id");
        if($this->db->numRows() == 1){
            $article = $this->db->getRows();
            if($article->category > 0){
            	$article->category = $this->getCategoryById($article->category);
            	$where['alias'] = $_SESSION['alias']->id;
            	$where['content'] = $article->category->id * -1;
            	if($_SESSION['language']) $where['language'] = $_SESSION['language'];
            	$article->category->name = $this->db->getAllDataById('wl_ntkd', $where)->name;
            }
            return $article;
		}
		return null;
	}

	function add_article($photo = -1){
		$data = array();
		$data['active'] = 1;
		$data['photo'] = $photo;
		$data['user'] = $_SESSION['user']->id;
		$data['date'] = time();
		if($this->db->insertRow($this->table(), $data)){
			$id = $this->db->getLastInsertedId();
			$data = array();
			$data['link'] = '';

			$ntkd['alias'] = $_SESSION['alias']->id;
			$ntkd['content'] = $id;
			if($_SESSION['language']){
				foreach ($_SESSION['all_languages'] as $lang) {
					$ntkd['language'] = $lang;
					$ntkd['name'] = $_POST['name_'.$lang];
					$ntkd['title'] = $_POST['name_'.$lang];
					if($lang == $_SESSION['language']){
						$data['link'] = $this->db->latterUAtoEN($ntkd['name']);
					}
					$this->db->insertRow('wl_ntkd', $ntkd);
				}
			} else {
				$ntkd['name'] = $_POST['name'];
				$ntkd['title'] = $_POST['name'];
				$data['link'] = $this->db->latterUAtoEN($ntkd['name']);
				$this->db->insertRow('wl_ntkd', $ntkd);
			}
			$data['link'] = $id .'-'. $data['link'];
			$data['position'] = $this->db->getCount($this->table());
			if($_SESSION['option']->useCategories){
				if($_SESSION['option']->articleMultiCategory && isset($_POST['category']) && is_array($_POST['category'])){
					foreach ($_POST['category'] as $category) {
						$this->db->insertRow($this->table('_article_category'), array('article' => $id, 'category' => $category));
					}
				} else {
					if(isset($_POST['category']) && is_numeric($_POST['category'])) $data['category'] = $_POST['category'];
				}
			}
			if($photo > 0) $data['photo'] = $id;
			if($this->db->updateRow($this->table(), $data, $id)) return $id;
		}
		return false;
	}

	function getCategories($active = true)
	{
		$where = '';
		if($active) $where = "WHERE c.active = '1'";
		$this->db->executeQuery("SELECT c.*, u.name as user_name FROM {$this->table('_categories')} as c LEFT JOIN wl_users as u ON u.id = c.user {$where} ORDER BY c.position");
		if($this->db->numRows() > 0){
            $categories = $this->db->getRows('array');

            $where = '';
            if($_SESSION['language']) $where = "AND `language` = '{$_SESSION['language']}'";
            foreach ($categories as $category) {
            	$this->db->executeQuery("SELECT `name`, `list` FROM `wl_ntkd` WHERE `alias` = '{$_SESSION['alias']->id}' AND `content` = '-{$category->id}' {$where}");
            	if($this->db->numRows() == 1){
            		$ntkd = $this->db->getRows();
            		$category->name = $ntkd->name;
            		$category->list = $ntkd->list;
            	}
            }

            return $categories;
		}
		return null;
	}

	function getCategoryByAlias($alias){
		$alias = $this->db->sanitizeString($alias);
		$this->db->executeQuery("SELECT c.*, u.name as user_name FROM {$this->table('_categories')} as c LEFT JOIN wl_users as u ON u.id = c.user WHERE c.link = '{$alias}'");
        if($this->db->numRows() == 1){
            return $this->db->getRows();
		}
		return null;
	}

	function getCategoryById($id){
		$this->db->executeQuery("SELECT c.*, u.name as user_name FROM {$this->table('_categories')} as c LEFT JOIN wl_users as u ON u.id = c.user WHERE c.id = $id");
        if($this->db->numRows() == 1){
            return $this->db->getRows();
		}
		return null;
	}

	function add_category($photo = -1){
		$data = array();
		$data['active'] = 1;
		$data['photo'] = $photo;
		$data['user'] = $_SESSION['user']->id;
		$data['date'] = time();
		if($this->db->insertRow($this->table('_categories'), $data)){
			$id = $this->db->getLastInsertedId();
			$data = array();
			$data['link'] = '';

			$ntkd['alias'] = $_SESSION['alias']->id;
			$ntkd['content'] = $id;
			$ntkd['content'] *= -1;
			if($_SESSION['language']){
				foreach ($_SESSION['all_languages'] as $lang) {
					$ntkd['language'] = $lang;
					$ntkd['name'] = $_POST['name_'.$lang];
					$ntkd['title'] = $_POST['name_'.$lang];
					if($lang == $_SESSION['language']){
						$data['link'] = $this->db->latterUAtoEN($ntkd['name']);
					}
					$this->db->insertRow('wl_ntkd', $ntkd);
				}
			} else {
				$ntkd['name'] = $_POST['name'];
				$ntkd['title'] = $_POST['name'];
				$data['link'] = $this->db->latterUAtoEN($ntkd['name']);
				$this->db->insertRow('wl_ntkd', $ntkd);
			}
			$data['link'] = $this->categoryLink($data['link']);
			$data['position'] = $this->db->getCount($this->table('_categories'));
			if($photo > 0) $data['photo'] = $id;
			if($this->db->updateRow($this->table('_categories'), $data, $id)) return $id;
		}
		return false;
	}

	function categoryLink($link){
		$category = $this->getCategoryByAlias($link);
		$end = 0;
		$link2 = $link;
		while ($category) {
			$end++;
			$link2 = $link.'-'.$end;
		 	$category = $this->getCategoryByAlias($link2);
		}
		return $link2;
	}
	
	function changePosition($table, $id, $new_pos){
		$table = $this->table($table);
		$this->db->executeQuery("SELECT id, position as pos FROM `{$table}` ORDER BY `position` ASC ");
		 if($this->db->numRows() > 0){
            $articles = $this->db->getRows();
			$old_pos = 0;
			foreach($articles as $a) if($a->id == $id) { $old_pos = $a->pos; break; }
			if($new_pos < $old_pos)	foreach($articles as $a){
				if($a->pos >= $new_pos){
					if($a->pos != $old_pos && $a->pos < $old_pos){
						$pos = $a->pos + 1;
						$this->db->executeQuery("UPDATE `{$table}` SET `position` = '{$pos}' WHERE `id` = {$a->id}");
					}
					if($a->pos == $old_pos){ 
						$this->db->executeQuery("UPDATE `{$table}` SET `position` = '{$new_pos}' WHERE `id` = {$a->id}");
						return true;
					}
				}
			}
			if($new_pos > $old_pos)	foreach($articles as $a){
				if($a->pos <= $new_pos){
					if($a->pos != $old_pos && $a->pos > $old_pos){
						$pos = $a->pos - 1;
						$this->db->executeQuery("UPDATE `{$table}` SET `position` = '{$pos}' WHERE `id` = {$a->id}");
					}
					if($a->pos == $old_pos){ 
						$this->db->executeQuery("UPDATE `{$table}` SET `position` = '{$new_pos}' WHERE `id` = {$a->id}");
					}
				} else return true;
			}
		}
		return true;
	}
	
}

?>
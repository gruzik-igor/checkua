<?php

class library_model {

	public function table($sufix = '')
	{
		return $_SESSION['service']->table.$sufix.$_SESSION['alias']->table;
	}
	
	public function getArticles($Group = 0, $active = true){
		$where = array();
		if($active) {
			$where['active'] = 1;
		}

		if($_SESSION['option']->useGroups > 0)
		{
			if(is_array($Group) && !empty($Group))
			{
				$where['id'] = array();
				foreach ($Group as $g) {
					$articles = $this->db->getAllDataByFieldInArray($this->table('_article_group'), $g->id, 'group');
					if($articles) {
						foreach ($articles as $article) {
							array_push($where['id'], $article->article);
						}
					}
				}
			}
			elseif($Group > 0)
			{
				if($_SESSION['option']->ArticleMultiGroup == 0) {
					$where['group'] = $Group;
				} else {
					$articles = $this->db->getAllDataByFieldInArray($this->table('_article_group'), $Group, 'group');
					if($articles) {
						$where['id'] = array();
						foreach ($articles as $article) {
							array_push($where['id'], $article->article);
						}
					} else {
						return null;
					}
				}
			}
		}
		
		if(count($_GET) > 1){
			foreach ($_GET as $key => $value) {
				if(isset($_GET['name']) && $_GET['name'] != '')
				{
					$articles = $this->db->getAllDataByFieldInArray('wl_ntkd', array('alias' => $_SESSION['alias']->id, 'content' => '>0', 'name' => '%'.$this->data->get('name')));
					if(!empty($articles))
					{
						if(!isset($where['id']))
						{
							$where['id'] = array();
							foreach ($articles as $p) {
								array_push($where['id'], $p->content);
							}
						}
						else
						{
							$ids = $where['id'];
							$where['id'] = array();
							foreach ($articles as $p) {
								if(in_array($p->content, $ids)) {
									array_push($where['id'], $p->content);
								}
							}
						}
					} else {
						return false;
					}
				}
			}
		}
		
		$this->db->select($this->table('_articles').' as p', '*', $where);
		
		$this->db->join('wl_users', 'name as user_name', '#p.author_edit');
		
		if($_SESSION['option']->useGroups > 0 && $_SESSION['option']->ArticleMultiGroup == 0)
		{
			$where_gn['alias'] = $_SESSION['alias']->id;
			$where_gn['content'] = "#-p.group";
			if($_SESSION['language']) $where_gn['language'] = $_SESSION['language'];
			$this->db->join('wl_ntkd as gn', 'name as group_name', $where_gn);
		}

		$where_ntkd['alias'] = $_SESSION['alias']->id;
		$where_ntkd['content'] = "#p.id";
		if($_SESSION['language']) $where_ntkd['language'] = $_SESSION['language'];
		$this->db->join('wl_ntkd as n', 'name, text, list', $where_ntkd);

		$this->db->order('position DESC');

		if($active && isset($_SESSION['option']->paginator_per_page) && $_SESSION['option']->paginator_per_page > 0)
		{
			$start = 0;
			if(isset($_GET['page']) && is_numeric($_GET['page']) && $_GET['page'] > 1) {
				$start = ($_GET['page'] - 1) * $_SESSION['option']->paginator_per_page;
			}
			$this->db->limit($start, $_SESSION['option']->paginator_per_page);
		}

		$articles = $this->db->get('array', false);
        if($articles)
        {
			$_SESSION['option']->paginator_total = $this->db->get('count');;

            $list = array();
        	if($_SESSION['option']->useGroups > 0)
        	{
	            $all_groups = $this->db->getAllData($this->table('_groups'));
	            if($all_groups) foreach ($all_groups as $g) {
	            	$list[$g->id] = clone $g;
	            }
	        }
	        $sizes = false;
	        if($_SESSION['option']->resize > 0){
				$sizes = $this->db->getAllDataByFieldInArray('wl_images_sizes', $_SESSION['alias']->id, 'alias');
			}

            foreach ($articles as $article)
            {
            	$article->link = $_SESSION['alias']->alias.'/'.$article->alias;
            	$article->videos = $this->getArticleVideos($article->id);
            	$article->photos = $this->getArticlePhotos($article->id);
            	if($article->photo != '')
            	{
					if($sizes){
						foreach ($sizes as $resize) if($resize->active == 1){
							$resize_name = $resize->prefix.'_photo';
							$article->$resize_name = $_SESSION['option']->folder.'/'.$article->id.'/'.$resize->prefix.'_'.$article->photo;
						}
					}
					$article->photo = $_SESSION['option']->folder.'/'.$article->id.'/'.$article->photo;
            	}

				$article->parents = array();
				if($_SESSION['option']->useGroups > 0)
				{
					if($_SESSION['option']->ArticleMultiGroup == 0 && $article->group > 0){
						$article->parents = $this->makeParents($list, $article->group, $article->parents);
						$link = $_SESSION['alias']->alias.'/';
						foreach ($article->parents as $parent) {
							$link .= $parent->alias .'/';
						}
						$article->link = $link . $article->alias;
					} elseif($_SESSION['option']->ArticleMultiGroup == 1){
						$article->group = array();

						$this->db->select($this->table('_article_group') .' as pg', '', $article->id, 'article');
						$this->db->join($this->table('_groups'), 'id, alias, parent', '#pg.group');
						$where_ntkd['content'] = "#-pg.group";
            			$this->db->join('wl_ntkd', 'name', $where_ntkd);
						$article->group = $this->db->get('array');

			            if(!empty($article->group)) foreach ($article->group as $g) {
			            	if($g->parent > 0) {
			            		$g->link = $_SESSION['alias']->alias.'/'.$this->makeLink($list, $g->parent, $g->alias);
			            	}
			            }
					}
				}
            }

			return $articles;
		}
		$this->db->clear();
		return null;
	}
	
	public function getArticleById($id)
	{
		$this->db->select($this->table('_articles').' as p', '*', $id);

		$this->db->join('wl_users as a', 'name as author_add_name', '#p.author_add');
		$this->db->join('wl_users as e', 'name as author_edit_name', '#p.author_edit');
		
		if($_SESSION['option']->useGroups > 0 && $_SESSION['option']->ArticleMultiGroup == 0)
		{
			$where_gn['alias'] = $_SESSION['alias']->id;
			$where_gn['content'] = "#-p.group";
			if($_SESSION['language']) $where_gn['language'] = $_SESSION['language'];
			$this->db->join('wl_ntkd as gn', 'name as group_name', $where_gn);
		}

		$where_ntkd['alias'] = $_SESSION['alias']->id;
		$where_ntkd['content'] = "#p.id";
		if($_SESSION['language']) $where_ntkd['language'] = $_SESSION['language'];
		$this->db->join('wl_ntkd as n', 'name, text, list', $where_ntkd);

		$article = $this->db->get('single');
        if($article)
        {
        	if($article->photo != '')
        	{
        		if($_SESSION['option']->resize > 0)
        		{
					$sizes = $this->db->getAllDataByFieldInArray('wl_images_sizes', $_SESSION['alias']->id, 'alias');
					if($sizes){
						foreach ($sizes as $resize) if($resize->active == 1){
							$resize_name = $resize->prefix.'_photo';
							$article->$resize_name = $_SESSION['option']->folder.'/'.$article->id.'/'.$resize->prefix.'_'.$article->photo;
						}
					}
				}
				$article->photo = $_SESSION['option']->folder.'/'.$article->id.'/'.$article->photo;
        	}
			$article->photos = $this->getArticlePhotos($article->id);
			$article->videos = $this->getArticleVideos($article->id);
        	$article->link = $_SESSION['alias']->alias.'/'.$article->alias;

			$article->parents = array();
			if($_SESSION['option']->useGroups > 0)
			{
				$list = array();
				$all_groups = $this->db->getAllData($this->table('_groups'));
	            if($all_groups) foreach ($all_groups as $g) {
	            	$list[$g->id] = clone $g;
	            }

				if($_SESSION['option']->ArticleMultiGroup == 0 && $article->group > 0){
					$article->parents = $this->makeParents($list, $article->group, $article->parents);
					$link = $_SESSION['alias']->alias.'/';
					foreach ($article->parents as $parent) {
						$link .= $parent->alias .'/';
					}
					$article->link = $link . $article->alias;
				} elseif($_SESSION['option']->ArticleMultiGroup == 1){
					$article->group = array();

					$this->db->select($this->table('_article_group') .' as pg', '', $article->id, 'article');
					$this->db->join($this->table('_groups'), 'id, alias, parent', '#pg.group');
					$where_ntkd['content'] = "#-pg.group";
        			$this->db->join('wl_ntkd', 'name', $where_ntkd);
					$article->group = $this->db->get('array');

		            if(!empty($article->group)) foreach ($article->group as $g) {
		            	if($g->parent > 0) {
		            		$g->link = $_SESSION['alias']->alias.'/'.$this->makeLink($list, $g->parent, $g->alias);
		            	}
		            }
				}
			}
            return $article;
		}
		return null;
	}

	public function getArticlePhotos($article)
	{
		$this->db->executeQuery("SELECT p.*, u.name as user_name FROM {$this->table('_article_photos')} as p LEFT JOIN wl_users as u ON p.user = u.id WHERE p.article = {$article} ORDER BY p.main DESC");
		if($this->db->numRows() > 0){
			$photos = $this->db->getRows('array');
			if($_SESSION['option']->resize > 0)
    		{
				$sizes = $this->db->getAllDataByFieldInArray('wl_images_sizes', $_SESSION['alias']->id, 'alias');
			}
			foreach ($photos as $photo) {
				$photo->path = $_SESSION['option']->folder.'/'.$article.'/'.$photo->name;
				if($sizes){
					foreach ($sizes as $resize) if($resize->active == 1){
						$resize_name = $resize->prefix.'_path';
						$photo->$resize_name = $_SESSION['option']->folder.'/'.$article.'/'.$resize->prefix.'_'.$photo->name;
					}
				}
			}
			return $photos;
		}
		return false;
	}

	public function getArticleVideos($article)
	{
		$this->db->executeQuery("SELECT v.*, u.name as user_name FROM wl_video as v LEFT JOIN wl_users as u ON v.author = u.id WHERE v.alias = {$_SESSION['alias']->id} AND v.content = {$article} AND v.active = 1 ORDER BY v.date_add DESC");
		if($this->db->numRows() > 0){
			return $this->db->getRows('array');
		}
		return false;
	}

	public function getGroups($parent = 0)
	{
		$where['active'] = 1;
		if($parent >= 0) $where['parent'] = $parent;
		$this->db->select($this->table('_groups') .' as g', '*', $where);

		$this->db->join('wl_users', 'name as user_name', '#g.author_edit');

		$where_ntkd['alias'] = $_SESSION['alias']->id;
		$where_ntkd['content'] = "#-g.id";
		if($_SESSION['language']) $where_ntkd['language'] = $_SESSION['language'];
		$this->db->join('wl_ntkd', "name, text, list", $where_ntkd);

		$this->db->order('position');
		
		if(isset($_SESSION['option']->paginator_per_page) && $_SESSION['option']->paginator_per_page > 0 && $parent >= 0){
			$start = 0;
			if(isset($_GET['page']) && is_numeric($_GET['page']) && $_GET['page'] > 1){
				$start = ($_GET['page'] - 1) * $_SESSION['option']->paginator_per_page;
			}
			$this->db->limit($start, $_SESSION['option']->paginator_per_page);
		}
		
		$categories = $this->db->get('array', false);
		if($categories)
		{
			@$_SESSION['option']->count_all_articles = $this->db->get('count');

            $list = array();
            $groups = $this->db->getAllData($this->table('_groups'));
            foreach ($groups as $Group) {
            	$list[$Group->id] = clone $Group;
            }

            $sizes = false;
	        if($_SESSION['option']->resize > 0){
				$sizes = $this->db->getAllDataByFieldInArray('wl_images_sizes', $_SESSION['alias']->id, 'alias');
			}

            foreach ($categories as $Group) {
            	$Group->link = $Group->alias;
            	if($Group->parent > 0) {
            		$Group->link = $this->makeLink($list, $Group->parent, $Group->alias);
            	}

            	if($Group->photo != '')
            	{
					if($sizes){
						foreach ($sizes as $resize) if($resize->active == 1){
							$resize_name = $resize->prefix.'_photo';
							$Group->$resize_name = $_SESSION['option']->folder.'/groups/'.$resize->prefix.'_'.$Group->photo.'.jpg';
						}
					}
					$Group->photo = $_SESSION['option']->folder.'/groups/'.$Group->photo.'.jpg';
            	}
            }

            return $categories;
		}
		else
		{
			$this->db->clear();
		}
		return null;
	}

	public function makeParents($all, $parent, $parents)
	{
		$group = clone $all[$parent];
		$where = '';
        if($_SESSION['language']) $where = "AND `language` = '{$_SESSION['language']}'";
        $this->db->executeQuery("SELECT `name` FROM `wl_ntkd` WHERE `alias` = '{$_SESSION['alias']->id}' AND `content` = '-{$group->id}' {$where}");
    	if($this->db->numRows() == 1){
    		$ntkd = $this->db->getRows();
    		$group->name = $ntkd->name;
    	}
    	array_unshift ($parents, $group);
		if($all[$parent]->parent > 0) $parents = $this->makeParents ($all, $all[$parent]->parent, $parents);
		return $parents;
	}

	public function getGroupByAlias($alias, $parent = 0)
	{
		$where['alias'] = $alias;
		$where['parent'] = $parent;
		$this->db->select($this->table('_groups') .' as c', '*', $where);
		$this->db->join('wl_users', 'name as user_name', '#c.author_edit');
		return $this->db->get('single');
	}

	public function GroupLink($link){
		$Group = $this->getGroupByAlias($link);
		$end = 0;
		$link2 = $link;
		while ($Group) {
			$end++;
			$link2 = $link.'-'.$end;
		 	$Group = $this->getGroupByAlias($link2);
		}
		return $link2;
	}

	public function getGroupParents($all, $list)
	{
		$childs = array();
		foreach ($list as $group) {
			$childs[] = $group;
			if(!empty($all[$group]->childs)) $childs = array_merge($childs, $this->getGroupParents($all, $all[$group]->childs));
		}
		return $childs;
	}

	private function makeLink($all, $parent, $link)
	{
		$link = $all[$parent]->alias .'/'.$link;
		if($all[$parent]->parent > 0) $link = $this->makeLink ($all, $all[$parent]->parent, $link);
		return $link;
	}
	
}

?>
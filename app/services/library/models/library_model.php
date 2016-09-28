<?php

class library_model {

	public function table($sufix = '', $useAliasTable = false)
	{
		if($useAliasTable) return $_SESSION['service']->table.$sufix.$_SESSION['alias']->table;
		return $_SESSION['service']->table.$sufix;
	}

	public function routeURL($url = array(), &$type = null, $admin = false)
	{		
		$i = 1;
		$last_i = count($url) - 1;
		$type = false;

		if($_SESSION['option']->useGroups)
		{
			$parent = 0;
			$group = false;
			
			for (; $i <= $last_i ; $i++) {
				$all_info = ($url[$i] == $url[$last_i]) ? true : false;
				$group = $this->getGroupByAlias($url[$i], $parent, $all_info);
				if($group)
				{
					$parent = $group->id;
					$type = 'group';
					if($i == $last_i) return $group;
				}
				else
				{
					$type = false;
					if($i != $last_i) return false;
					break;
				}
			}

			if($i == ++$last_i) return $group;
		}

		if($article = $this->getArticle(end($url)))
		{
			$url = implode('/', $url);
			if($url != $article->link)
			{
				$link = SITE_URL;
				if($admin) $link .= 'admin/';
				header ('HTTP/1.1 301 Moved Permanently');
				header ('Location: '. $link. $article->link);
				exit();
			}

			$type = 'article';
			return $article;
		}

		return false;
	}
	
	public function getArticles($Group = -1, $noInclude = 0, $active = true)
	{
		$where = array('wl_alias' => $_SESSION['alias']->id);
		if($active)
			$where['active'] = 1;

		if($_SESSION['option']->useGroups > 0)
		{
			if(is_array($Group) && !empty($Group))
			{
				$where['id'] = array();
				foreach ($Group as $g) {
					$articles = $this->db->getAllDataByFieldInArray($this->table('_article_group'), $g->id, 'group');
					if($articles) {
						foreach ($articles as $article) if($article->article != $noInclude) {
							array_push($where['id'], $article->article);
						}
					}
				}
			}
			elseif($Group >= 0)
			{
				if($_SESSION['option']->articleMultiGroup == 0 || $Group == 0)
					$where['group'] = $Group;
				else
				{
					$articles = $this->db->getAllDataByFieldInArray($this->table('_article_group'), $Group, 'group');
					if($articles)
					{
						$where['id'] = array();
						foreach ($articles as $article) if($article->article != $noInclude) {
							array_push($where['id'], $article->article);
						}
					}
					else
						return null;
				}
			}
			elseif($noInclude > 0)
				$where['id'] = '!'.$noInclude;
		}
		elseif($noInclude > 0)
			$where['id'] = '!'.$noInclude;
		
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
					$ids = clone $where['id'];
					$where['id'] = array();
					foreach ($articles as $p) {
						if(in_array($p->content, $ids))
							array_push($where['id'], $p->content);
					}
				}
			}
			else
				return false;
		}
		
		$this->db->select($this->table('_articles').' as a', '*', $where);
		
		$this->db->join('wl_users as aa', 'name as author_add_name', '#a.author_add');
			$this->db->join('wl_users as e', 'name as author_edit_name', '#a.author_edit');

		if($_SESSION['option']->useGroups > 0 && $_SESSION['option']->articleMultiGroup == 0)
		{
			$where_gn['alias'] = $_SESSION['alias']->id;
			$where_gn['content'] = "#-a.group";
			if($_SESSION['language'])
				$where_gn['language'] = $_SESSION['language'];
			$this->db->join('wl_ntkd as gn', 'name as group_name', $where_gn);
		}

		$where_ntkd['alias'] = $_SESSION['alias']->id;
		$where_ntkd['content'] = "#a.id";
		if($_SESSION['language'])
			$where_ntkd['language'] = $_SESSION['language'];
		$this->db->join('wl_ntkd as n', 'name, text, list', $where_ntkd);
		$this->db->order($_SESSION['option']->articleOrder);

		if(isset($_SESSION['option']->paginator_per_page) && $_SESSION['option']->paginator_per_page > 0)
		{
			$start = 0;
			if(isset($_GET['per_page']) && is_numeric($_GET['per_page']) && $_GET['per_page'] > 0)
				$_SESSION['option']->paginator_per_page = $_GET['per_page'];
			if(isset($_GET['page']) && is_numeric($_GET['page']) && $_GET['page'] > 1)
				$start = ($_GET['page'] - 1) * $_SESSION['option']->paginator_per_page;
			$this->db->limit($start, $_SESSION['option']->paginator_per_page);
		}

		$articles = $this->db->get('array', false);
        if($articles)
        {
			$_SESSION['option']->paginator_total = $this->db->get('count');

            $list = array();
        	if($_SESSION['option']->useGroups > 0)
        	{
	            $all_groups = $this->db->getAllDataByFieldInArray($this->table('_groups'), $_SESSION['alias']->id, 'wl_alias');
	            if($all_groups)
	            	foreach ($all_groups as $g) {
		            	$list[$g->id] = clone $g;
		            }
	        }

	        $sizes = $this->db->getAliasImageSizes();

            foreach ($articles as $article)
            {
            	$article->link = $_SESSION['alias']->alias.'/'.$article->alias;
            	$article->photo = null;

            	if($photo = $this->getArticlePhoto($article->id))
            	{
					if($sizes)
						foreach ($sizes as $resize) {
							$resize_name = $resize->prefix.'_path';
							$photo->$resize_name = $_SESSION['option']->folder.'/'.$article->id.'/'.$resize->prefix.'_'.$photo->file_name;
						}
					$article->photo = $_SESSION['option']->folder.'/'.$article->id.'/'.$photo->file_name;
            	}

				$article->parents = array();
				if($_SESSION['option']->useGroups > 0)
				{
					if($_SESSION['option']->articleMultiGroup == 0 && $article->group > 0)
					{
						$article->parents = $this->makeParents($list, $article->group, $article->parents);
						$link = '/';
						foreach ($article->parents as $parent) {
							$link .= $parent->alias .'/';
						}
						$article->group_link = $_SESSION['alias']->alias . $link;
						$article->link = $_SESSION['alias']->alias . $link . $article->alias;
					}
					elseif($_SESSION['option']->articleMultiGroup == 1)
					{
						$article->group = array();

						$this->db->select($this->table('_article_group') .' as pg', '', $article->id, 'article');
						$this->db->join($this->table('_groups'), 'id, alias, parent', '#pg.group');
						$where_ntkd['content'] = "#-pg.group";
            			$this->db->join('wl_ntkd', 'name', $where_ntkd);
						$article->group = $this->db->get('array');
						if($article->group)
				            foreach ($article->group as $g) {
				            	if($g->parent > 0) {
				            		$g->link = $_SESSION['alias']->alias . '/' . $this->makeLink($list, $g->parent, $g->alias);
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
	
	function getArticle($alias, $key = 'alias', $all_info = true)
	{
		$this->db->select($this->table('_articles').' as p', '*', array('wl_alias' => $_SESSION['alias']->id, $key => $alias));

		if($all_info)
		{
			$this->db->join('wl_users as a', 'name as author_add_name', '#p.author_add');
			$this->db->join('wl_users as e', 'name as author_edit_name', '#p.author_edit');

			if($_SESSION['option']->useGroups > 0 && $_SESSION['option']->articleMultiGroup == 0)
			{
				$where_gn['alias'] = $_SESSION['alias']->id;
				$where_gn['content'] = "#-p.group";
				if($_SESSION['language']) $where_gn['language'] = $_SESSION['language'];
				$this->db->join('wl_ntkd as gn', 'name as group_name', $where_gn);
			}

			$where_ntkd['alias'] = $_SESSION['alias']->id;
			$where_ntkd['content'] = "#p.id";
			if($_SESSION['language']) $where_ntkd['language'] = $_SESSION['language'];
			$this->db->join('wl_ntkd as n', 'name', $where_ntkd);
		}

		$article = $this->db->get('single');
        if($article)
        {
        	if(isset($_SESSION['alias']->breadcrumbs))
        	{
        		$_SESSION['alias']->breadcrumbs = array($_SESSION['alias']->name => $_SESSION['alias']->alias);
        	}
        	$article->link = $_SESSION['alias']->alias.'/'.$article->alias;

			$article->parents = array();
			if($_SESSION['option']->useGroups > 0)
			{
				$list = array();
				$all_groups = $this->db->getAllDataByFieldInArray($this->table('_groups'), $_SESSION['alias']->id, 'wl_alias');
	            if($all_groups) foreach ($all_groups as $g) {
	            	$list[$g->id] = clone $g;
	            }

				if($_SESSION['option']->articleMultiGroup == 0 && $article->group > 0)
				{
					$article->parents = $this->makeParents($list, $article->group, $article->parents);
					$link = $_SESSION['alias']->alias . '/';
					foreach ($article->parents as $parent) {
						$link .= $parent->alias .'/';
						if(isset($_SESSION['alias']->breadcrumbs)) $_SESSION['alias']->breadcrumbs[$parent->name] = $link;
					}
					$article->group_link = $link;
					$article->link = $link . $article->alias;
				}
				elseif($_SESSION['option']->articleMultiGroup == 1)
				{
					$article->group = array();

					$this->db->select($this->table('_article_group') .' as pg', '', $article->id, 'article');
					$this->db->join($this->table('_groups'), 'id, alias, parent', '#pg.group');
					$where_ntkd['content'] = "#-pg.group";
        			$this->db->join('wl_ntkd', 'name', $where_ntkd);
					$article->group = $this->db->get('array');

					if($article->group)
			            foreach ($article->group as $g) {
			            	if($g->parent > 0) {
			            		$g->link = $_SESSION['alias']->alias . '/' . $this->makeLink($list, $g->parent, $g->alias);
			            	}
			            }
				}
			}
        	if($all_info && isset($_SESSION['alias']->breadcrumbs)) $_SESSION['alias']->breadcrumbs[$article->name] = '';

            return $article;
		}
		return null;
	}

	public function getGroups($parent = 0)
	{
		$where['wl_alias'] = $_SESSION['alias']->id;
		$where['active'] = 1;
		if($parent >= 0) $where['parent'] = $parent;
		$this->db->select($this->table('_groups') .' as g', '*', $where);
		$this->db->join('wl_users', 'name as user_name', '#g.author_edit');

		$where_ntkd['alias'] = $_SESSION['alias']->id;
		$where_ntkd['content'] = "#-g.id";
		if($_SESSION['language']) $where_ntkd['language'] = $_SESSION['language'];
		$this->db->join('wl_ntkd', "name, text, list", $where_ntkd);

		$this->db->order($_SESSION['option']->groupOrder);
		
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
            $sizes = $this->db->getAliasImageSizes();
            $groups = $this->db->getAllDataByFieldInArray($this->table('_groups'), $_SESSION['alias']->id, 'wl_alias');
            foreach ($groups as $Group) {
            	$list[$Group->id] = clone $Group;
            }

            foreach ($categories as $Group) {
            	$Group->link = $_SESSION['alias']->alias.'/'.$Group->alias;
            	if($Group->parent > 0) {
            		$Group->link = $_SESSION['alias']->alias.'/'.$this->makeLink($list, $Group->parent, $Group->alias);
            	}

            	if($photo = $this->getArticlePhoto(-$Group->id))
            	{
					if($sizes)
						foreach ($sizes as $resize) {
							$resize_name = $resize->prefix.'_path';
							$photo->$resize_name = $_SESSION['option']->folder.'/-'.$Group->id.'/'.$resize->prefix.'_'.$photo->file_name;
						}
					$Group->photo = $_SESSION['option']->folder.'/-'.$Group->id.'/'.$photo->file_name;
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

	public function getGroupByAlias($alias, $parent = 0, $all_info = true)
	{
		$where['wl_alias'] = $_SESSION['alias']->id;
		$where['alias'] = $alias;
		$where['parent'] = $parent;
		$this->db->select($this->table('_groups') .' as c', '*', $where);
		if($all_info) $this->db->join('wl_users', 'name as user_name', '#c.author_edit');
		$group = $this->db->get('single');
		if($group)
			if($photo = $this->getArticlePhoto(-$group->id))
        	{
				if($sizes = $this->db->getAliasImageSizes())
					foreach ($sizes as $resize) {
						$resize_name = $resize->prefix.'_path';
						$photo->$resize_name = $_SESSION['option']->folder.'/-'.$group->id.'/'.$resize->prefix.'_'.$photo->file_name;
					}
				$group->photo = $_SESSION['option']->folder.'/-'.$group->id.'/'.$photo->file_name;
        	}
		return $group;
	}

	private function makeLink($all, $parent, $link)
	{
		$link = $all[$parent]->alias .'/'.$link;
		if($all[$parent]->parent > 0) $link = $this->makeLink ($all, $all[$parent]->parent, $link);
		return $link;
	}

	public function getArticlePhoto($article)
	{
		$where['alias'] = $_SESSION['alias']->id;
		$where['content'] = $article;
		$this->db->select('wl_images', '*', $where);
		$this->db->join('wl_users', 'name as user_name', '#author');
		$this->db->order('main DESC');
		$this->db->limit(1);
		return $this->db->get();
	}
	
}

?>
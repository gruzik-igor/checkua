<?php

class shop_search_model
{

	private $all_groups = NULL;

	public function table($sufix = '')
	{
		return $_SESSION['service']->table.$sufix;
	}
	
	public function getByContent($content, $admin = false)
	{
		$search = false;

		if($content > 0)
		{
			$this->db->select($this->table('_products').' as p', '*', $content);
			$this->db->join('wl_users', 'name as author_name', '#p.author_edit');
			if($_SESSION['option']->useMarkUp > 0){
				$this->db->join($this->table('_markup'), 'value as markup', array('from' => '<p.price', 'to' => '>=p.price'));
			}
			$product = $this->db->get('single');
			if($product && ($product->active || $admin || $_SESSION['option']->ProductMultiGroup))
			{
				if($_SESSION['option']->useGroups && $_SESSION['option']->ProductMultiGroup)
				{
					$where_product_group = array('product' => $product->id);
					if(!$admin)
						$where_product_group['active'] = 1;
					$this->db->select($this->table('_product_group') .' as pg', '', $where_product_group);
					$this->db->join($this->table('_groups'), 'id, alias, parent', '#pg.group');
					$where_ntkd['alias'] = $_SESSION['alias']->id;
					$where_ntkd['content'] = "#-pg.group";
					if($_SESSION['language']) $where_ntkd['language'] = $_SESSION['language'];
        			$this->db->join('wl_ntkd', 'name', $where_ntkd);
					$groups = $this->db->get('array');
					if(!$admin && empty($groups))
						return false;
				}

				$search = new stdClass();
				$search->id = $product->id;
				$search->article = $product->article;
				$search->link = $_SESSION['alias']->alias.'/'.$product->alias;
				$search->date = $product->date_edit;
				$search->author = $product->author_edit;
				$search->author_name = $product->author_name;
				$search->additional = false;
				$search->price = $product->price;

				if($_SESSION['option']->useMarkUp > 0 && $product->markup){
	        		$search->price = $product->price * $product->markup;
	        		$search->old_price = $product->old_price * $product->markup;
	        	}

	        	$search->old_price = $search->price != $search->old_price ? ceil($search->old_price) : 0;
		        $search->price = ceil($search->price);

				$search->folder = false;
				if(isset($_SESSION['option']->folder))
					$search->folder = $_SESSION['option']->folder;

				if($_SESSION['option']->useGroups)
				{
					$search->additional = array();

					$list = array();
					if($this->all_groups === NULL)
						$this->all_groups = $this->db->getAllData($this->table('_groups'));
		            if($this->all_groups) 
		            	foreach ($this->all_groups as $g) {
			            	$list[$g->id] = clone $g;
			            }

					if($_SESSION['option']->ProductMultiGroup == 0 && $product->group > 0)
					{
						$parents = $this->makeParents($list, $product->group, array());
						$link = $_SESSION['alias']->alias .'/';
						foreach ($parents as $parent) {
							$link .= $parent->alias .'/';
							$search->additional[$link] = $parent->name;
						}
						$search->link = $link . $product->alias;
					}
					elseif($_SESSION['option']->ProductMultiGroup == 1)
					{
						if($groups)
				            foreach ($groups as $g) {
			            		$link = $_SESSION['alias']->alias .'/';
			            		if($g->parent > 0)
			            			$link .= $this->makeLink($list, $g->parent, $g->alias);
			            		else
			            			$link .= $g->alias;
			            		$search->additional[$link] = $g->name;
			            	}
					}
				}
				if($admin)
				{
					$search->edit_link = 'admin/'.$search->link;
				}
			}
		}
		elseif($content == 0)
		{
			$search = new stdClass();
			$search->id = $_SESSION['alias']->id;
			$search->link = $_SESSION['alias']->alias;
			$search->date = 0;
			$search->author = 1;
			$search->author_name = '';
			$search->additional = false;
			$search->folder = false;
			if(isset($_SESSION['option']->folder))
				$search->folder = $_SESSION['option']->folder;
			return $search;
		}
		else
		{
			$content *= -1;
			$this->db->select($this->table('_groups'), '*', $content);
			$this->db->join('wl_users', 'name as author_name', '#author_edit');
			$group = $this->db->get('single');
			if($group && ($group->active || $admin))
			{
				$search = new stdClass();
				$search->id = $group->id;
				$search->link = $_SESSION['alias']->alias.'/'.$group->alias;
				$search->date = $group->date_edit;
				$search->author = $group->author_edit;
				$search->author_name = $group->author_name;
				$search->additional = false;
				$search->folder = false;
				if(isset($_SESSION['option']->folder))
					$search->folder = $_SESSION['option']->folder;
				if($admin)
				{
					$search->edit_link = 'admin/'.$_SESSION['alias']->alias.'/groups/'.$group->id;
				}

				if($group->parent > 0)
				{
					$search->additional = array();

					$list = array();
					if($this->all_groups === NULL)
						$this->all_groups = $this->db->getAllData($this->table('_groups'));
		            if($this->all_groups)
		            	foreach ($this->all_groups as $g) {
			            	$list[$g->id] = clone $g;
			            }

	            	$parents = $this->makeParents($list, $group->parent, array());
					$link = $_SESSION['alias']->alias .'/';
					foreach ($parents as $parent) {
						$link .= $parent->alias .'/';
						$search->additional[$link] = $parent->name;
					}
					$search->link = $link . $group->alias;
				}
			}
		}

		return $search;
	}

	private function makeParents($all, $parent, $parents)
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

	private function makeLink($all, $parent, $link)
	{
		$link = $all[$parent]->alias .'/'.$link;
		if($all[$parent]->parent > 0) $link = $this->makeLink ($all, $all[$parent]->parent, $link);
		return $link;
	}

}

?>
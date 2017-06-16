<?php

class shop_model {

	public function table($sufix = '', $useAliasTable = false)
	{
		if($useAliasTable)
			return $_SESSION['service']->table.$sufix.$_SESSION['alias']->table;
		return $_SESSION['service']->table.$sufix;
	}

	public function routeURL($url = array(), &$type = null, $admin = false)
	{
		if($product = $this->getProduct(end($url)))
		{
			$url = implode('/', $url);
			if($url != $product->link)
			{
				$link = SITE_URL;
				if(@!$_SESSION['user']->admin && @!$_SESSION['user']->manager)
					$this->db->sitemap_redirect($product->link);
				else
					$this->db->sitemap_update($product->id, 'link',  $product->link);
				if($admin)
					$link .= 'admin/';

				header ('HTTP/1.1 301 Moved Permanently');
				header ('Location: '. $link. $product->link);
				exit();
			}

			$type = 'product';
			return $product;
		}

		if($_SESSION['option']->useGroups)
		{
			$group = false;
			$parent = 0;
			array_shift($url);
			foreach ($url as $uri) {
				$group = $this->getGroupByAlias($uri, $parent);
				if($group)
					$parent = $group->id;
				else
					$group = false;
			}

			$type = 'group';
			return $group;
		}

		return false;
	}

	public function getProducts($Group = -1, $noInclude = 0, $active = true)
	{
		$where = array('wl_alias' => $_SESSION['alias']->id);
		if($active)
			$where['active'] = 1;

		if($_SESSION['option']->ProductUseArticle > 0 && is_string($Group) && $Group[0] == '%')
			$where['article'] = $Group;
		elseif($_SESSION['option']->useGroups > 0)
		{
			if(is_array($Group) && !empty($Group))
			{
				$where['id'] = array();
				foreach ($Group as $g) {
					if($_SESSION['option']->ProductMultiGroup == 0)
					{
						$products = $this->db->getAllDataByFieldInArray($this->table('_products'), $g->id, 'group');
					}
					else
					{
						$products = $this->db->getAllDataByFieldInArray($this->table('_product_group'), $g->id, 'group');
					}

					if($products)
					{
						foreach ($products as $product) 
						{
							if($_SESSION['option']->ProductMultiGroup == 0 && $product->id != $noInclude)
							{
								array_push($where['id'], $product->id);
							}
							elseif($product->product != $noInclude)
							{
								array_push($where['id'], $product->product);
							}
						}
						
					}
				}
			}
			elseif($Group >= 0)
			{
				if($_SESSION['option']->ProductMultiGroup == 0)
					$where['group'] = $Group;
				else
				{
					$products = $this->db->getAllDataByFieldInArray($this->table('_product_group'), $Group, 'group');
					if($products)
					{
						$where['id'] = array();
						foreach ($products as $product) if($product->product != $noInclude) {
							array_push($where['id'], $product->product);
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

		if(count($_GET) > 1)
		{
			foreach ($_GET as $key => $value) {
				if($key != 'request' && $key != 'page' && is_array($_GET[$key]))
				{
					$option = $this->db->getAllDataById($this->table('_options'), array('wl_alias' => $_SESSION['alias']->id, 'alias' => $key, 'filter' => 1));
					if($option)
					{
						$list_where['option'] = $option->id;
						if(!empty($where['id'])) $list_where['product'] = $where['id'];
						$where['id'] = array();
						foreach ($_GET[$key] as $value) if(is_numeric($value)) {
							if($option->type == 8) //checkbox
							{
								$list_where['value'] = '%'.$value;
								$list = $this->db->getAllDataByFieldInArray($this->table('_product_options'), $list_where);
								if($list)
									foreach ($list as $p) {
										$p->value = explode(',', $p->value);
										if(in_array($value, $p->value)) array_push($where['id'], $p->product);
									}
							} else {
								$list_where['value'] = $value;
								$list = $this->db->getAllDataByFieldInArray($this->table('_product_options'), $list_where);
								if($list)
									foreach ($list as $p) {
										array_push($where['id'], $p->product);
									}
							}
						}

						if(empty($where['id']))
							return false;
					}
				}
				if(isset($_GET['name']) && $_GET['name'] != '')
				{
					$products = $this->db->getAllDataByFieldInArray('wl_ntkd', array('alias' => $_SESSION['alias']->id, 'content' => '>0', 'name' => '%'.$this->data->get('name')));
					if(!empty($products))
					{
						if(!isset($where['id']))
						{
							$where['id'] = array();
							foreach ($products as $p) {
								array_push($where['id'], $p->content);
							}
						}
						else
						{
							$ids = clone $where['id'];
							$where['id'] = array();
							foreach ($products as $p) {
								if(in_array($p->content, $ids))
									array_push($where['id'], $p->content);
							}
						}
					}
					else
						return false;
				}
			}
		}
		if($_SESSION['option']->useGroups > 0 && $_SESSION['option']->ProductMultiGroup == 0 && isset($where['group']))
			$where['#g.active'] = 1;

		$this->db->select($this->table('_products').' as p', '*', $where);

		$this->db->join('wl_users', 'name as user_name', '#p.author_edit');

		if($_SESSION['option']->useAvailability > 0)
		{
			$this->db->join($_SESSION['service']->table.'_availability', 'color as availability_color', '#p.availability');

			$where_availability_name['availability'] = '#p.availability';
			if($_SESSION['language']) $where_availability_name['language'] = $_SESSION['language'];
			$this->db->join($_SESSION['service']->table.'_availability_name', 'name as availability_name', $where_availability_name);
		}

		if($_SESSION['option']->useGroups > 0)
		{
			if($_SESSION['option']->ProductMultiGroup)
			{
				if(!is_array($Group) && $Group >= 0)
					$this->db->join($this->table('_product_group').' as pg', 'id as position_id, position', array('group' => $Group, 'product' => '#p.id'));
			}
			else
			{
				$where_gn['alias'] = $_SESSION['alias']->id;
				$where_gn['content'] = "#-p.group";
				if($_SESSION['language']) $where_gn['language'] = $_SESSION['language'];
				$this->db->join('wl_ntkd as gn', 'name as group_name', $where_gn);
				$this->db->join($this->table('_groups').' as g', 'active as group_active', '#p.group');
			}
		}

		$where_ntkd['alias'] = $_SESSION['alias']->id;
		$where_ntkd['content'] = "#p.id";
		if($_SESSION['language']) $where_ntkd['language'] = $_SESSION['language'];
		$this->db->join('wl_ntkd as n', 'name, text, list', $where_ntkd);

		$prefix = 'p';
		$_SESSION['option']->productOrder = trim($_SESSION['option']->productOrder);
		$order = explode(' ', $_SESSION['option']->productOrder);
        if(((count($order) == 2 && $order[0] == 'position' && in_array($order[1], array('asc', 'ASC', 'desc', 'DESC'))) || (count($order) == 1 && $order[0] == 'position')) && $_SESSION['option']->ProductMultiGroup && !is_array($Group) && $Group >= 0)
        	$prefix = 'pg';

		if(isset($_GET['sort']))
		{
			switch ($this->data->get('sort')) {
				case 'price_up':
					$this->db->order('price DESC');
					break;
				case 'price_down':
					$this->db->order('price ASC');
					break;
				case 'article':
					$this->db->order('article ASC');
					break;
				default:
					$this->db->order($_SESSION['option']->productOrder, $prefix);
					break;
			}
		}
		else
			$this->db->order($_SESSION['option']->productOrder, $prefix);

		if(isset($_SESSION['option']->paginator_per_page) && $_SESSION['option']->paginator_per_page > 0)
		{
			$start = 0;
			if(isset($_GET['per_page']) && is_numeric($_GET['per_page']) && $_GET['per_page'] > 0)
				$_SESSION['option']->paginator_per_page = $_GET['per_page'];
			if(isset($_GET['page']) && is_numeric($_GET['page']) && $_GET['page'] > 1)
				$start = ($_GET['page'] - 1) * $_SESSION['option']->paginator_per_page;
			$this->db->limit($start, $_SESSION['option']->paginator_per_page);
		}

		$products = $this->db->get('array', false);
        if($products)
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

            foreach ($products as $product)
            {
            	$product->link = $_SESSION['alias']->alias.'/'.$product->alias;
            	$product->options = $this->getProductOptions($product);
            	$product->photo = null;

            	if($photo = $this->getProductPhoto($product->id))
            	{
					if($sizes)
						foreach ($sizes as $resize) {
							$resize_name = $resize->prefix.'_photo';
							$product->$resize_name = $_SESSION['option']->folder.'/'.$product->id.'/'.$resize->prefix.'_'.$photo->file_name;
						}
					$product->photo = $_SESSION['option']->folder.'/'.$product->id.'/'.$photo->file_name;
            	}

				$product->parents = array();
				if($_SESSION['option']->useGroups > 0)
				{
					if($_SESSION['option']->ProductMultiGroup == 0 && $product->group > 0)
					{
						$product->parents = $this->makeParents($list, $product->group, $product->parents);
						$link = '/';
						foreach ($product->parents as $parent) {
							$link .= $parent->alias .'/';
						}
						$product->group_link = $_SESSION['alias']->alias . $link;
						$product->link = $_SESSION['alias']->alias . $link . $product->alias;
					}
					elseif($_SESSION['option']->ProductMultiGroup == 1)
					{
						$product->group = array();

						$this->db->select($this->table('_product_group') .' as pg', '', $product->id, 'product');
						$this->db->join($this->table('_groups'), 'id, alias, parent', array('id' => '#pg.group', 'active' => 1));
						$where_ntkd['content'] = "#-pg.group";
            			$this->db->join('wl_ntkd', 'name', $where_ntkd);
						$product->group = $this->db->get('array');

						if($product->group)
				            foreach ($product->group as $g) {
				            	if($g->parent > 0) {
				            		$g->link = $_SESSION['alias']->alias . '/' . $this->makeLink($list, $g->parent, $g->alias);
				            	}
				            	else
				            		$g->link = $_SESSION['alias']->alias . '/' . $g->alias;
				            }
					}
				}
            }

			return $products;
		}
		$this->db->clear();
		return null;
	}

	public function getProduct($alias, $key = 'alias', $all_info = true)
	{
		$this->db->select($this->table('_products').' as p', '*', array('wl_alias' => $_SESSION['alias']->id, $key => $alias));

		if($all_info)
		{
			$this->db->join('wl_users as aa', 'name as author_add_name', '#p.author_add');
			$this->db->join('wl_users as e', 'name as author_edit_name', '#p.author_edit');

			if($_SESSION['option']->useAvailability)
			{
				$this->db->join($_SESSION['service']->table.'_availability', 'color as availability_color', '#p.availability');

				$where_availability_name['availability'] = '#p.availability';
				if($_SESSION['language']) $where_availability_name['language'] = $_SESSION['language'];
				$this->db->join($_SESSION['service']->table.'_availability_name', 'name as availability_name', $where_availability_name);
			}

			if($_SESSION['option']->useGroups > 0 && $_SESSION['option']->ProductMultiGroup == 0)
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

		$product = $this->db->get('single');
        if($product)
        {
        	$product->link = $_SESSION['alias']->alias.'/'.$product->alias;
        	if(isset($_SESSION['alias']->breadcrumbs))
        		$_SESSION['alias']->breadcrumbs = array($_SESSION['alias']->name => $_SESSION['alias']->alias);
        	if($all_info)
        	{
        		$product->options = $this->getProductOptions($product);
        		$product->photo = null;

            	if($photo = $this->getProductPhoto($product->id))
            	{
					if($sizes = $this->db->getAliasImageSizes())
						foreach ($sizes as $resize) {
							$resize_name = $resize->prefix.'_photo';
							$product->$resize_name = $_SESSION['option']->folder.'/'.$product->id.'/'.$resize->prefix.'_'.$photo->file_name;
						}
					$product->photo = $_SESSION['option']->folder.'/'.$product->id.'/'.$photo->file_name;
            	}
        	}

			$product->parents = array();

			if($_SESSION['option']->useGroups > 0)
			{
				$list = array();
				$all_groups = $this->db->getAllDataByFieldInArray($this->table('_groups'), $_SESSION['alias']->id, 'wl_alias');
	            if($all_groups)
	            	foreach ($all_groups as $g) {
		            	$list[$g->id] = clone $g;
		            }

		        $similars = $this->db->getAllDataByFieldInArray($this->table('_products_similar'), array('product' => $product->id));

				if($similars)
				{
					$product->similarProducts = array();
					$parents = array();
					foreach ($similars as $key => $similar) {

						$where_ntkd['alias'] = $_SESSION['alias']->id;
						$where_ntkd['content'] = '#p.id';
						if($_SESSION['language']) $where_ntkd['language'] = $_SESSION['language'];
						$this->db->select('s_shopshowcase_products as p', 'id, alias, article, group', $similar->similar_product);
						$this->db->join('wl_ntkd', 'name as product_name', $where_ntkd);
						$product->similarProducts[$key] = $similarProduct = $this->db->get();

						$parents = $this->makeParents($list, $similarProduct->group, $parents);
						$link = $_SESSION['alias']->alias . '/';
						foreach ($parents as $parent) {
							$link .= $parent->alias .'/';
						}

						if($photo = $this->getProductPhoto($similarProduct->id)){
							$product->similarProducts[$key]->photo = $_SESSION['option']->folder.'/'.$similarProduct->id.'/'.$photo->file_name;
						}

						$product->similarProducts[$key]->link = $link . $similarProduct->alias;

					}
				}

				if($_SESSION['option']->ProductMultiGroup == 0 && $product->group > 0)
				{
					$product->parents = $this->makeParents($list, $product->group, $product->parents);
					$link = $_SESSION['alias']->alias . '/';
					foreach ($product->parents as $parent) {
						$link .= $parent->alias .'/';
						if(isset($_SESSION['alias']->breadcrumbs))
							$_SESSION['alias']->breadcrumbs[$parent->name] = $link;
					}
					$product->group_link = $link;
					$product->link = $link . $product->alias;
				}
				elseif($_SESSION['option']->ProductMultiGroup == 1)
				{
					$product->group = array();

					$this->db->select($this->table('_product_group') .' as pg', '', $product->id, 'product');
					$this->db->join($this->table('_groups'), 'id, alias, parent', array('id' => '#pg.group', 'active' => 1));
					$where_ntkd['content'] = "#-pg.group";
        			$this->db->join('wl_ntkd', 'name', $where_ntkd);
					$product->group = $this->db->get('array');
					if($product->group){
			            foreach ($product->group as $g) {
			            	if($g->parent > 0)
			            		$g->link = $_SESSION['alias']->alias . '/' . $this->makeLink($list, $g->parent, $g->alias);
			            }
			        }
				}
			}
			if($all_info)
        	{
        		$name = ($_SESSION['option']->ProductUseArticle) ? $product->article  .' - ': '';
        		$name .= $product->name;
        		if(isset($_SESSION['alias']->breadcrumbs))
        			$_SESSION['alias']->breadcrumbs[$name] = '';
        	}
            return $product;
		}
		return null;
	}

	public function getProductPhoto($product, $all = false)
	{
		$where['alias'] = $_SESSION['alias']->id;
		$where['content'] = $product;
		$this->db->select('wl_images', '*', $where);
		$this->db->join('wl_users', 'name as user_name', '#author');
		$this->db->order('position ASC');
		if(!$all)
			$this->db->limit(1);
		return $this->db->get();
	}

	private function getProductOptions($product)
	{
		$product_options = array();
		$where_language = '';
        if($_SESSION['language']) $where_language = "AND (po.language = '{$_SESSION['language']}' OR po.language = '')";
		$this->db->executeQuery("SELECT go.id, go.alias, go.filter, po.value, it.name as type_name, it.options FROM `{$this->table('_product_options')}` as po LEFT JOIN `{$this->table('_options')}` as go ON go.id = po.option LEFT JOIN `wl_input_types` as it ON it.id = go.type WHERE go.active = 1 AND po.product = '{$product->id}' {$where_language} ORDER BY go.position");
		if($this->db->numRows() > 0)
		{
			$options = $this->db->getRows('array');
			foreach ($options as $option) {
				if($option->value != '')
				{
					@$product_options[$option->alias]->id = $option->id;
					$product_options[$option->alias]->alias = $option->alias;
					$product_options[$option->alias]->filter = $option->filter;
					$where = array();
					$where['option'] = $option->id;
					if($_SESSION['language']) $where['language'] = $_SESSION['language'];
					$name = $this->db->getAllDataById($this->table('_options_name'), $where);
					$photo = $this->db->getAllDataById($this->table('_options'), array('id' => $option->value));

					if($name)
					{
						$product_options[$option->alias]->name = $name->name;
						$product_options[$option->alias]->sufix = $name->sufix;
					}

					if(isset($photo->photo))
						$product_options[$option->alias]->photo = IMG_PATH.$_SESSION['option']->folder.'/options/'.$option->alias.'/'.$photo->photo;

					if($option->options == 1)
					{
						if($option->type_name == 'checkbox')
						{
							$option->value = explode(',', $option->value);
							$product_options[$option->alias]->value = array();
							foreach ($option->value as $value) {
								$where = array();
								$where['option'] = $value;
								if($_SESSION['language']) $where['language'] = $_SESSION['language'];
								$value = $this->db->getAllDataById($this->table('_options_name'), $where);
								if($value)
									$product_options[$option->alias]->value[] = $value->name;
							}
						}
						else
						{
							$where = array('option' => $option->value);
							if($_SESSION['language']) $where['language'] = $_SESSION['language'];
							$value = $this->db->getAllDataById($this->table('_options_name'), $where);
							if($value)
								$product_options[$option->alias]->value = $value->name;
						}
					}
					else
						$product_options[$option->alias]->value = $option->value;
				}
			}
		}
		return $product_options;
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
		
		if(isset($_SESSION['option']->paginator_per_page) && $_SESSION['option']->paginator_per_page > 0 && $parent >= 0)
		{
			$start = 0;
			if(isset($_GET['page']) && is_numeric($_GET['page']) && $_GET['page'] > 1)
				$start = ($_GET['page'] - 1) * $_SESSION['option']->paginator_per_page;
			$this->db->limit($start, $_SESSION['option']->paginator_per_page);
		}
		
		$categories = $this->db->get('array', false);
		if($categories)
		{
			@$_SESSION['option']->count_all_products = $this->db->get('count');

            $list = array();
            $sizes = $this->db->getAliasImageSizes();
            $groups = $this->db->getAllDataByFieldInArray($this->table('_groups'), $_SESSION['alias']->id, 'wl_alias');
            foreach ($groups as $Group) {
            	$list[$Group->id] = clone $Group;
            }

            foreach ($categories as $Group) {
            	$Group->link = $_SESSION['alias']->alias.'/'.$Group->alias;
            	$Group->photo = false;
            	if($Group->parent > 0)
            		$Group->link = $_SESSION['alias']->alias.'/'.$this->makeLink($list, $Group->parent, $Group->alias);

            	if($photo = $this->getProductPhoto(-$Group->id))
            	{
					if($sizes)
						foreach ($sizes as $resize) {
							$resize_name = $resize->prefix.'_photo';
							$Group->$resize_name = $_SESSION['option']->folder.'/-'.$Group->id.'/'.$resize->prefix.'_'.$photo->file_name;
						}
					$Group->photo = $_SESSION['option']->folder.'/-'.$Group->id.'/'.$photo->file_name;
            	}
            }
            return $categories;
		}
		else
			$this->db->clear();
		return null;
	}

	public function makeParents($all, $parent, $parents)
	{
		$group = clone $all[$parent];
		$where = '';
        if($_SESSION['language']) $where = "AND `language` = '{$_SESSION['language']}'";
        $ntkd = $this->db->getQuery("SELECT `name` FROM `wl_ntkd` WHERE `alias` = '{$_SESSION['alias']->id}' AND `content` = '-{$group->id}' {$where}");
    	if(is_object($ntkd))
    		$group->name = $ntkd->name;

    	array_unshift ($parents, $group);
		if($all[$parent]->parent > 0)
			$parents = $this->makeParents ($all, $all[$parent]->parent, $parents);
		return $parents;
	}

	public function getGroupByAlias($alias, $parent = 0)
	{
		$where['wl_alias'] = $_SESSION['alias']->id;
		$where['alias'] = $alias;
		$where['parent'] = $parent;
		$this->db->select($this->table('_groups') .' as c', '*', $where);
		$this->db->join('wl_users', 'name as user_name', '#c.author_edit');
		$group = $this->db->get('single');
		if($group)
			if($photo = $this->getProductPhoto(-$group->id))
        	{
				if($sizes = $this->db->getAliasImageSizes())
					foreach ($sizes as $resize) {
						$resize_name = $resize->prefix.'_photo';
						$group->$resize_name = $_SESSION['option']->folder.'/-'.$group->id.'/'.$resize->prefix.'_'.$photo->file_name;
					}
				$group->photo = $_SESSION['option']->folder.'/-'.$group->id.'/'.$photo->file_name;
        	}
		return $group;
	}

	public function getOptionsToGroup($group = 0, $filter = true)
	{
		$products = false;
		if($group === 0)
		{
			$where['group'] = 0;
			$group = new stdClass();
			$group->id = 0;
			$group->parent = 0;
		}
		elseif(is_numeric($group))
		{
			$group = $this->db->getAllDataById($this->table('_groups'), $group);
			if($group == false) return false;
		}

		if($_SESSION['option']->useGroups && $group->id > 0)
		{
			if($_SESSION['option']->ProductMultiGroup)
			{
				$products_id = $this->db->getAllDataByFieldInArray($this->table('_product_group'), $group->id, 'group');
				if($products_id)
					foreach ($products_id as $product) {
						$products[] = $product->product;
					}
			}
			else
			{
				$products_id = $this->db->getAllDataByFieldInArray($this->table('_products'), $group->id, 'group');
				if($products_id)
					foreach ($products_id as $product) {
						$products[] = $product->id;
					}
			}
		}

    	if($filter && ($group->id > 0 && $products || $group->id == 0) || !$filter)
    	{
    		$where['group'] = array(0);
			array_push($where['group'], $group->id);
			if($group->parent > 0)
			{
				array_push($where['group'], $group->id);
				while ($group->parent > 0) {
					$group = $this->db->getAllDataById($this->table('_groups'), $group->parent);
				}
			}
			$where['wl_alias'] = $_SESSION['alias']->id;
			$where['filter'] = 1;
			$where['active'] = 1;
			$this->db->select($this->table('_options').' as o', '*', $where);
			$this->db->join('wl_input_types', 'name as type_name', '#o.type');
			$where = array('option' => '#o.id');
	        if($_SESSION['language']) $where['language'] = $_SESSION['language'];
	        $this->db->join($this->table('_options_name'), 'name, sufix', $where);
	        $this->db->order('position');
			$options = $this->db->get('array');

			if($options)
			{
				$to_delete_options = array();
		        foreach ($options as $i => $option) {
		        	unset($where['product'], $where['value']);
		        	$where = array('option' => '#o.id', 'language' => $_SESSION['language']);
		        	$this->db->select($this->table('_options').' as o', 'id', -$option->id, 'group');
		        	$this->db->join($this->table('_options_name'), 'name', $where);
		        	$option->values = $this->db->get('array');

					if(!empty($option->values))
		    		{
		    			$to_delete_values = array();
		    			$where = array();
		    			if($products) $where['product'] = $products;
		    			foreach ($option->values as $i => $value) {
		    				$where['option'] = $option->id;
		    				if($option->type_name == 'checkbox')
		    				{
		    					$count = 0;
								$where['value'] = '%'.$value->id;
			        			$list = $this->db->getAllDataByFieldInArray($this->table('_product_options'), $where);
			        			if($list)
			        				foreach ($list as $key) {
			        					$key->value = explode(',', $key->value);
			        					if(in_array($value->id, $key->value)) $count++;
			        				}
		    				}
		    				else
		    				{
		    					$where['value'] = $value->id;
		        				$count = $this->db->getCount($this->table('_product_options'), $where);
		    				}

		        			$value->count = $count;
		        			if(!$count && $filter)
		        				$to_delete_values[] = $i;
		        		}
		        		if(!empty($to_delete_values) && $filter)
		        		{
		        			rsort($to_delete_values);
		        			foreach ($to_delete_values as $i) {
		        				unset($option->values[$i]);
		        			}
		        		}
		    		}
		    		elseif($filter)
		    			$to_delete_options[] = $i;
		        }
		        if(!empty($to_delete_options))
        		{
        			rsort($to_delete_options);
        			foreach ($to_delete_options as $i) {
        				unset($options[$i]);
        			}
        		}
			}

			return $options;
		}
		return false;
	}

	private function makeLink($all, $parent, $link)
	{
		$link = $all[$parent]->alias .'/'.$link;
		if($all[$parent]->parent > 0) $link = $this->makeLink ($all, $all[$parent]->parent, $link);
		return $link;
	}

	public function searchHistory($product_id, $product_article = NULL)
	{
		$data['user'] = $_SESSION['user']->id;
		$data['date'] = strtotime('today');

		if($product_id > 0)
			$data['product_id'] = $product_id;
		else
			$data['product_article'] = $product_article;

		$search = $this->db->getAllDataById($this->table('_search_history'), $data);
		if($search)
		{
			$this->db->updateRow($this->table('_search_history'), array('count_per_day' => $search->count_per_day + 1, 'last_view' => time()), $search->id);
			return true;
		}

		$data['product_id'] = $product_id;
		$data['product_article'] = $product_article;
		$data['last_view'] = time();
		$data['count_per_day'] = 1;
		$this->db->insertRow($this->table('_search_history'), $data);
		return true;
	}

}

?>
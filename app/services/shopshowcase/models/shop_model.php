<?php

class shop_model {

	public function table($sufix = '')
	{
		return $_SESSION['service']->table.$sufix.$_SESSION['alias']->table;
	}
	
	public function getProducts($Group = 0, $noInclude = 0, $active = true){
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
					$products = $this->db->getAllDataByFieldInArray($this->table('_product_group'), $g->id, 'group');
					if($products) {
						foreach ($products as $product) if($product->product != $noInclude) {
							array_push($where['id'], $product->product);
						}
					}
				}
			}
			elseif($Group > 0)
			{
				if($_SESSION['option']->ProductMultiGroup == 0) {
					$where['group'] = $Group;
				} else {
					$products = $this->db->getAllDataByFieldInArray($this->table('_product_group'), $Group, 'group');
					if($products) {
						$where['id'] = array();
						foreach ($products as $product) if($product->product != $noInclude) {
							array_push($where['id'], $product->product);
						}
					} else {
						return null;
					}
				}
			}
			elseif($noInclude > 0)
			{
				$where['id'] = '!'.$noInclude;
			}
		}
		elseif($noInclude > 0)
		{
			$where['id'] = '!'.$noInclude;
		}
		
		if(count($_GET) > 1){
			foreach ($_GET as $key => $value) {
				if($key != 'request' && $key != 'page' && $value != 'all'){
					$option = $this->db->getAllDataById($this->table('_group_options'), $key, 'link');
					if($option && $option->filter == 1){
						$type = $this->db->getAllDataById('wl_input_types', $option->type);
						$where_language = '';
        				if($_SESSION['language']) $where_language = "AND n.language = '{$_SESSION['language']}'";
						$this->db->executeQuery("SELECT v.id, n.name FROM {$this->table('_group_options')} as v LEFT JOIN {$this->table('_options_name')} as n ON n.option = v.id {$where_language} WHERE v.active = 1 AND v.group = -{$option->id}");
						if($this->db->numRows() > 0){
            				$values = $this->db->getRows('array');
            				foreach ($values as $val) {
            					if($val->name == $value){
            						if($type->name == 'checkbox'){
            							$products = array();
            							$list = $this->db->getAllDataByFieldInArray($this->table('_product_options'), array('option' => $option->id, 'value' => '%'.$val->id));
            							if($list){
            								foreach ($list as $p) {
            									$p->value = explode(',', $p->value);
            									if(in_array($val->id, $p->value)) $products[] = $p;
            								}
            							}
            						} else {
            							$products = $this->db->getAllDataByFieldInArray($this->table('_product_options'), array('option' => $option->id, 'value' => $val->id));
            						}

            						if(!empty($products))
            						{
            							if(!isset($where['id']))
            							{
            								$where['id'] = array();
            								foreach ($products as $p) {
												array_push($where['id'], $p->product);
											}
            							}
										else
										{
											$ids = clone $where['id'];
											$where['id'] = array();
											foreach ($products as $p) {
												if(in_array($p->products, $ids)) {
													array_push($where['id'], $p->product);
												}
											}
										}
        							} else {
        								return false;
        							}
            						break;
            					}
            				}
            			}
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
		
		$this->db->select($this->table('_products').' as p', '*', $where);
		
		$this->db->join('wl_users', 'name as user_name', '#p.author_edit');

		$this->db->join($_SESSION['service']->table.'_availability', 'color as availability_color', '#p.availability');
		
		$where_availability_name['availability'] = '#p.availability';
		if($_SESSION['language']) $where_availability_name['language'] = $_SESSION['language'];
		$this->db->join($_SESSION['service']->table.'_availability_name', 'name as availability_name', $where_availability_name);

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
		$this->db->join('wl_ntkd as n', 'name, text, list', $where_ntkd);

		$this->db->order('position');

		if($active && isset($_SESSION['option']->paginator_per_page) && $_SESSION['option']->paginator_per_page > 0)
		{
			$start = 0;
			if(isset($_GET['page']) && is_numeric($_GET['page']) && $_GET['page'] > 1) {
				$start = ($_GET['page'] - 1) * $_SESSION['option']->paginator_per_page;
			}
			$this->db->limit($start, $_SESSION['option']->paginator_per_page);
		}

		$products = $this->db->get('array');
        if($products)
        {
			$_SESSION['option']->count_all_products = count($products);
			@$_SESSION['products']->options = array();

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

            foreach ($products as $product)
            {
            	$product->link = $_SESSION['alias']->alias.'/'.$product->alias;
            	$product->photos = $this->getProductPhotos($product->id);
            	$product->options = $this->getProductOptions($product);
            	if($product->photo != '')
            	{
					if($sizes){
						foreach ($sizes as $resize) if($resize->active == 1){
							$resize_name = $resize->prefix.'_photo';
							$product->$resize_name = $_SESSION['option']->folder.'/'.$product->id.'/'.$resize->prefix.'_'.$product->photo;
						}
					}
					$product->photo = $_SESSION['option']->folder.'/'.$product->id.'/'.$product->photo;
            	}

				if(!empty($product->options)){
					foreach ($product->options as $option){
						if($option->filter == 1){
							if(isset($_SESSION['products']->options[$option->id])){
								if(!in_array($option->value, $_SESSION['products']->options[$option->id]->values)){
									$_SESSION['products']->options[$option->id]->values[] = $option->value;
								}
							} else {
								$_SESSION['products']->options[$option->id] = new stdClass();
								$_SESSION['products']->options[$option->id]->id = $option->id;
								$_SESSION['products']->options[$option->id]->name = $option->name;
								$_SESSION['products']->options[$option->id]->link = $option->link;
								$_SESSION['products']->options[$option->id]->values = array($option->value);
							}
						}
					}
				}

				$product->parents = array();
				if($_SESSION['option']->useGroups > 0)
				{
					if($_SESSION['option']->ProductMultiGroup == 0 && $product->group > 0){
						$product->parents = $this->makeParents($list, $product->group, $product->parents);
						$link = '/';
						foreach ($product->parents as $parent) {
							$link .= $parent->alias .'/';
						}
						$product->link = $_SESSION['alias']->alias . $link . $product->alias;
					} elseif($_SESSION['option']->ProductMultiGroup == 1){
						$product->group = array();

						$this->db->select($this->table('_product_group') .' as pg', '', $product->id, 'product');
						$this->db->join($this->table('_groups'), 'id, alias, parent', '#pg.group');
						$where_ntkd['content'] = "#-pg.group";
            			$this->db->join('wl_ntkd', 'name', $where_ntkd);
						$product->group = $this->db->get('array');

			            foreach ($product->group as $g) {
			            	if($g->parent > 0) {
			            		$g->link = $_SESSION['alias']->alias . '/' . $this->makeLink($list, $g->parent, $g->alias);
			            	}
			            }
					}
				}
            }

			return $products;
		}
		return null;
	}
	
	function getProductById($id)
	{
		$this->db->select($this->table('_products').' as p', '*', $id);

		$this->db->join('wl_users', 'name as user_name', '#p.author_edit');

		$this->db->join($_SESSION['service']->table.'_availability', 'color as availability_color', '#p.availability');
		
		$where_availability_name['availability'] = '#p.availability';
		if($_SESSION['language']) $where_availability_name['language'] = $_SESSION['language'];
		$this->db->join($_SESSION['service']->table.'_availability_name', 'name as availability_name', $where_availability_name);

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
		$this->db->join('wl_ntkd as n', 'name, text, list', $where_ntkd);

		$product = $this->db->get('single');
        if($product)
        {
        	if($product->photo != '')
        	{
        		if($_SESSION['option']->resize > 0)
        		{
					$sizes = $this->db->getAllDataByFieldInArray('wl_images_sizes', $_SESSION['alias']->id, 'alias');
					if($sizes){
						foreach ($sizes as $resize) if($resize->active == 1){
							$resize_name = $resize->prefix.'_photo';
							$product->$resize_name = $_SESSION['option']->folder.'/'.$product->id.'/'.$resize->prefix.'_'.$product->photo;
						}
					}
				}
				$product->photo = $_SESSION['option']->folder.'/'.$product->id.'/'.$product->photo;
        	}
			$product->photos = $this->getProductPhotos($product->id);
        	$product->options = $this->getProductOptions($product);
        	$product->link = $_SESSION['alias']->alias.'/'.$product->alias;

			$product->parents = array();
			if($_SESSION['option']->useGroups > 0)
			{
				$list = array();
				$all_groups = $this->db->getAllData($this->table('_groups'));
	            if($all_groups) foreach ($all_groups as $g) {
	            	$list[$g->id] = clone $g;
	            }

				if($_SESSION['option']->ProductMultiGroup == 0 && $product->group > 0){
					$product->parents = $this->makeParents($list, $product->group, $product->parents);
					$link = '/';
					foreach ($product->parents as $parent) {
						$link .= $parent->alias .'/';
					}
					$product->link = $_SESSION['alias']->alias . $link . $product->alias;
				} elseif($_SESSION['option']->ProductMultiGroup == 1){
					$product->group = array();

					$this->db->select($this->table('_product_group') .' as pg', '', $product->id, 'product');
					$this->db->join($this->table('_groups'), 'id, alias, parent', '#pg.group');
					$where_ntkd['content'] = "#-pg.group";
        			$this->db->join('wl_ntkd', 'name', $where_ntkd);
					$product->group = $this->db->get('array');

		            foreach ($product->group as $g) {
		            	if($g->parent > 0) {
		            		$g->link = $_SESSION['alias']->alias . '/' . $this->makeLink($list, $g->parent, $g->alias);
		            	}
		            }
				}
			}
            return $product;
		}
		return null;
	}

	public function getProductPhotos($product)
	{
		$this->db->executeQuery("SELECT p.*, u.name as user_name FROM {$this->table('_product_photos')} as p LEFT JOIN wl_users as u ON p.user = u.id WHERE p.product = {$product} ORDER BY p.main DESC");
		if($this->db->numRows() > 0){
			return $this->db->getRows('array');
		}
		return false;
	}

	private function getProductOptions($product){
		$product_options = array();
		$where_language = '';
        if($_SESSION['language']) $where_language = "AND (po.language = '{$_SESSION['language']}' OR po.language = '')";
		$this->db->executeQuery("SELECT go.id, go.alias, go.filter, po.value, it.name as type_name, it.options FROM `{$this->table('_product_options')}` as po LEFT JOIN `{$this->table('_group_options')}` as go ON go.id = po.option LEFT JOIN `wl_input_types` as it ON it.id = go.type WHERE go.active = 1 AND po.product = '{$product->id}' {$where_language} ORDER BY go.position");
		if($this->db->numRows() > 0){
			$options = $this->db->getRows('array');
			foreach ($options as $option) if($option->value != '') {
				@$product_options[$option->alias]->id = $option->id;
				$product_options[$option->alias]->alias = $option->alias;
				$product_options[$option->alias]->filter = $option->filter;
				$where = array();
				$where['option'] = $option->id;
				if($_SESSION['language']) $where['language'] = $_SESSION['language'];
				$name = $this->db->getAllDataById($this->table('_options_name'), $where);

				if($name){
					$product_options[$option->alias]->name = $name->name;
					$product_options[$option->alias]->sufix = $name->sufix;
				}
				if($option->options == 1){
					if($option->type_name == 'checkbox'){
						$option->value = explode(',', $option->value);
						$product_options[$option->alias]->value = array();
						foreach ($option->value as $value) {
							$where = array();
							$where['option'] = $value;
							if($_SESSION['language']) $where['language'] = $_SESSION['language'];
							$value = $this->db->getAllDataById($this->table('_options_name'), $where);
							if($value){
								$product_options[$option->alias]->value[] = $value->name;
							}
						}
					} else {
						$where = array();
						$where['option'] = $option->value;
						if($_SESSION['language']) $where['language'] = $_SESSION['language'];
						$value = $this->db->getAllDataById($this->table('_options_name'), $where);
						if($value){
							$product_options[$option->alias]->value = $value->name;
						}
					}
				} else {
					$product_options[$option->alias]->value = $option->value;
				}
			}
		}
		return $product_options;
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
			@$_SESSION['option']->count_all_products = $this->db->get('count');

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
		$group = $this->db->get('single');
		if($group->photo > 0)
		{
	        if($_SESSION['option']->resize > 0)
	        {
				$sizes = $this->db->getAllDataByFieldInArray('wl_images_sizes', $_SESSION['alias']->id, 'alias');
				if($sizes) {
					foreach ($sizes as $resize) if($resize->active == 1){
						$resize_name = $resize->prefix.'_photo';
						$group->$resize_name = $_SESSION['option']->folder.'/groups/'.$resize->prefix.'_'.$group->photo.'.jpg';
					}
				}
			}
			$group->photo = $_SESSION['option']->folder.'/groups/'.$group->photo.'.jpg';
		}
		return $group;
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

	public function getOptions($group = 0, $active = true)
	{
		$where = ''; $where_gn = ''; $select_gn = '';
		if($active) $where = 'AND o.active = 1';
		else {
			$select_gn = ', g.name as group_name';
			$where_gn = "LEFT JOIN `wl_ntkd` as g ON g.content = '-{$group}' AND g.alias = '{$_SESSION['alias']->id}'";
			if($_SESSION['language']) $where_gn .= " AND g.language = '{$_SESSION['language']}'";
		}
		$this->db->executeQuery("SELECT o.*, t.name as type_name {$select_gn} FROM `{$this->table('_group_options')}` as o LEFT JOIN wl_input_types as t ON t.id = o.type {$where_gn} WHERE o.group = '{$group}' {$where} ORDER BY o.position ASC");
        if($this->db->numRows() > 0){
            $options = $this->db->getRows('array');

			$where = '';
            if($_SESSION['language']) $where = "AND `language` = '{$_SESSION['language']}'";
            foreach ($options as $option) {
            	$this->db->executeQuery("SELECT * FROM `{$this->table('_options_name')}` WHERE `option` = '{$option->id}' {$where}");
            	if($this->db->numRows() == 1){
            		$ns = $this->db->getRows();
            		$option->name = $ns->name;
            		$option->sufix = $ns->sufix;
            	}
            }

			return $options;
		}
		return null;
	}

	private function makeLink($all, $parent, $link)
	{
		$link = $all[$parent]->alias .'/'.$link;
		if($all[$parent]->parent > 0) $link = $this->makeLink ($all, $all[$parent]->parent, $link);
		return $link;
	}
	
}

?>
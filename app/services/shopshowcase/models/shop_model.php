<?php

class shop_model {

	public $allGroups = false;
	private $productsIdInGroup = false;

    public function init()
    {
		if($_SESSION['option']->useGroups && empty($this->allGroups))
		{
			$where = array();
			$where['wl_alias'] = $_SESSION['alias']->id;
			$this->db->select($this->table('_groups') .' as g', '*', $where);

			$where_ntkd['alias'] = $_SESSION['alias']->id;
			$where_ntkd['content'] = "#-g.id";
			if($_SESSION['language']) $where_ntkd['language'] = $_SESSION['language'];
			$this->db->join('wl_ntkd', "name", $where_ntkd);
			$this->db->order($_SESSION['option']->groupOrder);
			if($list = $this->db->get('array'))
			{
				foreach ($list as $g) {
	            	$this->allGroups[$g->id] = clone $g;
	            }
	            unset($list);
	        }
		}
    }

	public function table($sufix = '', $useAliasTable = false)
	{
		if($useAliasTable)
			return $_SESSION['service']->table.$sufix.$_SESSION['alias']->table;
		return $_SESSION['service']->table.$sufix;
	}

	public function routeURL($url = array(), &$type = null, $admin = false)
	{
		$this->init();
		$_SESSION['alias']->breadcrumbs = array();
		if($_SESSION['alias']->content < 0)
			if($group = $this->getGroupByAlias(-$_SESSION['alias']->content, 0, 'id'))
			{
				$type = 'group';
				return $group;
			}

		$key = end($url);
		$keyName = 'alias';
		if($_SESSION['alias']->content > 0)
		{
			$key = $_SESSION['alias']->content;
			$keyName = 'id';
		}
		if($product = $this->getProduct($key, $keyName))
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

		if($_SESSION['option']->useGroups && !empty($this->allGroups))
		{
			$gId = false;
			$parent = 0;
			array_shift($url);
			foreach ($url as $uri) {
				$gId = false;
				foreach ($this->allGroups as $g) {
					if($g->alias == $uri && $g->parent == $parent)
					{
						$parent = $gId = $g->id;
						break;
					}
				}
			}
			if($gId)
				if($group = $this->getGroupByAlias($gId, 0, 'id'))
				{
					$type = 'group';
					return $group;
				}
		}

		return false;
	}

	public function getProducts($Group = -1, $noInclude = 0, $active = true, $getProductOptions = false)
	{
		$_SESSION['option']->paginator_total_active = 0;
		$where = array('wl_alias' => $_SESSION['alias']->id);
		if($active)
		{
			if($_SESSION['option']->useGroups == 1)
			{
				if($_SESSION['option']->ProductMultiGroup == 0)
					$where['active'] = 1;
			}
			else
				$where['active'] = 1;
		}

		if($_SESSION['option']->ProductUseArticle > 0 && is_string($Group) && $Group[0] == '%')
			$where['article'] = $Group;
		elseif($_SESSION['option']->useGroups > 0)
		{
			if(is_array($Group) && isset($Group[key($Group)]->id))
			{
				$list = array();
				foreach ($Group as $g) {
					$list[] = $g->id;
				}
				$Group = $list;
				unset($list);
			}
			if(!$active && $Group >= 0 && !$this->data->get('name'))
			{
				if($_SESSION['option']->ProductMultiGroup)
					$where['#pg.group'] = $Group;
				else
					$where['group'] = $Group;
			}
			else
			{
				if(is_array($Group) && in_array(0, $Group) || is_numeric($Group) && $Group <= 0)
				{
					if($_SESSION['option']->ProductMultiGroup)
					{
						$order = explode(' ', trim($_SESSION['option']->productOrder));
						if($order[0] == 'position')
							$order = 'ORDER BY '.trim($_SESSION['option']->productOrder);
						else
							$order = '';

						$getOk = true;
						if(count($_GET) > 1)
						{
							$getOk = false;
							if(count($_GET) == 2 && isset($_GET['request']) && isset($_GET['page']))
								$getOk = true;
						}
						if($getOk && isset($_SESSION['option']->paginator_per_page) && $_SESSION['option']->paginator_per_page > 0)
						{
							$start = 0;
							if(isset($_GET['per_page']) && is_numeric($_GET['per_page']) && $_GET['per_page'] > 0)
								$_SESSION['option']->paginator_per_page = $_GET['per_page'];
							if(isset($_GET['page']) && is_numeric($_GET['page']) && $_GET['page'] > 1)
								$start = ($_GET['page'] - 1) * $_SESSION['option']->paginator_per_page;
							$order .= ' LIMIT '.$start.', '.$_SESSION['option']->paginator_per_page;
						}
						$pgNoInclude = ($active) ? '`active` = 1' : '';
						$pgNoInclude .= ($noInclude > 0) ? ' AND `product` != '.$noInclude : '';
						if($pgNoInclude != '')
							$pgNoInclude = 'WHERE '.$pgNoInclude;
						if($products = $this->db->getQuery("SELECT `product` FROM `{$this->table('_product_group')}` {$pgNoInclude} GROUP BY `product` ".$order, 'array'))
						{
							$where['id'] = array();
							foreach ($products as $product) {
								array_push($where['id'], $product->product);
							}
						}
						else
							return null;
					}
				}
				else
				{
					$endGroups = $this->getEndGroups($Group);
					if(!empty($endGroups))
					{
						if($_SESSION['option']->ProductMultiGroup == 0)
							$where['group'] = $endGroups;
						else
						{
							$wherePG = array('active' => 1);
							if($noInclude > 0)
								$wherePG['product'] = '!'.$noInclude;
							$wherePG['group'] = $endGroups;
							$this->db->select($this->table('_product_group').' as pg', 'product', $wherePG);
							$this->db->group('product');

							$order = explode(' ', trim($_SESSION['option']->productOrder));
							if($order[0] == 'position')
								$this->db->order(trim($_SESSION['option']->productOrder));

							$start = -1;
							$getOk = true;
							if(count($_GET) > 1)
							{
								$getOk = false;
								if(count($_GET) == 2 && isset($_GET['request']) && isset($_GET['page']))
									$getOk = true;
							}
							if($getOk && isset($_SESSION['option']->paginator_per_page) && $_SESSION['option']->paginator_per_page > 0)
							{
								$start = 0;
								if(isset($_GET['per_page']) && is_numeric($_GET['per_page']) && $_GET['per_page'] > 0)
									$_SESSION['option']->paginator_per_page = $_GET['per_page'];
								if(isset($_GET['page']) && is_numeric($_GET['page']) && $_GET['page'] > 1)
									$start = ($_GET['page'] - 1) * $_SESSION['option']->paginator_per_page;
								$this->db->limit($start, $_SESSION['option']->paginator_per_page);
								$_SESSION['option']->paginator_per_page = 0;
							}

							if($products = $this->db->get('array', false))
							{
								if($start >= 0)
									$_SESSION['option']->paginator_total = $this->db->get('count');
								else
									$_SESSION['option']->paginator_total = count($products);
								$this->db->clear();
								$where['id'] = array();
								if($_SESSION['option']->paginator_total <= count($products))
									$this->productsIdInGroup = array();
								foreach ($products as $product) {
									array_push($where['id'], $product->product);
									if($this->productsIdInGroup)
										$this->productsIdInGroup[] = $product->product;
								}
							}
							else
								return null;
						}
					}
					else
						return false;
				}
			}
		}
		elseif($noInclude > 0)
			$where['id'] = '!'.$noInclude;

		if(count($_GET) > 1)
		{
			foreach ($_GET as $key => $value) {
				if($key != 'request' && $key != 'page' && $key != 'sale' && is_array($_GET[$key]))
				{
					$option = $this->db->getAllDataById($this->table('_options'), array('wl_alias' => $_SESSION['alias']->id, 'alias' => $key, 'filter' => 1));
					if($option)
					{
						$list_where['option'] = $option->id;
						if(!empty($where['id']))
							$list_where['product'] = $where['id'];
						$where['id'] = array();
						foreach ($_GET[$key] as $value) if(is_numeric($value)) {
							if($option->type == 8 || $option->type == 12) //checkbox || checkbox-select2
							{
								$list_where['value'] = '%'.$value;
								if($list = $this->db->getAllDataByFieldInArray($this->table('_product_options'), $list_where))
									foreach ($list as $p) {
										$p->value = explode(',', $p->value);
										if(in_array($value, $p->value)) array_push($where['id'], $p->product);
									}
							}
							else
							{
								$list_where['value'] = $value;
								if($list = $this->db->getAllDataByFieldInArray($this->table('_product_options'), $list_where))
									foreach ($list as $p) {
										array_push($where['id'], $p->product);
									}
							}
						}
						if(empty($where['id']))
							return false;
					}
				}
			}
			if(isset($_GET['name']) && $_GET['name'] != '')
			{
				$content = '>0';
				if(!empty($where['id']))
					$content = $where['id'];
				$products = $this->db->getAllDataByFieldInArray('wl_ntkd', array('alias' => $_SESSION['alias']->id, 'content' => $content, 'name' => '%'.$this->data->get('name')));
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
						$ids = $where['id'];
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
		if(isset($_GET['sale']) && $_GET['sale'] == 1)
		{
			$where['#p.old_price'] = '>0';
			$where['+#p.old_price'] = '> p.price';
		}
		if(isset($_GET['price_min']) && is_numeric($_GET['price_min']) && $_GET['price_min'] > 1)
		{
			$price_min = $this->data->get('price_min');
			if($_SESSION['option']->currency)
	        	$price_min /= $_SESSION['option']->currency;
			$where['#p.price'] = '>='.$price_min;
		}
		if(isset($_GET['price_max']) && is_numeric($_GET['price_max']) && $_GET['price_max'] > 1)
		{
			$price_max = $this->data->get('price_max');
			if($_SESSION['option']->currency)
	        	$price_max /= $_SESSION['option']->currency;
			$where['+#p.price'] = '<='.$price_max;
		}

		if($active && $_SESSION['option']->useGroups > 0 && $_SESSION['option']->ProductMultiGroup == 0 && isset($where['group']))
			$where['#g.active'] = 1;

		$this->db->select($this->table('_products').' as p', '*', $where);

		if(isset($where['#pg.group']))
			$this->db->join($this->table('_product_group').' as pg', 'id as position_id, position, active', array('group' => $Group, 'product' => '#p.id'));

		if(!$active)
			$this->db->join('wl_users', 'name as user_name', '#p.author_edit');

		if($_SESSION['option']->useAvailability > 0)
		{
			$this->db->join($_SESSION['service']->table.'_availability', 'color as availability_color', '#p.availability');

			$where_availability_name['availability'] = '#p.availability';
			if($_SESSION['language']) $where_availability_name['language'] = $_SESSION['language'];
			$this->db->join($_SESSION['service']->table.'_availability_name', 'name as availability_name', $where_availability_name);
		}

		if($_SESSION['option']->useMarkUp > 0)
			$this->db->join($this->table('_markup'), 'value as markup', array('from' => '<p.price', 'to' => '>=p.price'));

		if($_SESSION['option']->useGroups > 0 && $_SESSION['option']->ProductMultiGroup == 0)
		{
			$where_gn['alias'] = $_SESSION['alias']->id;
			$where_gn['content'] = "#-p.group";
			if($_SESSION['language']) $where_gn['language'] = $_SESSION['language'];
			$this->db->join('wl_ntkd as gn', 'name as group_name', $where_gn);
			$this->db->join($this->table('_groups').' as g', 'active as group_active', '#p.group');
		}

		$where_ntkd['alias'] = $_SESSION['alias']->id;
		$where_ntkd['content'] = "#p.id";
		if($_SESSION['language']) $where_ntkd['language'] = $_SESSION['language'];
		$this->db->join('wl_ntkd as n', 'name, text, list', $where_ntkd);

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
				case 'active_on':
					if($_SESSION['option']->useGroups && $_SESSION['option']->ProductMultiGroup)
						$this->db->order('active DESC', 'pg');
					else
						$this->db->order('active DESC');
					break;
				case 'active_off':
					if($_SESSION['option']->useGroups && $_SESSION['option']->ProductMultiGroup)
						$this->db->order('active ASC', 'pg');
					else
						$this->db->order('active ASC');
					break;
				default:
					$this->db->order($_SESSION['option']->productOrder);
					break;
			}
		}
		else
			$this->db->order($_SESSION['option']->productOrder);


		if(isset($_SESSION['option']->paginator_per_page) && $_SESSION['option']->paginator_per_page > 0)
		{
			$start = 0;
			if(isset($_GET['per_page']) && is_numeric($_GET['per_page']) && $_GET['per_page'] > 0)
				$_SESSION['option']->paginator_per_page = $_GET['per_page'];
			if(isset($_GET['page']) && is_numeric($_GET['page']) && $_GET['page'] > 1)
				$start = ($_GET['page'] - 1) * $_SESSION['option']->paginator_per_page;
			$this->db->limit($start, $_SESSION['option']->paginator_per_page);
		}

		if($products = $this->db->get('array', false))
        {
        	if(empty($_SESSION['option']->paginator_total) || count($_GET) > 1)
        	{
        		if(count($products) < $_SESSION['option']->paginator_per_page && empty($_GET['page']))
					$_SESSION['option']->paginator_total = count($products);
				else
				{
					$_SESSION['option']->paginator_total = $this->db->get('count');

					if(!$active)
					{
						$wherePG = array('active' => 1, 'group' => $Group);
						if($_SESSION['option']->useGroups && $_SESSION['option']->ProductMultiGroup)
							$_SESSION['option']->paginator_total_active = $this->db->getCount($this->table('_product_group'), $wherePG);
						else
							$_SESSION['option']->paginator_total_active = $this->db->getCount($this->table('_products'), $wherePG);
					}
				}
        	}
			$this->db->clear();

        	if($_SESSION['option']->useGroups && empty($this->allGroups))
        		$this->init();

			$sizes = $this->db->getAliasImageSizes();

			$products_ids = $products_photos = $main_options = $main_options_Alias = $product_group = array();
            foreach ($products as $product)
            	$products_ids[] = $product->id;
            if($photos = $this->getProductPhoto($products_ids))
            {
	            foreach ($photos as $photo) {
	            	$products_photos[$photo->content] = clone $photo;
	            }
	            unset($photos);
	        }
	        if(!$getProductOptions)
	        {
	        	if($mainOptions = $this->db->getAllDataByFieldInArray($this->table('_options'), array('wl_alias' => $_SESSION['alias']->id, 'main' => 1)))
	        	{
	        		$ids = array();
	        		foreach ($mainOptions as $o) {
	        			$ids[] = $o->id;
	        			$o->alias = explode('-', $o->alias);
	        			if($o->alias[0] == $o->id)
	        				array_shift($o->alias);
	        			$main_options_Alias[$o->id] = implode('_', $o->alias);
	        		}
	        		$where = array('option' => '#po.value');
	        		if($_SESSION['language'])
	        			$where['language'] = $_SESSION['language'];
			        $options = $this->db->select($this->table('_product_options'). ' as po', 'option, product, value', array('product' => $products_ids, 'option' => $ids))
		            						->join($this->table('_options_name'), 'name', $where)->get('array');
		            if($options)
		            	foreach ($options as $opt) {
		            		if(!empty($opt->name))
			            		$main_options[$opt->product][$opt->option] = $opt->name;
			            	else
			            		$main_options[$opt->product][$opt->option] = $opt->value;
			            }
		            unset($options);
		        }
	        }

	        $link = $_SESSION['alias']->alias.'/';
	        $parents = NULL;
			if($_SESSION['option']->useGroups > 0)
			{
				if($_SESSION['option']->ProductMultiGroup == 0 && $products[0]->group > 0)
				{
					$parents = $this->makeParents($products[0]->group, array());
					foreach ($parents as $parent) {
						$link .= $parent->alias .'/';
					}
				}
				else if($_SESSION['option']->ProductMultiGroup == 1)
				{
					
					if($active)
					{
						$this->db->select($this->table('_product_group') .' as pg', 'product, active', array('product' => $products_ids, 'active' => 1));
						$this->db->join($this->table('_groups'), 'id, alias, parent', array('id' => '#pg.group', 'active' => 1));
					}
					else
					{
						$this->db->select($this->table('_product_group') .' as pg', 'product, active', array('product' => $products_ids));
						$this->db->join($this->table('_groups'), 'id, alias, parent', '#pg.group');
					}
					$where_ntkd['content'] = "#-pg.group";
        			$this->db->join('wl_ntkd', 'name', $where_ntkd);

					if($list = $this->db->get('array'))
			            foreach ($list as $row) {
			            	if(!isset($product_group[$row->product]))
			            		$product_group[$row->product] = array();
			            	$product_group[$row->product][] = $row;
			            }
				}
			}


            foreach ($products as $product)
            {
            	if($_SESSION['option']->paginator_total <= $_SESSION['option']->paginator_per_page)
				{
					if($product->active)
						$_SESSION['option']->paginator_total_active++;
				}

				if($_SESSION['option']->ProductUseArticle && mb_strlen($product->name) > mb_strlen($product->article))
				{
					$name = explode(' ', $product->name);
					if(array_pop($name) == $product->article)
						$product->name = implode(' ', $name);
				}
            	$product->link = $link.$product->alias;
            	$product->parents = $parents;
            	if($getProductOptions)
            		$product->options = $this->getProductOptions($product);
            	elseif(isset($main_options[$product->id]))
            		foreach ($main_options[$product->id] as $opt_id => $value) {
            			$key = $main_options_Alias[$opt_id];
            			$product->$key = $value;
            		}
            	if($_SESSION['option']->useGroups > 0 && $_SESSION['option']->ProductMultiGroup == 0 && $products[0]->group > 0)
            	{
            		$product->group_link = $link;
            		if(substr($product->group_link, -1) == '/')
            			$product->group_link = substr($product->group_link, 0, -1);
            	}

            	$product->photo = null;
            	if(isset($products_photos[$product->id]))
            	{
            		$photo = $products_photos[$product->id];
					if($sizes)
						foreach ($sizes as $resize) {
							$resize_name = $resize->prefix.'_photo';
							$product->$resize_name = $_SESSION['option']->folder.'/'.$product->id.'/'.$resize->prefix.'_'.$photo->file_name;
						}
					$product->photo = $_SESSION['option']->folder.'/'.$product->id.'/'.$photo->file_name;
            	}

            	if(@$this->data->url()[0] != 'admin' || (@$this->data->url()[0] == 'admin' && @$this->data->url()[1] == 'cart'))
	        	{
	        		if($_SESSION['option']->useMarkUp > 0 && $product->markup){
		        		$product->price *= $product->markup;
		        		$product->old_price *= $product->markup;
		        	}

		        	// $product->old_price = $product->price != $product->old_price ? ceil($product->old_price) : 0;
		        	// $product->price = ceil($product->price);

		        	if(!empty($_SESSION['option']->currency))
		        	{
			        	$product->price *= $_SESSION['option']->currency;
			        	$product->old_price *= $_SESSION['option']->currency;
			        }

			        $product->price = $this->formatPrice($product->price);
			        $product->old_price = $this->formatPrice($product->old_price);
	        	}
				
				if($_SESSION['option']->useGroups > 0 && $_SESSION['option']->ProductMultiGroup == 1)
				{
					$product->group = array();
					if(!empty($product_group[$product->id]))
			            foreach ($product_group[$product->id] as $g) {
			            	if($g->parent > 0)
			            		$g->link = $_SESSION['alias']->alias . '/' . $this->makeLink($g->parent, $g->alias);
			            	else
			            		$g->link = $_SESSION['alias']->alias . '/' . $g->alias;
			            	$product->group[] = $g;
			            }
				}
            }

			return $products;
		}
		$this->db->clear();
		return false;
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

			if($_SESSION['option']->useMarkUp > 0)
				$this->db->join($this->table('_markup'), 'value as markup', array('from' => '<p.price', 'to' => '>=p.price'));

			$where_ntkd['alias'] = $_SESSION['alias']->id;
			$where_ntkd['content'] = "#p.id";
			if($_SESSION['language']) $where_ntkd['language'] = $_SESSION['language'];
			$this->db->join('wl_ntkd as n', 'name', $where_ntkd);
		}

        if($product = $this->db->get('single'))
        {
        	$product->link = $_SESSION['alias']->alias.'/'.$product->alias;
        	if(isset($_SESSION['alias']->breadcrumbs) && empty($_SESSION['alias']->breadcrumbs))
        	{
        		$where = array();
				$where['alias'] = $_SESSION['alias']->id;
				if($_SESSION['language'])
					$where['language'] = $_SESSION['language'];
        		$where['content'] = 0;
				if($data = $this->db->getAllDataById('wl_ntkd', $where))
				{
					$_SESSION['alias']->breadcrumb_name = $data->name;
					$_SESSION['alias']->breadcrumbs = array($data->name => $_SESSION['alias']->alias);
				}
        	}

        	if(@$this->data->url()[0] != 'admin')
        	{
        		if($_SESSION['option']->useMarkUp > 0 && $product->markup)
        		{
	        		$product->price *= $product->markup;
	        		$product->old_price *= $product->markup;
	        	}

	        	// $product->old_price = $product->price != $product->old_price ? ceil($product->old_price) : 0;
	        	// $product->price = ceil($product->price);

	        	if(!empty($_SESSION['option']->currency))
	        	{
		        	$product->price *= $_SESSION['option']->currency;
		        	$product->old_price *= $_SESSION['option']->currency;
		        }

		        $product->price = $this->formatPrice($product->price);
		        $product->old_price = $this->formatPrice($product->old_price);
        	}

			$product->parents = array();

			if($_SESSION['option']->useGroups > 0)
			{
				if(empty($this->allGroups))
        			$this->init();
        		if(!empty($this->allGroups))
        		{
					if($_SESSION['option']->ProductMultiGroup == 0 && $product->group > 0)
					{
						$product->parents = $this->makeParents($product->group, $product->parents);
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

						$this->db->select($this->table('_product_group') .' as pg', 'active', $product->id, 'product');
						$this->db->join($this->table('_groups'), 'id, alias, parent', array('id' => '#pg.group', 'active' => 1));
						$where_ntkd['content'] = "#-pg.group";
	        			$this->db->join('wl_ntkd', 'name', $where_ntkd);
						if($product->group = $this->db->get('array'))
						{
							$setBreadcrumbs = true;
				            foreach ($product->group as $g) {
				            	if($g->active)
				            		$product->active = $g->active;
				            	if($g->parent > 0)
				            		$g->link = $_SESSION['alias']->alias . '/' . $this->makeLink($g->parent, $g->alias);
				            	else
				            		$g->link = $_SESSION['alias']->alias . '/' . $g->alias;
				            	if(isset($_SESSION['alias']->breadcrumbs) && isset($_SERVER['HTTP_REFERER']) && $g->link == str_replace(SITE_URL, '', $_SERVER['HTTP_REFERER']))
				            	{
				            		$setBreadcrumbs = false;
				            		$parents = $this->makeParents($g->id, $product->parents);
									$link = $_SESSION['alias']->alias . '/';
									foreach ($parents as $parent) {
										$link .= $parent->alias .'/';
										$_SESSION['alias']->breadcrumbs[$parent->name] = $link;
									}
				            	}
				            }
				            if(isset($_SESSION['alias']->breadcrumbs) && $setBreadcrumbs)
				            {
				            	$parents = $this->makeParents($product->group[0]->id, $product->parents);
								$link = $_SESSION['alias']->alias . '/';
								foreach ($parents as $parent) {
									$link .= $parent->alias .'/';
									$_SESSION['alias']->breadcrumbs[$parent->name] = $link;
								}
				            }
				        }
					}
				}
			}
			if($all_info)
        	{
        		$parents_ids = array();
        		if($product->parents)
        			foreach ($product->parents as $pid) {
        				if(is_object($pid) && isset($pid->id))
        					$parents_ids[] = $pid->id;
        				elseif(is_numeric($pid) && $pid > 0)
        					$parents_ids[] = $pid;
        			}
        		$product->options = $this->getProductOptions($product, $parents_ids);
        		$product->photo = null;

        		$sizes = $this->db->getAliasImageSizes();

        		if(empty($_SESSION['alias']->breadcrumbs))
	            	if($photo = $this->getProductPhoto($product->id))
	            	{
						if($sizes)
							foreach ($sizes as $resize) {
								$resize_name = $resize->prefix.'_photo';
								$product->$resize_name = $_SESSION['option']->folder.'/'.$product->id.'/'.$resize->prefix.'_'.$photo->file_name;
							}
						$product->photo = $_SESSION['option']->folder.'/'.$product->id.'/'.$photo->file_name;
	            	}

				if($getSimilar = $this->db->getAllDataById($this->table('_products_similar'), array('product' => $product->id)))
				{
					if($similars = $this->db->getAllDataByFieldInArray($this->table('_products_similar'), array('product' => '!'.$product->id, 'group' => $getSimilar->group)))
					{
						$similars_ids = $similars_photos = array();
			            foreach ($similars as $similar)
			            	$similars_ids[] = $similar->product;
			            if($photos = $this->getProductPhoto($similars_ids))
			            {
				            foreach ($photos as $photo) {
				            	$similars_photos[$photo->content] = clone $photo;
				            }
				            unset($photos);
				        }

						foreach ($similars as $key => $similar) {
							$this->db->select('s_shopshowcase_products', 'id, alias, article', $similar->product);
							if($similarProduct = $this->db->get())
							{
								$product->similarProducts[$key] = $similarProduct;
								$product->similarProducts[$key]->photo = false;

								if(isset($similars_photos[$similarProduct->id]))
				            	{
				            		$photo = $similars_photos[$similarProduct->id];
									if($sizes)
										foreach ($sizes as $resize) {
											$resize_name = $resize->prefix.'_photo';
											$product->similarProducts[$key]->$resize_name = $_SESSION['option']->folder.'/'.$similarProduct->id.'/'.$resize->prefix.'_'.$photo->file_name;
										}
									$product->similarProducts[$key]->photo = $_SESSION['option']->folder.'/'.$similarProduct->id.'/'.$photo->file_name;
				            	}
							}
							else
								$this->db->deleteRow($this->table('_products_similar'), $similar->id);
						}
					}
				}
        	}
            return $product;
		}
		return false;
	}

	public function getProductPhoto($product, $all = false)
	{
		$where['alias'] = $_SESSION['alias']->id;
		$where['content'] = $product;
		if(is_array($product) || $product == '<0')
			$where['position'] = 1;
		$this->db->select('wl_images', '*', $where);
		if($all)
			$this->db->join('wl_users', 'name as user_name', '#author');
		elseif(is_numeric($product))
		{
			$this->db->order('position ASC');
			$this->db->limit(1);
		}
		if(is_array($product) || $all)
			return $this->db->get('array');
		else
			return $this->db->get();
	}

	private function getProductOptions($product, $parents = array())
	{
		$product_options = $positions = array();
		$where_language = $where_gon_language = '';
        if($_SESSION['language'])
    	{
    		$where_language = "AND (po.language = '{$_SESSION['language']}' OR po.language = '')";
    		$where_gon_language = "AND gon.language = '{$_SESSION['language']}'";
    	}
		$this->db->executeQuery("SELECT go.id, go.alias, go.filter, go.toCart, go.photo, go.changePrice, go.sort, go.position, po.value, it.name as type_name, it.options, gon.name, gon.sufix 
			FROM `{$this->table('_product_options')}` as po 
			LEFT JOIN `{$this->table('_options')}` as go ON go.id = po.option 
			LEFT JOIN `{$this->table('_options_name')}` as gon ON gon.option = go.id {$where_gon_language} 
			LEFT JOIN `wl_input_types` as it ON it.id = go.type 
			WHERE go.active = 1 AND po.product = '{$product->id}' {$where_language} 
			ORDER BY go.position");
		if($this->db->numRows() > 0)
		{
			$options = $this->db->getRows('array');
			foreach ($options as $option) {
				if($option->value != '')
				{
					$product_options[$option->alias] = new stdClass();
					$product_options[$option->alias]->id = $option->id;
					$product_options[$option->alias]->alias = $option->alias;
					$product_options[$option->alias]->filter = $option->filter;
					$product_options[$option->alias]->toCart = $option->toCart;
					$product_options[$option->alias]->name = $option->name;
					$product_options[$option->alias]->sufix = $option->sufix;
					$product_options[$option->alias]->changePrice = $option->changePrice;
					$product_options[$option->alias]->position = $option->position;
					$product_options[$option->alias]->photo = false;
					$positions[$option->alias] = $option->position;

					if($option->photo)
						$product_options[$option->alias]->photo = IMG_PATH.$_SESSION['option']->folder.'/options/'.$option->alias.'/'.$option->photo;

					if($option->options == 1)
					{
						if($option->type_name == 'checkbox' || $option->type_name == 'checkbox-select2' )
						{
							$where = array('option' => '#o.id');
							if($_SESSION['language']) $where['language'] = $_SESSION['language'];
							$this->db->select($this->table('_options') .' as o', 'id, photo', array('id' => explode(',', $option->value)))
												->join($this->table('_options_name') .' as n', 'name', $where);
							if($option->sort == 0)
								$this->db->order('position ASC');
							if($option->sort == 1)
								$this->db->order('name ASC', 'n');
							if($option->sort == 2)
								$this->db->order('name DESC', 'n');
							if($list = $this->db->get('array'))
								foreach ($list as $el) {
									$product_options[$option->alias]->value[] = $el;
									if($el->photo)
										$el->photo = IMG_PATH.$_SESSION['option']->folder.'/options/'.$option->alias.'/'.$el->photo;
								}
						}
						elseif($option->toCart)
						{
							$where = array('option' => '#o.id');
							if($_SESSION['language']) $where['language'] = $_SESSION['language'];
							$list = $this->db->select($this->table('_options') .' as o', 'id, photo', -$option->id, 'group')
												->join($this->table('_options_name') .' as n', 'name', $where);
							if($option->sort == 0)
								$this->db->order('position ASC');
							if($option->sort == 1)
								$this->db->order('name ASC', 'n');
							if($option->sort == 2)
								$this->db->order('name DESC', 'n');
							if($list = $this->db->get('array'))
								foreach ($list as $el) {
									$product_options[$option->alias]->value[] = $el;
									if($el->photo)
										$el->photo = IMG_PATH.$_SESSION['option']->folder.'/options/'.$option->alias.'/'.$el->photo;
								}
							else
								unset($product_options[$option->alias]);
						}
						else
						{
							$where = array('option' => '#o.id');
							if($_SESSION['language']) $where['language'] = $_SESSION['language'];
							$value = $this->db->select($this->table('_options') .' as o', 'id, photo', $option->value)
												->join($this->table('_options_name'), 'name', $where)
												->get('single');
							if($value)
							{
								$product_options[$option->alias]->value = $value->name;
								$product_options[$option->alias]->photo = IMG_PATH.$_SESSION['option']->folder.'/options/'.$option->alias.'/'.$value->photo;
							}
						}
					}
					else
						$product_options[$option->alias]->value = $option->value;
				}
			}
		}
		if(empty($parents))
			$parents[] = 0;
		$where = array('toCart' => 1, 'active' => 1);
		$where['group'] = $parents;
		if($options = $this->db->getAllDataByFieldInArray($this->table('_options'), $where))
		{
			foreach ($options as $option) {
				if(!isset($product_options[$option->alias]) && $option->type != 8)
				{
					$product_options[$option->alias] = new stdClass();
					$product_options[$option->alias]->id = $option->id;
					$product_options[$option->alias]->alias = $option->alias;
					$product_options[$option->alias]->filter = $option->filter;
					$product_options[$option->alias]->toCart = $option->toCart;
					$product_options[$option->alias]->changePrice = $option->changePrice;
					$product_options[$option->alias]->position = $option->position;
					$positions[$option->alias] = $option->position;

					$where = array('option' => $option->id);
					if($_SESSION['language']) $where['language'] = $_SESSION['language'];
					if($name = $this->db->getAllDataById($this->table('_options_name'), $where))
					{
						$product_options[$option->alias]->name = $name->name;
						$product_options[$option->alias]->sufix = $name->sufix;
					}

					$where = array('option' => '#o.id');
					if($_SESSION['language']) $where['language'] = $_SESSION['language'];
					$list = $this->db->select($this->table('_options') .' as o', 'id, photo', -$option->id, 'group')
										->join($this->table('_options_name') .' as n', 'name', $where);
					if($option->sort == 0)
						$this->db->order('position ASC');
					if($option->sort == 1)
						$this->db->order('name ASC', 'n');
					if($option->sort == 2)
						$this->db->order('name DESC', 'n');
					if($list = $this->db->get('array'))
						foreach ($list as $el) {
							$product_options[$option->alias]->value[] = $el;
							if($el->photo)
								$el->photo = IMG_PATH.$_SESSION['option']->folder.'/options/'.$option->alias.'/'.$el->photo;
						}
					else
						unset($product_options[$option->alias]);
				}
			}
		}
		array_multisort($positions, $product_options);

		return $product_options;
	}

	public function getGroups($parent = 0)
	{
		if($_SESSION['option']->useGroups && empty($this->allGroups))
    		$this->init();
        if(empty($this->allGroups))
        	return false;

		$categories = array();
		foreach ($this->allGroups as $group) {
			if($group->active)
			{
				if($parent < 0)
					$categories[] = clone $group;
				else if($group->parent == $parent)
					$categories[] = clone $group;
			}
		}
		if(empty($categories))
        	return false;
		if(isset($_SESSION['option']->paginator_per_page) && $_SESSION['option']->paginator_per_page > 0 && $parent >= 0)
		{
			$_SESSION['option']->paginator_total = count($categories);
			if($_SESSION['option']->paginator_total > $_SESSION['option']->paginator_per_page)
			{
				$start = $end = 0;
				if(isset($_GET['page']) && is_numeric($_GET['page']) && $_GET['page'] > 1)
					$start = ($_GET['page'] - 1) * $_SESSION['option']->paginator_per_page;
				$end = $start + $_SESSION['option']->paginator_per_page;
				foreach ($categories as $i => $cat) {
					if($i >= $start && $i < $end)
						continue;
					else
						unset($categories[$i]);
				}
			}
		}

		if(!empty($categories))
		{
			$link = $_SESSION['alias']->alias.'/';
            $list = $groups_ids = $groups_photos = array();
            $sizes = $this->db->getAliasImageSizes();

			if($parent < 0)
				$groups_ids = '<0';
			else
				foreach ($categories as $g) {
					$groups_ids[] = -$g->id;
				}
            if($photos = $this->getProductPhoto($groups_ids))
            {
	            foreach ($photos as $photo) {
	            	$groups_photos[-$photo->content] = clone $photo;
	            }
	            unset($photos);
	        }

	        if($parent > 0)
	        	$link .= $this->makeLink($parent, '');

            foreach ($categories as $Group) {
            	$Group->link = $link.$Group->alias;
            	if($parent < 0 && $Group->parent > 0)
            		$Group->link = $link.$this->makeLink($Group->parent, $Group->alias);
            	$Group->photo = null;
            	if(isset($groups_photos[$Group->id]))
            	{
            		$photo = $groups_photos[$Group->id];
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
		return false;
	}

	public function getProductPriceWithOptions($product, $options)
	{
		if($product = $this->db->getAllDataById($this->table('_products'), $product))
		{
			$options_id = array();
			foreach ($options as $key => $value) {
				if(is_numeric($key) && is_numeric($value))
					$options_id[] = $key;
			}
			if(!empty($options_id))
			{
				$list = $usedOptions = array();
				$price = $product->price;
				if($settings = $this->db->getAllDataByFieldInArray($this->table('_product_options'), array('product' => $product->id, 'option' => $options_id)))
					foreach ($settings as $setting) {
						$usedOptions[] = $setting->option;
						if(!empty($setting->changePrice))
							$list[$setting->option] = unserialize($setting->changePrice);
					}
				if(count($options_id) != count($usedOptions))
					if($settings = $this->db->getAllDataByFieldInArray($this->table('_options'), array('id' => $options_id)))
						foreach ($settings as $option) {
							if(!in_array($option->id, $usedOptions))
							{
								$usedOptions[] = $option->id;
								if(!empty($option->changePrice))
								{
									if($option_settings = $this->db->getAllDataByFieldInArray($this->table('_options'), -$option->id, 'group'))
										foreach ($option_settings as $os) {
											if(!empty($os->changePrice))
												$list[$option->id][$os->id] = unserialize($os->changePrice);
										}
									
								}
							}
						}
				foreach ($options as $key => $value) {
					if(is_numeric($key) && is_numeric($value) && isset($list[$key][$value]))
					{
						$changePrice = $list[$key][$value];
						if(is_numeric($changePrice) && $changePrice > 0)
							$price = $changePrice;
					}
				}
				foreach ($options as $key => $value) {
					if(is_numeric($key) && is_numeric($value) && isset($list[$key][$value]))
					{
						$changePrice = $list[$key][$value];
						if(is_numeric($changePrice) && $changePrice == 0)
						{
							if($setting = $this->db->getAllDataById($this->table('_options'), $value))
							{
								$setting->changePrice = unserialize($setting->changePrice);
								if($setting->changePrice['value'] > 0)
								{
									$plus = 0;
									if($setting->changePrice['currency'] == 'p')
										$plus = $price * $setting->changePrice['value'] / 100;
									else
										$plus = $setting->changePrice['value'];
									if($setting->changePrice['action'] == '+')
										$price += $plus;
									else if($setting->changePrice['action'] == '-')
										$price -= $plus;
									else if($setting->changePrice['action'] == '*')
										$price *= $plus;
								}
							}
						}
						else if(is_array($changePrice) && $changePrice['value'] > 0)
						{
							$plus = 0;
							if($changePrice['currency'] == 'p')
								$plus = $price * $changePrice['value'] / 100;
							else
								$plus = $changePrice['value'];
							if($changePrice['action'] == '+')
								$price += $plus;
							else if($changePrice['action'] == '-')
								$price -= $plus;
							else if($changePrice['action'] == '*')
								$price *= $plus;
						}
					}
				}
				return $price;
			}
			return $product->price;
		}
		return false;
	}

	public function makeParents($parent, $parents)
	{
		if(isset($this->allGroups[$parent]))
		{
			$group = clone $this->allGroups[$parent];
	    	array_unshift ($parents, $group);
			if($this->allGroups[$parent]->parent > 0)
				$parents = $this->makeParents ($this->allGroups[$parent]->parent, $parents);
		}
		return $parents;
	}

	public function getGroupByAlias($alias, $parent = 0, $key = 'alias')
	{
		if(empty($this->allGroups))
    		$this->init();
    	if(empty($this->allGroups))
    		return false;

    	$group = false;
		if($key == 'id')
		{
			if(isset($this->allGroups[$alias]))
				$group = $this->allGroups[$alias];
			else
				return false;
		}
		elseif($key == 'alias')
		{
			foreach ($this->allGroups as $g) {
				if($g->alias == $alias && $g->parent == $parent)
				{
					$group = $g;
					break;
				}
			}
		}
		else
		{
			$where['wl_alias'] = $_SESSION['alias']->id;
			$where['alias'] = $alias;
			$where['parent'] = $parent;
			$this->db->select($this->table('_groups') .' as c', '*', $where);
			$this->db->join('wl_users', 'name as user_name', '#c.author_edit');
			$group = $this->db->get('single');
		}
		
		if($group)
		{
			$group->haveChild = false;
			if(empty($this->allGroups))
				foreach ($this->allGroups as $g) {
					if($g->parent == $group->id)
					{
						$group->haveChild = true;
						break;
					}
				}

			if(isset($_SESSION['alias']->breadcrumbs) && empty($_SESSION['alias']->breadcrumbs))
        	{
        		$where = array();
				$where['alias'] = $_SESSION['alias']->id;
				if($_SESSION['language'])
					$where['language'] = $_SESSION['language'];
        		$where['content'] = 0;
				if($data = $this->db->getAllDataById('wl_ntkd', $where))
				{
					$_SESSION['alias']->breadcrumb_name = $data->name;
					$_SESSION['alias']->breadcrumbs = array($data->name => $_SESSION['alias']->alias);
				}
        	
				$group->parents = array();
				if($group->parent > 0)
				{
					$group->parents = $this->makeParents($group->parent, $group->parents);
					$link = $_SESSION['alias']->alias;
					foreach ($group->parents as $parent) {
						$link .= '/'.$parent->alias;
						$_SESSION['alias']->breadcrumbs[$parent->name] = $link;
					}
					$group->link = $link;
				}
				$_SESSION['alias']->breadcrumbs[$group->name] = '';
			}
			else
			{
				if($photo = $this->getProductPhoto(-$group->id))
	        	{
					if($sizes = $this->db->getAliasImageSizes())
						foreach ($sizes as $resize) {
							$resize_name = $resize->prefix.'_photo';
							$group->$resize_name = $_SESSION['option']->folder.'/-'.$group->id.'/'.$resize->prefix.'_'.$photo->file_name;
						}
					$group->photo = $_SESSION['option']->folder.'/-'.$group->id.'/'.$photo->file_name;
	        	}
	        }
        }
		return $group;
	}

	public function getOptionsToGroup($group = 0, $filter = true)
	{
		$products = false;
		if(empty($this->allGroups))
			$this->init();
		if($group === 0)
		{
			$where['group'] = 0;
			$group = new stdClass();
			$group->id = 0;
			$group->parent = 0;
		}
		elseif(is_numeric($group))
		{
			if(isset($this->allGroups[$group]))
				$group = $this->allGroups[$group];
			else
				return false;
		}
		if($_SESSION['option']->useGroups && $group->id > 0)
		{
			if(empty($this->productsIdInGroup))
			{
				if($_SESSION['option']->ProductMultiGroup)
				{
					$products_id = $this->db->getAllDataByFieldInArray($this->table('_product_group'), array('group' => $group->id, 'active' => 1));
					if($products_id)
						foreach ($products_id as $product) {
							$products[] = $product->product;
						}
				}
				else
				{
					$products_id = $this->db->getAllDataByFieldInArray($this->table('_products'), array('group' => $group->id, 'active' => 1));
					if($products_id)
						foreach ($products_id as $product) {
							$products[] = $product->id;
						}
				}
			}
			else
				$products = $this->productsIdInGroup;
		}

    	if($filter && ($group->id > 0 && $products || $group->id == 0) || !$filter)
    	{
    		$where['group'] = array(0, $group->id);
			if($group->parent > 0)
				while ($group->parent > 0) {
					if(isset($this->allGroups[$group->parent]))
						$group = $this->allGroups[$group->parent];
					array_push($where['group'], $group->id);
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
				$to_delete_options = $opt_ids = array();
				if($products)
				{
			        foreach ($options as $option)
			        	$opt_ids[] = $option->id;
			        $where = array('option' => $opt_ids);
			        $where['product'] = $products;
			        $list_product_options = $this->db->getAllDataByFieldInArray($this->table('_product_options'), $where);
	    			if(!$list_product_options)
	    				return false;
	    		}
		        foreach ($options as $i => $option) {
		        	if(!empty($list_product_options))
		        	{
			        	$next = true;
			        	foreach ($list_product_options as $row) {
			        		if($row->option == $option->id)
			        		{
			        			$next = false;
			        			break;
			        		}
			        	}
			        	if($next)
			        	{
			        		if($filter)
			        			$to_delete_options[] = $i;
			        		continue;
			        	}
			        }

		        	$where = array('option' => '#o.id');
		        	if($_SESSION['language'])
		        		$where['language'] = $_SESSION['language'];
		        	$this->db->select($this->table('_options').' as o', 'id', -$option->id, 'group');
		        	$this->db->join($this->table('_options_name') .' as n', 'name', $where);
					if($option->sort == 0)
						$this->db->order('position ASC');
					if($option->sort == 1)
						$this->db->order('name ASC', 'n');
					if($option->sort == 2)
						$this->db->order('name DESC', 'n');
		        	$option->values = $this->db->get('array');

					if(!empty($option->values))
		    		{
		    			if(!empty($list_product_options))
		    			{
		    				foreach ($option->values as $i => $value) {
			    				$count = 0;
			    				if($option->type_name == 'checkbox' || $option->type_name == 'checkbox-select2' )
			    				{
			    					foreach ($list_product_options as $row) {
				        				if($row->option == $option->id)
				        				{
				        					if(!is_array($row->value))
				        						$row->value = explode(',', $row->value);
				        					if(in_array($value->id, $row->value))
			        							$count++;
				        				}
				        			}
			    				}
			    				else
			    				{
			    					foreach ($list_product_options as $row) {
				        				if($row->option == $option->id && $row->value == $value->id)
				        					$count++;
				        			}
			    				}
			    				$option->values[$i]->count = $count;
			    				if(!$count && $filter)
			        				unset($option->values[$i]);
			        		}
		    			}
		    			else
		    			{
		    				$where = array();
			    			if($products)
			    				$where['product'] = $products;
			    			foreach ($option->values as $i => $value) {
			    				$where['option'] = $option->id;
			    				if($option->type_name == 'checkbox' || $option->type_name == 'checkbox-select2' )
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
								$option->values[$i]->count = $count;
			        			if(!$count && $filter)
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

	private function makeLink($parent, $link)
	{
		$link = $this->allGroups[$parent]->alias .'/'.$link;
		if($this->allGroups[$parent]->parent > 0)
			$link = $this->makeLink ($this->allGroups[$parent]->parent, $link);
		return $link;
	}

	public function getEndGroups($parentGroups)
	{
		$endGroups = $groups = array();
		if(empty($this->allGroups))
			$this->init();
		if(empty($this->allGroups))
			return false;
		foreach ($this->allGroups as $group) {
			if(isset($groups[$group->parent]))
				$groups[$group->parent][] = $group->id;
			else
				$groups[$group->parent] = array($group->id);
		}
		if(!is_array($parentGroups))
			$parentGroups = array($parentGroups);
		return $this->makeEndGroups($groups, $parentGroups, $endGroups);
	}

	private function makeEndGroups($all, $parentGroups, $endGroups)
	{
		$endGroups = array_merge($endGroups, $parentGroups);
		foreach ($parentGroups as $parent) {
			if(isset($all[$parent]))
				$endGroups = $this->makeEndGroups ($all, $all[$parent], $endGroups);
		}
		return $endGroups;
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

	public function formatPrice($price)
	{
		return round($price * 20) / 20;
	}

}

?>
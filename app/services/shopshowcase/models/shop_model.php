<?php

class shop_model {

	public function table($sufix = '')
	{
		return $_SESSION['service']->table.$sufix.$_SESSION['alias']->table;
	}
	
	function getProducts($Group = 0, $active = true){
		$where = '';
		if($active) $where = "WHERE a.active = '1'";
		if($Group > 0 && $_SESSION['option']->useGroups > 0){
			if($_SESSION['option']->ProductMultiGroup == 0){
				if($where == '') $where = "WHERE a.group = '{$Group}'";
				else $where .= " AND a.group = '{$Group}'";
			} else {
				$products = $this->db->getAllDataByFieldInArray($this->table('_article_Group'), $Group, 'Group');
				if($products) {
					if($where == '') $where = "WHERE a.id IN (";
					else $where .= " AND a.id IN (";
					foreach ($products as $id) {
						$where .= "'{$id}', ";
					}
					$where = substr($where, 0, -2);
					$where .= ")";
				} else return null;
			}
		}
		if(count($_GET) > 1){
			foreach ($_GET as $key => $value) {
				if($key != 'request' && $key != 'page'){
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

            						if(!empty($products)){
    									if($where == '') $where = "WHERE a.id IN (";
										else $where .= " AND a.id IN (";
										foreach ($products as $p) {
											$where .= "'{$p->product}', ";
										}
										$where = substr($where, 0, -2);
										$where .= ")";
        							} else {
        								return false;
        							}
            						break;
            					}
            				}
            			}
					}
					// exit();
				}
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
		
		$where_language = '';
        if($_SESSION['language']) $where_language = "AND an.language = '{$_SESSION['language']}'";
		$this->db->executeQuery("SELECT a.*, u.name as user_name, an.name as availability_name, ac.color as availability_color FROM `{$this->table('_products')}` as a LEFT JOIN wl_users as u ON u.id = user LEFT JOIN {$_SESSION['service']->table}_availability as ac ON a.availability = ac.id LEFT JOIN {$_SESSION['service']->table}_availability_name as an ON a.availability = an.availability {$where_language} {$where} ORDER BY a.position ASC {$limit}");
        if($this->db->numRows() > 0){
            $products = $this->db->getRows('array');

            $this->db->executeQuery("SELECT count(*) as count FROM `{$this->table('_products')}` as a {$where}");
			$_SESSION['option']->count_all_products = $this->db->getRows()->count;

			$where_language = '';
            if($_SESSION['language']) $where_language = "AND `language` = '{$_SESSION['language']}'";
            foreach ($products as $product) {
            	$this->db->executeQuery("SELECT `name`, `text` FROM `wl_ntkd` WHERE `alias` = '{$_SESSION['alias']->id}' AND `content` = '{$product->id}' {$where_language}");
            	if($this->db->numRows() == 1){
            		$ntkd = $this->db->getRows();
            		$product->name = $ntkd->name;
            		$product->text = $ntkd->text;
            	}

            	$product->options = array();
	            $this->db->executeQuery("SELECT go.id, go.link, go.filter, po.value, it.name as type_name, it.options FROM `{$this->table('_product_options')}` as po LEFT JOIN `{$this->table('_group_options')}` as go ON go.id = po.option LEFT JOIN `wl_input_types` as it ON it.id = go.type WHERE go.active = 1 AND po.product = '{$product->id}' ORDER BY go.position");
	            if($this->db->numRows() > 0){
	        		$options = $this->db->getRows('array');
	        		foreach ($options as $option) if($option->value != '') {
	        			@$product->options[$option->id]->id = $option->id;
	        			$product->options[$option->id]->link = $option->link;
	        			$product->options[$option->id]->filter = $option->filter;
	        			$where = array();
	    			    $where['option'] = $option->id;
	    				if($_SESSION['language']) $where['language'] = $_SESSION['language'];
	    				$name = $this->db->getAllDataById($this->table('_options_name'), $where);

	    				if($name){
	    					$product->options[$option->id]->name = $name->name;
	    					$product->options[$option->id]->sufix = $name->sufix;
	    				}
	        			if($option->options == 1){
	        				if($option->type_name == 'checkbox'){
	        					$option->value = explode(',', $option->value);
	        					$product->options[$option->id]->value = '';
	        					foreach ($option->value as $value) {
	        						$where = array();
	        						$where['option'] = $value;
	        						if($_SESSION['language']) $where['language'] = $_SESSION['language'];
	        						$value = $this->db->getAllDataById($this->table('_options_name'), $where);
			        				if($value){
			        					$product->options[$option->id]->value .= $value->name .' ';
			        				}
	        					}
	        				} else {
	        					$where = array();
	    						$where['option'] = $option->value;
	    						if($_SESSION['language']) $where['language'] = $_SESSION['language'];
	        					$value = $this->db->getAllDataById($this->table('_options_name'), $where);
		        				if($value){
		        					$product->options[$option->id]->value = $value->name;
		        				}
	        				}
	        			} else {
	        				$product->options[$option->id]->value = $option->value;
	        			}
	        		}
	        	}
            }

			return $products;
		}
		return null;
	}
	
	function getProductById($id, $lang = true){
		$where_language = '';
        if($_SESSION['language']) $where_language = "AND `language` = '{$_SESSION['language']}'";
		$this->db->executeQuery("SELECT a.*, u.name as user_name, an.name as availability_name, ac.color as availability_color FROM {$this->table('_products')} as a LEFT JOIN wl_users as u ON u.id = a.user LEFT JOIN {$_SESSION['service']->table}_availability as ac ON a.availability = ac.id LEFT JOIN {$_SESSION['service']->table}_availability_name as an ON a.availability = an.availability {$where_language} WHERE a.id = '{$id}'");
        if($this->db->numRows() == 1){
            $product = $this->db->getRows();
            if($lang) {
            	$where = '';
            	if($_SESSION['language']) $where = "AND `language` = '{$_SESSION['language']}'";
            	$this->db->executeQuery("SELECT `name`, `text` FROM `wl_ntkd` WHERE `alias` = '{$_SESSION['alias']->id}' AND `content` = '{$product->id}' {$where}");
            	if($this->db->numRows() == 1){
            		$ntkd = $this->db->getRows();
            		$product->name = $ntkd->name;
            		$product->text = $ntkd->text;
            	}
            }

			$product->parents = array();
			if($product->group > 0){
				$list = array();
	            $groups = $this->db->getAllData($this->table('_groups'));
	            foreach ($groups as $Group) {
	            	$list[$Group->id] = clone $Group;
	            }
				$product->parents = $this->makeParents($list, $product->group, $product->parents);
				$link = '';
				foreach ($product->parents as $parent) {
					$link .= $parent->link .'/';
				}
				$product->link = $link . $product->link;
			}

            $product->options = array();
            $this->db->executeQuery("SELECT go.id, po.value, it.name as type_name, it.options FROM `{$this->table('_product_options')}` as po LEFT JOIN `{$this->table('_group_options')}` as go ON go.id = po.option LEFT JOIN `wl_input_types` as it ON it.id = go.type WHERE go.active = 1 AND po.product = '{$product->id}' ORDER BY go.position");
            if($this->db->numRows() > 0){
        		$options = $this->db->getRows('array');
        		foreach ($options as $option) if($option->value != '') {
        			@$product->options[$option->id]->id = $option->id;
        			$where = array();
    			    $where['option'] = $option->id;
    				if($_SESSION['language']) $where['language'] = $_SESSION['language'];
    				$name = $this->db->getAllDataById($this->table('_options_name'), $where);

    				if($name){
    					$product->options[$option->id]->name = $name->name;
    					$product->options[$option->id]->sufix = $name->sufix;
    				}
        			if($option->options == 1){
        				if($option->type_name == 'checkbox'){
        					$option->value = explode(',', $option->value);
        					$product->options[$option->id]->value = '';
        					foreach ($option->value as $value) {
        						$where = array();
        						$where['option'] = $value;
        						if($_SESSION['language']) $where['language'] = $_SESSION['language'];
        						$value = $this->db->getAllDataById($this->table('_options_name'), $where);
		        				if($value){
		        					$product->options[$option->id]->value .= $value->name .' ';
		        				}
        					}
        				} else {
        					$where = array();
    						$where['option'] = $option->value;
    						if($_SESSION['language']) $where['language'] = $_SESSION['language'];
        					$value = $this->db->getAllDataById($this->table('_options_name'), $where);
	        				if($value){
	        					$product->options[$option->id]->value = $value->name;
	        				}
        				}
        			} else {
        				$product->options[$option->id]->value = $option->value;
        			}
        		}
        	}
            return $product;
		}
		return null;
	}

	function add_product($photo = -1){
		$data = array();
		$data['active'] = 1;
		$data['availability'] = 1;
		$data['price'] = 0;
		if(isset($_POST['price']) && is_numeric($_POST['price']) && $_POST['price'] > 0) $data['price'] = $_POST['price'];
		$data['photo'] = $photo;
		$data['user'] = $_SESSION['user']->id;
		$data['date'] = time();
		if($this->db->insertRow($this->table('_products'), $data)){
			$id = $this->db->getLastInsertedId();
			$data = array();
			$data['link'] = '';

			$ntkd['alias'] = $_SESSION['alias']->id;
			$ntkd['content'] = $id;
			if($_SESSION['language']){
				foreach ($_SESSION['all_languages'] as $lang) {
					$ntkd['language'] = $lang;
					$name = trim($_POST['name_'.$lang]);
					$ntkd['name'] = $name;
					$ntkd['title'] = $name;
					if($lang == $_SESSION['language']){
						$data['link'] = $this->db->latterUAtoEN($name);
					}
					$this->db->insertRow('wl_ntkd', $ntkd);
				}
			} else {
				$name = trim($_POST['name']);
				$ntkd['name'] = $name;
				$ntkd['title'] = $name;
				$data['link'] = $this->db->latterUAtoEN($name);
				$this->db->insertRow('wl_ntkd', $ntkd);
			}
			$data['link'] = $id .'-'. $data['link'];
			
			if($_SESSION['option']->useGroups){
				if($_SESSION['option']->ProductMultiGroup && isset($_POST['group']) && is_array($_POST['group'])){
					foreach ($_POST['group'] as $group) {
						$this->db->insertRow($this->table('_product_group'), array('product' => $id, 'group' => $group));
					}
					$data['position'] = $this->db->getCount($this->table('_products'));
				} else {
					if(isset($_POST['group']) && is_numeric($_POST['group'])) $data['group'] = $_POST['group'];
					$data['position'] = $this->db->getCount($this->table('_products', array('group' => $data['group'])));
				}
			} else {
				$data['position'] = $this->db->getCount($this->table('_products'));
			}
			if($photo > 0) $data['photo'] = $id;
			if($this->db->updateRow($this->table('_products'), $data, $id)) return $id;
		}
		return false;
	}

	public function saveProductOptios($id)
	{
		$options = array();
		foreach ($_POST as $key => $value) {
			$key = explode('-', $key);
			if($key[0] == 'option' && isset($key[1]) && is_numeric($key[1])){
				if(is_array($value)){
					$options[$key[1]] = implode(',', $value);
				} else {
					$options[$key[1]] = $value;
				}
			}
		}
		if(!empty($options)){
			$list_temp = $this->db->getAllDataByFieldInArray($this->table('_product_options'), $id, 'product');
			$list = array();
			if($list_temp) {
				foreach ($list_temp as $option) {
					$list[$option->option] = $option;
				}
			}
			foreach ($options as $key => $value) {
				if(isset($list[$key])){
					if($list[$key]->value != $value){
						$this->db->updateRow($this->table('_product_options'), array('value' => $value), $list[$key]->id);
					}
				} else {
					$data['product'] = $id;
					$data['option'] = $key;
					$data['value'] = $value;
					$this->db->insertRow($this->table('_product_options'), $data);
				}
			}
		}
		return true;
	}

	function getGroups($parent = 0, $active = true)
	{
		$where = '';
		if($parent >= 0) $where = "WHERE c.parent = '{$parent}'";
		if($active) $where .= " AND c.active = '1'";
		$this->db->executeQuery("SELECT c.*, u.name as user_name FROM {$this->table('_groups')} as c LEFT JOIN wl_users as u ON u.id = c.user {$where} ORDER BY c.position");
		if($this->db->numRows() > 0){
            $categories = $this->db->getRows('array');

            $where = '';
            if($_SESSION['language']) $where = "AND `language` = '{$_SESSION['language']}'";
            $list = array();
            $groups = $this->db->getAllData($this->table('_groups'));
            foreach ($groups as $Group) {
            	$list[$Group->id] = clone $Group;
            }
            foreach ($categories as $Group) {
            	if($Group->parent > 0) {
            		$Group->link = $this->makeLink($list, $Group->parent, $Group->link);
            	}
            	$this->db->executeQuery("SELECT `name`, `text` FROM `wl_ntkd` WHERE `alias` = '{$_SESSION['alias']->id}' AND `content` = '-{$Group->id}' {$where}");
            	if($this->db->numRows() == 1){
            		$ntkd = $this->db->getRows();
            		$Group->name = $ntkd->name;
            		$Group->text = $ntkd->text;
            	}
            }

            return $categories;
		}
		return null;
	}

	private function makeLink($all, $parent, $link)
	{
		$link = $all[$parent]->link .'/'.$link;
		if($all[$parent]->parent > 0) $link = $this->makeLink ($all, $all[$parent]->parent, $link);
		return $link;
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

	function getGroupByAlias($alias, $parent = 0){
		$alias = $this->db->sanitizeString($alias);
		$this->db->executeQuery("SELECT c.*, u.name as user_name FROM {$this->table('_groups')} as c LEFT JOIN wl_users as u ON u.id = c.user WHERE c.link = '{$alias}' AND c.parent = '{$parent}'");
        if($this->db->numRows() == 1){
            return $this->db->getRows();
		}
		return null;
	}

	function getGroupById($id){
		$this->db->executeQuery("SELECT c.*, u.name as user_name FROM {$this->table('_groups')} as c LEFT JOIN wl_users as u ON u.id = c.user WHERE c.id = $id");
        if($this->db->numRows() == 1){
            return $this->db->getRows();
		}
		return null;
	}

	function add_Group($photo = -1){
		$data = array();
		$data['parent'] = 0;
		if(isset($_POST['parent']) && is_numeric($_POST['parent']) && $_POST['parent'] > 0) $data['parent'] = $_POST['parent'];
		$data['active'] = 1;
		$data['photo'] = $photo;
		$data['user'] = $_SESSION['user']->id;
		$data['date'] = time();
		if($this->db->insertRow($this->table('_groups'), $data)){
			$id = $this->db->getLastInsertedId();

			$position = 0;
			$this->db->executeQuery("SELECT count(*) as count FROM {$this->table('_groups')} WHERE parent = '{$data['parent']}'");
            if($this->db->numRows() == 1){
                $count = $this->db->getRows();
                $position = $count->count;
            }

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
			$data['link'] = $this->GroupLink($data['link']);
			if($position == 0)	$data['position'] = $this->db->getCount($this->table('_groups'));
			else $data['position'] = $position;
			if($photo > 0) $data['photo'] = $id;
			if($this->db->updateRow($this->table('_groups'), $data, $id)) return $id;
		}
		return false;
	}

	function GroupLink($link){
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

	public function changeGroupParent($id, $old, $new)
	{
		$groups = $this->db->getAllData($this->table('_groups'));
		if($groups){
			$level_1 = array();
			$childs = array();
			$list = array();
			$emptyParentsList = array();
			foreach ($groups as $group) {
				$list[$group->id] = $group;
				$list[$group->id]->childs = array();
				if(isset($emptyParentsList[$group->id])){
					foreach ($emptyParentsList[$group->id] as $c) {
						$list[$group->id]->childs[] = $c;
					}
				}
				if($group->parent > 0) {
					if(isset($list[$group->parent]->childs)) $list[$group->parent]->childs[] = $group->id;
					else {
						if(isset($emptyParentsList[$group->parent])) $emptyParentsList[$group->parent][] = $group->id;
						else $emptyParentsList[$group->parent] = array($group->id);
					}
				}
				if($group->parent == $id){
					$level_1[] = $group->id;
					$childs[] = $group->id;
				}
			}
			if(!empty($level_1)){
				foreach ($level_1 as $group) {
					if(!empty($list[$group]->childs)){
						$childs = $this->getGroupParents($list, $list[$group]->childs);
					}
				}
			}
			if(in_array($new, $level_1) || in_array($new, $childs)){
				$position = $list[$id]->position;
				$groups = $this->db->getAllDataByFieldInArray($this->table('_groups'), array('parent' => $old, 'position' => '>'.$list[$id]->position), 'position ASC');
				foreach ($level_1 as $group) {
					$this->db->updateRow($this->table('_groups'), array('parent' => $old, 'position' => $position), $group);
					$position++;
				}
				if($groups){
					$step = $position - $groups[0]->position;
					foreach ($groups as $group) {
						$position = $group->position + $step;
						$this->db->updateRow($this->table('_groups'), array('position' => $position), $group->id);
					}
				}
			} else {
				$this->db->executeQuery("UPDATE `{$this->table('_groups')}` SET `position` = `position` - 1 WHERE `parent` = '{$old}' AND `position` > '{$list[$id]->position}'");
			}
			$this->db->executeQuery("SELECT count(*) as count FROM {$this->table('_groups')} WHERE parent = '{$new}'");
			if($this->db->numRows() == 1){
                $count = $this->db->getRows();
                $position = $count->count;
                $position++;
                $this->db->updateRow($this->table('_groups'), array('position' => $position), $id);
            }
		}
		return true;
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
	
	function changePosition($table, $id, $new_pos, $parent = -1){
		$where = '_products';
		if($table == '_groups') {
			if($parent < 0) {
				$group = $this->db->getAllDataById($this->table('_groups'), $id);
				if($group) $parent = $group->parent;
			}
			if($parent >= 0) $where = "WHERE `parent` = '{$parent}'";
		}
		if($table == '_group_options') {
			if($parent < 0) {
				$option = $this->db->getAllDataById($this->table('_group_options'), $id);
				if($option) $parent = $option->group;
			}
			if($parent >= 0) $where = "WHERE `group` = '{$parent}'";
		}
		$table = $this->table($table);
		$this->db->executeQuery("SELECT id, position as pos FROM `{$table}` {$where} ORDER BY `position` ASC ");
		 if($this->db->numRows() > 0){
            $products = $this->db->getRows();
			$old_pos = 0;
			foreach($products as $a) if($a->id == $id) { $old_pos = $a->pos; break; }
			if($new_pos < $old_pos)	foreach($products as $a){
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
			if($new_pos > $old_pos)	foreach($products as $a){
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

	public function add_option($property = false){
		$data = array();
		if(isset($_POST['group']) && is_numeric($_POST['group'])) $data['group'] = $_POST['group'];
		if($property && isset($_POST['id'])) $data['group'] = -1 * $_POST['id'];
		if(isset($_POST['type']) && is_numeric($_POST['type'])) $data['type'] = $_POST['type'];
		$data['active'] = 1;
		$data['filter'] = 0;
		if($this->db->insertRow($this->table('_group_options'), $data)){
			$id = $this->db->getLastInsertedId();
			$data = array();
			$data['link'] = '';

			$ntkd = array();
			$ntkd['option'] = $id;
			if($_SESSION['language']){
				foreach ($_SESSION['all_languages'] as $lang) {
					$ntkd['language'] = $lang;
					$ntkd['name'] = $_POST['name_'.$lang];
					$ntkd['sufix'] = $_POST['sufix_'.$lang];
					if($lang == $_SESSION['language']){
						$data['link'] = $this->db->latterUAtoEN($ntkd['name']);
					}
					$this->db->insertRow($this->table('_options_name'), $ntkd);
				}
			} else {
				$ntkd['name'] = $_POST['name'];
				$ntkd['sufix'] = $_POST['sufix'];
				$data['link'] = $this->db->latterUAtoEN($ntkd['name']);
				$this->db->insertRow($this->table('_options_name'), $ntkd);
			}
			$data['link'] = $id .'-'. $data['link'];
			
			$group = 0;
			if($_SESSION['option']->useGroups){			
				if(isset($_POST['group']) && is_numeric($_POST['group'])) $group = $_POST['group'];	
			}
			$data['position'] = $this->db->getCount($this->table('_group_options', array('group' => $group)));
			if($this->db->updateRow($this->table('_group_options'), $data, $id)) return $id;
		}
		return false;
	}

	public function saveOption($id)
	{
		$data = array('active' => 1, 'filter' => 0);
		if(isset($_POST['link']) && $_POST['link'] != '') $data['link'] = $_POST['id'] . '-' . $_POST['link'];
		if(isset($_POST['active']) && $_POST['active'] == 0) $data['active'] = 0;
		if(isset($_POST['filter']) && $_POST['filter'] == 1) $data['filter'] = 1;
		if(isset($_POST['type']) && is_numeric($_POST['type'])) $data['type'] = $_POST['type'];
		if($_SESSION['option']->useGroups){
			if(isset($_POST['group']) && is_numeric($_POST['group'])) $data['group'] = $_POST['group'];
		}
		if($this->db->updateRow($this->table('_group_options'), $data, $id)){
			if($_SESSION['language']){
				foreach ($_SESSION['all_languages'] as $lang){
					if(isset($_POST['name_'.$lang]) && isset($_POST['sufix_'.$lang])){
						$this->db->executeQuery("UPDATE `{$this->table('_options_name')}` SET `name` = '{$_POST['name_'.$lang]}', `sufix` = '{$_POST['sufix_'.$lang]}' WHERE `option` = '{$id}' AND `language` = '{$lang}'");
					}
				}
			} else {
				if(isset($_POST['name']) && isset($_POST['sufix'])){
					$data = array();
					$data['name'] = $_POST['name'];
					$data['sufix'] = $_POST['sufix'];
					$this->db->updateRow($this->table('_options_name'), $data, $id, 'option');
				}
			}
			if(isset($_POST['type']) && is_numeric($_POST['type'])){
				$type = $this->db->getAllDataById('wl_input_types', $_POST['type']);
				if($type->options == 1){
					$options = array();
					foreach ($_POST as $key => $value) {
						$key = explode('_', $key);
						if($key[0] == 'option' && isset($key[1]) && is_numeric($key[1]) && $key[1] > 0) $options[] = $key[1];
					}
					if($options){
						foreach ($options as $opt) {
							$this->db->updateRow($this->table('_options_name'), array('name' => $_POST['option_'.$opt]), $opt);
						}
					}
					if($_SESSION['language']){
						if(isset($_POST['option_0_'.$_SESSION['language']]) && is_array($_POST['option_0_'.$_SESSION['language']])){
							for($i = 0; $i < count($_POST['option_0_'.$_SESSION['language']]); $i++){
								$data = array();
								$data['group'] = $id * -1;
								$data['type'] = 0;
								$data['position'] = 0;
								$data['active'] = 1;
								$this->db->insertRow($this->table('_group_options'), $data);
								$option_id = $this->db->getLastInsertedId();
								foreach ($_SESSION['all_languages'] as $lang){
									$data = array();
									$data['option'] = $option_id;
									$data['language'] = $lang;
									$data['name'] = $_POST['option_0_'.$lang][$i];
									$this->db->insertRow($this->table('_options_name'), $data);
								}
							}
						}
					} else {
						if(isset($_POST['option_0']) && is_array($_POST['option_0'])){
							foreach ($_POST['option_0'] as $option) {
								$data = array();
								$data['group'] = $id * -1;
								$data['type'] = 0;
								$data['position'] = 0;
								$data['active'] = 1;
								$this->db->insertRow($this->table('_group_options'), $data);
								$option_id = $this->db->getLastInsertedId();
								$data = array();
								$data['option'] = $option_id;
								$data['name'] = $option;
								$this->db->insertRow($this->table('_options_name'), $data);
							}
						}
					}
				}
			}
			return true;
		}
		return false;
	}

	public function deleteOption($id)
	{
		$option = $this->db->getAllDataById($this->table('_group_options'), $id);
		if($option){
			$this->db->deleteRow($this->table('_product_options'), $option->id, 'option');
			$this->db->deleteRow($this->table('_options_name'), $option->id, 'option');
			$id = $option->id * -1;
			$options = $this->db->getAllDataByFieldInArray($this->table('_group_options'), $id, 'group');
			if($options){
				foreach ($options as $opt) {
					$this->db->deleteRow($this->table('_options_name'), $opt->id, 'option');
				}
			}
			$this->db->deleteRow($this->table('_group_options'), $option->id);
			$this->db->deleteRow($this->table('_group_options'), $id, 'group');
			$this->db->executeQuery("UPDATE `{$this->table('_group_options')}` SET `position` = position - 1 WHERE `position` > '{$option->position}' AND `group` = '{$option->group}'");
			return true;
		}
		return false;
	}
	
}

?>
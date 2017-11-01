<?php

/*

 	Service "Shop Showcase 2.5"
	for WhiteLion 1.0

*/

class shopshowcase extends Controller {
				
    function _remap($method, $data = array())
    {
    	if(isset($_SESSION['alias']->name))
    		$_SESSION['alias']->breadcrumb = array($_SESSION['alias']->name => '');
        if (method_exists($this, $method))
            return $this->$method($data);
        else
        	$this->index($method);
    }

    public function index($uri)
    {
    	$this->load->smodel('shop_model');
    	$_SESSION['option']->paginator_per_page = 50;

    	if(count($this->data->url()) > 2)
		{
			$type = null;
			$url = $this->data->url();
			array_shift($url);
			$product = $this->shop_model->routeURL($url, $type, true);

			if($type == 'product' && $product)
			{
				$this->edit($product);
			}

			if($_SESSION['option']->useGroups && $type == 'group' && $product)
			{
				$this->wl_alias_model->setContent();
				
				$group = clone $product;
				unset($product);

				$group->alias_name = $_SESSION['alias']->name;
				$group->parents = array();
				if($group->parent > 0)
				{
					$list = array();
		            $groups = $this->db->getAllData($this->shop_model->table('_groups'));
		            foreach ($groups as $Group) {
		            	$list[$Group->id] = clone $Group;
		            }
					$group->parents = $this->shop_model->makeParents($list, $group->parent, $group->parents);
				}
				$this->wl_alias_model->setContent(($group->id * -1));

				$groups = $this->shop_model->getGroups($group->id, false);
				$products = $this->shop_model->getProducts($group->id, 0, false);
				if (empty($groups))
					$this->load->admin_view('products/list_view', array('group' => $group, 'products' => $products));
				else
					$this->load->admin_view('index_view', array('group' => $group, 'groups' => $groups, 'products' => $products));
			}

			$this->load->page_404();
		}
		else
		{
			$this->wl_alias_model->setContent();
			
			if($_SESSION['option']->useGroups)
			{
				$groups = $this->shop_model->getGroups(0, false);
				if (empty($groups))
				{
					$products = $this->shop_model->getProducts(-1, 0, false);
					$this->load->admin_view('products/list_view', array('products' => $products));
				}
				else
				{
					$products = $this->shop_model->getProducts(0, 0, false);
					$this->load->admin_view('index_view', array('groups' => $groups, 'products' => $products));
				}
			}
			else
			{
				$products = $this->shop_model->getProducts(-1, 0, false);
				$this->load->admin_view('products/list_view', array('products' => $products));
			}
		}
    }

	public function all()
	{
		$this->load->smodel('products_model');
		$products = $this->products_model->getProducts(-1, false);
		$this->load->admin_view('products/all_view', array('products' => $products));
	}

	public function search()
	{
		$this->load->smodel('shop_model');
		if($this->data->get('id'))
		{
			$product = $this->shop_model->getProduct($this->data->get('id'), 'id', false);
			if($product)
				$this->redirect('admin/'.$product->link);
			$this->load->admin_view('products/list_view', array('products' => false));
		}
		elseif($this->data->get('article'))
		{
			if($products = $this->shop_model->getProducts('%'.$this->makeArticle($this->data->get('article')), 0, false))
			{
				if($cooperation = $this->db->getAllDataByFieldInArray('wl_aliases_cooperation', array('alias1' => $_SESSION['alias']->id, 'type' => 'storage')))
					$this->load->admin_view('products/search_view', array('products' => $products, 'cooperation' => $cooperation));
				else
					$this->load->admin_view('products/list_view', array('products' => $products, 'search' => true));
			}
			else
				$this->load->admin_view('products/list_view', array('products' => false));
		}
	}

	private function makeArticle($article)
	{
		$article = (string) $article;
		$article = trim($article);
		$article = strtoupper($article);
		$article = str_replace('-', '', $article);
		return str_replace(' ', '-', $article);
	}
	
	public function add()
	{
		$_SESSION['alias']->breadcrumb = array($_SESSION['alias']->name => 'admin/'.$_SESSION['alias']->alias, 'Додати новий запис' => '');
		$_SESSION['alias']->name .= '. Додати новий запис';
		$this->load->admin_view('products/add_view');
	}
	
	private function edit($product)
	{
		$this->wl_alias_model->setContent($product->id);
		$_SESSION['alias']->breadcrumb = array($_SESSION['alias']->name => 'admin/'.$_SESSION['alias']->alias, 'Редагувати запис' => '');

		$groups = null;
		if($_SESSION['option']->useGroups)
		{
			$groups = $this->shop_model->getGroups(-1);
			if($_SESSION['option']->ProductMultiGroup)
			{
				$product->group = array();
				if($activeGroups = $this->db->getAllDataByFieldInArray($this->shop_model->table('_product_group'), $product->id, 'product'))
					foreach ($activeGroups as $ag) {
						$product->group[] = $ag->group;
						foreach ($groups as $group) {
							if($group->id == $ag->group)
							{
								$group->product_position = $ag->position;
								$group->product_position_max = $this->db->getCount($this->shop_model->table('_product_group'), $group->id, 'group');
								break;
							}
						}
					}
			}
		}

		$similarProducts = null;
		$similars = $this->db->getAllDataByFieldInArray($this->shop_model->table('_products_similar'), array('product' => $product->id));

		if($similars)
		{
			foreach ($similars as $similar) {
				$where_ntkd['alias'] = $_SESSION['alias']->id;
				$where_ntkd['content'] = '#p.id';
				if($_SESSION['language']) $where_ntkd['language'] = $_SESSION['language'];
				$this->db->select('s_shopshowcase_products as p', 'id, alias, article, price', $similar->similar_product);
				$this->db->join('wl_ntkd', 'name as product_name', $where_ntkd);
				$similarProducts['products'][] = $this->db->get();
			}
		}

		$this->load->admin_view('products/edit_view', array('product' => $product, 'groups' => $groups, 'similarProducts' => $similarProducts));
	}
	
	public function save()
	{
		if(isset($_POST['id']) && is_numeric($_POST['id']))
		{
			$this->load->smodel('products_model');
			if($_POST['id'] == 0)
			{
				$link = '';
				if($id = $this->products_model->add($link))
				{
					if(!empty($_FILES['photo']['name']))
						$this->savephoto('photo', $id, $this->data->latterUAtoEN($name));
					$this->redirect("admin/{$_SESSION['alias']->alias}/{$link}");
				}
				$this->redirect();
			}
			else
			{
				$link = $this->products_model->save($_POST['id']);
				$this->products_model->saveProductOptios($_POST['id']);
				if($_SESSION['option']->ProductMultiGroup == 0)
				{
					$position = explode(' ', $_SESSION['option']->productOrder);
					if($position[0] == 'position' && $_POST['position_old'] != $this->data->post('position') && $_POST['group'] == $_POST['group_old'])
					{
						$this->load->model('wl_position_model');
						$this->wl_position_model->table = $this->products_model->table();
						$this->wl_position_model->where = '`wl_alias` = '.$_SESSION['alias']->id;
						if($_SESSION['option']->useGroups > 0)
							$this->wl_position_model->where .= " AND `group` = '{$_POST['group']}'";
						$this->wl_position_model->change($_POST['id'], $_POST['position']);
					}
				}
				elseif(!empty($this->products_model->multigroup_new_position))
				{
					$this->load->model('wl_position_model');
					$this->wl_position_model->table = $this->products_model->table('_product_group');
					foreach ($this->products_model->multigroup_new_position as $key) {
						$this->wl_position_model->where = "`group` = '{$key->group}'";
						$this->wl_position_model->change($key->id, $key->position);
					}
				}

				if(isset($_POST['to']) && $_POST['to'] == 'new')
					$this->redirect("admin/{$_SESSION['alias']->alias}/add");
				elseif(isset($_POST['to']) && $_POST['to'] == 'category')
				{
					$link = 'admin/'.$_SESSION['alias']->alias.'/'.$link;
					$link = explode('/', $link);
					array_pop ($link);
					$link = implode('/', $link);
					$this->redirect($link);
				}

				$_SESSION['notify'] = new stdClass();
				$_SESSION['notify']->success = 'Дані успішно оновлено!';
				$this->redirect('admin/'.$_SESSION['alias']->alias.'/'.$link.'#tab-main');
			}
		}
	}

	public function markup()
	{
		$markups = $this->db->getAllData('s_shopshowcase_markup');
		$this->load->admin_view('products/markup_view', array('markups' => $markups));
	}

	public function markup_save()
	{
		if($_POST){
			foreach ($_POST as $key => $value) {
				$data = array();
				$data['from'] = $value['from'];
				$data['to'] = $value['to'];
				$data['value'] = $value['value'];
				$this->db->updateRow('s_shopshowcase_markup', $data, $key);
			}
		}

		$this->redirect();
	}

	public function markup_add()
	{
		if($_POST)
		{
			$data = array();
			$data['from'] = $this->data->post('from');
			$data['to'] = $this->data->post('to');
			$data['value'] = $this->data->post('value');
			$this->db->insertRow('s_shopshowcase_markup', $data);

			$this->redirect('admin/'.$_SESSION['alias']->alias.'/markup');
		}
		else 
			$this->load->admin_view('products/markup_add_view');
	}

	public function markup_delete()
	{
		$res = array('result' => false);

		$id = $this->data->post('id');

		if($this->db->deleteRow('s_shopshowcase_markup', $id))
			$res['result'] = true;

		$this->json($res);
	}

	public function saveOption()
	{
		$this->load->smodel('products_model');
		$name = $this->data->post('option');
		$_POST[$name] = $this->data->post('data');
		$this->products_model->saveProductOptios($_POST['id']);
	}
	
	public function delete()
	{
		if(isset($_POST['id']) && is_numeric($_POST['id']))
		{
			$this->load->smodel('products_model');
			$link = $this->products_model->delete($_POST['id']);
			$_SESSION['notify'] = new stdClass();
			$_SESSION['notify']->success = $_SESSION['admin_options']['word:product_to_delete'].' успішно видалено!';
			$this->redirect("admin/{$_SESSION['alias']->alias}/{$link}");
		}
	}
	
	public function change_position()
	{
		$res = array('result' => false);
		if(isset($_POST['id']) && is_numeric($_POST['id']) && is_numeric($_POST['position']))
		{
			$this->load->smodel('products_model');
			$this->load->model('wl_position_model');

			$this->wl_position_model->table = $this->products_model->table();
			$this->wl_position_model->where = '`wl_alias` = '.$_SESSION['alias']->id;
			$newposition = $_POST['position'] + 1;

			$order = 'ASC';
			if($_SESSION['option']->productOrder == 'position DESC')
				$order = 'DESC';
			
			if($_SESSION['option']->useGroups > 0)
			{
				if($_SESSION['option']->ProductMultiGroup)
				{
					if($position = $this->db->getAllDataById($this->products_model->table('_product_group'), $_POST['id']))
					{
						$this->wl_position_model->table = $this->products_model->table('_product_group');
						$this->wl_position_model->where = "`group` = '{$position->group}'";
						if($order == 'DESC')
						{
							$all = $this->db->getCount($this->products_model->table('_product_group'), $position->group, 'group');
							if($all > 0)
								$newposition = $all + 1 - $newposition;
						}
					}
					else
						$this->wl_position_model->table = '';
				}
				else
				{
					if($product = $this->db->getAllDataById($this->products_model->table(), $_POST['id']))
					{
						$this->wl_position_model->where .= " AND `group` = '{$product->group}'";
						if($order == 'DESC')
						{
							$all = $this->db->getCount($this->products_model->table(), $product->group, 'group');
							if($all > 0)
								$newposition = $all + 1 - $newposition;
						}
					}
					else
						$this->wl_position_model->table = '';
				}
			}
			
			if($this->wl_position_model->change($_POST['id'], $newposition))
				$res['result'] = true;
		}
		$this->load->json($res);
	}

	public function changeAvailability()
	{
		$res = array('result' => false);
		if(isset($_POST['availability']) && is_numeric($_POST['availability']) && isset($_POST['id']) && is_numeric($_POST['id']))
		{
			if($this->db->updateRow($_SESSION['service']->table.'_products', array('availability' => $_POST['availability']), $_POST['id']))
				$res['result'] = true;
		}
		$this->load->json($res);
	}

	public function changeActive()
	{
		$res = array('result' => false);
		if(isset($_POST['active']) && is_numeric($_POST['active']) && isset($_POST['id']) && is_numeric($_POST['id']))
		{
			$table = $_SESSION['service']->table.'_products';
			$where = array('id' => $_POST['id']);
			if($_SESSION['option']->useGroups == 1 && $_SESSION['option']->ProductMultiGroup == 1 && isset($_POST['group']) && is_numeric($_POST['group']) && $_POST['group'] > 0) {
				$table = $_SESSION['service']->table.'_product_group';
				$where = array('product' => $_POST['id'], 'group' => $_POST['group']);
			}
			if($this->db->updateRow($table, array('active' => $_POST['active']), $where))
				$res['result'] = true;
		}
		$this->load->json($res);
	}

	public function groups()
	{
		$this->load->smodel('groups_model');
		$id = $this->data->uri(3);
		$id = explode('-', $id);
		if(is_numeric($id[0]))
			$this->edit_group($id[0]);
		else
		{
			$groups = $this->groups_model->getGroups(-1, false);
			$_SESSION['alias']->name = 'Групи '.$_SESSION['admin_options']['word:products_to_all'];
			$_SESSION['alias']->breadcrumb = array('Групи' => '');
			$this->load->admin_view('groups/index_view', array('groups' => $groups));
		}
	}

	public function add_group()
	{
		$this->load->smodel('groups_model');
		$groups = $this->groups_model->getGroups(-1);
		$_SESSION['alias']->name = $_SESSION['admin_options']['word:group_add'];
		$_SESSION['alias']->breadcrumb = array('Групи' => 'admin/'.$_SESSION['alias']->alias.'/groups', $_SESSION['admin_options']['word:group_add'] => '');
		$this->load->admin_view('groups/add_view', array('groups' => $groups));
	}

	private function edit_group($id)
	{
		if($group = $this->groups_model->getById($id, false))
		{
			$this->wl_alias_model->setContent(($group->id * -1));
			$groups = $this->groups_model->getGroups(-1);
			$_SESSION['alias']->breadcrumb = array('Групи' => 'admin/'.$_SESSION['alias']->alias.'/groups', 'Редагувати групу' => '');
			$this->load->admin_view('groups/edit_view', array('group' => $group, 'groups' => $groups));
		}
		else
			$this->load->page_404();
	}

	public function save_group()
	{
		if(isset($_POST['id']) && is_numeric($_POST['id']))
		{
			$this->load->smodel('groups_model');
			$_SESSION['notify'] = new stdClass();

			if($_POST['id'] == 0)
			{
				$alias = '';
				if($id = $this->groups_model->add($alias))
				{
					if(!empty($_FILES['photo']['name']) && $alias)
						$this->savephoto('photo', -$id, $alias);
					$_SESSION['notify']->success = 'Групу успішно додано! Продовжіть наповнення сторінки.';
					$this->redirect('admin/'.$_SESSION['alias']->alias.'/groups/'.$id);
				}
			}
			else
			{
				if($this->groups_model->save($_POST['id']))
					$_SESSION['notify']->success = 'Дані успішно оновлено!';
				else
					$_SESSION['notify']->errors = 'Сталася помилка, спробуйте ще раз!';
				$this->redirect('#tab-main');
			}
		}
	}

	public function delete_group()
	{
		if(isset($_POST['id']) && is_numeric($_POST['id']))
		{
			$this->load->smodel('groups_model');
			$this->groups_model->delete($_POST['id']);
			$this->redirect("admin/{$_SESSION['alias']->alias}/groups");
		}
	}

	public function change_group_position()
	{
		if(isset($_POST['id']) && is_numeric($_POST['id']) && is_numeric($_POST['position']))
		{
			$this->load->smodel('groups_model');
			$this->load->model('wl_position_model');
			
			$group = $this->db->getAllDataById($this->groups_model->table(), $_POST['id']);
			if($group) {
				$parent = $group->parent;
			}
			
			$this->wl_position_model->table = $this->groups_model->table();
			$this->wl_position_model->where = '`wl_alias` = '.$_SESSION['alias']->id;
			if($parent >= 0) {
				$this->wl_position_model->where .= " AND `parent` = '{$parent}'";
			}
			if($this->wl_position_model->change($_POST['id'], $_POST['position'])) {
				$this->redirect();
			}
		}
		$this->load->page_404();
	}

	public function options()
	{
		$this->load->smodel('groups_model');
		$this->load->smodel('options_model');

		$url = $this->data->url();
		$id = end($url);
		$id = explode('-', $id);
		$id = $id[0];

		if(is_numeric($id))
		{
			$option = $this->db->getAllDataById($this->options_model->table(), $id);
			if($option)
			{
				$_SESSION['alias']->name = 'Редагувати властивість "'.$_SESSION['admin_options']['word:option'].'"';
				$_SESSION['alias']->breadcrumb = array('Властивості' => 'admin/'.$_SESSION['alias']->alias.'/options', 'Редагувати властивість' => '');
				$this->load->admin_view('options/edit_view', array('option' => $option));
			}
			else
				$this->load->page404();
		}
		elseif($id != '' && $id != $_SESSION['alias']->alias)
		{
			if($_SESSION['option']->useGroups)
			{
				$group = false;
				$parent = 0;
				array_shift($url);
				array_shift($url);
				array_shift($url);
				if($url)
				{
					foreach ($url as $uri) {
						$group = $this->groups_model->getByAlias($uri, $parent);
						if($group)
							$parent = $group->id;
						else
							$group = false;
					}
				}

				if($group)
				{
					$group->alias_name = $_SESSION['alias']->name;
					$group->parents = array();
					if($group->parent > 0)
					{
						$list = array();
			            $groups = $this->db->getAllData($this->groups_model->table());
			            foreach ($groups as $Group) {
			            	$list[$Group->id] = clone $Group;
			            }
						$group->parents = $this->groups_model->makeParents($list, $group->parent, $group->parents);
					}
					$this->wl_alias_model->setContent(($group->id * -1));
					$group->group_name = $_SESSION['alias']->name;

					$groups = $this->groups_model->getGroups($group->id, false);
					$options = $this->options_model->getOptions($group->id, false);

					$_SESSION['alias']->name = $_SESSION['alias']->name .'. Керування властивостями ' . $_SESSION['admin_options']['word:products_to_all'];
					$_SESSION['alias']->breadcrumb = array('Властивості' => '');

					$this->load->admin_view('options/index_view', array('group' => $group, 'groups' => $groups, 'options' => $options));
				}
				else
				{
					$groups = $this->groups_model->getGroups(0, false);
					$options = $this->options_model->getOptions(0, false);

					$_SESSION['alias']->name = 'Керування властивостями ' . $_SESSION['admin_options']['word:products_to_all'];
					$_SESSION['alias']->breadcrumb = array('Властивості' => '');

					$this->load->admin_view('options/index_view', array('options' => $options, 'groups' => $groups));
				}
			}
			else
			{
				$options = $this->options_model->getOptions(0, false);

				$_SESSION['alias']->name = 'Керування властивостями ' . $_SESSION['admin_options']['word:products_to_all'];
				$_SESSION['alias']->breadcrumb = array('Властивості' => '');

				$this->load->admin_view('options/index_view', array('options' => $options));	
			}
		}
		$this->load->page_404();
	}

	public function add_option()
	{
		$_SESSION['alias']->name = $_SESSION['admin_options']['word:option_add'];
		$_SESSION['alias']->breadcrumb = array('Властивості' => 'admin/'.$_SESSION['alias']->alias.'/options', 'Додати властивість' => '');
		$this->load->admin_view('options/add_view');
	}

	public function save_option()
	{
		if(isset($_POST['id']) && is_numeric($_POST['id']))
		{
			$_SESSION['notify'] = new stdClass();
			$this->load->smodel('options_model');
			if($_POST['id'] == 0){
				$id = $this->options_model->add_option();
				if($id){
					$_SESSION['notify']->success = 'Властивість успішно додано!';
					$this->redirect('admin/'.$_SESSION['alias']->alias.'/options/'.$id);
				}
			} else {
				if($this->options_model->saveOption($_POST['id'])){
					$_SESSION['notify']->success = 'Властивість успішно оновлено!';

					if(!empty($_FILES['photo']['name']))
					{
						foreach ($_FILES['photo']['name'] as $key => $value) {
							if(!empty($value))
							{
								$path = IMG_PATH;
					            $path = substr($path, strlen(SITE_URL));
					            $path = substr($path, 0, -1);
					            if(!is_dir($path))
					            	mkdir($path, 0777);
					            $path .= '/'.$_SESSION['option']->folder;
					            if(!is_dir($path))
					            	mkdir($path, 0777);
					            $path .= '/options';
					            if(!is_dir($path))
					            	mkdir($path, 0777);
								$path .= '/'.$this->data->post('id').'-'.$this->data->post('alias');
					            if(!is_dir($path))
					            	mkdir($path, 0777);
					            $path .= '/';

					            $fileName = $this->db->select('s_shopshowcase_options', 'alias', $key)->get();

					            $this->load->library('image');
								$this->image->uploadArray('photo', $key, $path, $key.'_'.$fileName->alias);
								$fileName = $key.'_'.$fileName->alias.'.'.$this->image->getExtension();

								$this->db->updateRow('s_shopshowcase_options', array('photo' => $fileName), $key);
				        	}
						}
					}

					$this->redirect();
				}
			}
		}
	}

	public function delete_option()
	{
		if(isset($_POST['id']) && is_numeric($_POST['id']) && $_POST['id'] > 0)
		{
			$this->load->smodel('options_model');
			if($this->options_model->deleteOption($_POST['id'])){
				$_SESSION['notify'] = new stdClass();
				$_SESSION['notify']->success = 'Властивість успішно видалено!';
				$this->redirect('admin/'.$_SESSION['alias']->alias.'/options');
			}
		}
	}

	public function change_option_position()
	{
		if(isset($_POST['id']) && is_numeric($_POST['id']) && is_numeric($_POST['position']))
		{
			$this->load->smodel('options_model');
			$this->load->model('wl_position_model');
			
			$option = $this->db->getAllDataById($this->options_model->table('_options'), $_POST['id']);
			if($option) {
				$parent = $option->group;
			}
			
			$this->wl_position_model->table = $this->options_model->table();
			$this->wl_position_model->where = '`wl_alias` = '.$_SESSION['alias']->id;
			if($parent >= 0) {
				$this->wl_position_model->where .= " AND `group` = '{$parent}'";
			}
			if($this->wl_position_model->change($_POST['id'], $_POST['position'])) {
				$this->redirect();
			}
		}
		$this->load->page_404();
	}

	public function deleteOptionProperty()
	{
		if(isset($_POST['id']) && is_numeric($_POST['id']))
		{
			$this->load->smodel('options_model');
			if($this->db->deleteRow($this->options_model->table(), $_POST['id']) && $this->db->deleteRow($this->options_model->table('_options_name'), $_POST['id'], 'option'))
			{
				if(isset($_POST['json']) && $_POST['json']){
					$this->load->json(array('result' => true));
				} else {
					$this->redirect();
				}
			}
		}
	}

	public function deletePropertyPhoto()
	{
		if(isset($_POST['id']) && is_numeric($_POST['id']))
		{
			$id = $this->data->post('id');
			$path = $this->data->post('path');

			$path = IMG_PATH.$path;
            $path = substr($path, strlen(SITE_URL));

            

			@unlink($path);
			$this->db->updateRow('s_shopshowcase_options', array('photo' => 0), $id);
		}
	}

	private function savephoto($name_field, $content, $name)
	{
		if(!empty($_FILES[$name_field]['name']) && $_SESSION['option']->folder)
		{
			$path = IMG_PATH;
            $path = substr($path, strlen(SITE_URL));
            $path = substr($path, 0, -1);
            if(!is_dir($path))
            	mkdir($path, 0777);
            $path .= '/'.$_SESSION['option']->folder;
            if(!is_dir($path))
            	mkdir($path, 0777);
			$path .= '/'.$content;
            if(!is_dir($path))
            	mkdir($path, 0777);
            $path .= '/';

            $data['alias'] = $_SESSION['alias']->id;
            $data['content'] = $content;
            $data['file_name'] = $data['title'] = '';
            $data['author'] = $_SESSION['user']->id;
            $data['date_add'] = time();
            $data['position'] = 1;
            $this->db->insertRow('wl_images', $data);
            $photo_id = $this->db->getLastInsertedId();
            $name .= '-' . $photo_id;

            $this->load->library('image');
			$this->image->upload($name_field, $path, $name);
			$extension = $this->image->getExtension();
			$this->image->save();
			if($extension && $this->image->getErrors() == '')
			{
				if($sizes = $this->db->getAliasImageSizes())
				{
					foreach ($sizes as $resize) {
                        if($resize->prefix == '')
                        {
                            if($this->image->loadImage($path, $name, $extension))
                            {
                                if(in_array($resize->type, array(1, 11, 12)))
                                    $this->image->resize($resize->width, $resize->height, $resize->quality, $resize->type);
                                if(in_array($resize->type, array(2, 21, 22)))
                                    $this->image->preview($resize->width, $resize->height, $resize->quality, $resize->type);
                                $this->image->save($resize->prefix);
                            }
                        }
                    }
				}
				$name .= '.'.$extension;
                $this->db->updateRow('wl_images', array('file_name' => $name), $photo_id);
                return $name;
			}			
		}
		return false;
	}

	public function search_history()
	{
		$_SESSION['alias']->breadcrumb = array($_SESSION['alias']->name => 'admin/'.$_SESSION['alias']->alias, 'Історія пошуку' => '');
		$_SESSION['alias']->name .= '. Історія пошуку';

		$this->db->select('s_shopshowcase_search_history as psh');
        $this->db->join('s_shopshowcase_products', 'article', '#psh.product_id');
        $this->db->join('wl_users', 'name as user_name, email as user_email', '#psh.user');
        $this->db->order('last_view DESC');

        if(isset($_SESSION['option']->paginator_per_page) && $_SESSION['option']->paginator_per_page > 0)
		{
			$start = 0;
			if(isset($_GET['per_page']) && is_numeric($_GET['per_page']) && $_GET['per_page'] > 0)
				$_SESSION['option']->paginator_per_page = $_GET['per_page'];
			if(isset($_GET['page']) && is_numeric($_GET['page']) && $_GET['page'] > 1)
				$start = ($_GET['page'] - 1) * $_SESSION['option']->paginator_per_page;
			$this->db->limit($start, $_SESSION['option']->paginator_per_page);
		}

        $search_history = $this->db->get('array', false);
        $_SESSION['option']->paginator_total = $this->db->get('count');

        $this->load->admin_view('search_history_view', array('search_history' => $search_history));
	}

	public function __get_Search($content)
    {
    	$this->load->smodel('shop_search_model');
    	return $this->shop_search_model->getByContent($content, true);
    }

    public function addSimilarProduct()
    {
        $articleId = $this->data->post('article');
        $productId = $this->data->post('product');

        $articleInfo =  $this->db->select('s_shopshowcase_products', 'id', array('article' => $articleId))->get();

        if($articleInfo && $productId != $articleInfo->id)
        {
        	$this->db->executeQuery("INSERT INTO s_shopshowcase_products_similar(id,product, similar_product)VALUES(NULL,'{$productId}', '{$articleInfo->id}') ON DUPLICATE KEY UPDATE product = VALUES (product),similar_product = VALUES(similar_product)");
        	$this->db->executeQuery("INSERT INTO s_shopshowcase_products_similar(id,product, similar_product)VALUES(NULL,'{$articleInfo->id}', '{$productId}') ON DUPLICATE KEY UPDATE product = VALUES (product),similar_product = VALUES(similar_product)");
        }

        $this->redirect("#tab-similar");
    }
	
	public function deleteSimilarProduct()
	{
		$productId = $this->data->post('productId');
		$similarProduct = $this->data->post('similarProduct');

		$this->db->executeQuery("DELETE FROM `s_shopshowcase_products_similar` WHERE (product, similar_product)  IN(('{$similarProduct}','{$productId}'),('{$productId}','{$similarProduct}'))");
	}

	public function saveSimilarText()
	{
		$group = $this->data->post('group');

		if($group != 0)
		{
			$text = htmlentities($_POST['text'], ENT_QUOTES, 'utf-8');

			$products = $this->db->select('s_shopshowcase_products', 'id, wl_alias', array('group' => $group))->get('array');

			if($products)
			{
				foreach($products as $product)
				{
					$where_ntkd = array();
					$where_ntkd['alias'] = $product->wl_alias; 
					$where_ntkd['content'] = $product->id; 
					if($_SESSION['language'] && $_POST['language']) $where_ntkd['language'] = $this->data->post('language');
					if(!isset($_POST['all'])) $where_ntkd['text'] = '';
					$this->db->updateRow("wl_ntkd", array('text' => $text), $where_ntkd);
				}
				
			}
			
		}
		
		$this->redirect();
	}

	public function __getRobotKeyWords($content = 0)
    {
    	$words = array();
    	$this->load->smodel('shop_model');
    	if($content > 0)
    	{
    		$this->db->select($this->shop_model->table('_products'), 'id', $_SESSION['alias']->id, 'wl_alias');
    		$this->db->limit(1);
    		if($product = $this->db->get())
    		{
	    		if($product = $this->shop_model->getProduct($product->id, 'id'))
	    		{
	    			foreach ($product as $key => $value) {
	    				if(!is_object($value) && !is_array($value))
		    				$words[] = '{product.'.$key.'}';
	    			}
	    		}
	    	}
    		else
    			$words = array('{product.id}', '{product.name}', '{product.wl_alias}', '{product.article}', '{product.alias}', '{product.group}', '{product.price}', '{product.currency}', '{product.availability}', '{product.active}', '{product.position}', '{product.author_add}', '{product.date_add}', '{product.author_edit}', '{product.date_edit}', '{product.author_add_name}', '{product.author_edit_name}');
    	}
    	elseif($content < 0)
    	{
    		$this->db->select($this->shop_model->table('_groups'), 'alias', $_SESSION['alias']->id, 'wl_alias');
    		$this->db->limit(1);
    		if($group = $this->db->get())
    		{
	    		if($group = $this->shop_model->getGroupByAlias($group->alias))
	    		{
	    			foreach ($group as $key => $value) {
	    				if(!is_object($value) && !is_array($value))
		    				$words[] = '{group.'.$key.'}';
	    			}
	    		}
	    	}
    		else
    			$words = array('{group.id}', '{group.name}', '{group.wl_alias}', '{group.parent}', '{group.alias}', '{group.active}', '{group.position}', '{group.author_add}', '{group.date_add}', '{group.author_edit}', '{group.date_edit}', '{group.user_name}');
    	}
    	return $words;
    }
}

?>
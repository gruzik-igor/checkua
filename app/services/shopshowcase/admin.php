<?php

/*

 	Service "Shop Showcase 2.1"
	for WhiteLion 1.0

*/

class shopshowcase extends Controller {
				
    function _remap($method, $data = array())
    {
    	$_SESSION['alias']->breadcrumb = array($_SESSION['alias']->name => '');
        if (method_exists($this, $method)) {
        	if(empty($data)) $data = null;
            return $this->$method($data);
        } else {
        	$this->index($method);
        }
    }

    public function index($uri)
    {
    	$this->load->smodel('shop_model');
    	$_SESSION['option']->paginator_per_page = 0;
		
		$url = $this->data->url();
		$id = end($url);
		$id = explode($_SESSION['option']->idExplodeLink, $id);
		$id = $id[0];

		if(is_numeric($id))
		{
			$this->edit($id);
		}
		elseif($id != '' && $id != $_SESSION['alias']->alias)
		{
			if($_SESSION['option']->useGroups)
			{
				$group = false;
				$parent = 0;
				array_shift($url);
				foreach ($url as $uri) {
					$group = $this->shop_model->getGroupByAlias($uri, $parent);
					if($group){
						$parent = $group->id;
					} else $group = false;
				}

				if($group){
					$group->alias_name = $_SESSION['alias']->name;
					$group->parents = array();
					if($group->parent > 0){
						$list = array();
			            $groups = $this->db->getAllData($this->shop_model->table('_groups'));
			            foreach ($groups as $Group) {
			            	$list[$Group->id] = clone $Group;
			            }
						$group->parents = $this->shop_model->makeParents($list, $group->parent, $group->parents);
					}
					$this->load->model('wl_ntkd_model');
					$this->wl_ntkd_model->setContent(($group->id * -1));

					$list = $this->shop_model->getGroups($group->id, false);
					if (empty($list) || $_SESSION['option']->ProductMultiGroup == 1) {
						$list = $this->shop_model->getProducts($group->id, 0, false);
						$this->load->admin_view('products/list_view', array('group' => $group, 'products' => $list));
					} else {
						$this->load->admin_view('index_view', array('group' => $group, 'groups' => $list));
					}
				} else $this->load->page_404();
			} else $this->load->page_404();
		} else {
			if($_SESSION['option']->useGroups){
				$list = $this->shop_model->getGroups(0, false);
				if (empty($list) || $_SESSION['option']->ProductMultiGroup == 1) {
					$list = $this->shop_model->getProducts(-1, 0, false);
					$this->load->admin_view('products/list_view', array('products' => $list));
				} else {
					$this->load->admin_view('index_view', array('groups' => $list));
				}
				
			} else {
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
	
	public function add()
	{
		$_SESSION['alias']->breadcrumb = array($_SESSION['alias']->name => 'admin/'.$_SESSION['alias']->alias, 'Додати новий запис' => '');
		$_SESSION['alias']->name .= '. Додати новий запис';
		$this->load->admin_view('products/add_view');
	}
	
	private function edit($id = 0){
		$this->load->smodel('shop_model');
		$product = $this->shop_model->getProductById($id);
		if($product){
			$_SESSION['alias']->breadcrumb = array($_SESSION['alias']->name => 'admin/'.$_SESSION['alias']->alias, 'Редагувати запис' => '');
			$_SESSION['alias']->name = 'Редагувати '.$product->name;

			$groups = null;
			if($_SESSION['option']->useGroups){
				$groups = $this->shop_model->getGroups();
				if($_SESSION['option']->ProductMultiGroup){
					$activeGroups = $this->db->getAllDataByFieldInArray($this->shop_model->table('_product_group'), $product->id, 'product');
					$product->group = array();
					if($activeGroups){
						foreach ($activeGroups as $ag) {
							$product->group[] = $ag->group;
						}
					}
				}
			}

			$this->load->admin_view('products/edit_view', array('product' => $product, 'groups' => $groups));
		} else $this->load->page_404();
	}
	
	public function save()
	{
		if(isset($_POST['id']) && is_numeric($_POST['id']))
		{
			$this->load->smodel('products_model');
			if($_POST['id'] == 0)
			{
				$link = '';
				$id = $this->products_model->add($link);
				if($id){
					$path = IMG_PATH.$_SESSION['alias']->alias.'/'.$id;
					$path = substr($path, strlen(SITE_URL));
					if(!is_dir($path)){
						mkdir($path, 0777);
					}
					if(!empty($_FILES['photo']['name'])) {
						$data['product'] = $id;
						$data['user'] = $_SESSION['user']->id;
						$data['date'] = time();
						$data['main'] = time();
						$this->db->insertRow($this->products_model->table('_product_photos'), $data);
						$photo_id = $this->db->getLastInsertedId();
						$photo = $link . '-' . $photo_id;
						$extension = $this->savephoto('photo', $path.'/', $photo);
						if($extension){
							$photo .= '.'.$extension;
							$this->db->updateRow($this->products_model->table('_products'), array('photo' => $photo), $id);
							$this->db->updateRow($this->products_model->table('_product_photos'), array('name' => $photo), $photo_id);
						}
					}
					$this->redirect("admin/{$_SESSION['alias']->alias}/{$link}");
				}
			}
			else
			{
				$this->products_model->save($_POST['id']);
				$this->products_model->saveProductOptios($_POST['id']);

				if(isset($_POST['to']) && $_POST['to'] == 'new'){
					$this->redirect("admin/{$_SESSION['alias']->alias}/add");
				} elseif(isset($_POST['to']) && $_POST['to'] == 'category') {
					$link = 'admin/'.$_SESSION['alias']->alias;
					$product = $this->products_model->getById($_POST['id']);
					$product->link = explode('/', $product->link);
					array_pop ($product->link);
					if(!empty($product->link)){
						$product->link = implode('/', $product->link);
						$link .= '/'.$product->link;
					}
					$this->redirect($link);
				}

				$_SESSION['notify'] = new stdClass();
				$_SESSION['notify']->success = 'Дані успішно оновлено!';
				$this->redirect();
			}
		}
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
	
	public function changeposition()
	{
		if(isset($_POST['id']) && is_numeric($_POST['id']) && is_numeric($_POST['position']))
		{
			$this->load->smodel('products_model');
			$this->load->model('wl_position_model');
			
			if($_SESSION['option']->useGroups > 0 && $_SESSION['option']->ProductMultiGroup == 0)
			{
				$product = $this->db->getAllDataById($this->products_model->table(), $_POST['id']);
				if($product) {
					$this->wl_position_model->where = "`group` = '{$product->group}'";
				}
			}
			
			$this->wl_position_model->table = $this->products_model->table();
			if($this->wl_position_model->change($_POST['id'], $_POST['position'])) {
				$this->redirect();
			}
		}
	}

	function changeGroup(){
		$res = array('result' => false);
		if($this->userCan($_SESSION['alias']->alias)){
			if(isset($_POST['group']) && is_numeric($_POST['group']) && isset($_POST['id']) && is_numeric($_POST['id'])){
				if($this->db->updateRow($_SESSION['service']->table.'_products'.$_SESSION['alias']->table, array('group' => $_POST['group']), $_POST['id'])){
					$res['result'] = true;
				}
			}
		}
		header('Content-type: application/json');
		echo json_encode($res);
		exit;
	}

	function changeAvailability(){
		$res = array('result' => false);
		if(isset($_POST['availability']) && is_numeric($_POST['availability']) && isset($_POST['id']) && is_numeric($_POST['id'])){
			if($this->db->updateRow($_SESSION['service']->table.'_products'.$_SESSION['alias']->table, array('availability' => $_POST['availability']), $_POST['id'])){
				$res['result'] = true;
			}
		}
		header('Content-type: application/json');
		echo json_encode($res);
		exit;
	}

	public function groups()
	{
		$this->load->smodel('groups_model');
		$id = $this->data->uri(3);
		$id = explode('-', $id);
		if(is_numeric($id[0]))
		{
			$this->edit_group($id[0]);
		}
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
		$group = $this->groups_model->getById($id, false);
		if($group)
		{
			$groups = $this->groups_model->getGroups(-1);
			$_SESSION['alias']->name = 'Редагувати групу "'.$group->name.'"';
			$_SESSION['alias']->breadcrumb = array('Групи' => 'admin/'.$_SESSION['alias']->alias.'/groups', 'Редагувати групу' => '');
			$this->load->admin_view('groups/edit_view', array('group' => $group, 'groups' => $groups));
		}
		$this->load->page_404();
	}

	public function save_group(){
		if(isset($_POST['id']) && is_numeric($_POST['id']))
		{
			$this->load->smodel('groups_model');
			$_SESSION['notify'] = new stdClass();

			$path = IMG_PATH.$_SESSION['option']->folder.'/groups/';
			$path = substr($path, strlen(SITE_URL));
			if(!is_dir($path)) {
				mkdir($path, 0777);
			}

			if($_POST['id'] == 0)
			{
				$photo = false;
				if(!empty($_FILES['photo']['name'])) {
					$photo = true;
				}
				$id = $this->groups_model->add($photo);
				if($id)
				{
					if(!empty($_FILES['photo']['name'])) {
						$this->savephoto('photo', $path, $id);
					}
					$_SESSION['notify']->success = 'Групу успішно додано! Продовжіть наповнення сторінки.';
					$this->redirect('admin/'.$_SESSION['alias']->alias.'/groups/'.$id);
				}
			}
			else
			{
				if($this->groups_model->save($_POST['id']))
				{
					if(!empty($_FILES['photo']['name'])) {
						$this->savephoto('photo', $path, $_POST['id']);
					}
					$_SESSION['notify']->success = 'Дані успішно оновлено!';
				} else {
					$_SESSION['notify']->errors = 'Сталася помилка, спробуйте ще раз!';
				}
				$this->redirect();
			}
		}
	}

	public function delete_group(){
		if(isset($_POST['id']) && is_numeric($_POST['id'])){
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
			if($parent >= 0) {
				$this->wl_position_model->where = "`parent` = '{$parent}'";
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
			{
				$this->load->page404();
			}
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
				if($url){
					foreach ($url as $uri) {
						$group = $this->groups_model->getByAlias($uri, $parent);
						if($group){
							$parent = $group->id;
						} else $group = false;
					}
				}

				if($group){
					$group->alias_name = $_SESSION['alias']->name;
					$group->parents = array();
					if($group->parent > 0){
						$list = array();
			            $groups = $this->db->getAllData($this->groups_model->table());
			            foreach ($groups as $Group) {
			            	$list[$Group->id] = clone $Group;
			            }
						$group->parents = $this->groups_model->makeParents($list, $group->parent, $group->parents);
					}
					$this->load->model('wl_ntkd_model');
					$this->wl_ntkd_model->setContent(($group->id * -1));

					$groups = $this->groups_model->getGroups($group->id, false);
					$options = $this->options_model->getOptions($group->id, false);

					$_SESSION['alias']->name = $_SESSION['alias']->name .'. Керування властивостями ' . $_SESSION['admin_options']['word:products_to_all'];
					$_SESSION['alias']->breadcrumb = array('Властивості' => '');

					$this->load->admin_view('options/index_view', array('group' => $group, 'groups' => $groups, 'options' => $options));

				} else {
					$groups = $this->groups_model->getGroups(0, false);
					$options = $this->options_model->getOptions(0, false);

					$_SESSION['alias']->name = 'Керування властивостями ' . $_SESSION['admin_options']['word:products_to_all'];
					$_SESSION['alias']->breadcrumb = array('Властивості' => '');

					$this->load->admin_view('options/index_view', array('options' => $options, 'groups' => $groups));
				}
			} else {
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
			
			$option = $this->db->getAllDataById($this->options_model->table('_group_options'), $_POST['id']);
			if($option) {
				$parent = $option->group;
			}
			
			$this->wl_position_model->table = $this->options_model->table();
			if($parent >= 0) {
				$this->wl_position_model->where = "`group` = '{$parent}'";
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

	// --- photo
	public function photo_add()
	{
		$res = array();
		$id = $this->data->uri(3);
		if(is_numeric($id)){
			$path = IMG_PATH.$_SESSION['option']->folder.'/'.$id;
			$name_field = 'photos';
			$error = 0;
			if(strlen(IMG_PATH) > strlen(SITE_URL)) $path = substr($path, strlen(SITE_URL));
			if(!is_dir($path)){
				if(mkdir($path, 0777) == false){
					$error++;
					$res['error'] = 'Error create dir ' . $path;
				} 
			}
			$path = IMG_PATH.$_SESSION['option']->folder.'/'.$id.'/';

			$this->load->smodel('shop_model');
			$product = $this->db->getAllDataById($this->shop_model->table('_products'), $id);

			if($product && !empty($_FILES[$name_field]['name'][0]) && $error == 0){
				$length = count($_FILES[$name_field]['name']);
				for($i = 0; $i < $length; $i++){
					$data['product'] = $product->id;
					$data['user'] = $_SESSION['user']->id;
					$data['date'] = time();
					$data['main'] = time();
					$this->db->insertRow($this->shop_model->table('_product_photos'), $data);
					$photo_id = $this->db->getLastInsertedId();
					$photo_name = $product->alias . '-' . $photo_id;
					
					$extension = $this->savephoto($name_field, $path, $photo_name, true, $i);
					if($extension){
						$photo_name .= '.'.$extension;
						$this->db->updateRow($this->shop_model->table('_products'), array('author_edit' => $_SESSION['user']->id, 'date_edit' => time(), 'photo' => $photo_name), $id);
						$this->db->updateRow($this->shop_model->table('_product_photos'), array('name' => $photo_name), $photo_id);

						$photo['id'] = $photo_id;
						$photo['name'] = '';
						$photo['date'] = date('d.m.Y H:i');
						$photo['url'] = $path.$photo_name;
						$photo['thumbnailUrl'] = $path.'/s_'.$photo_name;
						$res[] = $photo;
					} else {
						$error++;
					}
				}
			}
			if($error > 0){
				$photo['result'] = false;
				$photo['error'] = "Access Denied!";
				$res[] = $photo;
			}
		}

		if(empty($res)){
			$photo['result'] = false;
			$photo['error'] = "Access Denied!";
			$res[] = $photo;
		}

		header('Content-type: application/json');
		echo json_encode(array('files' => $res));
		exit;
	}

	public function photo_save()
	{
		$res = array('result' => false, 'error' => 'Доступ заборонено! Тільки автор або адміністрація!');
		if(isset($_POST['photo']) && is_numeric($_POST['photo']) && isset($_POST['name'])){
			$photo = $this->db->getAllDataById($_SESSION['service']->table.'_product_photos'.$_SESSION['alias']->table, $_POST['photo']);
			if(!empty($photo)){
				$data = array();
				if($_POST['name'] == 'title') $data['title'] = $this->db->sanitizeString($_POST['title']);
				if($_POST['name'] == 'active'){
					$data['main'] = time();
					$this->db->updateRow($_SESSION['service']->table.'_products'.$_SESSION['alias']->table, array('author_edit' => $_SESSION['user']->id, 'date_edit' => time(), 'photo' => $photo->name), $photo->product);
				} 
				if(!empty($data)) if($this->db->updateRow($_SESSION['service']->table.'_product_photos'.$_SESSION['alias']->table, $data, $_POST['photo'])){
					$res['result'] = true;
					$res['error'] = '';
				}
			} else $res['error'] = 'Фотографію не знайдено!';
		}
		header('Content-type: application/json');
		echo json_encode($res);
		exit;
	}
	
	public function photo_delete()
	{
		$res = array('result' => false, 'error' => 'Доступ заборонено! Тільки автор або адміністрація!');
		if(isset($_POST['photo']) && is_numeric($_POST['photo'])){
			$photo = $this->db->getAllDataById($_SESSION['service']->table.'_product_photos'.$_SESSION['alias']->table, $_POST['photo']);
			if(!empty($photo)){
				if($this->db->deleteRow($_SESSION['service']->table.'_product_photos'.$_SESSION['alias']->table, $_POST['photo'])){
					$path = IMG_PATH.$_SESSION['alias']->alias.'/'.$photo->product.'/';
					if(strlen(IMG_PATH) > strlen(SITE_URL)) $path = substr($path, strlen(SITE_URL));
					$prefix = array('');
					$sizes = $this->db->getAllDataByFieldInArray('wl_images_sizes', $_SESSION['alias']->id, 'alias');
					if($sizes){
						foreach ($sizes as $resize) if($resize->active == 1){
							$prefix[] = $resize->prefix.'_';
						}
					}
					foreach ($prefix as $p) {
						$filename = $path.$p.$photo->name;
						@unlink ($filename);
					}
					$res['result'] = true;
					$res['error'] = '';

					$product = $this->db->getAllDataById($_SESSION['service']->table.'_products'.$_SESSION['alias']->table, $photo->product);
					if($product) {
						$data['author_edit'] = time();
						$data['date_edit'] = time();
						if($product->photo == $photo->name){
							$data['photo'] = 0;
							$photos = $this->db->getAllDataByFieldInArray($_SESSION['service']->table.'_product_photos'.$_SESSION['alias']->table, $product->id, 'product', 'main DESC');
							if($photos) $data['photo'] = $photos[0]->name;
							else $data['photo'] = '';
						}
						$this->db->updateRow($_SESSION['service']->table.'_products'.$_SESSION['alias']->table, $data, $product->id);
					}
				}
			} else $res['error'] = 'Фотографію не знайдено!';
		}
		header('Content-type: application/json');
		echo json_encode($res);
		exit;
	}

	private function savephoto($name_field, $path, $name, $array = false, $i = 0){
		if(!empty($_FILES[$name_field]['name'])){
			$this->load->library('image');
			if($array) $this->image->uploadArray($name_field, $i, $path, $name);
			else $this->image->upload($name_field, $path, $name);
			$extension = $this->image->getExtension();
			$this->image->save();
			if($this->image->getErrors() == ''){
				if($_SESSION['option']->resize > 0){
					$sizes = $this->db->getAllDataByFieldInArray('wl_images_sizes', $_SESSION['alias']->id, 'alias');
					if($sizes){
						foreach ($sizes as $resize) if($resize->active == 1){
							$this->image->loadImage($path, $name, $extension);
							if($resize->type == 1) $this->image->resize($resize->width, $resize->height, 100);
							if($resize->type == 2) $this->image->preview($resize->width, $resize->height, 100);
							$this->image->save($path, $resize->prefix);
						}
					}
				}
				return $this->image->getExtension();
			}
		}
		return false;
	}
	
}

?>
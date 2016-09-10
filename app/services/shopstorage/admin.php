<?php

/*

 	Service "Shop Storage 1.0"
	for WhiteLion 1.0

*/

class shopstorage extends Controller {
				
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
    	$this->load->smodel('storage_model');
    	if(is_numeric($uri))
    	{
    		$product = $this->storage_model->getProduct($uri, false);
    		if($product)
    		{
    			$_SESSION['alias']->breadcrumb = array($_SESSION['alias']->name => 'admin/'.$_SESSION['alias']->alias, 'Накладна #'.$uri => '');
				$_SESSION['alias']->name .= '. Накладна #'.$uri;

    			$product->info = $this->getProduct('id', $product->product);
    			$product->history = $this->storage_model->getProducts($product->product);
    			$this->load->admin_view('storage/detal_view', array('product' => $product));
    		}
    		$this->load->page_404();
    	}
    	else
    	{
    		$_SESSION['option']->paginator_per_page = 50;
	    	$products = $this->storage_model->getProducts();
	    	if($products)
	    	{
	    		foreach ($products as $product) {
	    			$product->info = $this->getProduct('id', $product->product);
	    		}
	    	}
	    	$this->load->admin_view('storage/list_view', array('products' => $products));
    	}
    	
    }
	
	public function add()
	{
		$_SESSION['alias']->breadcrumb = array($_SESSION['alias']->name => 'admin/'.$_SESSION['alias']->alias, 'Додати накладну' => '');
		$_SESSION['alias']->name .= '. Додати накладну';

		$this->load->smodel('storage_model');
		$storage = $this->storage_model->getStorage();

		$this->load->admin_view('storage/add_view', array('storage' => $storage));
	}
	
	public function edit()
	{
		$id = $this->data->uri(3);
    	if(is_numeric($id))
    	{
    		$_SESSION['alias']->breadcrumb = array($_SESSION['alias']->name => 'admin/'.$_SESSION['alias']->alias, 'Редагувати накладну' => '');
			$_SESSION['alias']->name = 'Редагувати накладну'.$id;

    		$this->load->smodel('storage_model');
    		$product = $this->storage_model->getProduct($id);
    		if($product)
    		{
    			$product->info = $this->getProduct('id', $product->product);
    			$product->history = $this->storage_model->getProducts($product->product);
    			$storage = $this->storage_model->getStorage();
    			$this->load->admin_view('storage/edit_view', array('product' => $product, 'storage' => $storage));
    		}
    		$this->load->page_404();
    	}
	}
	
	public function save()
	{
		
		if(isset($_POST['id']) && is_numeric($_POST['id']))
		{
			$this->load->smodel('storage_model');
			$id = 0;
			$_SESSION['notify'] = new stdClass();

			if($_POST['id'] == 0)
			{
				$id = $this->storage_model->save();
				if($id){
					$_SESSION['notify']->success = 'Накладну успішно додано!';
				}
			}
			else
			{
				if($this->storage_model->save($_POST['id']))
				{
					$id = $_POST['id'];
					$_SESSION['notify']->success = 'Дані успішно оновлено!';
				}
			}
			if(isset($_POST['to']) && $_POST['to'] == 'new') {
				$this->redirect("admin/{$_SESSION['alias']->alias}/add");
			}
			$this->redirect('admin/'.$_SESSION['alias']->alias.'/'.$id);
		}
	}
	
	public function delete()
	{
		if(isset($_POST['id']) && is_numeric($_POST['id']))
		{
			$this->load->smodel('storage_model');
			if($this->storage_model->delete($_POST['id']))
			{
				$_SESSION['notify'] = new stdClass();
				$_SESSION['notify']->success = 'Накладну успішно видалено!';
				$this->redirect("admin/{$_SESSION['alias']->alias}");
			}
		}
	}

	public function options()
	{
		$this->load->smodel('storage_model');

		$storage = $this->storage_model->getStorage();

		$_SESSION['alias']->name = 'Керування властивостями ' . $storage->name;
		$_SESSION['alias']->breadcrumb = array('Властивості' => '');

		$alias = $this->db->getAllDataById('wl_aliases', $_SESSION['alias']->id);
		$options = null;
        if($alias->options > 0)
        {
            $this->db->executeQuery("SELECT * FROM wl_options WHERE service = '{$alias->service}' AND alias = '0'");
            if($this->db->numRows() > 0){
                $options_all = $this->db->getRows('array');
                foreach ($options_all as $option) {
                    $options[$option->name] = new stdClass();
                    $options[$option->name]->name = $option->name;
                    $options[$option->name]->value = $option->value;
                    $options[$option->name]->type = 'text';
                    $options[$option->name]->title = $option->name;
                }
            } 
            $this->db->executeQuery("SELECT * FROM wl_options WHERE service = '{$alias->service}' AND alias = '{$alias->id}'");
            if($this->db->numRows() > 0){
                $options_all = $this->db->getRows('array');
                foreach ($options_all as $option)
                {
                    if(isset($options[$option->name])) $options[$option->name]->value = $option->value;
                    else
                    {
                        $options[$option->name] = new stdClass();
                        $options[$option->name]->name = $option->name;
                        $options[$option->name]->value = $option->value;
                        $options[$option->name]->type = 'text';
                        $options[$option->name]->title = $option->name;
                    }
                }
            } 
        }

        if($alias->service > 0)
        {
            $path = APP_PATH.'services'.DIRSEP.$_SESSION['alias']->service.DIRSEP.'models/install_model.php';
            if(file_exists($path)){
                require_once($path);
                $install = new install();

                if(!empty($install->options) && !empty($options))
                {
                    foreach ($install->options as $key => $value) {
                        if(isset($install->options_type[$key]) && isset($options[$key])) $options[$key]->type = $install->options_type[$key];
                        if(isset($install->options_title[$key]) && isset($options[$key])) $options[$key]->title = $install->options_title[$key];
                    }
                }
            }
        }

		$this->load->admin_view('options/index_view', array('storage' => $storage, 'options' => $options));	
	}

	public function options_save()
	{
		$data = array();
		$data['name'] = $this->data->post('name');
		$data['markup'] = $this->data->post('markup');
		$this->db->updateRow($_SESSION['service']->table, $data, $_SESSION['alias']->id);
		$_SESSION['notify'] = new stdClass();
		$_SESSION['notify']->success = 'Інформацію успішно оновлено!';
		$this->redirect();
	}

	public function markup_save()
	{
		$table = $_SESSION['service']->table.'_markup';
		$data = array();
		$data['storage'] = $_SESSION['alias']->id;
		foreach ($_POST as $key => $value) {
			$key = explode('-', $key);
			if($key[0] == 'markup' && isset($key[1]))
			{
				$data['user_type'] = $key[1];
				$markup = $this->db->getAllDataById($table, $data);
				if($markup)
				{
					if($markup->markup != $value) $this->db->updateRow($table, array('markup' => $value), $markup->id);
				}
				else
				{
					$data['markup'] = $value;
					$this->db->insertRow($table, $data);
					unset($data['markup']);
				}
			}
		}
		$_SESSION['notify'] = new stdClass();
		$_SESSION['notify']->success = 'Інформацію успішно оновлено!';
		$this->redirect();
	}

	public function getProductByArticle()
	{
		$product = $this->getProduct('article', $this->data->post('product'));
		$this->load->json($product);
	}

	public function getProductById()
	{
		$product = $this->getProduct('id', $this->data->post('product'));
		$this->load->json($product);
	}

	private function getProduct($key, $id)
	{
		$cooperation = $this->db->getAllDataByFieldInArray('wl_aliases_cooperation', $_SESSION['alias']->id, 'alias2');
		if($cooperation)
		{
			foreach ($cooperation as $shop) {
				if($shop->type == 'storage')
				{
					$product = $this->load->function_in_alias($shop->alias1, '__get_Product', array($key => $id, 'key' => $key));
					if($product) return $product;
				}
			}
		}
		return false;
	}
	
}

?>
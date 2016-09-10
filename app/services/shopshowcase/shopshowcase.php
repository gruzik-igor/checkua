<?php

/*

 	Service "Shop Showcase 2.2"
	for WhiteLion 1.0

*/

class shopshowcase extends Controller {
				
    function _remap($method, $data = array())
    {
    	if(isset($_SESSION['alias']->name) && $_SESSION['alias']->service == 'shopshowcase')
    	{
    		$_SESSION['alias']->breadcrumb_name = $_SESSION['alias']->name;
    	}
    	
        if (method_exists($this, $method)) {
        	if(empty($data)) $data = null;
            return $this->$method($data);
        } else {
        	$this->index($method);
        }
    }

    public function index($uri)
    {
    	$_SESSION['alias']->breadcrumbs = array($_SESSION['alias']->name => '');
    	$this->load->smodel('shop_model');
		
		if(count($this->data->url()) > 1)
		{
			$type = null;
			$product = $this->shop_model->routeURL($this->data->url(), $type);

			if($type == 'product' && $product && ($product->active == 1 || $this->userCan($_SESSION['alias']->alias)))
			{
				$this->load->model('wl_ntkd_model');
				$this->wl_ntkd_model->setContent($product->id);
				$videos = $this->wl_ntkd_model->getVideosFromText();
				if($videos)
				{
					$this->load->library('video');
					$this->video->setVideosToText($videos);
				}
				$_SESSION['alias']->image = $product->photo;
				
				$this->load->page_view('detal_view', array('product' => $product));
			}

			if($_SESSION['option']->useGroups && $type == 'group' && $product && ($product->active == 1 || $this->userCan($_SESSION['alias'])))
			{
				$group = clone $product;
				unset($product);

				$_SESSION['alias']->breadcrumbs = array($_SESSION['alias']->name => $_SESSION['alias']->alias);
				$group->parents = array();
				if($group->parent > 0){
					$list = array();
		            $groups = $this->db->getAllDataByFieldInArray($this->shop_model->table('_groups'), $_SESSION['alias']->id, 'wl_alias');
		            foreach ($groups as $Group) {
		            	$list[$Group->id] = clone $Group;
		            }
					$group->parents = $this->shop_model->makeParents($list, $group->parent, $group->parents);
					$link = $_SESSION['alias']->alias;
					foreach ($group->parents as $parent) {
						$link .= '/'.$parent->alias;
						$_SESSION['alias']->breadcrumbs[$parent->name] = $link;
					}
					$group->link = $link;
				}

				$this->load->model('wl_ntkd_model');
				$this->wl_ntkd_model->setContent(($group->id * -1));
				$videos = $this->wl_ntkd_model->getVideosFromText();
				if($videos)
				{
					$this->load->library('video');
					$this->video->setVideosToText($videos);
				}
				$_SESSION['alias']->image = $group->photo;
				$_SESSION['alias']->breadcrumbs[$_SESSION['alias']->name] = '';

				$groups = $this->shop_model->getGroups($group->id);
				$products = $this->shop_model->getProducts($group->id);
				$this->load->page_view('group_view', array('group' => $group, 'groups' => $groups, 'products' => $products));
			}

			$this->load->page_404();
		}
		else
		{
			$products = $this->shop_model->getProducts();
			if($_SESSION['option']->useGroups)
			{
				$groups = $this->shop_model->getGroups();
				$this->load->page_view('index_view', array('groups' => $groups, 'products' => $products));
			}
			else
			{
				$this->load->page_view('index_view', array('products' => $products));
			}
		}
    }

	public function search()
	{
		if(isset($_GET['name']) || isset($_GET['group'])){
			if(isset($_GET['name']) && is_numeric($_GET['name'])){
				$this->redirect($_SESSION['alias']->alias.'/'.$_GET['name']);
			}

			$this->load->smodel('shop_model');
			$_SESSION['alias']->name = 'Пошук по назві';
			$_SESSION['alias']->title = 'Пошук по назві';

			if(isset($_GET['name'])){
				$_SESSION['alias']->name = "Пошук по назві '{$this->data->get('name')}'";
				$_SESSION['alias']->title = "Пошук по назві '{$this->data->get('name')}'";
			}

			$group = 0;
			if(isset($_GET['group'])){
				$language = '';
				if($_SESSION['language']) $language = "AND n.language = '{$_SESSION['language']}'";
				$group = $this->db->getQuery("SELECT g.*, n.name, n.title FROM `{$this->shop_model->table('_groups')}` as g LEFT JOIN `wl_ntkd` as n ON n.alias = {$_SESSION['alias']->id} AND n.content = -g.id {$language} WHERE g.link = '{$this->data->get('group')}'");
				if($group) {
					$_SESSION['alias']->name = 'Пошук '.$group->name .' район';
					$_SESSION['alias']->title = 'Пошук '.$group->title .' район';
					$group = $group->id;
				}
			}

			$products = $this->shop_model->getProducts($group);
			$this->load->page_view('group_view', array('products' => $products));
		}
		if(isset($_GET['id']) || isset($_GET['article']))
		{
			$this->load->smodel('shop_model');
			$id = 0;
			$key = 'id';
			if(isset($_GET['article']))
			{
				$id = $this->data->get('article');
				$key = 'article';
			}
			else
			{
				$id = $this->data->get('id');
			}
			$product = $this->shop_model->getProduct($id, $key, false);
			if($product)
			{
				$this->redirect($product->link);
			}
			else
			{
				$this->load->page_view('group_view', array('products' => null));
			}
		}
	}

    public function __get_Search($content)
    {
    	$this->load->smodel('shop_search_model');
    	return $this->shop_search_model->getByContent($content);
    }

    // $id['key'] може мати любий ключ _products. Рекомендовано: id, article, alias.
	public function __get_Product($id = 0)
	{
		$key = 'id';
		if(is_array($id))
		{
			if(isset($id['key'])) $key = $id['key'];
			if(isset($id['id'])) $id = $id['id'];
			if(isset($id['article'])) $id = $id['article'];
		}
		$this->load->smodel('shop_model');
		return $this->shop_model->getProduct($id, $key);
	}

	public function __get_Products($data = array())
	{
		$group = 0;
		if(isset($data['group']) && is_numeric($data['group'])) $group = $data['group'];
		if(isset($data['limit']) && is_numeric($data['limit'])) $_SESSION['option']->paginator_per_page = $data['limit'];

		$this->load->smodel('shop_model');
		return $this->shop_model->getProducts($group);
	}

	public function __get_Groups($parent = 0)
	{
		$this->load->smodel('shop_model');
		return $this->shop_model->getGroups($parent);
	}

	public function __get_Options_By_Group($data = array())
	{
		$this->load->smodel('shop_model');
		$group = 0;
		if(isset($data['group'])) $group = $data['group'];
		return $this->db->getQuery("SELECT o.*, n.name FROM `{$this->shop_model->table('_group_options')}` as o LEFT JOIN `{$this->shop_model->table('_options_name')}` as n ON n.option = o.id WHERE o.group = -{$group}", 'array');
	}
	
}

?>
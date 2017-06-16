<?php

/*

 	Service "Shop Showcase 2.4.1"
	for WhiteLion 1.0

*/

class shopshowcase extends Controller {

	private $groups = array();

    function _remap($method, $data = array())
    {
        if (method_exists($this, $method))
            return $this->$method($data);
        else
        	$this->index($method);
    }

    public function index($uri)
    {
    	$this->load->smodel('shop_model');

		if(count($this->data->url()) > 1)
		{
			$type = null;
			$product = $this->shop_model->routeURL($this->data->url(), $type);

			if($type == 'product' && $product)
			{
				if($product->active == 0 && !$this->userCan())
					$this->load->page_404(false);
				$this->wl_alias_model->setContent($product->id);
				if($_SESSION['option']->ProductUseArticle)
					$_SESSION['alias']->name = substr($_SESSION['alias']->name, 0, (strlen($product->article) + 1) * -1);
				if($videos = $this->wl_alias_model->getVideosFromText())
				{
					$this->load->library('video');
					$this->video->setVideosToText($videos);
				}
				$_SESSION['alias']->breadcrumbs = '';
				$this->load->page_view('detal_view', array('product' => $product));
			}
			elseif($_SESSION['option']->useGroups && $type == 'group' && $product)
			{
				if($product->active == 0 && !$this->userCan())
					$this->load->page_404(false);
				$group = clone $product;
				unset($product);

				$this->wl_alias_model->setContent(($group->id * -1));
				$group->parents = array();
				if($group->parent > 0)
				{
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

				if($videos = $this->wl_alias_model->getVideosFromText())
				{
					$this->load->library('video');
					$this->video->setVideosToText($videos);
				}
				$_SESSION['alias']->breadcrumbs[$_SESSION['alias']->name] = '';

				$subgroups = $this->shop_model->getGroups($group->id);

				if($subgroups)
				{
					$this->getSubGroupIds($subgroups);
    				$products = $this->shop_model->getProducts($this->groups);
				}
				else {
					$products = $this->shop_model->getProducts($group->id);
				}

				$filters = $this->shop_model->getOptionsToGroup($group);

				$filterExists = false;
				if($filters)
				{
					foreach ($filters as $filter) {
						usort($filter->values, function($a, $b) { return strcmp($a->name, $b->name); });
						if(!empty($filter->values)){
							$filterExists = true;
							break;
						}
					}
				}

				$this->load->page_view('group_view', array('group' => $group, 'subgroups' => $subgroups, 'products' => $products, 'filters' => $filters, 'filterExists' => $filterExists));
			}
			else
				$this->load->page_404();
		}
		else
		{
			$this->wl_alias_model->setContent();
			if($videos = $this->wl_alias_model->getVideosFromText())
			{
				$this->load->library('video');
				$this->video->setVideosToText($videos);
			}

			$products = $this->shop_model->getProducts();
			if($_SESSION['option']->useGroups)
			{
				$groups = $this->shop_model->getGroups();
				$this->load->page_view('index_view', array('groups' => $groups, 'products' => $products));
			}
			else
				$this->load->page_view('index_view', array('products' => $products));
		}
    }

    private function getSubGroupIds($subGroups)
    {
    	foreach ($subGroups as $subGroup)
    	{
    		$hasSubGroups = $this->shop_model->getGroups($subGroup->id);

			if($hasSubGroups)
			{
				$this->getSubGroupIds($hasSubGroups);
			}
			else
			{

				$this->groups[$subGroup->id] = new stdClass();
				$this->groups[$subGroup->id]->id = $subGroup->id;
			}
    	}

    }

	public function search()
	{
		if(isset($_GET['name']) || isset($_GET['group']))
		{
			if(isset($_GET['name']) && is_numeric($_GET['name']))
				$this->redirect($_SESSION['alias']->alias.'/'.$_GET['name']);

			$this->load->smodel('shop_model');
			$_SESSION['alias']->name = $_SESSION['alias']->title = 'Пошук по назві';

			if(isset($_GET['name']))
			{
				$_SESSION['alias']->name = "Пошук по назві '{$this->data->get('name')}'";
				$_SESSION['alias']->title = "Пошук по назві '{$this->data->get('name')}'";
			}

			$group_id = 0;
			if(isset($_GET['group']))
			{
				$this->db->select($this->shop_model->table('_groups').' as g', 'id', $this->data->get('group'), 'link');
				$where = array('content' => '#-g.id');
				if($_SESSION['language']) $where['language'] = $_SESSION['language'];
				$this->db->join('wl_ntkd', 'name, title', $where);
				if($group = $this->db->get())
				{
					$_SESSION['alias']->name = 'Пошук '.$group->name;
					$_SESSION['alias']->title = 'Пошук '.$group->title;
					$group_id = $group->id;
				}
			}

			$this->load->page_view('group_view', array('products' => $this->shop_model->getProducts($group_id)));
		}
		if(isset($_GET['id']) || isset($_GET['article']))
		{
			$this->load->smodel('shop_model');
			$id = 0;
			$key = 'id';
			if(isset($_GET['article']))
			{
				$id = $this->makeArticle($this->data->get('article'));
				$key = 'article';
			}
			else
				$id = $this->data->get('id');
			$product = $this->shop_model->getProduct($id, $key, false);
			$products = $this->shop_model->getProducts('%'.$id);

			if($this->userIs() && !$this->userCan())
			{
				if($product)
					$this->shop_model->searchHistory($product->id);
				else
					$this->shop_model->searchHistory(0, $id);
			}

			if($product || count($products) > 0)
			{
				$link = $product ? $product->link : $products[0]->link;
				$this->load->page_view('detal_view', array('product' => $product, 'products' => $products));
			}
			else
				$this->load->page_view('group_view', array('products' => null));
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
			elseif(isset($id['article'])) $id = $id['article'];
		}
		$this->load->smodel('shop_model');
		return $this->shop_model->getProduct($id, $key);
	}

	public function __get_Products($data = array())
	{
		$group = -1;
		if(isset($data['article']) && $data['article'] != '') $group = '%'.$data['article'];
		elseif(isset($data['group']) && (is_numeric($data['group']) || is_array($data['group']))) $group = $data['group'];
		if(isset($data['limit']) && is_numeric($data['limit'])) $_SESSION['option']->paginator_per_page = $data['limit'];

		$this->load->smodel('shop_model');
		return $this->shop_model->getProducts($group);
	}

	public function ajaxGetProducts()
	{
		if(isset($_POST['params']))
		{
			foreach ($_POST['params'] as $key => $value) {
				if(is_array($value))
				{
					foreach ($value as $secondValue) {
						$_GET[$key][] = $secondValue;
					}
				}
				else
					$_GET[$key] = $value;
			}
		}

		$_SESSION['option']->paginator_per_page = 20;
		$_GET['page'] = $this->data->post('page');
		$group = $this->data->post('group') > 0 ? $this->data->post('group') : '-1' ;

		$this->load->smodel('shop_model');
		$this->load->json(array('products' => $this->shop_model->getProducts($group), 'page' => $_GET['page']+1, 'group' => $group));
	}

	public function __get_Groups($parent = 0)
	{
		if(empty($parent)) $parent = 0;
		$this->load->smodel('shop_model');
		return $this->shop_model->getGroups($parent);
	}

	public function __get_Values_To_Option($id = 0)
	{
		$this->load->smodel('shop_model');
		$this->db->select($this->shop_model->table('_options').' as o', '*', -$id, 'group');
		$where = array('option' => '#o.id');
		if($_SESSION['language']) $where['language'] = $_SESSION['language'];
		$this->db->join($this->shop_model->table('_options_name'), 'name', '');
		return $this->db->get('array');
	}

	private function makeArticle($article)
	{
		$article = (string) $article;
		$article = trim($article);
		$article = strtoupper($article);
		$article = str_replace('-', '', $article);
		return str_replace(' ', '', $article);
	}

}

?>
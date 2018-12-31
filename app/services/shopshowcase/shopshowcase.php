<?php

/*

 	Service "Shop Showcase 2.7"
	for WhiteLion 1.0

*/

class shopshowcase extends Controller {

	private $groups = array();
	private $marketing = array();

    function __construct()
    {
        parent::__construct();
        $_SESSION['option']->currency = 1;

        if($cooperation = $this->db->getAllDataByFieldInArray('wl_aliases_cooperation', $_SESSION['alias']->id, 'alias1'))
        	foreach ($cooperation as $c) {
        		if($c->type == 'currency')
        		{
		        	if($currency = $this->load->function_in_alias($c->alias2, '__get_Currency', 'USD'))
		            	$_SESSION['option']->currency = $currency;
		        }
		        if($c->type == 'marketing')
		        	$this->marketing[] = $c->alias2;
	        }
    }

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
				if($_SESSION['option']->ProductUseArticle && mb_strlen($_SESSION['alias']->name) > mb_strlen($product->article))
				{
					$name = explode(' ', $_SESSION['alias']->name);
					if(array_pop($name) == $product->article)
						$product->name = $_SESSION['alias']->name = implode(' ', $name);
				}
				if($videos = $this->wl_alias_model->getVideosFromText())
				{
					$this->load->library('video');
					$this->video->setVideosToText($videos);
				}

				if(!empty($this->marketing))
					foreach ($this->marketing as $marketingAliasId) {
						$product->currency = $_SESSION['option']->currency;
						$product = $this->load->function_in_alias($marketingAliasId, '__get_Product', $product);
					}
				$this->load->page_view('detal_view', array('product' => $product));
			}
			elseif($_SESSION['option']->useGroups && $type == 'group' && $product)
			{
				if($product->active == 0 && !$this->userCan())
					$this->load->page_404(false);
				$group = clone $product;
				unset($product);

				$this->wl_alias_model->setContent(-$group->id);
				if($videos = $this->wl_alias_model->getVideosFromText())
				{
					$this->load->library('video');
					$this->video->setVideosToText($videos);
				}
				if(!empty($this->marketing))
					foreach ($products as $product) {
						foreach ($this->marketing as $marketingAliasId) {
							$product->currency = $_SESSION['option']->currency;
							$product = $this->load->function_in_alias($marketingAliasId, '__get_Product', $product);
						}
					}
				$subgroups = $this->shop_model->getGroups($group->id);
				$products = $this->shop_model->getProducts($group->id);

				if(!$group->haveChild)
					$filters = $this->shop_model->getOptionsToGroup($group);
				else
					$filters = false;
				if($filters)
					foreach ($filters as $filter) {
						if(count($filter->values) > 1)
							usort($filter->values, function($a, $b) { return strcmp($a->name, $b->name); });
					}

				$this->load->page_view('group_view', array('group' => $group, 'subgroups' => $subgroups, 'products' => $products, 'filters' => $filters));
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

			// $products = $this->shop_model->getProducts();
			$products = false;
			if($_SESSION['option']->useGroups)
			{
				$groups = $this->shop_model->getGroups();
				$this->load->page_view('index_view', array('groups' => $groups, 'products' => $products));
			}
			else
				$this->load->page_view('index_view', array('products' => $products));
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

    public function __get_SiteMap_Links()
    {
        $data = $row = array();
        $row['link'] = $_SESSION['alias']->alias;
        $row['alias'] = $_SESSION['alias']->id;
        $row['content'] = 0;
        // $row['code'] = 200;
        // $row['data'] = '';
        // $row['time'] = time();
        // $row['changefreq'] = 'daily';
        // $row['priority'] = 5;
        $data[] = $row;

        $this->load->smodel('shop_search_model');
        if($products = $this->shop_search_model->getProducts_SiteMap())
        	foreach ($products as $product)
            {
            	$row['link'] = $product->link;
            	$row['content'] = $product->id;
            	$data[] = $row;
            }

       	if($_SESSION['option']->useGroups)
	        if($groups = $this->shop_search_model->getGroups_SiteMap())
	        	foreach ($groups as $group)
	            {
	            	$row['link'] = $group->link;
	            	$row['content'] = -$group->id;
	            	$data[] = $row;
	            }

        return $data;
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
		$_SESSION['alias']->breadcrumbs = NULL;
		$this->load->smodel('shop_model');
		$product = $this->shop_model->getProduct($id, $key);

		if(!empty($this->marketing) && $product)
			foreach ($this->marketing as $marketingAliasId) {
				$product->currency = $_SESSION['option']->currency;
				$product->price_before = $product->price;
				$product = $this->load->function_in_alias($marketingAliasId, '__get_Product', $product);
				$product->price = $this->shop_model->formatPrice($product->price);
				$product->old_price = $this->shop_model->formatPrice($product->old_price);
				$product->discount = $product->price_before - $product->price;
			}

		return $product;
	}

	public function __get_Products($data = array())
	{
		$group = -1;
		$noInclude = 0;
		$active = true;
		$getProductOptions = false;
		if(isset($data['article']) && $data['article'] != '') $group = '%'.$data['article'];
		elseif(isset($data['group']) && (is_numeric($data['group']) || is_array($data['group']))) $group = $data['group'];
		if(isset($data['limit']) && is_numeric($data['limit'])) $_SESSION['option']->paginator_per_page = $data['limit'];
		if(isset($data['sort']) && $data['sort'] != '') $_SESSION['option']->productOrder = $data['sort'];
		if(isset($data['sale']) && $data['sale'] == 1) $_GET['sale'] = 1;
		if(isset($data['noInclude']) && $data['noInclude'] > 0) $noInclude = $data['noInclude'];
		if(isset($data['active']) && $data['active'] == false) $active = false;
		if(isset($data['getProductOptions']) && $data['getProductOptions'] == true) $getProductOptions = true;

		$this->load->smodel('shop_model');
		$products = $this->shop_model->getProducts($group, $noInclude, $active, $getProductOptions);
		if(!empty($this->marketing) && $products)
			foreach ($products as $product) {
				foreach ($this->marketing as $marketingAliasId) {
					$product->currency = $_SESSION['option']->currency;
					$product = $this->load->function_in_alias($marketingAliasId, '__get_Product', $product);
					$product->price = $this->shop_model->formatPrice($product->price);
					$product->old_price = $this->shop_model->formatPrice($product->old_price);
				}
			}
		return $products;
	}

	public function ajaxGetProducts()
	{
		if(isset($_POST['params']))
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

		$_GET['page'] = $this->data->post('page');
		$group = $this->data->post('group') > 0 ? $this->data->post('group') : '-1' ;

		$this->load->smodel('shop_model');
		$this->load->json(array('products' => $this->shop_model->getProducts($group), 'page' => $_GET['page']+1, 'group' => $group));
	}

	public function ajaxUpdateProductPrice()
	{
		if ($product = $this->data->post('product')) {
			if (isset($_POST['options']) && is_array($_POST['options'])) {
				$this->load->smodel('shop_model');
				$this->load->json(array('price' => $this->shop_model->getProductPriceWithOptions($product, $_POST['options']), 'product' => $product));
			}
		}
	}

	public function exportyml()
	{
		if(isset($_GET['key']) && !empty($_SESSION['option']->exportKey) && $_SESSION['option']->exportKey == $_GET['key'])
		{
			$this->load->library('ymlgenerator');
			$this->load->smodel('export_model');
			$this->export_model->init();
			$products = $groups = array();

			$checkedGroups = -1;
	        if(!empty($_GET['group']) && is_numeric($_GET['group']))
	            $checkedGroups = $_GET['group'];
        
	        if($groups = $this->export_model->getGroups($checkedGroups))
	        {
	            if($checkedGroups > 0)
	            {
	                $checkedGroups = array();
	                foreach ($groups as $group) {
	                    $checkedGroups[] = $group->id;
	                }
	            }
	        }
	        $products = $this->export_model->getProducts($checkedGroups);

	        if(!empty($this->marketing))
				foreach ($this->marketing as $marketingAliasId) {
					$config = array('all' => true, 'products' => $products);
					if($_SESSION['option']->currency)
						$config['currency'] = $_SESSION['option']->currency;
					$products = $this->load->function_in_alias($marketingAliasId, '__get_Products', $config);
				}
	  //       echo "<pre>";
	  //       print_r($products);
			// exit;
			$this->ymlgenerator->createYml($products, $groups);
		}
		exit;
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
		$this->db->join($this->shop_model->table('_options_name'), 'name', $where);
		return $this->db->get('array');
	}

	public function __get_Option_Info($id = 0)
	{
		$this->load->smodel('shop_model');
		$this->db->select($this->shop_model->table('_options').' as o', '*', $id);
		$where = array('option' => '#o.id');
		if($_SESSION['language']) $where['language'] = $_SESSION['language'];
		$this->db->join($this->shop_model->table('_options_name'), 'name', $where);
		if($option = $this->db->get('single'))
		{
			$option->values = $this->__get_Values_To_Option($option->id);
			return $option;
		}
		return false;
	}

	public function __get_Price_With_options($info)
	{
		if (isset($info['product']) && isset($info['options']) && is_array($info['options'])) {
			$this->load->smodel('shop_model');
			return $this->shop_model->getProductPriceWithOptions($info['product'], $info['options']);
		}
		return false;
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
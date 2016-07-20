<?php

/*

 	Service "Shop Showcase 2.1"
	for WhiteLion 1.0

*/

class shopshowcase extends Controller {
				
    function _remap($method, $data = array())
    {
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
		
		$url = $this->data->url();
		$id = end($url);
		$id = explode($_SESSION['option']->idExplodeLink, $id);
		$id = $id[0];

		if(is_numeric($id))
		{
			$product = $this->shop_model->getProductById($id);
			if($product && ($product->active == 1 || $this->userCan($_SESSION['alias']->alias))){

				$url = implode('/', $url);
				if($url != $product->link){
					header ('HTTP/1.1 301 Moved Permanently');
					header ('Location: '. SITE_URL. $product->link);
					exit();
				}

				$product->alias_name = $_SESSION['alias']->name;

				$this->load->model('wl_ntkd_model');
				$this->wl_ntkd_model->setContent($product->id);
				$_SESSION['alias']->image = $product->photo;

				$otherProductsByGroup = $this->shop_model->getProducts($product->group, $product->id);
				
				$this->load->page_view('detal_view', array('product' => $product, 'otherProductsByGroup' => $otherProductsByGroup));
			} else $this->load->page_404();
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

				if($group && ($group->active == 1 || $this->userCan($_SESSION['alias']))){
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
					$_SESSION['alias']->image = $group->photo;

					$products = $this->shop_model->getProducts($group->id);
					$this->load->page_view('group_view', array('group' => $group, 'products' => $products));
				} else $this->load->page_404();
			} else $this->load->page_404();
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
	}

    public function __get_Search($content)
    {
    	$this->load->smodel('shop_search_model');
    	return $this->shop_search_model->getByContent($content);
    }

	public function __get_Product($id = 0)
	{
		$this->load->smodel('shop_model');
		return $this->shop_model->getProductById($id);
	}

	public function __get_Products($data = array())
	{
		$group = 0;
		if(isset($data['group']) && is_numeric($data['group'])) $group = $data['group'];
		if(isset($data['limit']) && is_numeric($data['limit'])) $_SESSION['option']->paginator_per_page = $data['limit'];

		$this->load->smodel('shop_model');
		return $this->shop_model->getProducts($group);
	}

	public function __get_Groups()
	{
		$this->load->smodel('shop_model');
		return $this->shop_model->getGroups();
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
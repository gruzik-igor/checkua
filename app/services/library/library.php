<?php

/*

 	Service "Library 2.4"
	for WhiteLion 1.0

*/

class library extends Controller {
				
    function _remap($method, $data = array())
    {
    	if(isset($_SESSION['alias']->name) && $_SESSION['alias']->service == 'library')
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
    	$this->load->smodel('library_model');
		
		if(count($this->data->url()) > 1)
		{
			$type = null;
			$article = $this->library_model->routeURL($this->data->url(), $type);

			if($type == 'article' && $article && ($article->active == 1 || $this->userCan($_SESSION['alias']->alias)))
			{
				$this->load->model('wl_ntkd_model');
				$this->wl_ntkd_model->setContent($article->id);
				$videos = $this->wl_ntkd_model->getVideosFromText();
				if($videos)
				{
					$this->load->library('video');
					$this->video->setVideosToText($videos);
				}
				$_SESSION['alias']->image = $article->photo;
				
				$this->load->page_view('detal_view', array('article' => $article));
			}

			if($_SESSION['option']->useGroups && $type == 'group' && $article && ($article->active == 1 || $this->userCan($_SESSION['alias'])))
			{
				$group = clone $article;
				unset($article);

				$_SESSION['alias']->breadcrumbs = array($_SESSION['alias']->name => $_SESSION['alias']->alias);
				$group->parents = array();
				if($group->parent > 0){
					$list = array();
		            $groups = $this->db->getAllDataByFieldInArray($this->library_model->table('_groups'), $_SESSION['alias']->id, 'wl_alias');
		            foreach ($groups as $Group) {
		            	$list[$Group->id] = clone $Group;
		            }
					$group->parents = $this->library_model->makeParents($list, $group->parent, $group->parents);
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

				$groups = $this->library_model->getGroups($group->id);
				$articles = $this->library_model->getArticles($group->id);
				$this->load->page_view('group_view', array('group' => $group, 'groups' => $groups, 'articles' => $articles));
			}

			$this->load->page_404();
		}
		else
		{
			$articles = $this->library_model->getArticles();
			if($_SESSION['option']->useGroups)
			{
				$groups = $this->library_model->getGroups();
				$this->load->page_view('index_view', array('groups' => $groups, 'articles' => $articles));
			}
			else
			{
				$this->load->page_view('index_view', array('articles' => $articles));
			}
		}
    }

	public function search()
	{
		if(isset($_GET['name']) || isset($_GET['group'])){
			if(isset($_GET['name']) && is_numeric($_GET['name'])){
				$this->redirect($_SESSION['alias']->alias.'/'.$_GET['name']);
			}

			$search_group = new stdClass();
			$search_group->alias = 'search';
			$search_group->alias_name = $_SESSION['alias']->name;
			$search_group->parents = array();

			$this->load->smodel('library_model');
			$_SESSION['alias']->name = 'Пошук по назві';
			$_SESSION['alias']->title = 'Пошук по назві';

			if(isset($_GET['name'])){
				$_SESSION['alias']->name = "Пошук по назві \"{$this->data->get('name')}\"";
				$_SESSION['alias']->title = "Пошук по назві \"{$this->data->get('name')}\"";
			}

			$group = 0;
			if(isset($_GET['group'])){
				$language = '';
				if($_SESSION['language']) $language = "AND n.language = '{$_SESSION['language']}'";
				$group = $this->db->getQuery("SELECT g.*, n.name, n.title FROM `{$this->library_model->table('_groups')}` as g LEFT JOIN `wl_ntkd` as n ON n.alias = {$_SESSION['alias']->id} AND n.content = -g.id {$language} WHERE g.link = '{$this->data->get('group')}'");
				if($group) {
					$_SESSION['alias']->name = 'Пошук '.$group->name;
					$_SESSION['alias']->title = 'Пошук '.$group->title;
					$group = $group->id;
				}
			}

			$articles = $this->library_model->getArticles($group);
			$this->load->page_view('group_view', array('articles' => $articles, 'group' => $search_group));
		}
	}

    public function __get_Search($content)
    {
    	$this->load->smodel('library_search_model');
    	return $this->library_search_model->getByContent($content);
    }

	public function __get_Articles($data = array())
	{
		$group = 0;
		if(isset($data['group']) && is_numeric($data['group'])) $group = $data['group'];
		if(isset($data['limit']) && is_numeric($data['limit'])) $_SESSION['option']->paginator_per_page = $data['limit'];

		$this->load->smodel('library_model');
		return $this->library_model->getArticles($group);
	}

	public function __get_Groups()
	{
		$this->load->smodel('library_model');
		return $this->library_model->getGroups();
	}
	
}

?>
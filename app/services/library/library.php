<?php

/*

 	Service "Library 2.3"
	for WhiteLion 1.0

*/

class library extends Controller {
				
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
    	$this->load->smodel('library_model');
		
		$url = $this->data->url();
		$id = end($url);
		$id = explode($_SESSION['option']->idExplodeLink, $id);
		$id = $id[0];

		if(is_numeric($id))
		{
			$article = $this->library_model->getArticleById($id);
			if($article && ($article->active == 1 || $this->userCan())){

				$url = implode('/', $url);
				if($url != $article->link) {
					header ('HTTP/1.1 301 Moved Permanently');
					header ('Location: '. SITE_URL. $article->link);
					exit();
				}

				$_SESSION['alias']->image = $article->photo;
				$article->alias_name = $_SESSION['alias']->name;

				$this->load->model('wl_ntkd_model');
				$this->wl_ntkd_model->setContent($article->id);
				$otherArticlesByGroup = $this->library_model->getArticles($article->group);
				
				$this->load->page_view('detal_view', array('article' => $article, 'otherArticlesByGroup' => $otherArticlesByGroup));
			} else $this->load->page_404();
		}
		elseif($id != '' && count($url) > 1)
		{
			if($_SESSION['option']->useGroups)
			{
				$group = false;
				$parent = 0;
				array_shift($url);
				foreach ($url as $uri) {
					$group = $this->library_model->getGroupByAlias($uri, $parent);
					if($group){
						$parent = $group->id;
					} else $group = false;
				}

				if($group && ($group->active == 1 || $this->userCan($_SESSION['alias']))){
					$_SESSION['alias']->image = $group->photo;
					$group->alias_name = $_SESSION['alias']->name;
					$group->parents = array();
					if($group->parent > 0){
						$list = array();
			            $groups = $this->db->getAllData($this->library_model->table('_groups'));
			            foreach ($groups as $Group) {
			            	$list[$Group->id] = clone $Group;
			            }
						$group->parents = $this->library_model->makeParents($list, $group->parent, $group->parents);
					}
					$this->load->model('wl_ntkd_model');
					$this->wl_ntkd_model->setContent(($group->id * -1));

					$articles = $this->library_model->getArticles($group->id);
					$this->load->page_view('group_view', array('group' => $group, 'articles' => $articles));
				} else $this->load->page_404();
			} else $this->load->page_404();
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
				$articles = $this->library_model->getArticles();
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

	public function __get_Groups($parent = 0)
	{
		$this->load->smodel('library_model');
		return $this->library_model->getGroups($parent);
	}
	
}

?>
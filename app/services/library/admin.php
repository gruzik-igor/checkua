<?php

/*

 	Service "Library 2.5"
	for WhiteLion 1.0

*/

class library extends Controller {
				
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
    	$this->load->smodel('library_model');
    	$_SESSION['option']->paginator_per_page = 50;

    	if(count($this->data->url()) > 2)
		{
			$type = null;
			$url = $this->data->url();
			array_shift($url);
			$article = $this->library_model->routeURL($url, $type, true);

			if($type == 'article' && $article)
				$this->edit($article);

			if($_SESSION['option']->useGroups && $type == 'group' && $article)
			{
				$group = clone $article;
				unset($article);

				$group->alias_name = $_SESSION['alias']->name;
				$group->parents = array();
				if($group->parent > 0)
				{
					$list = array();
		            $groups = $this->db->getAllData($this->library_model->table('_groups'));
		            foreach ($groups as $Group) {
		            	$list[$Group->id] = clone $Group;
		            }
					$group->parents = $this->library_model->makeParents($list, $group->parent, $group->parents);
				}
				$this->wl_alias_model->setContent(($group->id * -1));

				$list = $this->library_model->getGroups($group->id, false);
				if (empty($list) || $_SESSION['option']->articleMultiGroup == 1)
				{
					$list = $this->library_model->getArticles($group->id, 0, false);
					$this->load->admin_view('articles/list_view', array('group' => $group, 'articles' => $list));
				}
				else
					$this->load->admin_view('index_view', array('group' => $group, 'groups' => $list));
			}

			$this->load->page_404();
		}
		else
		{
			$this->wl_alias_model->setContent();
			if($_SESSION['option']->useGroups)
			{
				$list = $this->library_model->getGroups(0, false);
				if (empty($list) || $_SESSION['option']->articleMultiGroup == 1)
				{
					$list = $this->library_model->getArticles(-1, 0, false);
					$this->load->admin_view('articles/list_view', array('articles' => $list));
				}
				else
					$this->load->admin_view('index_view', array('groups' => $list));
			}
			else
			{
				$articles = $this->library_model->getArticles(-1, 0, false);
				$this->load->admin_view('articles/list_view', array('articles' => $articles));
			}
		}
    }

	public function all()
	{
		$this->load->smodel('articles_model');
		$articles = $this->articles_model->getArticles(-1, false);
		$this->load->admin_view('articles/all_view', array('articles' => $articles));
	}
	
	public function add()
	{
		$_SESSION['alias']->breadcrumb = array($_SESSION['alias']->name => 'admin/'.$_SESSION['alias']->alias, 'Додати новий запис' => '');
		$_SESSION['alias']->name .= '. Додати новий запис';
		$this->load->admin_view('articles/add_view');
	}
	
	private function edit($article){
		$_SESSION['alias']->breadcrumb = array($_SESSION['alias']->name => 'admin/'.$_SESSION['alias']->alias, 'Редагувати запис' => '');
		$this->wl_alias_model->setContent($article->id);

		$groups = null;
		if($_SESSION['option']->useGroups)
		{
			$groups = $this->library_model->getGroups();
			if($_SESSION['option']->articleMultiGroup)
			{
				$activeGroups = $this->db->getAllDataByFieldInArray($this->library_model->table('_article_group'), $article->id, 'article');
				$article->group = array();
				if($activeGroups)
				{
					foreach ($activeGroups as $ag) {
						$article->group[] = $ag->group;
					}
				}
			}
		}

		$this->load->admin_view('articles/edit_view', array('article' => $article, 'groups' => $groups));
	}
	
	public function save()
	{
		if(isset($_POST['id']) && is_numeric($_POST['id']))
		{
			$this->load->smodel('articles_model');
			if($_POST['id'] == 0)
			{
				$link = '';
				$name = '';
				$id = $this->articles_model->add($link, $name);
				if($id)
				{
					if(!empty($_FILES['photo']['name']))
						$this->savephoto('photo', $id, $this->data->latterUAtoEN($name), $name);
					$this->redirect("admin/{$_SESSION['alias']->alias}/{$link}");
				}
				$this->redirect();
			}
			else
			{
				$_SESSION['notify'] = new stdClass();

				$link = $this->articles_model->save($_POST['id']);
				if(empty($_SESSION['notify']->errors))
				{
					if(isset($_POST['to']) && $_POST['to'] == 'new')
						$this->redirect("admin/{$_SESSION['alias']->alias}/add");
					elseif(isset($_POST['to']) && $_POST['to'] == 'category')
					{
						$link = 'admin/'.$_SESSION['alias']->alias;
						$article = $this->articles_model->getById($_POST['id']);
						$article->link = explode('/', $article->link);
						array_pop ($article->link);
						if(!empty($article->link))
						{
							$article->link = implode('/', $article->link);
							$link .= '/'.$article->link;
						}
						$this->redirect($link);
					}

					$_SESSION['notify']->success = 'Дані успішно оновлено!';
				}
				$this->redirect('admin/'.$_SESSION['alias']->alias.'/'.$link);
			}
		}
	}
	
	public function delete()
	{
		if(isset($_POST['id']) && is_numeric($_POST['id']))
		{
			$this->load->smodel('articles_model');
			$link = $this->articles_model->delete($_POST['id']);
			$_SESSION['notify'] = new stdClass();
			$_SESSION['notify']->success = $_SESSION['admin_options']['word:article_to_delete'].' успішно видалено!';
			$this->redirect("admin/{$_SESSION['alias']->alias}/{$link}");
		}
	}
	
	public function changeposition()
	{
		if(isset($_POST['id']) && is_numeric($_POST['id']) && is_numeric($_POST['position']))
		{
			$this->load->smodel('articles_model');
			$this->load->model('wl_position_model');
			
			if($_SESSION['option']->useGroups > 0 && $_SESSION['option']->articleMultiGroup == 0)
			{
				$article = $this->db->getAllDataById($this->articles_model->table(), $_POST['id']);
				if($article) {
					$this->wl_position_model->where = "`group` = '{$article->group}'";
				}
			}
			
			$this->wl_position_model->table = $this->articles_model->table();
			if($this->wl_position_model->change($_POST['id'], $_POST['position'])) {
				$this->redirect();
			}
		}
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
			$_SESSION['alias']->name = 'Групи '.$_SESSION['admin_options']['word:articles_to_all'];
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
			$this->wl_alias_model->setContent(($group->id * -1));
			$groups = $this->groups_model->getGroups(-1);
			$_SESSION['alias']->breadcrumb = array('Групи' => 'admin/'.$_SESSION['alias']->alias.'/groups', 'Редагувати групу' => '');
			$this->load->admin_view('groups/edit_view', array('group' => $group, 'groups' => $groups));
		}
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
				$alias = $title = false;
				if($id = $this->groups_model->add($alias, $title))
				{
					if(!empty($_FILES['photo']['name']) && $alias)
						$this->savephoto('photo', -$id, $alias, $title);
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
			if($group)
				$parent = $group->parent;
			
			$this->wl_position_model->table = $this->groups_model->table();
			if($parent >= 0)
				$this->wl_position_model->where = "`parent` = '{$parent}'";

			if($this->wl_position_model->change($_POST['id'], $_POST['position']))
				$this->redirect();
		}
		$this->load->page_404();
	}

	private function savephoto($name_field, $content, $name, $title = '')
	{
		if(!empty($_FILES[$name_field]['name']) && $_SESSION['option']->folder)
		{
			$path = IMG_PATH.$_SESSION['option']->folder.'/'.$content;
            $path = substr($path, strlen(SITE_URL));

            if(!is_dir($path))
            	mkdir($path, 0777);
            $path .= '/';

            $data['alias'] = $_SESSION['alias']->id;
            $data['content'] = $content;
            $data['file_name'] = '';
            $data['title'] = $title;
            $data['author'] = $_SESSION['user']->id;
            $data['date_add'] = $data['main'] = time();
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
						$this->image->loadImage($path, $name, $extension);
						if($resize->type == 1) $this->image->resize($resize->width, $resize->height, 100);
						if($resize->type == 2) $this->image->preview($resize->width, $resize->height, 100);
						$this->image->save($path, $resize->prefix);
					}
				}
				$name .= '.'.$extension;
                $this->db->updateRow('wl_images', array('file_name' => $name), $photo_id);
                return $name;
			}			
		}
		return false;
	}

	public function __get_Search($content)
    {
    	$this->load->smodel('library_search_model');
    	return $this->library_search_model->getByContent($content, true);
    }
	
}

?>
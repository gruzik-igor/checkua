<?php

/*

 	Service "forum 2.2"
	for WhiteLion 1.0

*/

class forum extends Controller {
				
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
    	$this->load->smodel('forum_model');
		
		$url = $this->data->url();
		$id = end($url);
		$id = explode($_SESSION['option']->idExplodeLink, $id);
		$id = $id[0];

		if(is_numeric($id))
		{
			$this->edit($id);
		}
		elseif($id != '' && count($url) > 2)
		{
			if($_SESSION['option']->useGroups)
			{
				$group = false;
				$parent = 0;
				array_shift($url);
				foreach ($url as $uri) {
					$group = $this->forum_model->getGroupByAlias($uri, $parent);
					if($group){
						$parent = $group->id;
					} else $group = false;
				}

				if($group){
					$group->alias_name = $_SESSION['alias']->name;
					$group->parents = array();
					if($group->parent > 0){
						$list = array();
			            $groups = $this->db->getAllData($this->forum_model->table('_groups'));
			            foreach ($groups as $Group) {
			            	$list[$Group->id] = clone $Group;
			            }
						$group->parents = $this->forum_model->makeParents($list, $group->parent, $group->parents);
					}
					$this->load->model('wl_ntkd_model');
					$this->wl_ntkd_model->setContent(($group->id * -1));

					$list = $this->forum_model->getGroups($group->id, false);
					if (empty($list) || $_SESSION['option']->ArticleMultiGroup == 1) {
						$list = $this->forum_model->getArticles($group->id, false);
						$this->load->admin_view('articles/list_view', array('group' => $group, 'articles' => $list));
					} else {
						$this->load->admin_view('index_view', array('group' => $group, 'groups' => $list));
					}
				} else $this->load->page_404();
			} else $this->load->page_404();
		} else {
			if($_SESSION['option']->useGroups){
				$list = $this->forum_model->getGroups(0, false);
				if (empty($list) || $_SESSION['option']->ArticleMultiGroup == 1) {
					$list = $this->forum_model->getArticles();
					$this->load->admin_view('articles/list_view', array('articles' => $list));
				} else {
					$this->load->admin_view('index_view', array('groups' => $list));
				}
				
			} else {
				$articles = $this->forum_model->getarticles(-1, false);
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
	
	private function edit($id = 0){
		$this->load->smodel('forum_model');
		$article = $this->forum_model->getArticleById($id);
		if($article){
			$_SESSION['alias']->breadcrumb = array($_SESSION['alias']->name => 'admin/'.$_SESSION['alias']->alias, 'Редагувати запис' => '');
			$_SESSION['alias']->name = 'Редагувати '.$article->name;

			$groups = null;
			if($_SESSION['option']->useGroups){
				$groups = $this->forum_model->getGroups();
				if($_SESSION['option']->ArticleMultiGroup){
					$activeGroups = $this->db->getAllDataByFieldInArray($this->forum_model->table('_article_group'), $article->id, 'article');
					$article->group = array();
					if($activeGroups){
						foreach ($activeGroups as $ag) {
							$article->group[] = $ag->group;
						}
					}
				}
			} 
			$photos = $this->forum_model->getArticlePhotos($article->id);

			$this->load->admin_view('articles/edit_view', array('article' => $article, 'groups' => $groups, 'photos' => $photos));
		} else $this->load->page_404();
	}
	
	public function save()
	{
		if(isset($_POST['id']) && is_numeric($_POST['id']))
		{
			$this->load->smodel('articles_model');
			if($_POST['id'] == 0)
			{
				$link = '';
				$id = $this->articles_model->add($link);
				if($id){
					$path = IMG_PATH.$_SESSION['alias']->alias.'/'.$id;
					$path = substr($path, strlen(SITE_URL));
					if(!is_dir($path)){
						mkdir($path, 0777);
					}
					if(!empty($_FILES['photo']['name'])) {
						$data['article'] = $id;
						$data['user'] = $_SESSION['user']->id;
						$data['date'] = time();
						$data['main'] = time();
						$this->db->insertRow($this->articles_model->table('_article_photos'), $data);
						$photo_id = $this->db->getLastInsertedId();
						$photo = $link . '-' . $photo_id;
						$extension = $this->savephoto('photo', $path.'/', $photo);
						if($extension){
							$photo .= '.'.$extension;
							$this->db->updateRow($this->articles_model->table('_articles'), array('photo' => $photo), $id);
							$this->db->updateRow($this->articles_model->table('_article_photos'), array('name' => $photo), $photo_id);
						}
					}
					$this->redirect("admin/{$_SESSION['alias']->alias}/{$link}");
				}
			}
			else
			{
				$this->articles_model->save($_POST['id']);

				if(isset($_POST['to']) && $_POST['to'] == 'new'){
					$this->redirect("admin/{$_SESSION['alias']->alias}/add");
				} elseif(isset($_POST['to']) && $_POST['to'] == 'category') {
					$link = 'admin/'.$_SESSION['alias']->alias;
					$article = $this->articles_model->getById($_POST['id']);
					$article->link = explode('/', $article->link);
					array_pop ($article->link);
					if(!empty($article->link)){
						$article->link = implode('/', $article->link);
						$link .= '/'.$article->link;
					}
					$this->redirect($link);
				}

				$_SESSION['notify'] = new stdClass();
				$_SESSION['notify']->success = 'Дані успішно оновлено!';
				$this->redirect();
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
			
			if($_SESSION['option']->useGroups > 0 && $_SESSION['option']->ArticleMultiGroup == 0)
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

	function changeGroup(){
		$res = array('result' => false);
		if($this->userCan($_SESSION['alias']->alias)){
			if(isset($_POST['group']) && is_numeric($_POST['group']) && isset($_POST['id']) && is_numeric($_POST['id'])){
				if($this->db->updateRow($_SESSION['service']->table.'_articles'.$_SESSION['alias']->table, array('group' => $_POST['group']), $_POST['id'])){
					$res['result'] = true;
				}
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

	// --- photo
	public function photo_add()
	{
		$res = array();
		$id = $this->data->uri(3);
		if(is_numeric($id)){
			$path = IMG_PATH.$_SESSION['option']->folder.'/'.$id;
			$name_field = 'photos';
			$error = 0;
			$path = substr($path, strlen(SITE_URL));
			if(!is_dir($path)){
				if(mkdir($path, 0777) == false){
					$error++;
					$res['error'] = 'Error create dir ' . $path;
				} 
			}
			$path = IMG_PATH.$_SESSION['option']->folder.'/'.$id.'/';

			$this->load->smodel('forum_model');
			$article = $this->db->getAllDataById($this->forum_model->table('_articles'), $id);

			if($article && !empty($_FILES[$name_field]['name'][0]) && $error == 0){
				$length = count($_FILES[$name_field]['name']);
				for($i = 0; $i < $length; $i++){
					$data['article'] = $article->id;
					$data['user'] = $_SESSION['user']->id;
					$data['date'] = time();
					$data['main'] = time();
					$this->db->insertRow($this->forum_model->table('_article_photos'), $data);
					$photo_id = $this->db->getLastInsertedId();
					$photo_name = $article->alias . '-' . $photo_id;
					
					$extension = $this->savephoto($name_field, $path, $photo_name, true, $i);
					if($extension){
						$photo_name .= '.'.$extension;
						$this->db->updateRow($this->forum_model->table('_articles'), array('author_edit' => $_SESSION['user']->id, 'date_edit' => time(), 'photo' => $photo_name), $id);
						$this->db->updateRow($this->forum_model->table('_article_photos'), array('name' => $photo_name), $photo_id);

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
			$photo = $this->db->getAllDataById($_SESSION['service']->table.'_article_photos'.$_SESSION['alias']->table, $_POST['photo']);
			if(!empty($photo)){
				$data = array();
				if($_POST['name'] == 'title') $data['title'] = $this->db->sanitizeString($_POST['title']);
				if($_POST['name'] == 'active'){
					$data['main'] = time();
					$this->db->updateRow($_SESSION['service']->table.'_articles'.$_SESSION['alias']->table, array('author_edit' => $_SESSION['user']->id, 'date_edit' => time(), 'photo' => $photo->name), $photo->article);
				} 
				if(!empty($data)) if($this->db->updateRow($_SESSION['service']->table.'_article_photos'.$_SESSION['alias']->table, $data, $_POST['photo'])){
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
			$photo = $this->db->getAllDataById($_SESSION['service']->table.'_article_photos'.$_SESSION['alias']->table, $_POST['photo']);
			if(!empty($photo)){
				if($this->db->deleteRow($_SESSION['service']->table.'_article_photos'.$_SESSION['alias']->table, $_POST['photo'])){
					$path = IMG_PATH.$_SESSION['alias']->alias.'/'.$photo->article.'/';
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

					$article = $this->db->getAllDataById($_SESSION['service']->table.'_articles'.$_SESSION['alias']->table, $photo->article);
					if($article) {
						$data['author_edit'] = time();
						$data['date_edit'] = time();
						if($article->photo == $photo->name){
							$data['photo'] = 0;
							$photos = $this->db->getAllDataByFieldInArray($_SESSION['service']->table.'_article_photos'.$_SESSION['alias']->table, $article->id, 'article', 'main DESC');
							if($photos) $data['photo'] = $photos[0]->name;
							else $data['photo'] = '';
						}
						$this->db->updateRow($_SESSION['service']->table.'_articles'.$_SESSION['alias']->table, $data, $article->id);
					}
				}
			} else $res['error'] = 'Фотографію не знайдено!';
		}
		header('Content-type: application/json');
		echo json_encode($res);
		exit;
	}

	private function savephoto($name_field, $path, $name, $array = false, $i = 0)
	{
		if(!empty($_FILES[$name_field]['name']))
		{
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
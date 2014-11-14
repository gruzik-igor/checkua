<?php

class library extends Controller {
				
    function _remap($method)
    {
        if (method_exists($this, $method)) {
            $this->$method();
        } else {
        	$request = $_GET['request'];
        	$request = explode('/', $request);
        	if($request[0] == 'admin') $this->admin($method);
            else $this->index($method);
        }
    }

    public function index()
    {
    	$this->load->smodel('articles_model');
		
		$uri = $this->data->uri(1);
		$uri = explode('-',$uri);
		$id = $uri[0];
		
		if(is_numeric($id)) {
			$article = $this->articles_model->getArticleById($id);
			if($article && ($article->active == 1 || $this->userCan($_SESSION['alias']))){
				$article->alias_name = $_SESSION['alias']->name;
				$this->load->model('wl_ntkd_model');
				$this->wl_ntkd_model->setContent($article->id);
				$this->load->page_view('detal_view', array('article' => $article));
			} else $this->load->page_404();
		} elseif($id != '') {
			if($_SESSION['option']->useCategories){
				$category = $this->articles_model->getCategoryByAlias($this->data->uri(1));
				if($category && ($category->active == 1 || $this->userCan($_SESSION['alias']))){
					$this->load->model('wl_ntkd_model');
					$this->wl_ntkd_model->setContent(($category->id * -1));
					$articles = $this->articles_model->getArticles($category->id);
					$categories = $this->articles_model->getCategories();
					$this->load->page_view('index_view', array('category' => $category, 'articles' => $articles, 'categories' => $categories));
				} else $this->load->page_404();
			} else $this->load->page_404();
		} else {
			// if($_SESSION['option']->useCategories){
				$categories = $this->articles_model->getCategories();
				$articles = $this->articles_model->getArticles();
				$this->load->page_view('index_view', array('categories' => $categories, 'articles' => $articles));
			// } else {
				// $articles = $this->articles_model->getArticles();
				// $this->load->page_view('articles_view', array('articles' => $articles));
			// }
		}
    }
	
	function getCategories()
	{
		$this->load->smodel('articles_model');
		return $this->articles_model->getCategories();
	}

	function getArticles()
	{
		$this->load->smodel('articles_model');
		return $this->articles_model->getArticles();
	}

	function admin(){
		if($this->userCan($_SESSION['alias']->alias)){
			$this->load->smodel('articles_model');
			if($_SESSION['option']->useCategories){
				if($_SESSION['option']->articleMultiCategory){
					$_SESSION['option']->PerPage = 0;
					$articles = $this->articles_model->getArticles(0, false);
					$this->load->admin_view('admin/list_view', array('articles' => $articles));
				} else {
					if($this->data->uri(2) != ''){
						$category = $this->articles_model->getCategoryByAlias($this->data->uri(2));
						if($category){
							$_SESSION['option']->PerPage = 0;
							$articles = $this->articles_model->getArticles($category->id, false);
							$this->load->admin_view('admin/list_view', array('articles' => $articles, 'category' => $category));
						} else $this->load->page_404();
					} else {
						$categories = $this->articles_model->getCategories();
						$this->load->admin_view('admin/index_view', array('categories' => $categories));
					}
				}
			} else {
				$_SESSION['option']->PerPage = 0;
				$articles = $this->articles_model->getArticles(0, false);
				$this->load->admin_view('admin/list_view', array('articles' => $articles));
			}			
		} else $this->load->page_404();
	}

	function all(){
		if($this->userCan($_SESSION['alias']->alias)){
			$this->load->smodel('articles_model');
			$_SESSION['option']->PerPage = 0;
			$articles = $this->articles_model->getArticles(0, false);
			$this->load->admin_view('admin/list_view', array('articles' => $articles));			
		} else $this->load->page_404();
	}
	
	function add(){
		if($this->userCan($_SESSION['alias']->alias)){
			$this->load->admin_view('admin/add_article_view');
		}
	}
	
	function edit(){
		if($this->userCan($_SESSION['alias']->alias)){
			$id = $this->data->uri(3);	
			if(is_numeric($id)){
				$this->load->smodel('articles_model');
				$article = $this->articles_model->getArticleById($id, false);
				if($article){
					$categories = null;
					if($_SESSION['option']->useCategories){
						$categories = $this->articles_model->getCategories();
						if($_SESSION['option']->articleMultiCategory){
							$activeCategories = $this->db->getAllDataByFieldInArray($this->articles_model->table('_article_category'), $article->id, 'article');
							$article->category = array();
							if($activeCategories){
								foreach ($activeCategories as $ac) {
									$article->category[] = $ac->category;
								}
							}
						}
					} 
					$this->load->admin_view('admin/edit_article_view', array('article' => $article, 'categories' => $categories));
				} else $this->load->page_404();
			} else $this->load->page_404();
		}
	}
	
	function save(){
		if($this->userCan($_SESSION['alias']->alias)){
			if(isset($_POST['id']) && is_numeric($_POST['id'])){
				$this->load->smodel('articles_model');
				if($_POST['id'] == 0){
					$photo = false;
					if(!empty($_FILES['photo']['name'])) $photo = true;
					$id = $this->articles_model->add_article($photo);
					if($id){
						if(!empty($_FILES['photo']['name'])) $this->savephoto('photo', IMG_PATH.$_SESSION['option']->folder.'/', $id);
						header("Location: ".SITE_URL.'admin/'.$_SESSION['alias']->alias.'/edit/'.$id);
						exit;
					}
				} else {
					$data = array('active' => 1);
					if(isset($_POST['link']) && $_POST['link'] != '') $data['link'] = $_POST['id'] . '-' . $_POST['link'];
					if(isset($_POST['active']) && $_POST['active'] == 0) $data['active'] = 0;
					if($_SESSION['option']->useCategories){
						if($_SESSION['option']->articleMultiCategory){
							$use = array();
							$activeCategories = $this->db->getAllDataByFieldInArray($this->articles_model->table('_article_category'), $_POST['id'], 'article');
							if($activeCategories) {
								$temp = array();
								foreach ($activeCategories as $ac) {
									$temp[] = $ac->category;
								}
								$activeCategories = $temp;
								$temp = null;
							}
							if(isset($_POST['category']) && is_array($_POST['category'])){
								foreach ($_POST['category'] as $category) {
									if(!in_array($category, $activeCategories)){
										$this->db->insertRow($this->articles_model->table('_article_category'), array('article' => $_POST['id'], 'category' => $category));
									}
									$use[] = $category;
								}
							}
							if($activeCategories) {
								foreach ($activeCategories as $ac) {
									if(!in_array($ac, $use)){
										$this->db->executeQuery("DELETE FROM {$this->articles_model->table('_article_category')} WHERE `article` = '{$_POST['id']}' AND `category` = '{$ac}'");
									}
								}
							}
						} else {
							if(isset($_POST['category']) && is_numeric($_POST['category'])) $data['category'] = $_POST['category'];
						}
					}
					if(!empty($_FILES['photo']['name'])){
						$data['photo'] = $_POST['id'];

						$path = IMG_PATH.$_SESSION['option']->folder.'/';
						if(strlen(IMG_PATH) > strlen(SITE_URL)) $path = substr($path, strlen(SITE_URL));
						if(!is_dir($path)) mkdir($path, 0777);

						$this->savephoto('photo', $path, $_POST['id']);
					}
					if($this->db->updateRow($this->articles_model->table(), $data, $_POST['id'])){
						$link = '';
						if($_SESSION['option']->useCategories == 1 && $_SESSION['option']->articleMultiCategory == 0 && isset($data['category'])){
							$category = $this->db->getAllDataById($this->articles_model->table('_categories'), $data['category']);
							if($category) $link = '/'.$category->link;
						}
						header("Location: ".SITE_URL.'admin/'.$_SESSION['alias']->alias.$link);
						exit;
					}
				}
			}
		} else $this->load->page_404();
	}
	
	function delete(){
		if($this->userCan($_SESSION['alias']->alias)){
			if(isset($_POST['id']) && is_numeric($_POST['id'])){
				$this->load->smodel('articles_model');
				$article = $this->db->getAllDataById($this->articles_model->table(), $_POST['id']);
				if($article){

					$this->db->deleteRow($this->articles_model->table(), $article->id);
					$this->db->executeQuery("UPDATE `{$this->articles_model->table()}` SET `position` = position - 1 WHERE `id` > '{$article->id}'");
					$this->db->executeQuery("DELETE FROM wl_ntkd WHERE alias = '{$_SESSION['alias']->id}' AND content = '{$article->id}'");
					if($article->photo > 0){
						$path = IMG_PATH.$_SESSION['option']->folder.'/';
						if(strlen(IMG_PATH) > strlen(SITE_URL)) $path = substr($path, strlen(SITE_URL));

						$prefix = array('');
						$sizes = $this->db->getAllDataByFieldInArray($_SESSION['service']->table.'_photo_size', $_SESSION['alias']->id, 'alias');
						if($sizes){
							foreach ($sizes as $resize) if($resize->active == 1 && $resize->prefix != ''){
								$prefix[] = $resize->prefix .'_';
							}
						}
						
						foreach ($prefix as $p) {
							$filename = $path.$p.$article->photo.'.jpg';
							@unlink ($filename);
						}
					}

					$link = '';
					if($_SESSION['option']->useCategories == 1 && $_SESSION['option']->articleMultiCategory == 0){
						$category = $this->db->getAllDataById($this->articles_model->table('_categories'), $article->category);
						if($category) $link = '/'.$category->link;
					}
					header("Location: ".SITE_URL.'admin/'.$_SESSION['alias']->alias.$link);
					exit;

				}
			}
		} else $this->load->page_404();
	}
	
	function changeposition(){
		if($this->userCan($_SESSION['alias']->alias)){
			if(isset($_POST['id']) && is_numeric($_POST['id']) && is_numeric($_POST['position'])){
				$this->load->smodel('articles_model');
				if($this->articles_model->changePosition('', $_POST['id'], $_POST['position'])){
					header("Location: ".$_SERVER['HTTP_REFERER']);
					exit;
				} else $this->load->page_404();
			}
		}
	}

	function changeCategory(){
		$res = array('result' => false);
		if($this->userCan($_SESSION['alias']->alias)){
			if(isset($_POST['category']) && is_numeric($_POST['category']) && isset($_POST['id']) && is_numeric($_POST['id'])){
				if($this->db->updateRow($_SESSION['service']->table.$_SESSION['alias']->table, array('category' => $_POST['category']), $_POST['id'])){
					$res['result'] = true;
				}
			}
		}
		header('Content-type: application/json');
		echo json_encode($res);
		exit;
	}

	function categories(){
		if($this->userCan($_SESSION['alias']->alias)){
			$this->load->smodel('articles_model');
			$categories = $this->articles_model->getCategories(false);
			$this->load->admin_view('admin/categories_view', array('categories' => $categories));			
		} else $this->load->page_404();
	}

	function add_category(){
		if($this->userCan($_SESSION['alias']->alias)){
			$this->load->admin_view('admin/add_category_view');
		} else $this->load->page_404();
	}

	function edit_category(){
		if($this->userCan($_SESSION['alias']->alias)){
			$id = $this->data->uri(3);	
			if(is_numeric($id)){
				$this->load->smodel('articles_model');
				$category = $this->articles_model->getCategoryById($id, false);
				if($category){
					$this->load->admin_view('admin/edit_category_view', array('category' => $category));
				} else $this->load->page_404();
			} else $this->load->page_404();
		}
	}

	function save_category(){
		if($this->userCan($_SESSION['alias']->alias)){
			if(isset($_POST['id']) && is_numeric($_POST['id'])){
				$this->load->smodel('articles_model');
				if($_POST['id'] == 0){
					$photo = false;
					if(!empty($_FILES['photo']['name'])) $photo = true;
					$id = $this->articles_model->add_category($photo);
					if($id){
						if(!empty($_FILES['photo']['name'])) $this->savephoto('photo', IMG_PATH.$_SESSION['option']->folder.'/categories/', $id);
						header("Location: ".SITE_URL.'admin/'.$_SESSION['alias']->alias.'/edit_category/'.$id);
						exit;
					}
				} else {
					$data = array('active' => 1);
					if(isset($_POST['link']) && $_POST['link'] != '') $data['link'] = $_POST['link'];
					if(isset($_POST['active']) && $_POST['active'] == 0) $data['active'] = 0;
					if(!empty($_FILES['photo']['name'])){
						$data['photo'] = $_POST['id'];

						$path = IMG_PATH.$_SESSION['option']->folder.'/categories/';
						if(strlen(IMG_PATH) > strlen(SITE_URL)) $path = substr($path, strlen(SITE_URL));
						if(!is_dir($path)) mkdir($path, 0777);

						$this->savephoto('photo', $path, $_POST['id']);
					}
					if($this->db->updateRow($this->articles_model->table('_categories'), $data, $_POST['id'])){
						header("Location: ".SITE_URL.'admin/'.$_SESSION['alias']->alias.'/categories');
						exit;
					}
				}
			}
		} else $this->load->page_404();
	}

	function delete_category(){
		if($this->userCan($_SESSION['alias']->alias)){
			if(isset($_POST['id']) && is_numeric($_POST['id'])){
				$this->load->smodel('articles_model');
				$category = $this->db->getAllDataById($this->articles_model->table('_categories'), $_POST['id']);
				if($category){

					$content = false;
					if(isset($_POST['content']) && $_POST['content'] == 1) $content = true;
					if($content){
						$articles = $this->articles_model->getArticles($category->id);
						if($articles) foreach ($articles as $a) {
							$this->db->deleteRow($this->articles_model->table(), $a->id);
							$this->db->executeQuery("DELETE FROM wl_ntkd WHERE alias = '{$_SESSION['alias']->id}' AND content = '{$a->id}'");
							if($a->photo > 0){
								$path = IMG_PATH.$_SESSION['option']->folder.'/';
								if(strlen(IMG_PATH) > strlen(SITE_URL)) $path = substr($path, strlen(SITE_URL));

								$prefix = array('');
								$sizes = $this->db->getAllDataByFieldInArray($_SESSION['service']->table.'_photo_size', $_SESSION['alias']->id, 'alias');
								if($sizes){
									foreach ($sizes as $resize) if($resize->active == 1 && $resize->prefix != ''){
										$prefix[] = $resize->prefix .'_';
									}
								}
								
								foreach ($prefix as $p) {
									$filename = $path.$p.$a->photo.'.jpg';
									@unlink ($filename);
								}
							}
						}
					}

					$this->db->deleteRow($this->articles_model->table('_categories'), $category->id);
					$this->db->executeQuery("UPDATE `{$this->articles_model->table('_categories')}` SET `position` = position - 1 WHERE `id` > '{$category->id}'");
					$this->db->executeQuery("DELETE FROM wl_ntkd WHERE alias = '{$_SESSION['alias']->id}' AND content = '-{$category->id}'");
					if($category->photo > 0){
						$path = IMG_PATH.$_SESSION['option']->folder.'/categories/';
						if(strlen(IMG_PATH) > strlen(SITE_URL)) $path = substr($path, strlen(SITE_URL));

						$prefix = array('');
						$sizes = $this->db->getAllDataByFieldInArray($_SESSION['service']->table.'_photo_size', $_SESSION['alias']->id, 'alias');
						if($sizes){
							foreach ($sizes as $resize) if($resize->active == 1 && $resize->prefix != ''){
								$prefix[] = $resize->prefix .'_';
							}
						}
						
						foreach ($prefix as $p) {
							$filename = $path.$p.$category->photo.'.jpg';
							@unlink ($filename);
						}
					}

					header("Location: ".SITE_URL.'admin/'.$_SESSION['alias']->alias.'/categories');
					exit;

				}
			}
		} else $this->load->page_404();
	}

	function change_category_position(){
		if($this->userCan($_SESSION['alias']->alias)){
			if(isset($_POST['id']) && is_numeric($_POST['id']) && is_numeric($_POST['position'])){
				$this->load->smodel('articles_model');
				if($this->articles_model->changePosition('_categories', $_POST['id'], $_POST['position'])){
					header("Location: ".SITE_URL.'admin/'.$_SESSION['alias']->alias.'/categories');
					exit;
				} else $this->load->page_404();
			}
		}
	}

	private function savephoto($name_field, $path, $name, $array = false, $i = 0){
		if(!empty($_FILES[$name_field]['name'])){
			$this->load->library('image');
			if($array) $this->image->uploadArray($name_field, $i, $path, $name);
			else $this->image->upload($name_field, $path, $name);
			$this->image->save();
			if($this->image->getErrors() == ''){
				if($_SESSION['option']->resize > 0){
					$sizes = $this->db->getAllDataByFieldInArray($_SESSION['service']->table.'_photo_size', $_SESSION['alias']->id, 'alias');
					if($sizes){
						foreach ($sizes as $resize) if($resize->active == 1){
							$this->image->loadImage($path, $name);
							if($resize->type == 1) $this->image->resize($resize->width, $resize->height, 100);
							if($resize->type == 2) $this->image->preview($resize->width, $resize->height, 100);
							$this->image->save($path, $resize->prefix);
						}
					}
				}
				return true;
			}
		}
		return false;
	}
	
}

?>
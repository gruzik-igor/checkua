<?php

// Простий фотоальбом без розмежування по користувачах. Підтримує аякс завантаження фото. Розміри із БД

class photo extends Controller {
				
    function _remap($method)
    {
        if (method_exists($this, $method)) {
            $this->$method();
        } else {
            $this->index($method);
        }
    }

    function index(){
		$this->load->smodel('photo_model');
		$uri = $this->data->uri(1);
		$uri = explode('-',$uri);
		$id = $uri[0];
		
		if(is_numeric($id)){
			$album = $this->photo_model->getAlbumInfoById($id);
			if($album){
				$this->load->model('wl_ntkd_model');
				$this->wl_ntkd_model->setContent($album->id);
				$photo = $this->photo_model->getAlbumPhotos($id);
				$this->load->page_view('photos_view', array('photos' => $photo, 'album' => $album));
			} else $this->load->page_404();
		} else {
			$albums = $this->photo_model->getAlbums();
			$this->load->page_view('albums_view', array('albums' => $albums));
		}
    }
	
	public function admin(){
		$go = false;
		if($_SESSION['option']->canAdd < 3){
			if($this->userCan()) $go = true;
		} elseif(isset($_SESSION['user']->id) && $_SESSION['user']->id > 0) $go = true;

		if($go){
			$this->load->smodel('photo_model');
			$albums = $this->photo_model->getAlbums(false);
			$this->load->admin_view('admin_all_albums_view', array('albums' => $albums));
		}
	}
	
	public function add_album(){
		$go = false;
		if($_SESSION['option']->canAdd < 3){
			if($this->userCan()) $go = true;
		} elseif(isset($_SESSION['user']->id) && $_SESSION['user']->id > 0) $go = true;

		if($go){
			$this->load->admin_view('add_album_view');
		} else {
			header('Location: '.SITE_URL.'login');
			exit;
		}
	}

	public function create_album()
	{
		$go = false;
		if($_SESSION['option']->canAdd < 3){
			if($this->userCan()) $go = true;
		} elseif(isset($_SESSION['user']->id) && $_SESSION['user']->id > 0) $go = true;

		if($go){
			$data['user'] = $_SESSION['user']->id;
			$data['photo'] = -1;
			$data['date'] = time();
			$data['active'] = 1;
			$this->load->smodel('photo_model');
			if($this->db->insertRow($this->photo_model->table('_albums'), $data)){
				$id_album = $this->db->getLastInsertedId();
				$link = $id_album.'-'.$this->db->latterUAtoEN($_POST['name']);
				$this->db->updateRow($this->photo_model->table('_albums'), array('link' => $link, 'position' => $id_album), $id_album);

				$ntkd['alias'] = $_SESSION['alias']->id;
				$ntkd['content'] = $id_album;
				$ntkd['name'] = $this->data->post('name');
				$ntkd['title'] = $this->data->post('name');
				$ntkd['description'] = $this->data->post('description');
				$ntkd['text'] = $this->data->post('description');

				if($_SESSION['language']){
					foreach ($_SESSION['all_languages'] as $lang) {
						$ntkd['language'] = $lang;
						$this->db->insertRow('wl_ntkd', $ntkd);
					}
				} else $this->db->insertRow('wl_ntkd', $ntkd);
			
				$path = IMG_PATH.$_SESSION['option']->folder.'/'.$id_album;
				if(strlen(IMG_PATH) > strlen(SITE_URL)) $path = substr($path, strlen(SITE_URL));
				if(!is_dir($path)) mkdir($path, 0777);

				header("Location: ".SITE_URL.$_SESSION['alias']->alias.'/edit/'.$id_album);
				exit;
			} else {

			}
		} else {
			header('Location: '.SITE_URL.'login');
			exit;
		}
	}

	function edit(){
		$go = false;
		if($_SESSION['option']->canAdd < 3){
			if($this->userCan('photo')) $go = true;
		} elseif(isset($_SESSION['user']->id) && $_SESSION['user']->id > 0) $go = true;

		if($go){
			$id = $this->data->uri(2);		
			if(is_numeric($id)){
				$this->load->smodel('photo_model');
				$album = $this->photo_model->getAlbumInfoById($id);
				if($album && ($this->userCan('photo') || $album->user == $_SESSION['user']->id)){
					$this->load->model('wl_ntkd_model');
					if($_SESSION['language']){
						$current_languade = $_SESSION['language'];
						foreach ($_SESSION['all_languages'] as $lang) {
							$_SESSION['language'] = $lang;
							$ntkd[$lang] = $this->wl_ntkd_model->get($_SESSION['alias']->alias, $album->id, false);
							if($current_languade == $lang){
								$album->name = $ntkd[$lang]->name;
								$album->text = $ntkd[$lang]->text;
							}
						}
						$_SESSION['language'] = $current_languade;
					} else {
						$ntkd = $this->wl_ntkd_model->get($_SESSION['alias']->alias, $album->id, false);
					}

					$photos = $this->photo_model->getAlbumPhotos($id);
					$this->load->admin_view('edit_album_view', array('album' => $album, 'ntkd' => $ntkd, 'photos' => $photos));
				} else {
					$this->load->notify_view(array('errors' => 'Редагувати альбом може тільки автор або адміністрація!'));
				}
			} else {
				$this->load->page_404();
			}
		} else {
			header('Location: '.SITE_URL.'login');
			exit;
		}
	}

	function save_album(){
		$go = false;
		if($_SESSION['option']->canAdd < 3){
			if($this->userCan('photo')) $go = true;
		} elseif(isset($_SESSION['user']->id) && $_SESSION['user']->id > 0) $go = true;

		if($go){
			if(isset($_POST['album']) && is_numeric($_POST['album']) && $_POST['album'] > 0){
				$this->load->smodel('photo_model');

				$album = $this->db->getAllDataById($this->photo_model->table('_albums'), $_POST['album']);
				if(!empty($album) && ($album->user == $_SESSION['user']->id || $this->userCan('photo'))){
					$data = array();

					$active = 1;
					if(isset($_POST['active']) == 0) $active = 0;
					if($album->active != $active) $data['active'] = $active;
					
					$link = $album->link;
					if(isset($_POST['link']) && $_POST['link'] != '') $link = $album->id .'-'. $this->db->sanitizeString($_POST['link']);
					if($album->link != $link) $data['link'] = $link;
				
					if(!empty($_FILES['photo']['name'])){
						$path = IMG_PATH.$_SESSION['option']->folder.'/'.$album->id.'/';
						if(strlen(IMG_PATH) > strlen(SITE_URL)) $path = substr($path, strlen(SITE_URL));
						if($this->savephoto('photo', $path, '0'))	$data['photo'] = 0;
					}

					if(!empty($data)){
						$this->db->updateRow($this->photo_model->table('_albums'), $data, $album->id);
					}
					
					if($_SESSION['language'] && isset($_POST['name_'.$_SESSION['all_languages'][0]])){
						foreach ($_SESSION['all_languages'] as $lang) {
							$ntkd = array();
							if(isset($_POST['name_'.$lang]) && $_POST['name_'.$lang] != '') $ntkd['name'] = $_POST['name_'.$lang];
							if(isset($_POST['text_'.$lang])) $ntkd['text'] = $_POST['text_'.$lang];
							if(isset($_POST['title_'.$lang]) && $_POST['title_'.$lang] != '') $ntkd['title'] = $_POST['title_'.$lang];
							if(isset($_POST['description_'.$lang])) $ntkd['description'] = $_POST['description_'.$lang];

							$update = "UPDATE `wl_ntkd` SET ";
					        foreach ($ntkd as $key => $value){
					            $value = $this->db->sanitizeString($value);
					            $update .= "`{$key}` = '{$value}',";
					        }
					        $update = substr($update, 0, -1);
					        $update .= "WHERE `alias` = '{$_SESSION['alias']->id}' AND `content` = '{$album->id}' AND `language` = '{$lang}'";
					        $this->db->executeQuery($update);
							if($this->db->affectedRows() == 0){
								$ntkd['alias'] = $_SESSION['alias']->id;
								$ntkd['content'] = $album->id;
								$ntkd['language'] = $lang;
								$this->db->insertRow('wl_ntkd', $ntkd);
							}
						}
					} else {
						$ntkd = array();
						if(isset($_POST['name']) && $_POST['name'] != ''){
							$ntkd['name'] = $_POST['name'];
							$ntkd['title'] = $_POST['name'];
						} 
						if(isset($_POST['text'])){
							$ntkd['text'] = $_POST['text'];
							$ntkd['description'] = $_POST['text'];
						} 

						$update = "UPDATE `wl_ntkd` SET ";
				        foreach ($ntkd as $key => $value){
				            $value = $this->db->sanitizeString($value);
				            $update .= "`{$key}` = '{$value}',";
				        }
				        $update = substr($update, 0, -1);
				        $update .= "WHERE `alias` = '{$_SESSION['alias']->id}' AND `content` = '{$album->id}'";
				        $this->db->executeQuery($update);
						if($this->db->affectedRows() == 0){
							$ntkd['alias'] = $_SESSION['alias']->id;
							$ntkd['content'] = $album->id;
							$this->db->insertRow('wl_ntkd', $ntkd);
						}
					}
					header("Location: ".SITE_URL.$_SESSION['alias']->alias.'/'.$link);
					exit();
				}
			}
		}
	}

	public function setAlbumPhoto()
	{
		$res = array('result' => false, 'error' => 'Доступ заборонено! Тільки автор або адміністрація!');
		if(isset($_SESSION['user']) && $_SESSION['user']->id > 0 && isset($_POST['photo']) && is_numeric($_POST['photo']) && isset($_POST['album']) && is_numeric($_POST['album'])){
			$this->load->smodel('photo_model');
			$album = $this->db->getAllDataById($this->photo_model->table('_albums'), $_POST['album']);
			if(!empty($album) && ($album->user == $_SESSION['user']->id || $this->userCan('photo'))){
				if($this->db->updateRow($this->photo_model->table('_albums'), array('photo' => $_POST['photo']), $_POST['album'])){
					$res['result'] = true;
					$res['error'] = '';
				}
			} else $res['error'] = 'Фотографію не знайдено!';
		}
		header('Content-type: application/json');
		echo json_encode($res);
		exit;
	}
	
	public function upload(){
		$res = array();

		if (isset($_SESSION['user']->id) && $_SESSION['user']->id > 0) {
			$album_id = $this->data->uri(3);
			if(is_numeric($album_id)){
				$error = 0;
				$name_field = 'photos';

				$path = IMG_PATH.$_SESSION['option']->folder.'/'.$album_id;
				if(strlen(IMG_PATH) > strlen(SITE_URL)) $path = substr($path, strlen(SITE_URL));
				if(!is_dir($path)){
					if(mkdir($path, 0777) == false){
						$error++;
						$res['error'] = 'Error create dir ' . $path;
					} 
				}
				$path .= '/';

				if(!empty($_FILES[$name_field]['name'][0]) && $error == 0){
					$length = count($_FILES[$name_field]['name']);
					for($i = 0; $i < $length; $i++){
						$data = array();
						$data['album'] = $album_id;
						$data['user'] = $_SESSION['user']->id;
						$data['name'] = $_FILES[$name_field]['name'][$i];
						$data['date'] = time();

						$this->load->smodel('photo_model');
						$this->db->insertRow($this->photo_model->table(), $data);
						if($this->db->affectedRows() > 0){
							$name = $this->db->getLastInsertedId();
							if(!$this->savephoto($name_field, $path, $name, true, $i)) $error++;
							else {
								$photo['id'] = $name;
								$photo['name'] = $_FILES[$name_field]['name'][$i];
								$photo['date'] = date('d.m.Y H:i');
								$photo['url'] = IMG_PATH.$_SESSION['option']->folder.'/'.$album_id.'/'.$name.'.jpg';
								$photo['thumbnailUrl'] = IMG_PATH.$_SESSION['option']->folder.'/'.$album_id.'/s_'.$name.'.jpg';
								$res[] = $photo;
							}
						} else $error++;						
					}
				}
				if($error > 0){
					$photo['result'] = false;
					$photo['error'] = "Access Denied!";
					$res[] = $photo;
				}
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
	
	private function savephoto($name_field, $path, $name, $array = false, $i = 0){
		if(!empty($_FILES[$name_field]['name'])){
			$this->load->library('image');
			if($array) $this->image->uploadArray($name_field, $i, $path, $name);
			else $this->image->upload($name_field, $path, $name);
			$this->image->save();
			if($this->image->getErrors() == ''){
				if($_SESSION['option']->resize > 0){
					$sizes = $this->db->getAllDataByFieldInArray('s_photos_photo_size', $_SESSION['alias']->id, 'alias');
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

	public function save_photo()
	{
		$res = array('result' => false, 'error' => 'Доступ заборонено! Тільки автор або адміністрація!');
		if(isset($_SESSION['user']) && $_SESSION['user']->id > 0 && isset($_POST['photo']) && is_numeric($_POST['photo']) && isset($_POST['name'])){
			$this->load->smodel('photo_model');
			$photo = $this->db->getAllDataById($this->photo_model->table(), $_POST['photo']);
			if(!empty($photo)){
				$data = array();
				if($photo->user == $_SESSION['user']->id || $this->userCan()){
					$data['name'] = $this->db->sanitizeString($_POST['name']);
					if($this->db->updateRow($this->photo_model->table(), $data, $_POST['photo'])){
						$res['result'] = true;
						$res['error'] = '';
					}
				}
			} else $res['error'] = 'Фотографію не знайдено!';
		}
		header('Content-type: application/json');
		echo json_encode($res);
		exit;
	}
	
	public function delete_photo()
	{
		$res = array('result' => false, 'error' => 'Доступ заборонено! Тільки автор або адміністрація!');
		if(isset($_SESSION['user']) && $_SESSION['user']->id > 0 && isset($_POST['photo']) && is_numeric($_POST['photo'])){
			$this->load->smodel('photo_model');
			$photo = $this->db->getAllDataById($this->photo_model->table(), $_POST['photo']);
			if(!empty($photo) && ($photo->user == $_SESSION['user']->id || $this->userCan('photo'))){
				if($this->db->deleteRow($this->photo_model->table(), $_POST['photo'])){
					$path = IMG_PATH.$_SESSION['option']->folder.'/'.$photo->album.'/';
					if(strlen(IMG_PATH) > strlen(SITE_URL)) $path = substr($path, strlen(SITE_URL));

					$prefix = array('');
					$sizes = $this->db->getAllDataByFieldInArray('s_photos_photo_size', $_SESSION['alias']->id, 'alias');
					if($sizes){
						foreach ($sizes as $resize) if($resize->active == 1 && $resize->prefix != ''){
							$prefix[] = $resize->prefix .'_';
						}
					}
					
					foreach ($prefix as $p) {
						$filename = $path.$p.$_POST['photo'].'.jpg';
						unlink ($filename);
					}
					$res['result'] = true;
					$res['error'] = '';
				}
			} else $res['error'] = 'Фотографію не знайдено!';
		}
		header('Content-type: application/json');
		echo json_encode($res);
		exit;
	}

	public function delete_album(){
		if(isset($_POST['id']) && is_numeric($_POST['id'])){
			$this->load->smodel('photo_model');
			$access = false;
			if($this->userCan('photo'))	$access = true;
			elseif(isset($_SESSION['user']->id)){
				$album = $this->photo_model->getAlbumInfoById($_POST['id']);
				if($album->user == $_SESSION['user']->id) $access = true;
			}
			if($access){
				$path = IMG_PATH.$_SESSION['option']->folder.'/'.$_POST['id'];
				if(strlen(IMG_PATH) > strlen(SITE_URL)) $path = substr($path, strlen(SITE_URL));
				if(is_dir($path)) $this->removeDirectory($path);

				$this->db->executeQuery("DELETE FROM `wl_ntkd` WHERE `alias` = '{$_SESSION['alias']->id}' AND `content` = '{$_POST['id']}'");
				$this->db->deleteRow($this->photo_model->table(), $_POST['id'], 'album');
				$this->db->deleteRow($this->photo_model->table('_albums'), $_POST['id']);
			}
		}
		header('Location: '.SITE_URL.$_SESSION['alias']->alias);
		exit;
	}

	private function removeDirectory($dir) {
	    if ($objs = glob($dir."/*")) {
	       foreach($objs as $obj) {
	         is_dir($obj) ? $this->removeDirectory($obj) : unlink($obj);
	       }
	    }
	    rmdir($dir);
	}

	public function change_position(){
		if($this->userCan('photo')){
			if(isset($_POST['id']) && is_numeric($_POST['id']) && isset($_POST['position']) && is_numeric($_POST['position'])){
				$this->load->smodel('photo_model');
				if($this->photo_model->changePosition($_POST['id'], $_POST['position'])){
					header('Location: '.SITE_URL.'photo/all');
					exit;
				} else echo "ERROR!";
			}
		}
	}
	
}

?>
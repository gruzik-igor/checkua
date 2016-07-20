<?php

class static_pages extends Controller {
				
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

    public function index()
    {
    	$this->load->smodel('static_page_model');
        $page = $this->static_page_model->get($_SESSION['alias']->id);
        $this->load->admin_view('edit_view', array('article' => $page));
    }

    // --- photo
    public function photo_add()
    {
        $res = array();

        $path = IMG_PATH.$_SESSION['option']->folder;
        $name_field = 'photos';
        $error = 0;
        if(strlen(IMG_PATH) > strlen(SITE_URL)) $path = substr($path, strlen(SITE_URL));
        if(!is_dir($path)){
            if(mkdir($path, 0777) == false){
                $error++;
                $res['error'] = 'Error create dir ' . $path;
            } 
        }
        $path = IMG_PATH.$_SESSION['option']->folder.'/';

        if(!empty($_FILES[$name_field]['name'][0]) && $error == 0){
            $length = count($_FILES[$name_field]['name']);
            for($i = 0; $i < $length; $i++){
                $data['alias'] = $_SESSION['alias']->id;
                $data['user'] = $_SESSION['user']->id;
                $data['date'] = time();
                $data['main'] = time();
                $this->db->insertRow($_SESSION['service']->table.'_photos', $data);
                $photo_id = $this->db->getLastInsertedId();
                $photo_name = $_SESSION['alias']->alias . '-' . $photo_id;
                
                $extension = $this->savephoto($name_field, $path, $photo_name, true, $i);
                if($extension){
                    $photo_name .= '.'.$extension;
                    $this->db->updateRow($_SESSION['service']->table, array('author_edit' => $_SESSION['user']->id, 'date_edit' => time(), 'photo' => $photo_name), $_SESSION['alias']->id);
                    $this->db->updateRow($_SESSION['service']->table.'_photos', array('name' => $photo_name), $photo_id);

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
            $photo = $this->db->getAllDataById($_SESSION['service']->table.'_photos', $_POST['photo']);
            if(!empty($photo)){
                $data = array();
                if($_POST['name'] == 'title') $data['title'] = $this->db->sanitizeString($_POST['title']);
                if($_POST['name'] == 'active'){
                    $data['main'] = time();
                    $this->db->updateRow($_SESSION['service']->table, array('author_edit' => $_SESSION['user']->id, 'date_edit' => time(), 'photo' => $photo->name), $_SESSION['alias']->id);
                } 
                if(!empty($data)) if($this->db->updateRow($_SESSION['service']->table.'_photos', $data, $_POST['photo'])){
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
            $photo = $this->db->getAllDataById($_SESSION['service']->table.'_photos', $_POST['photo']);
            if(!empty($photo)){
                if($this->db->deleteRow($_SESSION['service']->table.'_photos', $_POST['photo'])){
                    $path = IMG_PATH.$_SESSION['option']->folder.'/';
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

                    $article = $this->db->getAllDataById($_SESSION['service']->table, $_SESSION['alias']->id);
                    if($article) {
                        $data['author_edit'] = time();
                        $data['date_edit'] = time();
                        if($article->photo == $photo->name){
                            $data['photo'] = '';
                            $photos = $this->db->getAllDataByFieldInArray($_SESSION['service']->table.'_photos', $_SESSION['alias']->id, 'page', 'main DESC');
                            if($photos) $data['photo'] = $photos[0]->name;
                            else $data['photo'] = '';
                        }
                        $this->db->updateRow($_SESSION['service']->table, $data, $article->id);
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
<?php

class wl_photos extends Controller {

    function _remap($method)
    {
    	$_SESSION['alias']->name = 'Images';
        $_SESSION['alias']->breadcrumb = array('Images' => '');
        if (method_exists($this, $method) && $method != 'library' && $method != 'db')
            $this->$method();
        else
            $this->index($method);
    }

    public function index()
    {
        $this->redirect("admin/wl_images");
    }

    public function add()
    {
        $filejson = new stdClass();
        $id = $this->data->uri(3);
        if(is_numeric($id) && isset($_POST['ALIAS_ID']) && isset($_POST['ALIAS_FOLDER']) && isset($_POST['PHOTO_FILE_NAME']) && isset($_POST['PHOTO_TITLE']))
        {
            $path = IMG_PATH.$this->data->post('ALIAS_FOLDER').'/'.$id;
            $path = substr($path, strlen(SITE_URL));
            $name_field = 'photos';
            $error = 0;
            if(!is_dir($path))
            {
                if(mkdir($path, 0777) == false)
                {
                    $error++;
                    $filejson->files['error'] = 'Error create dir ' . $path;
                } 
            }
            $path .= '/';

            if(!empty($_FILES[$name_field]['name'][0]) && $error == 0)
            {
                $length = count($_FILES[$name_field]['name']);
                for($i = 0; $i < $length; $i++) {
                    $data['alias'] = $this->data->post('ALIAS_ID');
                    $data['content'] = $id;
                    $data['file_name'] = '';
                    $data['title'] = $this->data->post('PHOTO_TITLE');
                    $data['author'] = $_SESSION['user']->id;
                    $data['date_add'] = $data['main'] = time();
                    $this->db->insertRow('wl_images', $data);
                    $photo_id = $this->db->getLastInsertedId();
                    $photo_name = $this->data->post('PHOTO_FILE_NAME') . '-' . $photo_id;
                    
                    if($extension = $this->savephoto($name_field, $path, $photo_name, true, $i))
                    {
                        $photo_name .= '.'.$extension;
                        $this->db->updateRow('wl_images', array('file_name' => $photo_name), $photo_id);

                        $this->updateAdditionall();

                        $photo['id'] = $photo_id;
                        $photo['name'] = $this->data->post('PHOTO_TITLE');
                        $photo['date'] = date('d.m.Y H:i');
                        $photo['url'] = IMG_PATH.$this->data->post('ALIAS_FOLDER').'/'.$id.'/'.$photo_name;
                        $photo['thumbnailUrl'] = IMG_PATH.$this->data->post('ALIAS_FOLDER').'/'.$id.'/admin_'.$photo_name;
                        $filejson->files[] = $photo;
                    }
                    else
                        $error++;
                }
            }
            if($error > 0)
            {
                $photo['result'] = false;
                $photo['error'] = "Access Denied!";
                $filejson->files[] = $photo;
            }
        }
        if(empty($filejson->files))
        {
            $photo['result'] = false;
            $photo['error'] = "Access Denied!";
            $filejson->files[] = $photo;
        }
        
        $this->load->json($filejson);
    }

    public function save()
    {
        $res = array('result' => false, 'error' => 'Доступ заборонено! Тільки автор або адміністрація!');
        if(isset($_POST['photo']) && is_numeric($_POST['photo']) && isset($_POST['name']))
        {
            if($photo = $this->db->getAllDataById('wl_images', $_POST['photo']))
            {
                switch ($_POST['name']) {
                    case 'title':
                        if($this->db->updateRow('wl_images', array('title' => $this->data->post('title')), $photo->id))
                        {
                            $res['result'] = true;
                            $res['error'] = '';
                        }
                        $this->updateAdditionall();
                        break;
                    
                    case 'main':
                        if($this->db->updateRow('wl_images', array('main' => time()), $photo->id))
                        {
                            $res['result'] = true;
                            $res['error'] = '';
                        }
                        $this->updateAdditionall();
                        break;
                }
            }
            else
                $res['error'] = 'Фотографію не знайдено!';
        }
        $this->load->json($res);
    }
    
    public function delete()
    {
        $res = array('result' => false, 'error' => 'Доступ заборонено! Тільки автор або адміністрація!');
        if(isset($_POST['photo']) && is_numeric($_POST['photo']) && isset($_POST['ALIAS_FOLDER']))
        {
            if($photo = $this->db->getAllDataById('wl_images', $_POST['photo']))
            {
                if($this->db->deleteRow('wl_images', $photo->id))
                {
                    $path = IMG_PATH.$this->data->post('ALIAS_FOLDER').'/'.$photo->content.'/';
                    $path = substr($path, strlen(SITE_URL));
                    $prefix = array('');
                    if($sizes = $this->db->getAliasImageSizes($photo->alias))
                        foreach ($sizes as $resize) {
                            $prefix[] = $resize->prefix.'_';
                        }
                    foreach ($prefix as $p) {
                        $filename = $path.$p.$photo->file_name;
                        @unlink ($filename);
                    }

                    $this->updateAdditionall();

                    $res['result'] = true;
                    $res['error'] = '';
                }
            }
            else
                $res['error'] = 'Фотографію не знайдено!';
        }
        $this->load->json($res);
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
            if($this->image->getErrors() == '')
            {
                if($sizes = $this->db->getAliasImageSizes($this->data->post('ALIAS_ID')))
                {
                    foreach ($sizes as $resize) {
                        $this->image->loadImage($path, $name, $extension);
                        if($resize->type == 1)
                            $this->image->resize($resize->width, $resize->height, 100);
                        if($resize->type == 2)
                            $this->image->preview($resize->width, $resize->height, 100);
                        $this->image->save($path, $resize->prefix);
                    }
                }
                return $this->image->getExtension();
            }
        }
        return false;
    }

    private function updateAdditionall()
    {
        if($this->data->post('additional_table') && $this->data->post('additional_table_id') && $this->data->post('additional_fields'))
        {
            $data = array();
            $fields = explode(',', $this->data->post('additional_fields', false));
            foreach ($fields as $field) {
                $field = explode('=>', $field);
                if(isset($field[1]))
                {
                    switch ($field[1]) {
                        case 'user':
                            $data[$field[0]] = $_SESSION['user']->id;
                            break;
                        case 'time':
                            $data[$field[0]] = time();
                            break;
                        default:
                            $data[$field[0]] = $field[1];
                            break;
                    }
                }
            }
            if(!empty($data))
            {
                $additional_table_key = $this->data->post('additional_table_key');
                if(!$additional_table_key)
                    $additional_table_key = 'id';
                $this->db->updateRow($this->data->post('additional_table'), $data, $this->data->post('additional_table_id'), $additional_table_key);
            }
        }
    }

}

?>
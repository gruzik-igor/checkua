<?php

class wl_images extends Controller {

    function _remap($method)
    {
        $_SESSION['alias']->name = 'Редагування розміру зображень';
        $_SESSION['alias']->breadcrumb = array('Розміри зображень' => '');
        if (method_exists($this, $method) && $method != 'library' && $method != 'db') {
            $this->$method();
        } else {
            $this->index($method);
        }
    }

    public function index(){
        if($_SESSION['user']->admin == 1){
            @$_SESSION['alias']->id = 0;
            $_SESSION['alias']->table = '';
            $_SESSION['alias']->service = false;
            if($this->data->uri(2) != ''){
                $alias = $this->data->uri(2);
                $alias = $this->db->sanitizeString($alias);
                $alias = $this->db->getAllDataById('wl_aliases', $alias, 'alias');
                if($alias){
                    $alias->name = '';
                    $alias->title = '';

                    if($alias->service > 0){
                        $service = $this->db->getAllDataById('wl_services', $alias->service);
                        if($alias->service) {
                            $alias->title = $service->title;
                            $alias->service_name = $service->name;
                        }
                    }

                    $text = '';
                    if($alias->service > 0) $text = " (на основі сервісу ".$alias->title.")";
                    $_SESSION['alias']->name = 'Розміри зображень '.$alias->alias.$text;
                    $_SESSION['alias']->breadcrumb = array('Розміри зображень' => 'admin/wl_images', $alias->alias => '');
                    
                    if($this->data->uri(3) == 'add'){
                        $this->load->admin_view('wl_images/add_view', array('alias' => $alias));
                    } elseif(is_numeric($this->data->uri(3))){
                        $wl_image = $this->db->getAllDataById('wl_images_sizes', $this->data->uri(3));
                        if($wl_image && $wl_image->alias == $alias->id){
                            $_SESSION['alias']->breadcrumb = array('Розміри зображень' => 'admin/wl_images', $alias->alias => 'admin/wl_images/'.$alias->alias, $wl_image->name => '');
                            $this->load->admin_view('wl_images/edit_view', array('alias' => $alias, 'wl_image' => $wl_image));
                        } else $this->load->page_404();
                    } else {
                        $this->load->admin_view('wl_images/list_view', array('alias' => $alias));
                    }
                } else {
                    $this->load->page_404();
                }
            } else {
                $this->load->admin_view('wl_images/index_view');
            }
        } else {
            $this->load->page_404();
        }
    }

    public function add()
    {
        if(isset($_POST['alias']) && is_numeric($_POST['alias'])){
            $photo = array();
            $photo['alias'] = $this->data->post('alias');
            $photo['active'] = 1;
            $photo['name'] = $this->data->post('name');
            $photo['prefix'] = $this->data->post('prefix');
            $photo['type'] = $this->data->post('type');
            $photo['width'] = $this->data->post('width');
            $photo['height'] = $this->data->post('height');

            $this->db->insertRow('wl_images_sizes', $photo);

            header('Location:'.SITE_URL.'admin/wl_images/'.$this->data->post('alias_name'));
            exit();
        }
    }

    public function save()
    {
        if(isset($_POST['id']) && is_numeric($_POST['id']) && $_POST['id'] > 0)
        {
            $data['active'] = $this->data->post('active');
            $data['name'] = $this->data->post('name');
            $data['prefix'] = $this->data->post('prefix');
            $data['type'] = $this->data->post('type');
            $data['width'] = $this->data->post('width');
            $data['height'] = $this->data->post('height');

            $this->db->updateRow('wl_images_sizes', $data, $_POST['id']);

            header('Location:'.SITE_URL.'admin/wl_images/'.$this->data->post('alias_name'));
            exit();
        }
    }

    public function delete()
    {
        if(isset($_POST['id']) && is_numeric($_POST['id']) && $_POST['id'] > 0)
        {
            if($this->data->post('close_number') == $this->data->post('user_namber')){
                $this->db->deleteRow('wl_images_sizes', $this->data->post('id'));
                header('Location:'.SITE_URL.'admin/wl_images/'.$this->data->post('alias_name'));
                exit();
            } else {
                $_SESSION['notify_error_delete'] = 'Невірний номер! Введіть коректний номер зліва у вільне поле:';
                header("Location: ".$_SERVER['HTTP_REFERER']);
                exit();
            }
        }
    }

    public function copy()
    {
        if(isset($_POST['id']) && is_numeric($_POST['id']) && $_POST['id'] > 0)
        {
            if($this->data->post('close_number') == $this->data->post('user_namber')){
                $wl_image = $this->db->getAllDataById('wl_images_sizes', $this->data->post('id'));
                if($wl_image){
                    $photo = array();
                    $photo['alias'] = $this->data->post('alias');
                    $photo['active'] = 1;
                    $photo['name'] = $this->data->post('name');
                    $photo['prefix'] = $this->data->post('prefix');
                    $photo['type'] = $wl_image->type;
                    $photo['width'] = $wl_image->width;
                    $photo['height'] = $wl_image->height;

                    $this->db->insertRow('wl_images_sizes', $photo);
                    $id = $this->db->getLastInsertedId();
                    $alias = $this->db->getAllDataById('wl_aliases', $this->data->post('alias'));
                    header('Location:'.SITE_URL.'admin/wl_images/'.$alias->alias.'/'.$id);
                    exit();
                }
            } else {
                $_SESSION['notify_error_copy'] = 'Невірний номер! Введіть коректний номер зліва у вільне поле:';
            }
        }
        header("Location: ".$_SERVER['HTTP_REFERER']);
        exit();
    }

}

?>
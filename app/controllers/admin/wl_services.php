<?php

class wl_services extends Controller {
				
    function _remap($method)
    {
        $_SESSION['alias']->name = 'Сервіси';
        $_SESSION['alias']->breadcrumb = array('Сервіси' => '');
        if (method_exists($this, $method) && $method != 'library' && $method != 'db') {
            $this->$method();
        } else {
            $this->index($method);
        }
    }

    public function index(){
        if($_SESSION['user']->admin == 1){
            if($this->data->uri(2) != ''){
                $name = $this->data->uri(2);
                $name = $this->db->sanitizeString($name);
                $service = $this->db->getAllDataById('wl_services', $name, 'name');
                if($service){
                    $aliases = $this->db->getAllDataByFieldInArray('wl_aliases', $service->id, 'service');
                    $options = $this->db->getAllDataByFieldInArray('wl_options', $service->id, 'service');
                    $_SESSION['alias']->name = 'Сервіс "'.$service->title.'"';
                    $_SESSION['alias']->breadcrumb = array('Сервіси' => 'admin/wl_services', $service->title => '');
                    $this->load->admin_view('wl_services/options_view', array('service' => $service, 'aliases' => $aliases,'options' => $options));
                } else {
                    $this->load->page_404();
                }
            } else $this->load->admin_view('wl_services/list_view');
        } else {
            $this->load->page_404();
        }
    }

    public function install()
    {
        if($_SESSION['user']->admin == 1 && isset($_POST['name']) && $_POST['name'] != ''){
            $path = APP_PATH.'services'.DIRSEP.$_POST['name'].DIRSEP.'models/install_model.php';
            if(file_exists($path)){
                require_once($path);
                $install = new install();
                $install->db = $this->db;

                $multi_alias = 1;
                if(isset($install->multi_alias)) $multi_alias = $install->multi_alias;
                $admin_ico = '';
                if(isset($install->admin_ico)) $admin_ico = $install->admin_ico;

                $query = "INSERT INTO `wl_services` (`id`, `name`, `title`, `description`, `table`, `multi_alias`, `version`, `admin_ico`, `active`) 
                                 VALUES (NULL, '{$install->name}', '{$install->title}', '{$install->description}', '{$install->table_service}', '{$multi_alias}', '{$install->version}', '{$admin_ico}', '1');";
                $this->db->executeQuery($query);
                $id = $this->db->getLastInsertedId();

                $options = 0;

                if(!empty($install->options)){
                    $options = 1;
                    foreach ($install->options as $key => $value) {
                        $query = "INSERT INTO `wl_options` ( `id` , `service` , `alias` , `name` , `value` )
                                             VALUES ( NULL ,  '{$id}',  '0',  '{$key}',  '{$value}' )";
                        $this->db->executeQuery($query);
                    }
                }

                $install->service = $this->db->getAllDataById('wl_services', $id);
                
                $install->install_go();

                $this->db->register('service_install', $id.'. '.$install->name.' ('.$install->version.')');

                header("Location: ".SITE_URL."admin/wl_aliases/add?service=".$id);
                exit;
            }
        }
    }

    // Видалити сервіс разом зі всіма налаштуваннями. 
    // У параметрі передаються дані про видалення контенту, що був зібраний.
    public function uninstall()
    {
        if($_SESSION['user']->admin == 1 && isset($_POST['admin-password']) && isset($_POST['id'])){
            $admin = $this->db->getAllDataById('wl_users', $_SESSION['user']->id);
            $password = md5($_POST['admin-password']);
            $password = sha1($_SESSION['user']->email . $password . SYS_PASSWORD . $_SESSION['user']->id);
            if($password == $admin->password){
                $service = $this->db->getAllDataById('wl_services', $_POST['id']);
                if($service){
                    $path = APP_PATH.'services'.DIRSEP.$service->name.DIRSEP.'models/install_model.php';
                    if(file_exists($path)){
                        require_once($path);
                        $install = new install();
                        $install->db = $this->db;

                        $content = false;
                        if(isset($_POST['content']) && $_POST['content'] == 1) $content = true;
                        if($content){
                            $aliases = $this->db->getAllDataByFieldInArray('wl_aliases', $service->id, 'service');
                            if(!empty($aliases)){
                                foreach ($aliases as $alias) {
                                    $additionally = "{$alias->id}. {$alias->alias}. ";
                                    $additionally .= $service->name .' ('.$service->id.')';

                                    if(isset($install->options['folder'])){
                                        $where = array('service' => $alias->service, 'alias' => $alias->id, 'name' => 'folder');
                                        $option = $this->db->getAllDataById('wl_options', $where);
                                        if($option){
                                            $install->options['folder'] = $option->value;
                                        }
                                    }
                                    if(method_exists("install", "alias_delete")) $install->alias_delete($alias->id, $alias->table);
      
                                    $this->db->deleteRow('wl_ntkd', $alias->id, 'alias');
                                    $this->db->deleteRow('wl_images_sizes', $alias->id, 'alias');
                                    $this->db->register('alias_delete', $additionally);
                                }
                                $this->db->deleteRow('wl_aliases', $service->id, 'service');
                            }
                            $this->db->deleteRow('wl_options', $service->id, 'service');
                        }

                        if(method_exists("install", "uninstall")) $install->uninstall($service->id);
                    }

                    $this->db->deleteRow('wl_services', $service->id);
                    $this->db->register('service_uninstall', $service->id.'. '.$service->name.' ('.$service->version.')');

                    header("Location: ".SITE_URL."admin/wl_services");
                    exit;
                }
            } else {
                $_SESSION['notify']->error = 'Невірний пароль адміністратора';
            }
        }

        header("Location: ".$_SERVER['HTTP_REFERER']);
        exit();
    }

    public function saveOption()
    {
        $res = array('result' => false, 'error' => "Помилка! Дані не збережено!");
        if($_SESSION['user']->admin == 1 && isset($_POST['id']) && is_numeric($_POST['id'])){
            if($this->db->updateRow('wl_options', array('value' => $_POST['value']), $_POST['id'])) $res['result'] = true;
        }
        if(isset($_POST['json']) && $_POST['json']){
            header('Content-type: application/json');
            echo json_encode($res);
        } else {
            header("Location: ".SITE_URL."admin/wl_services");
            exit;
        }
    }

}

?>
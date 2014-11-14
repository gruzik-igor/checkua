<?php

class wl_services extends Controller {
				
    function _remap($method)
    {
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

                $query = "INSERT INTO `wl_services` (`id`, `name`, `title`, `description`, `table`, `version`, `active`) 
                                 VALUES (NULL, '{$install->name}', '{$install->title}', '{$install->description}', '{$install->table_service}', '{$install->version}', '1');";
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
                
                header("Location: ".SITE_URL."admin/wl_aliases/add?service=".$id);
                exit;
            }
        }
    }

    // Видалити сервіс разом зі всіма налаштуваннями. 
    // У параметрі передаються дані про видалення контенту, що був зібраний.
    public function uninstall()
    {
        $res = array('result' => false, 'error' => "Помилка! Дані не збережено!");
        if($_SESSION['user']->admin == 1 && isset($_POST['id']) && is_numeric($_POST['id'])){
            $service = $this->db->getAllDataById('wl_services', $_POST['id']);
            if($service){
                $aliases = $this->db->getAllDataByFieldInArray('wl_aliases', $service->id, 'service');
                $this->db->deleteRow('wl_aliases', $service->id, 'service');
                $this->db->deleteRow('wl_options', $service->id, 'service');
                $this->db->deleteRow('wl_services', $service->id);
                if($service->table != '') $this->db->executeQuery("DROP TABLE IF EXISTS {$service->table}");
                $content = false;
                if(isset($_POST['content']) && $_POST['content'] == 1) $content = true;
                if($content && !empty($aliases)){
                    foreach ($aliases as $alias) {
                        if($alias->table != '') $this->db->executeQuery("DROP TABLE IF EXISTS {$alias->table}");
                        $this->db->deleteRow('wl_ntkd', $alias->id, 'alias');
                    }
                }
                $path = APP_PATH.'services'.DIRSEP.$service->name.DIRSEP.'models/install_model.php';
                if(file_exists($path)){
                    require_once($path);
                    $install = new install();
                    $install->db = $this->db;
                    if(method_exists("install", "uninstall")) $install->uninstall();
                }
                $res['result'] = true;
            }
        }
        if(isset($_POST['json']) && $_POST['json']){
            header('Content-type: application/json');
            echo json_encode($res);
        } else {
            header("Location: ".SITE_URL."wl_services");
            exit;
        }
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
            header("Location: ".SITE_URL."wl_services");
            exit;
        }
    }

}

?>
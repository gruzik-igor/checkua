<?php

class wl_aliases extends Controller {
				
    function _remap($method)
    {
        $_SESSION['alias']->name = 'Основні адреси сайту';
        $_SESSION['alias']->breadcrumb = array('Основні адреси' => '');
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

                    $options = null;
                    if($alias->options > 0){
                        $this->db->executeQuery("SELECT * FROM wl_options WHERE service = '{$alias->service}' AND alias = '0'");
                        if($this->db->numRows() > 0){
                            $options_all = $this->db->getRows('array');
                            foreach ($options_all as $option) {
                                $options[$option->name] = $option->value;
                            }
                        } 
                        $this->db->executeQuery("SELECT * FROM wl_options WHERE service = '{$alias->service}' AND alias = '{$alias->id}'");
                        if($this->db->numRows() > 0){
                            $options_all = $this->db->getRows('array');
                            foreach ($options_all as $option) {
                                $options[$option->name] = $option->value;
                            }
                        } 
                    }

                    if($alias->service > 0){
                        $service = $this->db->getAllDataById('wl_services', $alias->service);
                        if($alias->service) {
                            $alias->title = $service->title;
                            $alias->service_name = $service->name;
                        }
                    }

                    $this->db->executeQuery("SELECT name, language FROM `wl_ntkd` WHERE `alias` = '{$alias->id}' AND `content` = '0'");
                    if($this->db->numRows() > 0){
                        $wl_ntkd = $this->db->getRows('array');
                        $alias->name = $wl_ntkd[0]->name;
                        if($_SESSION['language']){
                            foreach ($wl_ntkd as $ntkd) {
                                if($ntkd->language == $_SESSION['language']){
                                    $alias->name = $ntkd->name;
                                    break;
                                }
                            }
                        }
                    }

                    $text = '';
                    if($alias->service > 0) $text = " (на основі сервісу ".$alias->title.")";
                    $_SESSION['alias']->name = 'Редагувати '.$alias->alias.$text;
                    $_SESSION['alias']->breadcrumb = array('Основні адреси' => 'admin/wl_aliases', 'Редагувати '.$alias->alias => '');
                    $this->load->admin_view('wl_aliases/edit_view', array('alias' => $alias, 'options' => $options));
                } else {
                    $this->load->page_404();
                }
            } else {
                $this->load->admin_view('wl_aliases/list_view');
            }
        } else {
            $this->load->page_404();
        }
    }

    public function add()
    {
        @$_SESSION['alias']->id = 0;
        $_SESSION['alias']->alias = 'admin';
        $_SESSION['alias']->table = '';
        $_SESSION['alias']->service = false;
        if($_SESSION['user']->admin == 1){
            @$alias->id = 0;
            $alias->service = 0;
            $alias->alias = '';
            $alias->name = '';
            $alias->admin_ico = 'fa-file-text-o';

            $options = null;

            if(isset($_GET['alias'])){
                $alias->alias = $this->db->sanitizeString($_GET['alias']);
            }

            if(isset($_GET['service']) && is_numeric($_GET['service']) && $_GET['service'] > 0){
                $service = $this->db->getAllDataById('wl_services', $_GET['service']);
                if($service){
                    $alias->service = $service->id;
                    $alias->title = $service->title;
                    $alias->admin_ico = $service->admin_ico;

                    $path = APP_PATH.'services'.DIRSEP.$service->name.DIRSEP.'models/install_model.php';
                    if(file_exists($path)){
                        require_once($path);
                        $install = new install();
                        $install->db = $this->db;

                        if($alias->alias == '') $alias->alias = $install->name;
                        $alias->name = $install->seo_name;
                        
                        if(isset($install->options['folder'])) $install->options['folder'] = $alias->alias;
                        $options = $install->options;

        //                 if(isset($this->options['folder']) && $this->options['folder'] != ''){
        //     $path = IMG_PATH.$this->options['folder'];
        //     if(strlen(IMG_PATH) > strlen(SITE_URL)) $path = substr($path, strlen(SITE_URL));
        //     if(!is_dir($path)) mkdir($path, 0777);
        // }
        //                 if($this->options['resize'] > 0){
        //     $query = "INSERT INTO `wl_images_sizes` (`id`, `alias`, `active`, `name`, `prefix`, `type`, `height`, `width`) VALUES
        //                                          ( NULL, {$alias}, 1, 'Оригінал', '', 1, 1500, 1500),
        //                                          ( NULL, {$alias}, 1, 'Preview', 's', 2, 200, 200);";
        //     $this->db->executeQuery($query);
        // }
                    }
                }
            } 

            if ($alias->alias != '') {
                $aliases = $this->db->getAllDataByFieldInArray('wl_aliases', 1, 'active');
                $go = 0;
                foreach ($aliases as $a) {
                    if($a->alias == $alias->alias){
                        $go++;
                    }
                }
                if($go > 0) $_SESSION['notify']->error = 'Поле "Адреса посилання" має бути унікальним!';
            }
                
            $text = '';
            if($alias->service > 0) $text = "на основі сервісу ".$alias->title;
            $_SESSION['alias']->name = 'Додати сторінку '.$text;
            $_SESSION['alias']->breadcrumb = array('Основні адреси' => 'admin/wl_aliases', 'Додати сторінку' => '');
            $this->load->admin_view('wl_aliases/edit_view', array('alias' => $alias, 'options' => $options));

        } else $this->load->page_404();
    }

    public function save()
    {
        if(isset($_SESSION['user']->id) && $_SESSION['user']->admin == 1){
            if(isset($_POST['id']) && is_numeric($_POST['id']) && isset($_POST['alias']) && $_POST['alias'] != ''){
                $aliases = $this->db->getAllDataByFieldInArray('wl_aliases', 1, 'active');
                $go = 0;
                foreach ($aliases as $alias) {
                    if($alias->alias == $_POST['alias']){
                        $go++;
                    }
                }
                $data = array();
                $data['alias'] = $this->db->sanitizeString($_POST['alias']);
                $data['service'] = 0;
                $data['options'] = 0;
                $data['active'] = 1;
                if(isset($_POST['service']) && is_numeric($_POST['service'])) $data['service'] = $_POST['service'];
                if(count($_POST) > 4) $data['options'] = 1;

                if($_POST['id'] == 0 && $go == 0) {
                    $this->db->insertRow('wl_aliases', $data);
                    $alias = $this->db->getLastInsertedId();
                    if($data['service'] > 0) $this->db->updateRow('wl_aliases', array('table' => '_'.$alias.'_'.$data['alias']), $alias);
                    $this->db->register('alias_add', $data['alias'].' ('.$alias.')');

                    $seo_keywords = '';
                    $seo_description = '';

                    if($data['service'] > 0){
                        $service = $this->db->getAllDataById('wl_services', $data['service']);
                        if($service){
                            $path = APP_PATH.'services'.DIRSEP.$service->name.DIRSEP.'models/install_model.php';
                            if(file_exists($path)){
                                require_once($path);
                                $install = new install();
                                $install->db = $this->db;

                                if($service->admin_ico != ''){
                                    $this->db->updateRow('wl_aliases', array('admin_ico' => $service->admin_ico), $alias);
                                }
                                $seo_keywords = $install->seo_keywords;
                                $seo_description = $install->seo_description;

                                if($data['options'] > 0 && !empty($install->options)){
                                    $options = array();

                                    foreach ($install->options as $option => $value) {
                                        $options[$option] = $value;
                                    }

                                    $option = array();
                                    $option['service'] = $data['service'];
                                    $option['alias'] = $alias;

                                    $reserved = array('id', 'service', 'alias', 'name');
                                    foreach ($_POST as $key => $value) if(!in_array($key, $reserved)) {
                                        if(isset($options[$key]) && $options[$key] != $value){
                                            $option['name'] = $key;
                                            $option['value'] = $value;
                                            $this->db->insertRow('wl_options', $option);
                                        }
                                    }

                                    if(isset($install->options_admin) && !empty($install->options_admin)) {
                                        $option['alias'] = -$alias;
                                        foreach ($install->options_admin as $name => $value) {
                                            $option['name'] = $name;
                                            $option['value'] = $value;
                                            $this->db->insertRow('wl_options', $option);
                                        }
                                    }

                                    if(isset($install->sub_menu) && !empty($install->sub_menu)) {
                                        $option['alias'] = -$alias;
                                        foreach ($install->sub_menu as $sublink => $name) {
                                            $option['name'] = 'sub-menu';
                                            $option['value'] = serialize(array('alias' => $sublink, 'name' => $name));
                                            $this->db->insertRow('wl_options', $option);
                                        }
                                    }
                                }

                                if(isset($install->options['folder'])) $install->options['folder'] = $data['alias'];
                                $install->alias($alias, '_'.$alias.'_'.$data['alias']);
                            }
                        }
                    }

                    $values = '';
                    $name = $this->db->sanitizeString($_POST['name']);
                    if($_SESSION['language']){
                        foreach ($_SESSION['all_languages'] as $key => $lng) {
                            $values .= "(NULL, '{$alias}', '0', '{$lng}', '{$name}', '{$name}', '{$seo_keywords}', '{$seo_description}', ''), ";
                        }
                        $values = substr($values, 0, -2);
                    } else $values = "(NULL, '{$alias}', '0', '', '{$name}', '{$name}', '{$seo_keywords}', '{$seo_description}', '')";
                    $query = "INSERT INTO `wl_ntkd` (`id`, `alias`, `content`, `language`, `name`, `title`, `keywords`, `description`, `text`) VALUES {$values};";
                    $this->db->executeQuery($query);

                    header("Location: ".SITE_URL.'admin/wl_aliases/'.$data['alias']);
                    exit();

                } elseif($_POST['id'] > 0 && $go < 2) {
                    $data['admin_ico'] = $this->data->post('admin_ico');
                    $this->db->updateRow('wl_aliases', $data, $_POST['id']);
                    if($data['options'] > 0 && $data['service'] > 0){
                        $options = array();
                        $options_id = array();
                        $this->db->executeQuery("SELECT * FROM wl_options WHERE service = '{$data['service']}' AND alias = '0'");
                        if($this->db->numRows() > 0){
                            $options_all = $this->db->getRows('array');
                            foreach ($options_all as $option) {
                                $options[$option->name] = $option->value;
                                $options_id[$option->name] = 0;
                            }
                        } 
                        $this->db->executeQuery("SELECT * FROM wl_options WHERE service = '{$data['service']}' AND alias = '{$_POST['id']}'");
                        if($this->db->numRows() > 0){
                            $options_all = $this->db->getRows('array');
                            foreach ($options_all as $option) {
                                $options[$option->name] = $option->value;
                                $options_id[$option->name] = $option->id;
                            }
                        } 

                        $install = null;
                        $table = '';

                        if($data['service'] > 0){
                            $service = $this->db->getAllDataById('wl_services', $data['service']);
                            if($service){
                                $path = APP_PATH.'services'.DIRSEP.$service->name.DIRSEP.'models/install_model.php';
                                if(file_exists($path)){
                                    require_once($path);
                                    $install = new install();
                                    $install->db = $this->db;

                                    $alias_all = $this->db->getAllDataById('wl_aliases', $_POST['id']);
                                    $table = $alias_all->table;
                                }
                            }
                        }

                        $reserved = array('id', 'service', 'alias', 'name');
                        foreach ($_POST as $key => $value) if(!in_array($key, $reserved)) {
                            if(isset($options[$key]) && $options[$key] != $value){

                                if(!empty($install)) $install->setOption($key, $value, $table);

                                $option = array();
                                $option['service'] = $data['service'];
                                $option['alias'] = $_POST['id'];
                                $option['name'] = $key;
                                $option['value'] = $value;
                                if($options_id[$key] == 0) $this->db->insertRow('wl_options', $option);
                                else $this->db->updateRow('wl_options', $option, $options_id[$key]);
                            }
                        }
                    }
                    $_SESSION['notify']->success = 'Інформацію успішно оновлено!';
                    header("Location: ".SITE_URL.'admin/wl_aliases/'.$_POST['alias']);
                    exit();
                } else {
                    $_SESSION['notify']->error = 'Поле "Адреса посилання" має бути унікальним!';
                    header("Location: ".$_SERVER['HTTP_REFERER']);
                    exit();
                }
            } else {
                $_SESSION['notify']->error = 'Поле "Адреса посилання" є обов\'язковим!';
                header("Location: ".$_SERVER['HTTP_REFERER']);
                exit();
            }
        } else $this->load->page_404();
    }

    public function delete()
    {
        if($_SESSION['user']->admin == 1 && isset($_POST['admin-password']) && isset($_POST['id'])){
            $admin = $this->db->getAllDataById('wl_users', $_SESSION['user']->id);
            $password = md5($_POST['admin-password']);
            $password = sha1($_SESSION['user']->email . $password . SYS_PASSWORD . $_SESSION['user']->id);
            if($password == $admin->password){
                if($_POST['id'] > 1) {
                    $alias = $this->db->getAllDataById('wl_aliases', $_POST['id']);
                    if($alias) {
                        $additionally = "{$alias->id}. {$alias->alias}. ";

                        if($alias->service > 0){
                            $service = $this->db->getAllDataById('wl_services', $alias->service);
                            if($service){
                                $additionally .= $service->name .' ('.$service->id.')';
                                $path = APP_PATH.'services'.DIRSEP.$service->name.DIRSEP.'models/install_model.php';
                                if(file_exists($path)){
                                    require_once($path);
                                    $install = new install();
                                    $install->db = $this->db;
                                    if(isset($install->options['folder'])){
                                        $where = array('service' => $alias->service, 'alias' => $alias->id, 'name' => 'folder');
                                        $option = $this->db->getAllDataById('wl_options', $where);
                                        if($option){
                                            $install->options['folder'] = $option->value;
                                        }
                                    }
                                    if(method_exists("install", "alias_delete")) $install->alias_delete($alias->id, $alias->table);
                                }
                            }
                        }

                        $this->db->deleteRow('wl_aliases', $_POST['id']);
                        $this->db->deleteRow('wl_ntkd', $_POST['id'], 'alias');
                        $this->db->deleteRow('wl_options', $_POST['id'], 'alias');
                        $this->db->deleteRow('wl_options', -$_POST['id'], 'alias');

                        $this->db->register('alias_delete', $additionally);

                        header("Location: ".SITE_URL."admin/wl_aliases");
                        exit();
                    } else {
                        $_SESSION['notify']->error = 'Адресу не знайдено!';
                    }
                } else {
                    $_SESSION['notify']->error = 'Видалити головну сторінку неможна!';
                }
            } else {
                $_SESSION['notify']->error = 'Невірний пароль адміністратора';
            }
        }

        header("Location: ".$_SERVER['HTTP_REFERER']);
        exit();
    }

    public function saveSubMenu()
    {
        $res = array('result' => false, 'error' => "Помилка! Дані не збережено!");
        if($_SESSION['user']->admin == 1 && isset($_POST['id']) && is_numeric($_POST['id'])){
            $value = array();
            $value['alias'] = $_POST['alias'];
            $value['name'] = $_POST['name'];
            $value = serialize($value);
            if($this->db->updateRow('wl_options', array('value' => $value), $_POST['id'])) $res['result'] = true;
        }
        if(isset($_POST['json']) && $_POST['json']){
            header('Content-type: application/json');
            echo json_encode($res);
        } else {
            header("Location: ".$_SERVER['HTTP_REFERER']);
            exit;
        }
    }

    public function add_admin_option()
    {
        if($_SESSION['user']->admin == 1){
            $alias = $this->data->uri(3);
            if($alias){
                $alias = $this->db->getAllDataById('wl_aliases', $alias, 'alias');
                if($alias){
                    $_SESSION['alias']->name = 'Додати налаштування до '.$alias->alias;
                    $_SESSION['alias']->breadcrumb = array('Основні адреси' => 'admin/wl_aliases', $alias->alias => 'admin/wl_aliases/'.$alias->alias, 'Додати налаштування' => '');
                    $this->load->admin_view('wl_aliases/add_admin_option_view', array('alias' => $alias));
                } else {
                    $this->load->page_404();
                }
            } else {
                $this->load->page_404();
            }  
        } else {
            $this->load->page_404();
        }
    }

    public function saveOption()
    {
        if($_SESSION['user']->admin == 1 && isset($_POST['alias_id']) && is_numeric($_POST['alias_id'])){
            $data = array('service' => $_POST['service']);
            if($_POST['type'] == 'sub-menu'){
                $data['alias'] = $_POST['alias_id'] * -1;
                $data['name'] = 'sub-menu';
                $data['value'] = serialize(array('alias' => $this->data->post('alias'), 'name' => $this->data->post('name')));
            } else if($_POST['type'] == 'admin'){
                $data['alias'] = $_POST['alias_id'] * -1;
                $data['name'] = $this->data->post('name');
                $data['value'] = $this->data->post('value');
            } else {
                $data['alias'] = $_POST['alias_id'];
                $data['name'] = $this->data->post('name');
                $data['value'] = $this->data->post('value');
            }
            if($this->db->insertRow('wl_options', $data)){
                header('Location: '.SITE_URL.'admin/wl_aliases/'.$this->data->post('alias_link'));
                exit();
            }
        } else {
            $this->load->page_404();
        }
    }

    public function deleteOption()
    {
        $res = array('result' => false, 'error' => "Помилка! Дані не збережено!");
        if($_SESSION['user']->admin == 1 && isset($_POST['id']) && is_numeric($_POST['id'])){
            if($this->db->deleteRow('wl_options', $_POST['id'])) $res['result'] = true;
        }
        if(isset($_POST['json']) && $_POST['json']){
            header('Content-type: application/json');
            echo json_encode($res);
        } else {
            header("Location: ".$_SERVER['HTTP_REFERER']);
            exit;
        }
    }

}

?>
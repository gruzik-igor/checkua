<?php

class wl_aliases extends Controller {
				
    public function _remap($method)
    {
        $_SESSION['alias']->name = 'Основні адреси сайту';
        $_SESSION['alias']->breadcrumb = array('Основні адреси' => '');
        if (method_exists($this, $method) && $method != 'library' && $method != 'db')
            $this->$method();
        else
            $this->index($method);
    }

    public function index()
    {
        if($_SESSION['user']->admin == 1)
        {
            @$_SESSION['alias']->id = 0;
            $_SESSION['alias']->table = '';
            $_SESSION['alias']->service = false;
            if($this->data->uri(2) != '')
            {
                $alias = $this->data->uri(2);
                $alias = $this->db->sanitizeString($alias);
                $alias = $this->db->getAllDataById('wl_aliases', $alias, 'alias');
                if($alias)
                {
                    $alias->name = '';
                    $alias->title = '';

                    $options = null;                  

                    if($alias->service > 0)
                    {
                        if($options_all = $this->db->getAllDataByFieldInArray('wl_options', array('service' => $alias->service, 'alias' => 0)))
                            foreach ($options_all as $option) {
                                $options[$option->name] = new stdClass();
                                $options[$option->name]->name = $option->name;
                                $options[$option->name]->value = $option->value;
                                $options[$option->name]->type = 'text';
                                $options[$option->name]->title = $option->name;
                            }

                        if($options_all = $this->db->getAllDataByFieldInArray('wl_options', array('service' => $alias->service, 'alias' => $alias->id)))
                            foreach ($options_all as $option) {
                                $options[$option->name] = new stdClass();
                                $options[$option->name]->name = $option->name;
                                $options[$option->name]->value = $option->value;
                                $options[$option->name]->type = 'text';
                                $options[$option->name]->title = $option->name;
                            }

                        $service = $this->db->getAllDataById('wl_services', $alias->service);
                        $alias->title = $service->title;
                        $alias->service_name = $service->name;

                        $path = APP_PATH.'services'.DIRSEP.$service->name.DIRSEP.'models/install_model.php';
                        if(file_exists($path))
                        {
                            require_once($path);
                            $install = new install();

                            if(!empty($install->options) && !empty($options))
                            {
                                foreach ($install->options as $key => $value) {
                                    if(isset($install->options_type[$key]) && isset($options[$key]))
                                        $options[$key]->type = $install->options_type[$key];
                                    if(isset($install->options_title[$key]) && isset($options[$key]))
                                        $options[$key]->title = $install->options_title[$key];
                                }
                            }
                        }
                    }
                    else
                    {
                        if($options_all = $this->db->getAllDataByFieldInArray('wl_options', array('service' => 0, 'alias' => $alias->id)))
                            foreach ($options_all as $option) {
                                $options[$option->name] = new stdClass();
                                $options[$option->name]->name = $option->name;
                                $options[$option->name]->value = $option->value;
                                $options[$option->name]->type = 'text';
                                $options[$option->name]->title = $option->name;
                            }
                    }

                    $where = array('alias' => $alias->id, 'content' => 0);
                    if($_SESSION['language'])
                        $where['language'] = $_SESSION['language'];
                    $this->db->select('wl_ntkd', 'name', $where);
                    $wl_ntkd = $this->db->get('single');
                    $alias->name = $wl_ntkd->name;

                    $text = '';
                    if($alias->service > 0) $text = " (на основі сервісу ".$alias->title.")";
                    $_SESSION['alias']->name = 'Редагувати '.$alias->alias.$text;
                    $_SESSION['alias']->breadcrumb = array('Основні адреси' => 'admin/wl_aliases', 'Редагувати '.$alias->alias => '');
                    $this->load->admin_view('wl_aliases/edit_view', array('alias' => $alias, 'options' => $options));
                }
                else
                    $this->load->page_404();
            }
            else
                $this->load->admin_view('wl_aliases/list_view');
        }
        else
            $this->load->page_404();
    }

    public function add()
    {
        @$_SESSION['alias']->id = 0;
        $_SESSION['alias']->alias = 'admin';
        $_SESSION['alias']->table = '';
        $_SESSION['alias']->service = false;
        if($_SESSION['user']->admin == 1)
        {
            @$alias->id = 0;
            $alias->alias = '';
            $alias->service = 0;
            $alias->name = '';
            $alias->admin_ico = 'fa-file-text-o';

            $options = null;

            if(isset($_GET['alias']))
                $alias->alias = $this->data->get('alias');

            if(isset($_GET['service']) && is_numeric($_GET['service']) && $_GET['service'] > 0)
            {
                $service = $this->db->getAllDataById('wl_services', $this->data->get('service'));
                if($service)
                {
                    $alias->service = $service->id;

                    $path = APP_PATH.'services'.DIRSEP.$service->name.DIRSEP.'models'.DIRSEP.'install_model.php';
                    if(file_exists($path))
                    {
                        require_once($path);
                        $install = new install();

                        if($alias->alias == '') $alias->alias = $install->name;
                        $alias->name = $install->title;
                        $alias->admin_ico = $install->admin_ico;
                        
                        if(isset($install->options['folder'])) $install->options['folder'] = $alias->alias;
                        if(!empty($install->options))
                        {
                            $options = array();
                            foreach ($install->options as $key => $value) {
                                $options[$key] = new stdClass();
                                $options[$key]->name = $key;
                                $options[$key]->value = $value;
                                $options[$key]->type = 'text';
                                $options[$key]->title = $key;

                                if(isset($install->options_type[$key])) $options[$key]->type = $install->options_type[$key];
                                if(isset($install->options_title[$key])) $options[$key]->title = $install->options_title[$key];
                            }
                        }
                    }
                }
            } 

            if($alias->alias != '')
            {
                $aliases = $this->db->getAllData('wl_aliases');
                $go = 0;
                foreach ($aliases as $a) {
                    if($a->alias == $alias->alias)
                        $go++;
                }
                if($go > 0)
                    $_SESSION['notify']->errors = 'Поле "Адреса посилання" має бути унікальним!';
            }
                
            $text = '';
            if($alias->service > 0) $text = "на основі сервісу ".$install->title;
            $_SESSION['alias']->name = 'Додати сторінку '.$text;
            $_SESSION['alias']->breadcrumb = array('Основні адреси' => 'admin/wl_aliases', 'Додати сторінку' => '');
            $this->load->admin_view('wl_aliases/edit_view', array('alias' => $alias, 'options' => $options));
        }
        else
            $this->load->page_404();
    }

    public function save()
    {
        if($_SESSION['user']->admin == 1)
        {
            if(isset($_POST['id']) && is_numeric($_POST['id']) && isset($_POST['alias']) && $_POST['alias'] != '')
            {
                $go = 0;
                foreach ($this->db->getAllData('wl_aliases') as $alias) {
                    if($alias->alias == $_POST['alias'] && $alias->id != $_POST['id'])
                        $go++;
                }

                $data = array();
                $data['alias'] = $this->db->sanitizeString($_POST['alias']);
                $data['service'] = $this->data->post('service');
                $data['admin_ico'] = NULL;
                $data['admin_order'] = 0;

                if($_POST['id'] == 0 && $go == 0)
                {
                    $data['table'] = NULL;
                    $this->db->insertRow('wl_aliases', $data);
                    $alias = $this->db->getLastInsertedId();
                    $this->db->register('alias_add', $data['alias'].' ('.$alias.')');
                    
                    if($data['service'] > 0)
                    {
                        if($service = $this->db->getAllDataById('wl_services', $data['service']))
                        {
                            $update = array('table' => '_'.$alias.'_'.$data['alias']);
                            $path = APP_PATH.'services'.DIRSEP.$service->name.DIRSEP.'models/install_model.php';
                            if(file_exists($path))
                            {
                                require_once($path);
                                $install = new install();
                                $install->db = $this->db;

                                $update['admin_ico'] = $install->admin_ico;
                                $update['admin_order'] = $install->order_alias;

                                if(!empty($install->options))
                                {
                                    $options = array();

                                    foreach ($install->options as $option => $value) {
                                        $options[$option] = $value;
                                    }

                                    $option = array();
                                    $option['service'] = $data['service'];
                                    $option['alias'] = $alias;

                                    $reserved = array('id', 'service', 'alias', 'name');
                                    foreach ($_POST as $key => $value) {
                                        if(!in_array($key, $reserved) && isset($options[$key]) && $options[$key] != $value)
                                        {
                                            $option['name'] = $key;
                                            $option['value'] = $value;
                                            $this->db->insertRow('wl_options', $option);
                                            $install->options[$key] = $value;
                                        }
                                    }

                                    if(isset($install->options['folder']) && $install->options['folder'] != '')
                                    {
                                        $path = IMG_PATH.$install->options['folder'];
                                        $path = substr($path, strlen(SITE_URL));
                                        if(!is_dir($path)) mkdir($path, 0777);

                                        $path = 'audio'.DIRSEP.$install->options['folder'];
                                        if(!is_dir($path)) mkdir($path, 0777);
                                    }

                                    if(isset($install->options_admin) && !empty($install->options_admin))
                                    {
                                        $option['alias'] = -$alias;
                                        foreach ($install->options_admin as $name => $value) {
                                            $option['name'] = $name;
                                            $option['value'] = $value;
                                            $this->db->insertRow('wl_options', $option);
                                        }
                                    }

                                    if(isset($install->sub_menu) && !empty($install->sub_menu))
                                    {
                                        $option['alias'] = -$alias;
                                        foreach ($install->sub_menu as $sublink => $name) {
                                            $option['name'] = 'sub-menu';
                                            $option['value'] = serialize(array('alias' => $sublink, 'name' => $name));
                                            $this->db->insertRow('wl_options', $option);
                                        }
                                    }
                                }

                                $install->alias($alias, $update['table']);
                            }
                            $this->db->updateRow('wl_aliases', $update, $alias);
                        }
                    }

                    $ntkd = array('alias' => $alias, 'content' => 0, 'language' => NULL, 'name' => $this->data->post('name'));
                    if($_SESSION['language'])
                        foreach ($_SESSION['all_languages'] as $language) {
                            $ntkd['language'] = $language;
                            $this->db->insertRow('wl_ntkd', $ntkd);
                        }
                    else
                        $this->db->insertRow('wl_ntkd', $ntkd);

                    $this->load->redirect('admin/wl_aliases/'.$data['alias']);
                }
                elseif($_POST['id'] > 0 && $go == 0)
                {
                    $data['admin_ico'] = $this->data->post('admin_ico');
                    $data['admin_order'] = $this->data->post('admin_order');
                    $this->db->updateRow('wl_aliases', $data, $_POST['id']);

                    $options = array();
                    $options_id = array();
                    if($data['service'] > 0)
                    {
                        if($options_all = $this->db->getAllDataByFieldInArray('wl_options', array('service' => $alias->service, 'alias' => 0)))
                            foreach ($options_all as $option) {
                                $options[$option->name] = $option->value;
                                $options_id[$option->name] = 0;
                            }
                    }
                    if($options_all = $this->db->getAllDataByFieldInArray('wl_options', array('service' => $alias->service, 'alias' => $_POST['id'])))
                        foreach ($options_all as $option) {
                            $options[$option->name] = $option->value;
                            $options_id[$option->name] = $option->id;
                        }

                    if(!empty($options))
                    {
                        $install = null;
                        $table = '';

                        if($data['service'] > 0)
                        {
                            if($service = $this->db->getAllDataById('wl_services', $data['service']))
                            {
                                $path = APP_PATH.'services'.DIRSEP.$service->name.DIRSEP.'models/install_model.php';
                                if(file_exists($path))
                                {
                                    require_once($path);
                                    $install = new install();
                                    $install->db = $this->db;

                                    $alias_all = $this->db->getAllDataById('wl_aliases', $_POST['id']);
                                    $table = $alias_all->table;
                                }
                            }
                        }

                        $reserved = array('id', 'service', 'alias', 'name', 'admin_ico', 'admin_order');
                        foreach ($options as $key => $value)
                            if(!in_array($key, $reserved))
                            {
                                if(isset($_POST[$key]) && $_POST[$key] != $value)
                                {
                                    if(!empty($install)) $install->setOption($key, $this->data->post($key), $_POST['id'], $table);

                                    $option = array();
                                    $option['service'] = $data['service'];
                                    $option['alias'] = $_POST['id'];
                                    $option['name'] = $key;
                                    $option['value'] = $this->data->post($key);
                                    if($options_id[$key] == 0)
                                        $this->db->insertRow('wl_options', $option);
                                    else
                                        $this->db->updateRow('wl_options', $option, $options_id[$key]);
                                }
                                elseif(!isset($_POST[$key]) && $value != 0)
                                {
                                    $option = array();
                                    $option['service'] = $data['service'];
                                    $option['alias'] = $_POST['id'];
                                    $option['name'] = $key;
                                    $option['value'] = $this->data->post($key);
                                    if($options_id[$key] == 0)
                                        $this->db->insertRow('wl_options', $option);
                                    else
                                        $this->db->updateRow('wl_options', $option, $options_id[$key]);
                                }
                            }
                    }
                    $_SESSION['notify'] = new stdClass();
                    $_SESSION['notify']->success = 'Інформацію успішно оновлено!';
                    $this->load->redirect('admin/wl_aliases/'.$_POST['alias']);
                    exit();
                }
                else
                {
                    $_SESSION['notify'] = new stdClass();
                    $_SESSION['notify']->errors = 'Поле "Адреса посилання" має бути унікальним!';
                }
            }
            else
            {
                $_SESSION['notify'] = new stdClass();
                $_SESSION['notify']->errors = 'Поле "Адреса посилання" є обов\'язковим!';
            }
            $this->load->redirect();
        }
        else
            $this->load->page_404();
    }

    public function delete()
    {
        if($_SESSION['user']->admin == 1 && isset($_POST['admin-password']) && isset($_POST['id']))
        {
            $this->load->model('wl_user_model');
            $admin = $this->wl_user_model->getInfo(0, false);
            $password = $this->wl_user_model->getPassword($_SESSION['user']->id, $_SESSION['user']->email, $_POST['admin-password']);
            if($password == $admin->password)
            {
                if($_POST['id'] > 1)
                {
                    if($alias = $this->db->getAllDataById('wl_aliases', $_POST['id']))
                    {
                        $additionally = "{$alias->id}. {$alias->alias}. ";
                        $where = array('service' => $alias->service, 'alias' => $alias->id, 'name' => 'folder');
                        if($option = $this->db->getAllDataById('wl_options', $where))
                        {
                            $folder = $option->value;

                            $path = IMG_PATH.$folder;
                            $path = substr($path, strlen(SITE_URL));
                            $this->data->removeDirectory($path);

                            $path = 'audio'.DIRSEP.$folder;
                            $this->data->removeDirectory($path);
                        }

                        if($alias->service > 0)
                        {
                            if($service = $this->db->getAllDataById('wl_services', $alias->service))
                            {
                                $additionally .= $service->name .' ('.$service->id.')';
                                $path = APP_PATH.'services'.DIRSEP.$service->name.DIRSEP.'models/install_model.php';
                                if(file_exists($path))
                                {
                                    require_once($path);
                                    $install = new install();
                                    $install->db = $this->db;

                                    if(method_exists("install", "alias_delete"))
                                        $install->alias_delete($alias->id, $alias->table);
                                }
                            }
                        }

                        $this->db->deleteRow('wl_aliases', $alias->id);
                        $this->db->deleteRow('wl_aliases_cooperation', $alias->id, 'alias1');
                        $this->db->deleteRow('wl_aliases_cooperation', $alias->id, 'alias2');
                        $this->db->deleteRow('wl_images', $alias->id, 'alias');
                        $this->db->deleteRow('wl_images_sizes', $alias->id, 'alias');
                        $this->db->deleteRow('wl_options', $alias->id, 'alias');
                        $this->db->deleteRow('wl_options', -$alias->id, 'alias');
                        $this->db->deleteRow('wl_language_words', $alias->id, 'alias');
                        $this->db->deleteRow('wl_ntkd', $alias->id, 'alias');
                        $this->db->deleteRow('wl_sitemap', $alias->id, 'alias');
                        $this->db->deleteRow('wl_statistic_pages', $alias->id, 'alias');
                        $this->db->deleteRow('wl_video', $alias->id, 'alias');
                        $this->db->deleteRow('wl_audio', $alias->id, 'alias');
                        $this->db->deleteRow('wl_user_permissions', $alias->id, 'permission');

                        $this->db->register('alias_delete', $additionally);
                    }
                    else
                    {
                        $_SESSION['notify'] = new stdClass();
                        $_SESSION['notify']->errors = 'Адресу не знайдено!';
                    }
                    $this->load->redirect("admin/wl_aliases");
                }
                else
                {
                    $_SESSION['notify'] = new stdClass();
                    $_SESSION['notify']->errors = 'Видалити головну сторінку неможна!';
                }
            }
            else
            {
                $_SESSION['notify'] = new stdClass();
                $_SESSION['notify']->errors = 'Невірний пароль адміністратора';
            }
        }
        $this->load->redirect();
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

                    $service = null;
                    if($alias->service > 0)
                    {
                        $wl_service = $this->db->getAllDataById('wl_services', $alias->service);
                        if($wl_service){
                            $path = APP_PATH.'services'.DIRSEP.$wl_service->name.DIRSEP.'models/install_model.php';
                            if(file_exists($path)){
                                require_once($path);
                                $service = new install();
                            }
                        }
                    }
                    $this->load->admin_view('wl_aliases/add_admin_option_view', array('alias' => $alias, 'service' => $service));
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

    public function saveCooperation()
    {
        if(isset($_POST['alias_id']))
        {
            $cooperation['type'] = $this->data->post('type');
            if ($_POST['alias_index'] == 2)
            {
                $cooperation['alias1'] = $this->data->post('alias');
                $cooperation['alias2'] = $this->data->post('alias_id');
            }
            else
            {
                $cooperation['alias1'] = $this->data->post('alias_id');
                $cooperation['alias2'] = $this->data->post('alias');
            }
            if($this->db->insertRow('wl_aliases_cooperation', $cooperation))
            {
                header('Location: '.SITE_URL.'admin/wl_aliases/'.$this->data->post('alias_link'));
                exit();
            }
        }
    }

}

?>
<?php

class wl_aliases extends Controller {
				
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
            @$_SESSION['alias']->id = 0;
            $_SESSION['alias']->alias = 'admin';
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
                    if($alias->options > 0 && $alias->service > 0){
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
                        if($alias->service) $alias->title = $service->title;
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

            $options = null;
            $errors = '';

            if(isset($_GET['alias'])){
                $alias->alias = $this->db->sanitizeString($_GET['alias']);
            }

            if(isset($_GET['service']) && is_numeric($_GET['service']) && $_GET['service'] > 0){
                $service = $this->db->getAllDataById('wl_services', $_GET['service']);
                if($service){
                    $alias->service = $service->id;
                    $alias->title = $service->title;

                    $path = APP_PATH.'services'.DIRSEP.$service->name.DIRSEP.'models/install_model.php';
                    if(file_exists($path)){
                        require_once($path);
                        $install = new install();
                        $install->db = $this->db;

                        if($alias->alias == '') $alias->alias = $install->name;
                        $alias->name = $install->seo_name;
                        
                        if(isset($install->options['folder'])) $install->options['folder'] = $alias->alias;
                        $options = $install->options;
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
                if($go > 0) $errors = 'Поле "Адреса посилання" має бути унікальним!';
            }
                
            $this->load->admin_view('wl_aliases/edit_view', array('alias' => $alias, 'options' => $options, 'errors' => $errors));

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
                    if($data['service'] > 0) $this->db->updateRow('wl_aliases', array('table' => '_'.$alias), $alias);

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

                                $seo_keywords = $install->seo_keywords;
                                $seo_description = $install->seo_description;

                                if($data['options'] > 0 && !empty($install->options)){
                                    $options = array();

                                    foreach ($install->options as $option => $value) {
                                        $options[$option] = $value;
                                    }

                                    $reserved = array('id', 'service', 'alias', 'name');
                                    foreach ($_POST as $key => $value) if(!in_array($key, $reserved)) {
                                        if(isset($options[$key]) && $options[$key] != $value){
                                            $option = array();
                                            $option['service'] = $data['service'];
                                            $option['alias'] = $alias;
                                            $option['name'] = $key;
                                            $option['value'] = $value;
                                            $this->db->insertRow('wl_options', $option);
                                        }
                                    }                        
                                }

                                if(isset($install->options['folder'])) $install->options['folder'] = $data['alias'];
                                $install->alias($alias, '_'.$alias);
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

                    header("Location: ".SITE_URL.$data['alias']);
                    exit();

                } elseif($_POST['id'] > 0 && $go < 2) {
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
                    header("Location: ".SITE_URL.'admin/wl_aliases');
                    exit();
                } else $this->showError('Поле "Адреса посилання" має бути унікальним!');
            } else {
                $this->showError('Поле "Адреса посилання" є обов\'язковим!');
            }
        } else $this->load->page_404();
    }

    private function showError($error = '')
    {
        @$alias->id = 0;
        if(is_numeric($_POST['id'])) $alias->id = $_POST['id'];
        $alias->service = 0;
        $alias->alias = $_POST['alias'];
        $alias->name = $_POST['name'];
        $alias->title = '';
        
        if(isset($_POST['service']) && is_numeric($_POST['service'])){
            $service = $this->db->getAllDataById('wl_services', $_POST['service']);
            if($service){
                $alias->service = $service->id;
                $alias->title = $service->title;
            }
        } 

        $options = array();
        $reserved = array('id', 'service', 'alias', 'name');
        foreach ($_POST as $key => $value) if(!in_array($key, $reserved)) {
            $options[$key] = $value;
        }
        $this->load->admin_view('wl_aliases/edit_view', array('alias' => $alias, 'options' => $options, 'errors' => $error));
    }

}

?>
<?php

class static_pages extends Controller {
				
    function _remap($method)
    {
        if (method_exists($this, $method)) {
            $this->$method();
        } else {
            $this->index($method);
        }
    }

    function index(){
    	$this->load->page_view('index_view');
    }

    public function edit()
    {
        if($this->userCan($_SESSION['alias']->alias)){     
            $this->load->model('wl_ntkd_model');
            if($_SESSION['language']){
                $name = $_SESSION['alias']->name;
                $current_languade = $_SESSION['language'];
                foreach ($_SESSION['all_languages'] as $lang) {
                    $_SESSION['language'] = $lang;
                    $ntkd[$lang] = $this->wl_ntkd_model->get($_SESSION['alias']->alias, 0, false);
                }
                $_SESSION['language'] = $current_languade;
                $_SESSION['alias']->name = $name;
            } else {
                $ntkd = $this->wl_ntkd_model->get($_SESSION['alias']->alias, 0, false);
            }
            $this->load->admin_view('edit_view', array('ntkd' => $ntkd));
        } else {
            header("Location: ".SITE_URL.'login');
            exit();
        }  
    }

    public function save()
    {
        $res = array('result' => false, 'error' => 'Доступ заборонено! Тільки автор або адміністрація!');
        $fields = array('name', 'title', 'keywords', 'description', 'text');
        if($this->userCan($_SESSION['alias']->alias)){
            if(isset($_POST['field']) && in_array($_POST['field'], $fields) && isset($_POST['data'])){
                $field = htmlentities($_POST['data'], ENT_QUOTES, 'utf-8');
                $language = '';
                if($_SESSION['language'] && isset($_POST['language'])){
                    $language = htmlentities($_POST['language'], ENT_QUOTES, 'utf-8');
                    $language = "AND `language` = '{$language}'";
                }

                $this->db->executeQuery("UPDATE `wl_ntkd` SET `{$_POST['field']}` = '{$field}' WHERE `alias` = '{$_SESSION['alias']->id}' AND `content` = '0' {$language}");
                $this->db->executeQuery("SELECT `id` FROM `wl_ntkd` WHERE `alias` = '{$_SESSION['alias']->id}' AND `content` = '0' {$language}");
                if($this->db->numRows() == 0){
                    $data['alias'] = $_SESSION['alias']->id;
                    $data['content'] = 0;
                    if($_SESSION['language'] && isset($_POST['language'])) $data['language'] = htmlentities($_POST['language'], ENT_QUOTES, 'utf-8');
                    $data[$_POST['field']] = $field;
                    $this->db->insertRow('wl_ntkd', $data);
                }

                $res['result'] = true;
                $res['error'] = '';
            } else $res['error'] = 'Невірне поле для зберігання даних!';
        }
        header('Content-type: application/json');
        echo json_encode($res);
        exit;
    }

    public function admin()
    {
        $this->edit();
    }
	
}

?>
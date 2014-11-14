<?php

class wl_ntkd extends Controller {
				
    function _remap($method)
    {
        if (method_exists($this, $method) && $method != 'library' && $method != 'db') {
            $this->$method();
        } else {
            $this->index($method);
        }
    }

    function index(){
        $alias = $this->data->uri(2);
        if($alias != ''){
            if($this->userCan($_SESSION['alias']->alias) || $this->userCan($alias)){
                $alias = $this->db->getAllDataById('wl_aliases', $alias, 'alias');
                if($alias){
                    $id = $this->data->uri(3);
                    if($id == 'edit') $this->load->admin_view('wl_ntkd/edit_view', array('alias' => $alias, 'content' => 0, 'ntkd' => $this->get($alias)));
                    elseif(is_numeric($id)) $this->load->admin_view('wl_ntkd/edit_view', array('alias' => $alias, 'content' => $id, 'ntkd' => $this->get($alias, $id)));
                    else {
                        $ntkd = $this->db->getAllDataByFieldInArray('wl_ntkd', $alias->id, 'alias');
                        if(count($ntkd) == 1) $ntkd = $ntkd[0];
                        elseif(count($ntkd) > 1) $this->load->admin_view('wl_ntkd/list_view', array('alias' => $alias));
                        else $this->load->admin_view('wl_ntkd/edit_view', array('alias' => $alias, 'content' => 0, 'ntkd' => $ntkd));
                    }
                } else $this->load->page_404();
            } else $this->load->notify_view(array('errors' => 'Доступ заборонено!'));
        } elseif($this->userCan($_SESSION['alias']->alias)){
        	$this->load->admin_view('wl_ntkd/index_view');
        } else {
            $this->load->notify_view(array('errors' => 'Доступ заборонено!'));
        }
        $_SESSION['alias']->id = 0;
        $_SESSION['alias']->alias = 'admin';
        $_SESSION['alias']->table = '';
        $_SESSION['alias']->service = '';
    }

    private function get($alias, $content = 0)
    {
        $this->db->executeQuery("SELECT * FROM `wl_ntkd` WHERE `alias` = '{$alias->id}' AND `content` = '{$content}'");
        if($this->db->numRows() > 0){
            return $this->db->getRows();
        }
        return false;
    }

    public function save()
    {
        $res = array('result' => false, 'error' => 'Доступ заборонено! Тільки автор або адміністрація!');
        $fields = array('name', 'title', 'keywords', 'description', 'text');
        if($this->userCan($_SESSION['alias']->alias) || $this->access()){
            if(isset($_POST['field']) && in_array($_POST['field'], $fields) && isset($_POST['data'])){
                $field = htmlentities($_POST['data'], ENT_QUOTES, 'utf-8');
                $language = '';
                if($_SESSION['language'] && isset($_POST['language'])){
                    $language = htmlentities($_POST['language'], ENT_QUOTES, 'utf-8');
                    $language = "AND `language` = '{$language}'";
                }

                $alias = 0;
                $content = 0;
                if(isset($_POST['alias']) && is_numeric($_POST['alias']) && $_POST['alias'] > 0) $alias = $_POST['alias'];
                if(isset($_POST['content']) && is_numeric($_POST['content'])) $content = $_POST['content'];

                if($alias > 0){
                    $this->db->executeQuery("UPDATE `wl_ntkd` SET `{$_POST['field']}` = '{$field}' WHERE `alias` = '{$alias}' AND `content` = '{$content}' {$language}");
                    $this->db->executeQuery("SELECT `id` FROM `wl_ntkd` WHERE `alias` = '{$alias}' AND `content` = '{$content}' {$language}");
                    if($this->db->numRows() == 0){
                        $data['alias'] = $alias;
                        $data['content'] = $content;
                        if($_SESSION['language'] && isset($_POST['language'])) $data['language'] = htmlentities($_POST['language'], ENT_QUOTES, 'utf-8');
                        $data[$_POST['field']] = $field;
                        $this->db->insertRow('wl_ntkd', $data);
                    }

                    $res['result'] = true;
                    $res['error'] = '';
                } else $res['error'] = 'Невірне адреса!';
            } else $res['error'] = 'Невірне поле для зберігання даних!';
        }
        header('Content-type: application/json');
        echo json_encode($res);
        exit;
    }

    private function access()
    {
        if(isset($_POST['alias']) && is_numeric($_POST['alias'])){
            $alias = $this->db->getAllDataById('wl_aliases', $_POST['alias']);
            if($alias && $this->userCan($alias->alias)) return true;
            return false;
        }
        
    }
	
}

?>
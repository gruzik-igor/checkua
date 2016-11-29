<?php

class wl_ntkd extends Controller {
				
    function _remap($method)
    {
        $_SESSION['alias']->name = 'Налаштування SEO (name, title, descriptions)';
        $_SESSION['alias']->breadcrumb = array('SEO' => '');
        if (method_exists($this, $method) && $method != 'library' && $method != 'db')
            $this->$method();
        else
            $this->index($method);
    }

    public function index()
    {
        $alias = $this->data->uri(2);
        if($alias != '')
        {
            if($this->userCan() || $this->userCan($alias))
            {
                if($alias = $this->db->getAllDataById('wl_aliases', $alias, 'alias'))
                {
                    $id = $this->data->uri(3);
                    if($id == 'edit')
                    {
                        $_SESSION['alias']->name = 'SEO Головна сторінка "'.$alias->alias.'"';
                        $_SESSION['alias']->breadcrumb = array('SEO' => 'admin/wl_ntkd', $alias->alias => 'admin/wl_ntkd/'.$alias->alias, 'Головна сторінка' => '');
                        $this->load->admin_view('wl_ntkd/edit_view', array('alias' => $alias, 'content' => 0, 'ntkd' => $this->get($alias)));
                    }
                    elseif(is_numeric($id))
                    {
                        $ntkd = $this->get($alias, $id);
                        $name = $id;
                        if(is_array($ntkd)) $name = $ntkd[0]->name;
                        elseif(is_object($ntkd)) $name = $ntkd->name;
                        $_SESSION['alias']->name = 'SEO сторінка '.$id.' "'.$name.'"';
                        $_SESSION['alias']->breadcrumb = array('SEO' => 'admin/wl_ntkd', $alias->alias => 'admin/wl_ntkd/'.$alias->alias, $name => '');
                        $this->load->admin_view('wl_ntkd/edit_view', array('alias' => $alias, 'content' => $id, 'ntkd' => $ntkd));
                    }
                    else
                    {
                        $where['alias'] = $alias->id;
                        if($_SESSION['language'])
                            $where['language'] = $_SESSION['language'];
                        $this->db->select('wl_ntkd as n', 'name, content', $where);
                        $where['content'] = '#n.content';
                        $this->db->join('wl_sitemap', 'link, time, changefreq, priority', $where);
                        $ntkd = $this->db->get('array');
                        if(count($ntkd) > 1)
                        {
                            $_SESSION['alias']->name = 'SEO '.$alias->alias;
                            $_SESSION['alias']->breadcrumb = array('SEO' => 'admin/wl_ntkd', $alias->alias => '');
                            $this->load->admin_view('wl_ntkd/list_view', array('alias' => $alias, 'articles' => $ntkd));
                        }
                        else
                        {
                            $name = $alias->alias;
                            if(is_array($ntkd)) $name = '"'.$ntkd[0]->name.'" (../'.$alias->alias.')';
                            $_SESSION['alias']->name = 'SEO Головна сторінка '.$name;
                            $_SESSION['alias']->breadcrumb = array('SEO' => 'admin/wl_ntkd', $alias->alias => '');
                            $this->load->admin_view('wl_ntkd/edit_view', array('alias' => $alias, 'content' => 0, 'ntkd' => $this->get($alias)));
                        }
                    }
                }
                else
                    $this->load->page_404();
            }
            else
                $this->load->notify_view(array('errors' => 'Доступ заборонено!'));
        }
        elseif($this->userCan())
        	$this->load->admin_view('wl_ntkd/index_view');
        else
            $this->load->notify_view(array('errors' => 'Доступ заборонено!'));
        
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

    /**
     * Зберігаємо дані до wl_ntkd
     * Ключові поля:
     * @params $alias назва індексу масива
     * @params $content назва індексу масива
     * @params $field назва індексу масива
     * @params $data назва індексу масива
     * @params $language назва індексу масива
     * Додаткові поля (для зберігання у додаткову таблицю): (27.10.2015)
     * @params $additional_table назва таблиці
     * @params $additional_table_id ідентифікатор рядка в якому оновлюються дані
     * @params $additional_table_key назва індентифікатору рядка. Якщо не вказано = id
     * @params $additional_fields перелік полів із значеннями. Допускаються:
     * user = $_SESSION['user']->id
     * time = time()
     * * = *
     * формат запису: назва поля=>значення,назва поля=>значення
     *
     * @return значення асоційованого масиву у форматі application/json
     */
    public function save()
    {
        $res = array('result' => false, 'error' => 'Доступ заборонено! Тільки автор або адміністрація!');
        if($this->userCan($_SESSION['alias']->alias) || $this->access())
        {
            $table = false;
            $fields_ntkd = array('name', 'title', 'keywords', 'description', 'text', 'list', 'meta');
            $fields_sitemap = array('changefreq', 'priority', 'SiteMapIndex');
            if(isset($_POST['field']) && isset($_POST['data']))
            {
                if(in_array($_POST['field'], $fields_ntkd)) $table = 'wl_ntkd';
                if(in_array($_POST['field'], $fields_sitemap)) $table = 'wl_sitemap';

                if($table)
                {
                    $field = htmlentities($_POST['data'], ENT_QUOTES, 'utf-8');
                    $language = '';
                    if($_SESSION['language'] && isset($_POST['language']))
                    {
                        $language = htmlentities($_POST['language'], ENT_QUOTES, 'utf-8');
                        $language = "AND `language` = '{$language}'";
                    }

                    $alias = $content = 0;
                    if(isset($_POST['alias']) && is_numeric($_POST['alias']) && $_POST['alias'] > 0) $alias = $_POST['alias'];
                    if(isset($_POST['content']) && is_numeric($_POST['content'])) $content = $_POST['content'];

                    if($alias > 0)
                    {
                        if($_POST['field'] == 'priority')
                            $field *= 10;
                        if($_POST['field'] == 'SiteMapIndex')
                            $this->db->executeQuery("UPDATE `wl_sitemap` SET `priority` = `priority` * -1 WHERE `alias` = '{$alias}' AND `content` = '{$content}' {$language}");
                        else
                            $this->db->executeQuery("UPDATE `{$table}` SET `{$_POST['field']}` = '{$field}' WHERE `alias` = '{$alias}' AND `content` = '{$content}' {$language}");
                        $this->db->executeQuery("SELECT `id` FROM `{$table}` WHERE `alias` = '{$alias}' AND `content` = '{$content}' {$language}");
                        if($this->db->numRows() == 0)
                        {
                            $data['alias'] = $alias;
                            $data['content'] = $content;
                            if($_SESSION['language'] && isset($_POST['language']))
                                $data['language'] = htmlentities($_POST['language'], ENT_QUOTES, 'utf-8');
                            if($_POST['field'] == 'SiteMapIndex')
                                $data['priority'] = ($field == 1) ? 5 : -5;
                            else
                                $data[$_POST['field']] = $field;
                            $this->db->insertRow($table, $data);
                        }

                        $res['result'] = true;
                        $res['error'] = '';
                    }
                    else
                        $res['error'] = 'Невірне адреса!';

                    if($this->data->post('additional_table') && $this->data->post('additional_table_id') && $this->data->post('additional_fields'))
                    {
                        $data = array();
                        $fields = explode(',', $this->data->post('additional_fields', false));
                        foreach ($fields as $field) {
                            $field = explode('=>', $field);
                            if(isset($field[1])){
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
                            if(!$additional_table_key) $additional_table_key = 'id';
                            $this->db->updateRow($this->data->post('additional_table'), $data, $this->data->post('additional_table_id'), $additional_table_key);
                        }
                    }
                }
            }
            else
                $res['error'] = 'Невірне поле для зберігання даних!';
        }
        header('Content-type: application/json');
        echo json_encode($res);
        exit;
    }

    private function access()
    {
        if(isset($_POST['alias']) && is_numeric($_POST['alias']))
        {
            $alias = $this->db->getAllDataById('wl_aliases', $_POST['alias']);
            if($alias && $this->userCan($alias->alias))
                return true;
            return false;
        }
        
    }
	
}

?>
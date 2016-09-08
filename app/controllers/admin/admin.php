<?php

class admin extends Controller {
                
    function _remap($method, $data = array())
    {
        if (method_exists($this, $method) && $method != 'library' && $method != 'db')
        {
            if(empty($data)) $data = null;
            return $this->$method($data);
        }
        else
            $this->index($method);
    }

    function index()
    {
        @$_SESSION['alias']->id = 0;
        $_SESSION['alias']->alias = 'admin';
        $_SESSION['alias']->table = '';
        $_SESSION['alias']->name = 'Панель керування';
        $_SESSION['alias']->text = '';
        $_SESSION['alias']->js_load = array();
        $_SESSION['alias']->js_init = array();
        $_SESSION['alias']->breadcrumb = array();

        $alias = $this->data->uri(1);
        if($alias != '')
        {
            $alias = $this->db->getAllDataById('wl_aliases', $alias, 'alias');
            if(is_object($alias))
            {
                $_SESSION['alias'] = clone $alias;
                $this->load->model('wl_alias_model');
                $this->wl_alias_model->alias($alias->alias);
                if($alias->service > 0)
                {
                    $function = 'index';
                    if($this->data->uri(2) != '')
                        $function = $this->data->uri(2);
                    $this->load->function_in_alias($alias->alias, $function, array(), true);
                }
                $_SESSION['alias']->id = 0;
                $_SESSION['alias']->alias = 'admin';
                $_SESSION['alias']->table = '';
                $_SESSION['alias']->service = '';
            }
            else
                $this->load->page_404();
        }
        else
        {
            $this->load->model('wl_analytic_model');
            $views = $this->wl_analytic_model->getViewers();

            $this->load->admin_view('index_view', array('views' => $views));
        }
    }

    public function __get_Search($value='')
    {
        echo $value;
        $search = new stdClass();
        $search->id = 0;
        $search->link = '';
        $search->image = false;
        $search->date = time();
        $search->author = 0;
        $search->author_name = 'admin';
        $search->additional = false;

        if($user = $this->db->getAllDataById('wl_users', 1))
        {
            $search->date = $user->registered;
            $search->author = 1;
            $search->author_name = $user->name;
        }

        return $search;
    }
    
}

?>
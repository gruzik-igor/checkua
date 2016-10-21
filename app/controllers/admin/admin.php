<?php

class admin extends Controller {
                
    public function _remap($method, $data = array())
    {
        if (method_exists($this, $method) && $method != 'library' && $method != 'db')
        {
            if(empty($data)) $data = null;
            return $this->$method($data);
        }
        else
            $this->index($method);
    }

    public function index()
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
            $this->load->admin_view('index_view');
    }
    
}

?>
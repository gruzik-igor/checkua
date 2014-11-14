<?php

class admin extends Controller {
				
    function _remap($method)
    {
        if (method_exists($this, $method) && $method != 'library' && $method != 'db') {
            $this->$method();
        } else {
            $this->index($method);
        }
    }

    function index(){
        @$_SESSION['alias']->id = 0;
        $_SESSION['alias']->alias = 'admin';
        $_SESSION['alias']->table = '';

        $alias = $this->data->uri(1);
        if($alias != ''){
            $alias = $this->db->getAllDataById('wl_aliases', $alias, 'alias');
            if(is_object($alias)){
                $_SESSION['alias']->id = $alias->id;
                $_SESSION['alias']->alias = $alias->alias;
                $_SESSION['alias']->table = $alias->table;
                $this->load->model('wl_alias_model');
                $this->wl_alias_model->alias($alias->alias);
                if($alias->service > 0){
                    $service = $this->db->getAllDataById('wl_services', $alias->service);
                    $function = 'admin';
                    if($this->data->uri(2) != '') $function = $this->data->uri(2);
                    $this->load->service($service->name, $function);
                }
                $_SESSION['alias']->id = 0;
                $_SESSION['alias']->alias = 'admin';
                $_SESSION['alias']->table = '';
                $_SESSION['alias']->service = '';
            } else $this->load->page_404();
        } else $this->load->admin_view();
    }
	
}

?>
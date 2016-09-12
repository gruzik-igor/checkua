<?php

class static_pages extends Controller {
				
    function _remap($method, $data = array())
    {
        $_SESSION['alias']->breadcrumb = array($_SESSION['alias']->name => '');
        if (method_exists($this, $method)) {
            if(empty($data)) $data = null;
            return $this->$method($data);
        } else {
            $this->index($method);
        }
    }

    public function index()
    {
        $this->load->model('wl_alias_model');
        $this->wl_alias_model->setContent();
    	$this->load->smodel('static_page_model');
        $this->load->admin_view('index_view', array('page' => $this->static_page_model->get()));
    }

    public function __get_Search($content)
    {
        $this->load->smodel('static_pages_search_model');
        return $this->static_pages_search_model->getByContent($content, true);
    }
	
}

?>
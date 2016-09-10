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
    	$this->load->smodel('static_page_model');
        $page = $this->static_page_model->get($_SESSION['alias']->id);
        $this->load->admin_view('edit_view', array('article' => $page));
    }
	
}

?>
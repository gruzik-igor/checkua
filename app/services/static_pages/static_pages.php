<?php

class static_pages extends Controller {
				
    function _remap($method, $data = array())
    {
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
        $this->load->library('video');
        $this->video->makeVideosInText();
        $_SESSION['alias']->image = $page->photo;
        $this->load->page_view('index_view', array('page' => $page));
    }

    public function __get_Search($content)
    {
        $this->load->smodel('static_pages_search_model');
        return $this->static_pages_search_model->getByContent($content);
    }
	
}

?>
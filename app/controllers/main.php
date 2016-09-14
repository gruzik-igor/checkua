<?php

class Main extends Controller {

    public function index()
    {
    	$this->load->model('wl_alias_model');
        $this->wl_alias_model->setContent();
        $this->load->page_view('index_view');
    }

}

?>
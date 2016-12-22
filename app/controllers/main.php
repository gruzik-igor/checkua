<?php

class Main extends Controller {

    public function index()
    {
        $this->wl_alias_model->setContent();
        $this->load->page_view('index_view');
    }

}

?>
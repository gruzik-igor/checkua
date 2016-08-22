<?php

class Main extends Controller {

    function index()
    {
    	$_SESSION['alias']->content = 0;
        $this->load->page_view('index_view');
    }

}

?>
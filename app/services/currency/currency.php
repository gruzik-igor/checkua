<?php

/*

 	Service "Currency 1.0"
	for WhiteLion 1.0

*/

class currency extends Controller {
				
    function _remap($method, $data = array())
    {
        if (method_exists($this, $method)) {
        	if(empty($data)) $data = null;
            return $this->$method($data);
        } else {
        	$this->index($method);
        }
    }

    public function index($uri)
    {
    	$this->load->page_404();
    }

    public function __get_Search($content)
    {
    	return false;
    }

    public function __get_Currency($code)
    {
        $this->load->smodel('currency_model');
        return $this->currency_model->get($code);
    }

}

?>
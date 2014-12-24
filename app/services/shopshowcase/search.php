<?php

/*

 	Service "Shop Showcase 2.0"
	for WhiteLion 1.0

*/

class shopshowcase extends Controller {
				
    function _remap($method, $data = array())
    {
        if (method_exists($this, $method)) {
            $this->$method($data);
        } else {
        	$request = $_GET['request'];
        	$request = explode('/', $request);
        	if($request[0] == 'admin') $this->admin($method);
            else $this->index();
            // else $this->index($method);
        }
    }

    public function index($admin = false)
    {
        echo 123;
    }

}

?>
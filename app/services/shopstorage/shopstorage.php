<?php

/*

 	Service "Shop Storage 1.0"
	for WhiteLion 1.0

*/

class shopstorage extends Controller {
				
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

	public function __get_Invoices_to_Product($id = 0)
	{
		$this->load->smodel('storage_model');
		$_SESSION['option']->paginator_per_page = 0;
		return $this->storage_model->getProducts($id);
	}

	public function __get_Invoice($id = 0)
	{
		$this->load->smodel('storage_model');
		return $this->storage_model->getProduct($id);
	}

	public function __get_Products($data = array())
	{
		$group = 0;
		if(isset($data['group']) && is_numeric($data['group'])) $group = $data['group'];
		if(isset($data['limit']) && is_numeric($data['limit'])) $_SESSION['option']->paginator_per_page = $data['limit'];

		$this->load->smodel('shop_model');
		return $this->shop_model->getProducts($group);
	}
	
}

?>
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
		$productId = 0;
		$userType = 0;
		if(is_array($id))
		{
			if(isset($id['id'])) $productId = $id['id'];
			if(isset($id['user_type'])) $userType = $id['user_type'];
		}
		else $productId = $id;
		if($productId > 0)
		{
			$this->load->smodel('storage_model');
			$_SESSION['option']->paginator_per_page = 0;
			return $this->storage_model->getProducts($productId, $userType);
		}
		return false;
	}

	public function __get_Invoice($id = 0)
	{
		$productId = 0;
		$userType = 0;
		if(is_array($id))
		{
			if(isset($id['id'])) $productId = $id['id'];
			if(isset($id['user_type'])) $userType = $id['user_type'];
		}
		else $productId = $id;
		if($productId > 0)
		{
			$this->load->smodel('storage_model');
			return $this->storage_model->getProduct($productId, $userType);
		}
		return false;
	}

	// Зарезервувати товар за номером Invoice
	// invoise, amount
	public function __set_Reserve($data = array())
	{
		$this->load->smodel('storage_model');
		return $this->storage_model->setReserve($data);
	}

	// Списати товар за номером Invoice
	// invoise, amount, reserve
	public function __set_Book($data = array())
	{
		$this->load->smodel('storage_model');
		return $this->storage_model->setBook($data);
	}
	
}

?>
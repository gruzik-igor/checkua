<?php

/*

 	Service "Shop product price per user type 1.0"
	for WhiteLion 1.0

*/

class price_per_type extends Controller {
				
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
		$this->load->page_404(false);
    }

    public function __get_Search($content)
    {
    	return false;
    }

    public function __get_Product($product)
    {
        $this->load->smodel('ppt_model');
        return $this->ppt_model->getProduct($product);
    }

	public function __get_Products($products)
	{
		$this->load->smodel('ppt_model');
        $all = $currency = false;
        if(isset($products['all']) && isset($products['products']))
        {
            $all = true;
            if(isset($products['currency']))
                $currency = $products['currency'];
            $products = $products['products'];
        }
		return $this->ppt_model->getProducts($products, $currency, $all);
	}
	
}

?>
<?php

/*

 	Service "PayPal 1.0"
	for WhiteLion 1.0

*/

class paypal extends Controller {
				
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

    public function validate()
    {
        $this->load->smodel('paypal_model');
        if($pay = $this->paypal_model->validate())
            $this->load->function_in_alias($pay->cart_alias, '__set_Payment', $pay, true);
    }

    public function __get_Search($content)
    {
    	return false;
    }

    public function __get_Payment($cart)
    {
        $this->wl_alias_model->setContent();
        $_SESSION['alias']->link = $_SESSION['alias']->alias;
        
        $this->load->smodel('paypal_model');
        $pay = $this->paypal_model->create($cart);
        $pay->return_url = $cart->return_url;
        $this->load->page_view('paypal_form_view', array('pay' => $pay));
    	return true;
    }

}

?>
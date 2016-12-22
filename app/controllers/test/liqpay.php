<?php

class liqpay extends Controller {

    function _remap($method)
    {
        if (method_exists($this, $method)) {
            $this->$method();
        } else {
            $this->index($method);
        }
    }

    function index()
    {
		$this->load->page_view('test/liqpay_view');
	}
	
	function go()
	{
		if(isset($_POST['server_url']) && $_POST['server_url'] != '')
		{
			$payment = array();
	        $payment['version'] = 3;
	        $payment['amount'] = $this->data->post('amount');
	        $payment['currency'] = 'UAH';
	        $payment['transaction_id'] = 'WL test pay system';
	        $payment['order_id'] = $this->data->post('order_id');
	        $payment['status'] = $this->data->post('status');
	        $payment['action'] = 'buy';
	        $payment['language'] = 'uk';

        	$data = base64_encode( json_encode($payment) );
			$private_key = $this->data->post('private_key', false);
			$signature = base64_encode( sha1( $private_key . $data . $private_key , 1 ) );

			$curl_data = array('data' => $data, 'signature' => $signature);

			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $_POST['server_url']);
			curl_setopt($ch, CURLOPT_USERAGENT, 'server');
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_TIMEOUT, 3);
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $curl_data);
			$res = curl_exec($ch);
			curl_close($ch);

			$this->load->page_view('test/liqpay_view', array('res' => $res));
		}
	}

}
	
?>
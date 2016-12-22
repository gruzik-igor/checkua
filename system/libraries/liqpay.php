<?php  if (!defined('SYS_PATH')) exit('Access denied');

/*
 * Шлях: SYS_PATH/libraries/liqpay.php
 *
 * Робота з liqpay
 * @author Ostap Matskiv
 * @version 0.3
 */
 
class liqpay {
	
	public $data = false;
	public $transaction = '';
	public $server_url = 'account/validate';
	public $result_url = 'account/validate';
	private $public_key = false;
	private $private_key = false;

	/*
     * Отримуємо дані для з'єднання з конфігураційного файлу
     */
    function __construct($cfg)
    {
        $this->public_key = $cfg['public_key'];
        $this->private_key = $cfg['private_key'];
    }

	public function validate($order_id, $balance_wont)
	{
    	if(isset($_POST['data']) && $_POST['data'] != '')
    	{
			$signature = base64_encode( sha1( $this->private_key . $_POST['data'] . $this->private_key , 1 ) );
			if($_POST['signature'] == $signature)
			{
				$data = json_decode ( base64_decode ($_POST['data']) );
				if($data->version == 3 && ($data->status == 'success' || $data->status == 'sandbox'))
				{
					$data->amount = floatval($data->amount);
					$sender_phone = '';
					if(isset($data->sender_phone)) $sender_phone = ' '.$data->sender_phone;
					$this->transaction = 'LiqPay Transaction ID: '.$data->transaction_id.$sender_phone;
					$this->data = $data;
					if($data->amount == $balance_wont && $data->order_id == $order_id)
						return true;
				}
				else if($data->status == 'processing')
				{
					$this->transaction = 'processing';
					return false;
				}
			}
		}
		return false;
	}

	public function echoInputs($info, $test = false)
	{
		$payment = array();
        $payment['version'] = 3;
        $payment['public_key'] = $this->public_key;
        $payment['amount'] = $info['amount'];
        $payment['currency'] = 'UAH';
        $payment['description'] = $info['description'];
        $payment['order_id'] = $info['order_id'];
        $payment['action'] = 'buy';
        $payment['language'] = 'uk';
        if($test)
        	$payment['sandbox'] = 1;
        $payment['server_url'] = SITE_URL.$this->server_url;
        $payment['result_url'] = SITE_URL.$this->result_url;

        $data = base64_encode( json_encode($payment) );
        $signature = base64_encode(sha1($this->private_key . $data . $this->private_key, 1));

        echo('<input type="hidden" name="data" value="'.$data.'"/>');
        echo('<input type="hidden" name="signature" value="'.$signature.'"/>');
        return true;
	}

}

?>

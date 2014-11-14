<?php  if (!defined('SYS_PATH')) exit('Access denied');

/*
 * Шлях: SYS_PATH/libraries/db.php
 *
 * Робота з liqpay
 * @author Ostap Matskiv
 * @version 0.1
 */
 
class liqpay {
	
	public $transaction;

    public function validate($pay, $method){
		if(isset($_POST['operation_xml']) && $_POST['operation_xml'] != ''){
			$merchant_pass = $method->key;
			$xml = base64_decode($_POST['operation_xml']);
			$signature = base64_encode(sha1($merchant_pass.$xml.$merchant_pass, 1));
			if($_POST['signature']==$signature) {
				$xml_arr = simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA);
				$id_cart = array_pop(explode('_', $xml_arr->order_id));
				
				if($xml_arr->status=='success') {
					$amount = floatval($xml_arr->amount);
					$this->transaction = 'LiqPay Transaction ID: '.$xml_arr->transaction_id .' '.$xml_arr->sender_phone;
					if($amount == $pay->money && $id_cart == $pay->id) return true;
				}
			}
		}
		return false;
	}

}

?>

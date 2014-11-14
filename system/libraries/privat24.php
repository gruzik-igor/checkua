<?php  if (!defined('SYS_PATH')) exit('Access denied');

/*
 * Шлях: SYS_PATH/libraries/db.php
 *
 * Робота з Privat24
 * @author Ostap Matskiv
 * @version 0.1
 */
 
class privat24 {
	
	public $transaction = 0;

    public function validate($pay, $method){
		if(isset($_POST['payment']) && $_POST['payment'] != ''){
			$signature = sha1(md5($_POST['payment'].$method->key));
			if($_POST['signature'] == $signature){
				parse_str($_POST['payment'], $output);
				$id_cart = $output['order'];
				if($output['state']=='test' || $output['state']=='ok'){
				// if($output['state']=='ok'){
					$amount = floatval($output['amt']);
					$pay->money = floatval($pay->money);
					$this->transaction = 'Privat24 Transaction ID: '.$output['ref'].' '.$output['sender_phone'];
					if($amount == $pay->money && $id_cart == $pay->id) return true;
				}
			}
		}
		return false;
	}
	
	public function getTransaction(){
		return $this->transaction;
	}

}

?>

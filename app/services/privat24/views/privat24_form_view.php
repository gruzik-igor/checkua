<?php

/**
* @author Sokil
* @copyright to service 30.03.2016
* @copyright 01.02.2015
*/

if(isset($pay) && $pay->amount > 0 && isset($_SESSION['option']->merchant) && isset($_SESSION['option']->password)) {
	$payament = "amt={$pay->amount}&ccy=UAH&details={$pay->details}&ext_details=&pay_way=privat24&order={$pay->id}&merchant={$_SESSION['option']->merchant}";
	$signature = sha1(md5($payament.$_SESSION['option']->password));
?>
<div class="panel panel-default">
	<div class="panel-heading">
		<h4 class="panel-title">
			<a data-toggle="collapse" data-parent="#accordion" href="#collapseOne">
				<i class="fa fa-credit-card"></i>
				Privat24
			</a>
		</h4>
	</div>
	<div id="collapseOne" class="panel-collapse collapse in">
		<div class="panel-body cus-form-horizontal">
			<img src="<?=SITE_URL?>app/services/privat24/views/logo_privat24.png" alt="Privat24">
		
			<form action="https://api.privatbank.ua/p24api/ishop" method="post" id="pay_form" name="pay_form">
				<input type="hidden" name="amt" value="<?=$pay->amount?>"/>
				<input type="hidden" name="ccy" value="UAH" />
				<input type="hidden" name="merchant" value="<?=$_SESSION['option']->merchant?>" />
				<input type="hidden" name="order" value="<?=$pay->id?>" />
				<input type="hidden" name="details" value="<?=$pay->details?>" />
				<input type="hidden" name="ext_details" value="" />
				<input type="hidden" name="pay_way" value="privat24" />
				<input type="hidden" name="return_url" value="<?=SITE_URL.$pay->return_url?>" />
				<input type="hidden" name="server_url" value="<?=SITE_URL.'privat24/validate/'.$pay->id ?>" />
				<input type="hidden" name="signature" value="<?=$signature?>" />
				<button type="submit" class="btn-u btn-u-sea-shop">Оплатити <?=number_format($pay->amount, 2)?> грн</button>
			</form>
		</div>
	</div>
</div>

<?php } ?>
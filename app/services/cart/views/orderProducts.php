<?php if(isset($controls) && $controls) { ?>
<div class="container">
	<div class="row">
		<a href="<?=SITE_URL?>profile" class="btn btn-success btn-sm">До всіх замовлень</a>
		
		<a href="<?=SITE_URL?>cart/order/<?= $cartInfo->id?>" class="btn btn-danger btn-sm pull-right" target="_blank">Друкувати</a>
		<?php if($cartInfo->status == 2) { ?>
			<a href="<?=SITE_URL?>cart/pay/<?= $cartInfo->id?>" class="btn btn-warning btn-sm pull-right" style="margin-right:5px">Оплатити</a> 
		<?php } ?>
	</div>
</div>
<?php } else { ?>
	<!DOCTYPE html>
	<html lang="uk">
		<head>
			<title>Замовлення</title>
		    <meta charset="utf-8">
		    <meta name="viewport" content="width=device-width, initial-scale=1.0">
			<meta name="viewport" content="width=device-width, maximum-scale=1, initial-scale=1, user-scalable=0" />
			<link href="<?=SITE_URL?>assets/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
		</head>

		<body onload="window.print();">
<?php } ?>
		<div class="container">
		    <div class="row">
		    	<div class="clearfix">
		    		<h1>Замовлення #<?= $cartInfo->id?> від <?= date('d.m.Y H:i', $cartInfo->date_edit)?></h1>
			    	<h4 class="pull-right"><div align='right'><?= $cartInfo->user_name ."</div>". $cartInfo->user_email .", " . $cartInfo->user_phone ?> </h4>
			    	<h4> Статус: <?= $cartInfo->status_name ."<br>Тип покупця: ". $cartInfo->user_type_name ?></h4>
		    	</div>
		   		<div class="table-responsive" >
		    		<table class="table table-striped table-bordered nowrap" width="100%">
		    			<thead>
			    			<tr>
			    				<th>Артикул</th>
			    				<th>Виробник</th>
			    				<th>Назва</th>
			    				<th>Кількість</th>
			    				<th>Ціна за од.</th>
			    				
			    			</tr>
			    		</thead>
			    		<tbody>
							<?php if($orderProducts) foreach($orderProducts as $product) {?>
			    			<tr>
			    				<td><?= $product->info->article ?></td>
			    				<td><?= $product->info->options['1-vyrobnyk']->value ?></td>
			    				<td><?= $product->info->name ?></td>
			    				<td><?= $product->quantity ?></td>
			    				<td><?= $product->price ?> $</td>
			    			</tr>
							<?php } ?>
							<tr>
								<td></td><td></td><td></td><td></td><td><b><?= $cartInfo->total?> $</b></td>
							</tr>
			    		</tbody>
		    		</table>
		    	</div>
				
				<?php if($_SESSION['option']->useShipping && $cartInfo->shipping_id > 0) { ?>

			    	<h3>Доставка</h3>
	                <b>Служба доставки:</b> <?= $cartInfo->shipping->method_name ?> <br>
	                <b>Сайт:</b> <?= $cartInfo->shipping->method_site ?> <br>
	                <b>Адреса:</b> <?= $cartInfo->shipping->address ?><br><br>

                <?php } if(isset($controls) && $controls) { ?>

			    	<div class="table-responsive">
			    		<h3>Історія замовлення</h3>
					    <table class="table table-striped table-bordered nowrap" width="100%">
					        <thead>
					        	<tr>
					        		<th>Дата</th>
					    	    	<th>Статус</th>
					    	    	<th>Коментар</th>
					        	</tr>	
					        </thead>
					        <tbody>
					        	<?php if($cartHistory) foreach($cartHistory as $history) {?>
					        	<tr>
					                <td><?= date('d.m.Y H:i',$history->date)?></td>
					                <td><?= $history->status_name?></td>
					                <td><?= $history->comment?></td>
					        	</tr>
					        	<?php } ?>
					        	<tr>
					                <td><?= date('d.m.Y H:i',$cartInfo->date_add)?></td>
					                <td>Нова</td>
					                <td></td>
					            </tr>
					        </tbody>
					    </table>
					</div>
				<?php } ?>
		    </div>
		</div>
<?php if(!isset($controls)|| !$controls) { ?>
	</body>
</html>
<?php } ?>
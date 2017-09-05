<?php if($controls) { ?>
	<div class="container content">
		<div class="row">
			<div class="col-md-12">
				<a href="<?=SITE_URL.$_SESSION['alias']->alias?>/my" class="btn btn-success btn-sm"><?=$this->text('До всіх замовлень')?></a>
				<a href="<?=SITE_URL?>cart/<?= $cart->id?>/print" class="btn btn-danger btn-sm pull-right" target="_blank"><?=$this->text('Друкувати')?></a>
				<?php if($cart->action == 'new') { ?>
					<a href="<?=SITE_URL?>cart/pay/<?= $cart->id?>" class="btn btn-warning btn-sm pull-right" style="margin-right:5px">Оплатити</a>
				<?php } ?>
			</div>
		</div>
	</div>
<?php } else { ?>
	<!DOCTYPE html>
	<html lang="uk">
		<head>
			<title>.</title>
		    <meta charset="utf-8">
		    <meta name="viewport" content="width=device-width, initial-scale=1.0">
			<meta name="viewport" content="width=device-width, maximum-scale=1, initial-scale=1, user-scalable=0" />
			<link href="<?=SITE_URL?>assets/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
		</head>

		<body onload="window.print();">
		<center><img src="<?=SITE_URL?>style/images/logo.png" style="padding-top: 15px"></center>
<?php } ?>
		<div class="container">
		    <div class="row">
		    	<div class="col-md-12">
			    	<div style="clear:both">
			    		<h1><?=$this->text('Замовлення')?> #<?= $cart->id?> <?=$this->text('від')?> <?= date('d.m.Y H:i', $cart->date_edit)?></h1>
				    	<table class="cartUserinfo">
							<tr>
								<td>Покупець:</td>
								<th><?= $cart->shipping->receiver .", " . $cart->shipping->phone ?></th>
							</tr>
							<?php if($_SESSION['option']->useShipping && $cart->shipping_id > 0) { ?>
								<tr>
									<td>Служба доставки: </td>
									<th><?= $cart->shipping->method_name ?><?= ($cart->shipping->method_site) ? ', '. $cart->shipping->method_site : '' ?></th>
								</tr>
								<tr>
									<td>Адреса: </td>
									<th><?= $cart->shipping->address ?></th>
								</tr>
							<?php } ?>
						</table>
						<div style="float:right; text-align: right;"> Статус замовлення: <b><?= $cart->status_name ?></b></div>
			    	</div>
			   		<div class="table-responsive" style="clear:both">
			    		<table class="table table-striped table-bordered nowrap" width="100%">
			    			<thead>
				    			<tr>
				    				<th><?=$this->text('Артикул')?></th>
				    				<th><?=$this->text('Назва')?></th>
				    				<th><?=$this->text('Кількість')?></th>
				    				<th class="text-right"><?=$this->text('Ціна за од.')?></th>
				    			</tr>
				    		</thead>
				    		<tbody>
								<?php if($orderProducts) foreach($orderProducts as $product) {?>
				    			<tr>
				    				<td><?= $product->info->article ?></td>
				    				<td><?= str_replace($product->info->article, "", $product->info->name) ?></td>
				    				<td><?= $product->quantity ?></td>
				    				<td class="text-right"><?= $product->price ?> грн</td>
				    			</tr>
								<?php } ?>
								<tr>
									<td></td><td></td><td></td><td class="text-right"><b><?= $cart->total?> грн</b></td>
								</tr>
				    		</tbody>
			    		</table>
			    	</div>

					<div class="pull-right text-right">
                		<h3><?=$this->text('До оплати')?>: <b class="color-red"><?= $cart->total ?> грн</b></h3>
                	</div>

	                <?php if(isset($controls) && $controls) { ?>
				    	<div class="table-responsive" style="clear:both">
				    		<h3><?=$this->text('Історія замовлення')?></h3>
						    <table class="table table-striped table-bordered nowrap" width="100%">
						        <thead>
						        	<tr>
						        		<th>Дата</th>
						    	    	<th>Статус</th>
						    	    	<th><?=$this->text('Коментар')?></th>
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
						                <td><?= date('d.m.Y H:i',$cart->date_add)?></td>
						                <td><?=$this->text('Нова, не оплачено')?></td>
						                <td></td>
						            </tr>
						        </tbody>
						    </table>
						</div>
					<?php } ?>
				</div>
		    </div>
		</div>
		<style>
			h1
			{
			    margin: 40px 0 30px;
			}
			table th {
				font-weight: bold;
			}
			table.cartUserinfo tr td
			{
				padding: 5px;
			}
		</style>
<?php if(!isset($controls)|| !$controls) { ?>
	</body>
</html>
<?php } ?>
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
	    		<h1 class="col-md-12"><?=$this->text('Замовлення')?> #<?= $cart->id?> <?=$this->text('від')?> <?= date('d.m.Y H:i', $cart->date_edit)?></h1>
    			<div class="col-md-6">
    				<p><strong><?= $cart->user_name .", " . $cart->user_phone ?></strong></p>
    				<?php if($cart->shipping_id) { ?>
	    				<p><?= $cart->shipping->method_name ?><?= ($cart->shipping->method_site) ? ', '. $cart->shipping->method_site : '' ?></p>
	    				<p><?= $cart->shipping->address ?></p>
	    				<p><?= $cart->shipping->receiver .", " . $cart->shipping->phone ?></p>
    				<?php } ?>
    			</div>
    			<div class="col-md-6 text-right">
    				<p>Статус замовлення: <strong><?= $cart->status_name ?></strong></p>
    			</div>
			</div>
			<?php if($cart->products) foreach($cart->products as $product) { ?>
				<div class="row">
					<?php if($product->info->photo) { ?>
						<div class="col-md-2">
							<a href="<?=SITE_URL.$product->info->link?>">
								<img src="<?=IMG_PATH?><?=(isset($product->info->cart_photo)) ? $product->info->cart_photo : $product->info->photo ?>" alt="<?= $product->info->name ?>" class="w-100">
							</a>
						</div>
					<?php } ?>
					<div class="col-md-<?=($product->info->photo) ? 6 : 8?>">
						<a href="<?=SITE_URL.$product->info->link?>"><?= $product->info->name ?></a>
						<?php if(!empty($product->options))
						{
							$product->options = unserialize($product->options);
							foreach ($product->options as $key => $value) {
								echo "<br>{$key}: <strong>{$value}</strong>";
							}
						} ?>
					</div>
					<div class="col-md-4 text-right"><?=$this->cart_model->priceFormat($product->price) ?> x <?= $product->quantity ?> = <strong><?=$this->cart_model->priceFormat($product->price * $product->quantity) ?></strong></div>
				</div>
			<?php } ?>


					<div class="pull-right text-right">
                		<h3><?=$this->text('До оплати')?>: <b class="color-red"><?= $cart->total ?> грн</b></h3>
                	</div>

	                <?php if(isset($controls) && $controls) { ?>
				    	<div class="table-responsive" style="clear:both">
				    		<h3><?=$this->text('Історія замовлення')?></h3>
						    <table class="table table-striped table-bordered " width="100%">
						        <thead>
						        	<tr>
						        		<th>Дата</th>
						    	    	<th>Статус</th>
						    	    	<th><?=$this->text('Коментар')?></th>
						        	</tr>
						        </thead>
						        <tbody>
						        	<?php if($cart->history) foreach($cart->history as $history) {?>
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
<?php if(!$controls) { ?>
	</body>
</html>
<?php } ?>
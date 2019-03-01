<?php if($controls) { ?>
<div class="page-head content-top-margin">
	<div class="container">
		<div class="row">
			<h1 class="col-md-12"><?=$this->text('Замовлення')?> #<?= $cart->id?> <?=$this->text('від')?> <?= date('d.m.Y H:i', $cart->date_edit)?></h1>
		</div>
	</div>
</div>

	<div class="container content">
		<div class="row">
			<div class="col-md-12">
				<a href="<?=SITE_URL.$_SESSION['alias']->alias?>/my" class="btn btn-success btn-sm"><?=$this->text('До всіх замовлень')?></a>
				<a href="<?=SITE_URL?>cart/<?= $cart->id?>/print" class="btn btn-danger btn-sm pull-right lilbotbtn" target="_blank"><?=$this->text('Друкувати')?></a>
				<?php if($cart->action == 'new') { ?>
					<a href="<?=SITE_URL?>cart/<?= $cart->id?>/pay" class="btn btn-warning btn-sm pull-right lilbotbtn" style="margin-right:5px"><?=$this->text('Оплатити')?></a>
				<?php } ?>
			</div>
		</div>
	</div>
	<section class="section">
<?php } else { ?>
	<!DOCTYPE html>
	<html lang="<?=$_SESSION['language']?>">
		<head>
			<title>.</title>
		    <meta charset="utf-8">
		    <meta name="viewport" content="width=device-width, initial-scale=1.0">
			<meta name="viewport" content="width=device-width, maximum-scale=1, initial-scale=1, user-scalable=0" />
			<link href="<?=SERVER_URL?>assets/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
		</head>

		<body onload="window.print();">
		<center><img src="<?=IMG_PATH?>main/logo.png" style="padding-top: 15px; width: 300px;"></center>
<?php } ?>
		<div class="container">
		    <div class="row">
		    	<div class="col-xs-12">
				<?php if(!$controls) { ?>
					<h1 class="col-md-12" style="font-size: 20px; text-align: center;"><?=$this->text('Замовлення')?> #<?= $cart->id?> <?=$this->text('від')?> <?= date('d.m.Y H:i', $cart->date_edit)?></h1>
					<p><strong><?= $cart->user_name .", " . $cart->user_email ?></strong></p>
					<p><?=$this->text('Статус')?>: <strong><?= $cart->status_name ?></strong></p>
				<?php } ?>
				    <?php if($cart->shipping_id) {
			        echo "<legend>{$this->text('Доставка')}</legend>";
			        echo "<p><b>{$cart->shipping->name}</b> </p>";
			        if(!empty($cart->shipping->text))
			            echo "<p>{$cart->shipping->text}</p>";
			        else
			        {
			            if(!empty($cart->shipping_info['city']))
			                echo "<p>{$this->text('Місто')}: <b>{$cart->shipping_info['city']}</b> </p>";
			            if(!empty($cart->shipping_info['department']))
			                echo "<p>{$this->text('Відділення')}: <b>{$cart->shipping_info['department']}</b> </p>";
			            if(!empty($cart->shipping_info['address']))
			                echo "<p>{$this->text('Адреса')}: <b>{$cart->shipping_info['address']}</b> </p>";
			        }
			        if(!empty($cart->shipping_info['recipient']))
			        {
			            echo "<p>{$this->text('Отримувач')}: <b>{$cart->shipping_info['recipient']}</b> ";
			            if(!empty($cart->shipping_info['phone']))
			            	echo " <b>{$cart->shipping_info['phone']}</b>";
			        	echo "</p>";
			        }
			    } ?>
				<hr>
				</div>
			</div>
			
			<?php if($cart->products) foreach($cart->products as $product) { //print_r($product); ?>
				<div class="row">
					<?php if($product->info->photo) { ?>
						<div class="col-sm-2 col-xs-3" style="max-width: 20%">
							<a href="<?=SITE_URL.$product->info->link?>">
								<img src="<?=IMG_PATH?><?=(isset($product->info->cart_photo)) ? $product->info->cart_photo : $product->info->photo ?>" alt="<?= $product->info->name ?>" style="width: 100%">
							</a>
						</div>
					<?php } ?>
					<div class="col-sm-<?=($product->info->photo) ? 8 : 10?> col-xs-9" style="max-width: 80%">
						<a href="<?=SITE_URL.$product->info->link?>"><strong><?= $product->info->name ?></strong></a>
						<?php if(!empty($product->product_options))
						{
							$product->product_options = unserialize($product->product_options);
							foreach ($product->product_options as $key => $value) {
								echo "<br>{$key}: <strong>{$value}</strong>";
							}
						} ?>
						<br>
						<br>
						<?=$this->cart_model->priceFormat($product->price) ?> x <?= $product->quantity ?> = <strong><?=$this->cart_model->priceFormat($product->price * $product->quantity) ?></strong></div>
				</div>
				<hr>
			<?php }

			if (!empty($cart->discount)){ ?>
				<h4><?=$this->text('Sum')?>: <b class="color-red"><?= $this->cart_model->priceFormat($cart->total + $cart->discount) ?></b></h4>
				<h4><?=$this->text('Discount')?>: <b class="color-red"><?= $this->cart_model->priceFormat($cart->discount) ?></b></h4>
			<?php } ?>

            	<h4><?=$this->text('До оплати')?>: <b class="color-red"><?= $this->cart_model->priceFormat($cart->total) ?></b></h4>

                <?php if(isset($controls) && $controls) { ?>
			    	<div class="table-responsive" style="clear:both">
			    		<h3><?=$this->text('Історія замовлення')?></h3>
					    <table class="table table-striped table-bordered " width="100%">
					        <thead>
					        	<tr>
					        		<th><?=$this->text('Дата')?></th>
					    	    	<th><?=$this->text('Статус')?></th>
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
		<script type="text/javascript" src="<?=SERVER_URL?>assets/bootstrap/js/bootstrap.min.js"></script>
	</body>
</html>
<?php } else { ?>
</section>
<?php } ?>
<div class="page-head content-top-margin">
	<div class="container">
		<h1><?=$this->text('Оплатити Замовлення')?> #<?= $cart->id?> <?=$this->text('від')?> <?= date('d.m.Y H:i', $cart->date_edit)?></h1>
	</div>
</div>

<div class="container content">
	<div class="row">
	<?php if($cart->products) foreach($cart->products as $product) { //print_r($product); ?>
		<div class="col-md-6 ">
			<div class="row" style="margin-bottom: 20px">
				<?php if($product->info->photo) { ?>
					<div class="col-sm-2">
						<a href="<?=SITE_URL.$product->info->link?>">
							<img src="<?=IMG_PATH?><?=(isset($product->info->cart_photo)) ? $product->info->cart_photo : $product->info->photo ?>" alt="<?= $product->info->name ?>" style="width: 100%">
						</a>
					</div>
				<?php } ?>
				<div class="col-sm-<?=($product->info->photo) ? 8 : 10?>">
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
		</div>
	<?php } ?>
</div>
	<div class="row">
		<?php if (!empty($cart->discount) || !empty($cart->shipping_info['price'])){
			if(empty($cart->shipping_info['price']))
				$cart->shipping_info['price'] = 0;
			?>
			<h4><?=$this->text('Sum')?>: <b class="color-red"><?= $this->cart_model->priceFormat($cart->total + $cart->discount - $cart->shipping_info['price']) ?></b></h4>
			<?php if (!empty($cart->discount)) { ?>
				<h4><?=$this->text('Discount')?>: <b class="color-red"><?= $this->cart_model->priceFormat($cart->discount) ?></b></h4>
			<?php } if (!empty($cart->shipping_info['price'])) { ?>
				<h4><?=$this->text('Доставка')?>: <b class="color-red"><?= $this->cart_model->priceFormat($cart->shipping_info['price']) ?></b></h4>
		<?php } } ?>

        	<h4><?=$this->text('До оплати')?>: <b class="color-red"><?= $this->cart_model->priceFormat($cart->total) ?></b></h4>
		
			<h2 class="title-type"><?=$this->text('Оберіть платіжний механізм')?></h2>

			<form action="<?= SERVER_URL?>cart/pay" method="POST">
				<input type="hidden" name="cart" value="<?=$cart->id?>">
				<?php if($payments) {
			        foreach ($payments as $payment) { ?>
					    <button type="submit" name="method" value="<?=$payment->id?>" class="btn btn-success col-md-5 md-margin-bottom-50">
					    	<?=$payment->name?>
					    		<br>
					    		<br>
					    		<?=htmlspecialchars_decode($payment->info)?>
					    	</button>
					    <div class="col-md-1"></div>
			        <?php }
			    } else 
			    	echo "Платіжні сервіси не підключено. Зверніться до адміністратора";
				?>
			</form>
		
	</div>
</div>
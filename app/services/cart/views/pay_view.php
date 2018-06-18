<div class="page-head content-top-margin">
	<div class="container">
		<h1><?=$this->text('Оплатити Замовлення')?> #<?= $cart->id?> <?=$this->text('від')?> <?= date('d.m.Y H:i', $cart->date_edit)?></h1>
	</div>
</div>

<div class="container content">
	<?php if($cart->products) foreach($cart->products as $product) { //print_r($product); ?>
		<div class="row">
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
		<h4><?=$this->text('До оплати')?>: <b class="color-red"><?= $cart->total ?> грн</b></h4>
	<?php } ?>

	<div class="row">
		<div class="col-md-6 md-margin-bottom-50">
			<h2 class="title-type"><?=$this->text('Оберіть платіжний механізм')?></h2>

			<form action="<?= SERVER_URL?>cart/pay" method="POST">
				<input type="hidden" name="cart" value="<?=$cart->id?>">
				<?php if($payments) {
			        foreach ($payments as $payment) { ?>
				        <div class="form-group">
						    <button type="submit" name="method" value="<?=$payment->id?>" class="btn btn-success"><?=$payment->name?></button>
						    <?=htmlspecialchars_decode($payment->info)?>
						</div>
			        <?php }
			    } else 
			    	echo "Платіжні сервіси не підключено. Зверніться до адміністратора";
				?>
			</form>
		</div>
	</div>
</div>
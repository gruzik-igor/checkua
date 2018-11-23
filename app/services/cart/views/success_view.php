<div class="page-head content-top-margin">
	<div class="container">
		<div class="row">
			<h1 class="col-md-12"><?=$_SESSION['alias']->name?></h1>
		</div>
	</div>
</div>
<section class="section">
	<div class="container content">
		<div class="row">
			<div class="col-md-12">
				<?=$_SESSION['alias']->text?>
			</div>
		</div>
	</div>
</section>
<section class="section">
	<div class="container content">
		<div class="row">
			<div class="col-md-12">
				<h1 style="margin-bottom: 40px"><?=$this->text('Замовлення')?> #<?= $cart['id']?></h1>

				<h3><b>Покупець</b></h3>
				<?php
				echo '<p><b>'.$cart['user_name'].'</b><br> '.$cart['user_email'].', '.$cart['user_phone'].'</p>';

				if(!empty($cart['delivery']))
				{
					echo '<h3><b>Доставка</b></h3>';
					echo '<p>'.$cart['delivery'].'</p>';
				}

				if(!empty($cart['payment']))
					echo '<p>Платіжний механізм: <b>'.$cart['payment'].'</b></p>';

				if(!empty($cart['comment']))
				{
					echo '<h3><b>Коментар</b></h3>';
					echo '<p>'.$cart['comment'].'</p>';
				}

				echo '<h3><b>Замовлення</b></h3><div class="row">';

				if(!empty($cart['products']))
					foreach($cart['products'] as $product) { ?>
						<div class="col-md-6 ">
							<div class="row" style="margin-bottom: 20px">
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
									if(!is_array($product->product_options))
										$product->product_options = unserialize($product->product_options);
									foreach ($product->product_options as $key => $value) {
										echo "<br>{$key}: <strong>{$value}</strong>";
									}
								} ?>
								<br>
								<br>
								<?=$product->price .' x '. $product->quantity .' = <strong>'.$product->sum ?></strong>
							</div>
						</div>
					</div>
				<?php } ?>
				</div>
				<hr>
				<h4><?=$this->text('До оплати')?>: <b class="color-red"><?= $cart['total_formatted'] ?></b></h4>
			</div>
		</div>
	</div>
</section>


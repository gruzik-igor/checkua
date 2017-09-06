<?php
if($_SESSION['cart']->initJsStyle) {
	$_SESSION['alias']->js_load[] = 'js/'.$_SESSION['alias']->alias.'/cart.js';
	echo '<link rel="stylesheet" type="text/css" href="'.SITE_URL.'style/'.$_SESSION['alias']->alias.'/cart.css">';
	$_SESSION['cart']->initJsStyle = false;
}
?>

<div id="cart" class="container content">
    <div class="row">
		<h1 class="col-md-12"><?=$_SESSION['alias']->name?></h1>
	</div>
	<div id="cart_notify" class="alert alert-danger fade">
		<span class="close" data-dismiss="alert">×</span>
		<p></p>
	</div>
	<div class="row">
		<form action="<?=SITE_URL.$_SESSION['alias']->alias?>/checkout" method="POST">
			<div class="col-md-8">
				<?php $subtotal = 0;
				if($products) foreach($products as $product) {
					$subtotal += $product->price * $product->quantity;
				?>
					<div class="row product">
						<div class="col-md-1">
							<button type="button" class="close" onclick="cart.remove(<?= $product->id?>, this)" ><span>×</span><span class="sr-only">Close</span></button>
						</div>
						<?php if($product->info->photo) { ?>
							<div class="col-md-3">
								<a href="<?=SITE_URL.$product->info->link?>">
									<img src="<?=IMG_PATH?><?=(isset($product->info->cart_photo)) ? $product->info->cart_photo : $product->info->photo ?>" alt="<?=$this->text('Фото'). ' '. $product->info->name ?>">
								</a>
							</div>
						<?php } ?>
						<div class="col-md-<?=($product->info->photo) ? 8 : 11?>">
							<h3><a href="<?=SITE_URL.$product->info->link?>"><?= $product->info->name ?></a></h3>
							<?php if(!empty($product->options))
							{
								$product->options = unserialize($product->options);
								foreach ($product->options as $key => $value) {
									echo "<br>{$key}: <strong>{$value}</strong>";
								}
							} ?>
							<p class="price"><?=$this->cart_model->priceFormat($product->price) ?></p>
							<div class="input-group has-success col-md-3">
								<div class="input-group-btn">
									<button type="button" class="btn btn-success" onclick="cart.update(<?= $product->id?>, event)" value="-">-</button>
								</div>
								<input type="number" min="1" name="productQuantity-<?= $product->id?>" id="productQuantity-<?= $product->id?>" onchange="cart.update(<?= $product->id?>, event)" value="<?= $product->quantity?>" class="form-control">
								<div class="input-group-btn">
									<button type="button" class="btn btn-success" onclick="cart.update(<?= $product->id?>, event)" value="+">+</button>
								</div>
							</div>
						</div>
					</div>
				<?php } else { ?>
					<div class="alert alert-warning">
						<p><?=$this->text('Корзина пуста')?></p>
					</div>
				<?php } ?>
			</div>
			<div class="col-md-4">
				<?php if($products) { ?>
					<h3><?=$this->text('Попередня сума')?></h3>
					<p id="subTotal" class="price"><?=$this->cart_model->priceFormat($subtotal) ?></p>
					<input type="submit" class="btn btn-warning" value="<?=$this->text('Оформити замовлення')?>">
				<?php } ?>
			</div>
		</form>
	</div>
</div>


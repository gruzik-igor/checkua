<div class="table-responsive" id="invoice">
	<table class="table table-striped">
		<thead>
			<tr>
				<th width="14%">Артикул</th>
				<th width="50%">Продукт</th>
				<th width="8%">Ціна</th>
				<th width="20%">Кількість</th>
				<th width="8%">Разом</th>
			</tr>
		</thead>
		<tbody>
		<?php if(isset($_SESSION['cart']) && !empty($_SESSION['cart']->products)){ foreach($_SESSION['cart']->products as $product) {?>
			<tr>
				<td><?= $product['article'] ?> </td>
				<td class="product-in-table">
					<?php if(!empty($product['s_photo'])){ ?>
					<img class="img-responsive" src="<?= $product['s_photo']?>" alt="">
					<?php } ?>
					<div class="product-it-in">
						<h3><?= $product['name']?></h3>
					</div>
				</td>
				<td>$<?= $product['price']?></td>
				<td><?= $product['quantity']?></td>
				<td class="shop-red"> <?= $product['price'] * $product['quantity']?> $</td>
			</tr>
		<?php } } ?>
		</tbody>
	</table>
</div>

<?php if($_SESSION['option']->useShipping && isset($_SESSION['cart']->shipping)) { ?>
<h2>Доставка</h2>
<b>Служба доставки:</b> <?=$_SESSION['cart']->shipping['method-info']->name?> <br>
<?php if($_SESSION['cart']->shipping['method-info']->site != '') echo("Сайт: {$_SESSION['cart']->shipping['method-info']->site}<br>"); ?>
<b>Адреса:</b> <?=$_SESSION['cart']->shipping['shippingAddress']?>
<?php } ?>

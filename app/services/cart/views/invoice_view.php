<div class="table-responsive" id="invoice">
	<table class="table table-striped">
		<thead>
			<tr>
				<th>Продукт</th>
				<th>Ціна</th>
				<th>Кількість</th>
				<th>Разом</th>
			</tr>
		</thead>
		<tbody>
		<?php if(isset($_SESSION['cart']) && !empty($_SESSION['cart']->products)){ foreach($_SESSION['cart']->products as $product) {?>
			<tr>
				<td class="product-in-table">
					<?php if(!empty($product['s_photo'])){ ?>
					<img class="img-responsive" src="<?= $product['s_photo']?>" alt="">
					<?php } ?>
					<div class="product-it-in">
						<h3><?= $product['name']?></h3>
					</div>
				</td>
				<td ><?= $product['price']?> грн.</td>
				<td><?= $product['quantity']?></td>
				<td class="shop-red"> <?= $product['price'] * $product['quantity']?> грн.</td>
			</tr>
		<?php } } ?>
		</tbody>
	</table>
</div>
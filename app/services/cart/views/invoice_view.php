<div class="table-responsive" id="invoice">
	<table class="table table-striped">
		<thead>
			<tr>
				<th width="14%"><?=$this->text('Артикул')?></th>
				<th width="50%"><?=$this->text('Продукт')?></th>
				<th width="8%"><?=$this->text('Ціна')?></th>
				<th width="20%"><?=$this->text('Кількість')?></th>
				<th width="8%"><?=$this->text('Разом')?></th>
			</tr>
		</thead>
		<tbody>
		<?php if(isset($_SESSION['cart']) && !empty($_SESSION['cart']->products)){ foreach($_SESSION['cart']->products as $product) {?>
			<tr>
				<td><?= $product['article'] ?> </td>
				<td class="product-in-table">
					<?php if(!empty($product['m_photo'])){ ?>
					<img class="img-responsive" src="<?= $product['m_photo']?>" alt="">
					<?php } ?>
					<div class="product-it-in">
						<h3><?= $product['name']?></h3>
						<?php if(!empty($product['additional']['Розмір'])) { ?>
						<span><?=$this->text('Розмір')?>: <?= $product['additional']['Розмір'] ?></span>
						<?php } ?>
					</div>
				</td>
				<td><?= $product['price']?> грн</td>
				<td><?= $product['quantity']?></td>
				<td class="shop-red"><?= $product['price'] * $product['quantity']?> грн</td>
			</tr>
		<?php } } ?>
		</tbody>
	</table>
</div>


<?php if($_SESSION['option']->useShipping && isset($_SESSION['cart']->shipping)) { ?>

	<h2>Доставка</h2>
	<div class="table-responsive">
		<table class="table table-striped">
			<tbody>
				<td><?=$_SESSION['cart']->shipping['method-info']->name?></td>
				<td><?=$_SESSION['cart']->shipping['shippingAddress']?></td>
				<td><?=$_SESSION['cart']->shipping['shippingReceiver']?></td>
				<td><?=$_SESSION['cart']->shipping['shippingPhone']?></td>
			</tbody>
		</table>
	</div>

<?php } ?>

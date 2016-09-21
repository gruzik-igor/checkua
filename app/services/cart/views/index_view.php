<link href="<?=SITE_URL?>style/css/custom-wizard-steps.css" rel="stylesheet" type="text/css"/>

<div class="container">
	<div class="shopping-cart" action="#">
		<div class="header-tags">
			<div>
				<h2>Корзина</h2>
				<p>Перегляд і редагування</p>
				<i class="rounded-x fa fa-check"></i>
			</div>
		</div>
		<section>
			<div class="alert alert-danger alert-dismissible fade" role="alert">
				<strong>Помилка!</strong> Максимальна кількість доступних товарів <span id="maxQuantity"></span>
			</div>
			<div class="table-responsive">
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
						<tr id="mainCartProduct-<?= $product['productId'].'-'.$product['invoiceId'].'-'.$product['storageId']?>">
							<td><?= $product['article'] ?></td>
							<td class="product-in-table">
								<?php if(!empty($product['s_photo'])){ ?>
								<img class="img-responsive" src="<?= $product['s_photo']?>" alt="">
								<?php } ?>
								<div class="product-it-in">
									<h3><?= $product['name']?></h3>
								</div>
							</td>
							<td id="productPrice-<?= $product['productId'].'-'.$product['invoiceId'].'-'.$product['storageId']?>">$<?= $product['price']?></td>
							<td>
								<button type="button" class="quantity-button" onclick="cart.update(<?= $product['productId'].','.$product['invoiceId'].','.$product['storageId']?>, event)" value="-">-</button>
								<input type="text" min="1" class="quantity-field" id="productQuantity-<?= $product['productId'].'-'.$product['invoiceId'].'-'.$product['storageId']?>" onkeyup="cart.update(<?= $product['productId'].','.$product['invoiceId'].','.$product['storageId']?>, event)" value="<?= $product['quantity']?>">
								<button type="button" class="quantity-button" onclick="cart.update(<?= $product['productId'].','.$product['invoiceId'].','.$product['storageId']?>, event)" value="+">+</button>
							</td>
							<td class="shop-red" id="productTotalPrice-<?= $product['productId'].'-'.$product['invoiceId'].'-'.$product['storageId']?>">$<?= $product['price'] * $product['quantity']?></td>
							<td>
								<button type="button" class="close" onclick="cart.remove(<?= $product['productId'].','.$product['invoiceId'].','.$product['storageId'] ?>)" ><span>×</span><span class="sr-only">Close</span></button>
							</td>
						</tr>
					<?php } } ?>
						<tr id="mainCartEmpty" <?= (!isset($_SESSION['cart']) || empty($_SESSION['cart']->products)) ? '' : 'hidden' ?> >
							<td colspan="5"><h2>Корзина Пуста</h2></td>
						</tr>
					</tbody>
				</table>
			</div>
		</section>

		<?php if(!$this->userIs()) {?>
		<div class="header-tags">
			<div>
				<h2>Покупець</h2>
				<p>Вхід / Реєстрація</p>
				<i class="rounded-x fa fa-home"></i>
			</div>
		</div>
		<section class="billing-info">
			<div class="row">
				<div class="col-md-6 md-margin-bottom-40">
					<h2 class="title-type text-center">Для постійних клієнтів</h2>

					<div class="billing-info-inputs checkbox-list">
						<div class="row"  id="checkUser">
							<div class="form-horizontal">
								<div class="col-md-12 text-center" id="clientError">

								</div>
								<div class="form-group required" style="margin-bottom: 0">
				                    <label class="col-md-2 control-label" for="email">E-mail</label>
				                    <div class="col-md-10">
				                        <input class="form-control" name="email" id="clientEmail" type="email" value="" required="">
				                    </div>
				                </div>
				                <h4 class="text-center">або</h4>
				                <div class="form-group required">
				                    <label class="col-md-2 control-label" for="phone">Телефон</label>
				                    <div class="col-md-10">
				                        <input class="form-control" name="phone" id="clientPhone" type="text" value="" required="">
				                    </div>
				                </div>
				                <div class="form-group required" id="clientPassword" >
				                    <label class="col-md-2 control-label" for="password">Пароль</label>
				                    <div class="col-md-10">
				                        <input class="form-control" name="password" type="password">
				                    </div>
				                </div>
				                <div class="form-group required">
				                    <div class="col-md-12 text-center">
				                        <input type="submit" class="btn btn-primary" data-loading-text="Увійти..." id="clientEntry" value="Увійти">
				                    </div>
				                </div>
							</div>
						</div>
					</div>
				</div>

				<div class="col-md-6">
					<h2 class="title-type text-center">Перша покупка</h2>
					<div class="billing-info-inputs checkbox-list">
						<div class="row">
							<div class="form-horizontal">
								<div class="col-md-12 text-center" id="newClientError"></div>
				                <div class="form-group required">
				                    <label class="col-md-2 control-label" for="name">Ім'я</label>
				                    <div class="col-md-10">
				                        <input class="form-control" name="name" id="newClientName" type="text" value="" required="">
				                    </div>
				                </div>
				                <div class="form-group required" style="margin-bottom: 0">
				                    <label class="col-md-2 control-label" for="email">E-mail</label>
				                    <div class="col-md-10">
				                        <input class="form-control" name="email" id="newClientEmail"  type="email" value="" required="">
				                    </div>
				                </div>
				                <h4 class="text-center">або</h4>
				                <div class="form-group required">
				                    <label class="col-md-2 control-label" for="phone">Телефон</label>
				                    <div class="col-md-10">
				                        <input class="form-control" name="phone" type="text" id="newClientPhone" value="" required="">
				                    </div>
				                </div>
				                <div class="form-group required">
				                	<div class="text-left col-md-11 col-md-offset-1">
				                		<div class="radio">
					                		<label>
					                			<input type="radio" name="passwordOption" onclick="showPassword('show')" id="myPassword" value="1" checked>Задати свій пароль<br>
					                		</label>
				                		</div>
				                		<div class="radio">
					                		<label>
					                			<input type="radio" name="passwordOption"  onclick="showPassword('hide')" value="2" >Згенерувати пароль і відіслати на e-mail<br>
					                		</label>
				                		</div>
				                		<?php if($_SESSION['option']->usePassword == 1) {?>
				                		<div class="radio">
					                		<label>
					                			<input type="radio" name="passwordOption" onclick="showPassword('hide')" value="3" >Без паролю (Вашими даними зможуть користуватись інші)
					                		</label>
				                		</div>
				                		<?php } ?>
				                	</div>
				                </div>
			                	<div class="form-group">
									<div id="password">
										<label class="col-md-2 control-label" for="password">Пароль</label>
					                    <div class="col-md-10">
					                        <input class="form-control" name="password" type="password"  id="myPasswordValue" value="">
					                    </div>
									</div>
			                	</div>
			                	<div class="form-group required">
				                    <div class="col-md-12 text-center">
				                        <input type="submit" class="btn btn-primary" id="newClientRegistration" value="Зареєструватись">
				                    </div>
				                </div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</section>
		<?php } ?>

		<?php if($_SESSION['option']->useShipping) { ?>
			<div class="header-tags">
				<div>
					<h2>Доставка</h2>
					<p>Адреса доставки</p>
					<i class="rounded-x fa fa-car"></i>
				</div>
			</div>
			<section data-mode="async" data-url="<?= SITE_URL?>cart/loadShipping"> </section>
		<?php } ?>

		<div class="header-tags">
			<div>
				<h2>Накладна</h2>
				<p>Підтвердження замовлення</p>
				<i class="rounded-x fa fa-file"></i>
			</div>
		</div>
		<section data-mode="async" data-url="<?= SITE_URL?>cart/loadInvoice"> </section>

		<?php if($_SESSION['option']->usePayments) { ?>
			<div class="header-tags">
				<div>
					<h2>Оплата</h2>
					<p>Оберіть платіжний механізм</p>
					<i class="rounded-x fa fa-credit-card"></i>
				</div>
			</div>
			<section data-mode="async" data-url="<?= SITE_URL?>cart/loadPayments"> </section>
		<?php } ?>

		<div class="coupon-code">
			<div class="row">
				<div class="col-sm-3 col-sm-offset-9">
					<ul class="list-inline total-result">
						<li class="divider"></li>
						<li class="total-price">
							<h4>Разом:</h4>
							<div class="total-result-in">
								<span id="productsSubTotalPrice">$<?= isset($_SESSION['cart']->subTotal) ? $_SESSION['cart']->subTotal : '0'?></span> <br>
								<span id="productsSubTotalPriceUAH"><?= isset($_SESSION['cart']->subTotal) ? round($_SESSION['cart']->subTotal * $currency_USD, 2) .' грн': '0'?></span>
							</div>
						</li>
					</ul>
				</div>
			</div>
		</div>
	</div>
</div>

<?php
	$_SESSION['alias']->js_load[] = 'assets/jquery-steps/jquery.steps.js';
	$_SESSION['alias']->js_load[] = 'js/shopping-cart.js';
	if(!$this->userIs() && ($_SESSION['option']->useShipping || $_SESSION['option']->usePayments)) {
?>
<style>
	.wizard > .steps > ul > li {
	    width: 24% !important;
	    margin-left: 10px !important;
	}
	.wizard > .steps > ul > li:first-child {
	    margin-left: 0 !important;
	}
</style>
<?php } ?>
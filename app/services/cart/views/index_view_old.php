<link href="<?=SITE_URL?>assets/jquery-ui/themes/base/minified/jquery-ui.min.css" rel="stylesheet" type="text/css"/>
<link href="<?=SERVER_URL.'css/'.$_SESSION['alias']->alias?>/custom-wizard-steps.css" rel="stylesheet" type="text/css"/>

<div class="container content">
	<div class="shopping-cart" action="#">
		<div class="header-tags">
			<div>
				<h2><?=$this->text('Корзина')?></h2>
				<p><?=$this->text('Перегляд і редагування')?></p>
				<i class="rounded-x fa fa-check"></i>
			</div>
		</div>
		<section>
			<div class="alert alert-danger alert-dismissible fade hidden" role="alert">
				<strong>Помилка!</strong> Максимальна кількість доступних товарів <span id="maxQuantity"></span>
			</div>
			<div class="table-responsive">
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
						<tr id="mainCartProduct-<?= $product['productId'].'-'.$product['invoiceId'].'-'.$product['storageId']?>">
							<td><?= $product['article'] ?></td>
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
							<td id="productPrice-<?= $product['productId'].'-'.$product['invoiceId'].'-'.$product['storageId']?>"><?= $product['price']?> грн</td>
							<td>
								<button type="button" class="quantity-button" onclick="cart.update(<?= $product['productId'].','.$product['invoiceId'].','.$product['storageId']?>, event)" value="-">-</button>
								<input type="text" min="1" class="quantity-field" id="productQuantity-<?= $product['productId'].'-'.$product['invoiceId'].'-'.$product['storageId']?>" onkeyup="cart.update(<?= $product['productId'].','.$product['invoiceId'].','.$product['storageId']?>, event)" value="<?= $product['quantity']?>">
								<button type="button" class="quantity-button" onclick="cart.update(<?= $product['productId'].','.$product['invoiceId'].','.$product['storageId']?>, event)" value="+">+</button>
							</td>
							<td class="shop-red" id="productTotalPrice-<?= $product['productId'].'-'.$product['invoiceId'].'-'.$product['storageId']?>"><?= $product['price'] * $product['quantity']?> грн</td>
							<td>
								<button type="button" class="close" onclick="cart.remove(<?= $product['productId'].','.$product['invoiceId'].','.$product['storageId'] ?>)" ><span>×</span><span class="sr-only">Close</span></button>
							</td>
						</tr>
					<?php } } ?>
						<tr id="mainCartEmpty" <?= (!isset($_SESSION['cart']) || empty($_SESSION['cart']->products)) ? '' : 'hidden' ?> >
							<td colspan="5"><h2><?=$this->text('Корзина Пуста')?></h2></td>
						</tr>
					</tbody>
				</table>
			</div>
		</section>

		<?php if(!$this->userIs()) {?>
		<div class="header-tags">
			<div>
				<h2><?=$this->text('Покупець')?></h2>
				<p><?=$this->text('Вхід / Реєстрація')?></p>
				<i class="rounded-x fa fa-home"></i>
			</div>
		</div>
		<section class="billing-info">
			<div class="row">
				<div class="col-md-6 md-margin-bottom-40">
					<h2 class="title-type text-center"><?=$this->text('Для постійних клієнтів')?></h2>

					<div class="billing-info-inputs checkbox-list">
						<div class="row"  id="checkUser">
							<div class="form-horizontal">
								<div class="col-md-12 text-center" id="clientError">
								</div>
								<div class="col-md-6 col-md-offset-3 margin-bottom-10">
									<button class="facebookSignUp" onclick="facebookSignUp()">Facebook <i class="fa fa-facebook-square fa-lg pull-right" aria-hidden="true"></i></button>
								</div>
								<div class="col-md-12 form-group required">
				                    <label class="col-md-2 control-label" for="email">E-mail/телефон</label>
				                    <div class="col-md-10">
				                        <input class="form-control" name="email" id="clientEmailorPhone" type="email" value="" required="">
				                    </div>
				                </div>
				                <div class="form-group required" id="clientPassword" style="margin-bottom: 0">
				                    <label class="col-md-2 control-label" for="password">Пароль</label>
				                    <div class="col-md-10">
				                        <input class="form-control" name="password" type="password">
				                    </div>
				                </div>
				                <span class="pull-right"><a href="<?= SITE_URL?>reset"><?=$this->text('Забули пароль')?>?</a></span>
				                <div class="form-group required">
				                    <div class="col-md-12 text-center">
				                        <input type="submit" class="btn btn-shop" id="clientEntry" value="<?=$this->text('Увійти')?>">
				                    </div>
				                </div>
							</div>
						</div>
					</div>
				</div>

				<div class="col-md-6">
					<h2 class="title-type text-center"><?=$this->text('Перша покупка')?></h2>
					<div class="billing-info-inputs checkbox-list">
						<div class="row">
							<div class="form-horizontal">
								<div class="col-md-12 text-center" id="newClientError"></div>
				                <div class="form-group required">
				                    <label class="col-md-2 control-label" for="name"><?=$this->text("Ім'я")?></label>
				                    <div class="col-md-10">
				                        <input class="form-control" name="name" id="newClientName" type="text" value="" required="">
				                    </div>
				                </div>
				                <div class="form-group required">
				                    <label class="col-md-2 control-label" for="email">E-mail</label>
				                    <div class="col-md-10">
				                        <input class="form-control" name="email" id="newClientEmail"  type="email" value="" required="">
				                    </div>
				                </div>
				                <div class="form-group required">
				                	<div class="text-left col-md-11 col-md-offset-1">
				                		<div class="radio">
					                		<label>
					                			<input type="radio" name="passwordOption" onclick="showPassword('show')" id="myPassword" value="1" checked><?=$this->text('Задати свій пароль')?><br>
					                		</label>
				                		</div>
				                		<div class="radio">
					                		<label>
					                			<input type="radio" name="passwordOption"  onclick="showPassword('hide')" value="2" ><?=$this->text('Згенерувати пароль і відіслати на e-mail')?><br>
					                		</label>
				                		</div>
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
				                        <input type="submit" class="btn btn-shop" id="newClientRegistration" value="<?=$this->text('Зареєструватись')?>">
				                    </div>
				                </div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div id="divLoading"></div>
		</section>
		<?php } ?>

		<?php if($_SESSION['option']->useShipping) { ?>
			<div class="header-tags">
				<div>
					<h2><?=$this->text('Доставка')?></h2>
					<p><?=$this->text('Адреса доставки')?></p>
					<i class="rounded-x fa fa-car"></i>
				</div>
			</div>
			<section data-mode="async" data-url="<?= SITE_URL?>cart/loadShipping"> </section>
		<?php } ?>

		<div class="header-tags">
			<div>
				<h2><?=$this->text('Накладна')?></h2>
				<p><?=$this->text('Підтвердження замовлення')?></p>
				<i class="rounded-x fa fa-file"></i>
			</div>
		</div>
		<section data-mode="async" data-url="<?= SITE_URL?>cart/loadInvoice"> </section>

		<?php if($_SESSION['option']->usePayments) { ?>
			<div class="header-tags">
				<div>
					<h2><?=$this->text('Оплата')?></h2>
					<p><?=$this->text('Оберіть платіжний механізм')?></p>
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
							<h4><?=$this->text('До оплати')?>:</h4>
							<div class="total-result-in">
								<span id="productsSubTotalPrice"><?= isset($_SESSION['cart']->subTotal) ? $_SESSION['cart']->subTotal. ' грн' : '0'?></span>
							</div>
						</li>
					</ul>
				</div>
			</div>
		</div>
	</div>
</div>

<script>
	cartLabels = {
		cancel: "<?=$this->text('Відмінити')?>",
        current: "<?=$this->text('Поточний крок:')?>",
        finish: "<?=$this->text('Завершити')?>",
        next: "<?=$this->text('Наступний крок')?>",
        previous: "<?=$this->text('Попередній крок')?>"
	};

	window.fbAsyncInit = function() {
		<?php $this->load->library('facebook'); ?>
	    FB.init({
	      appId      : '<?=$this->facebook->getAppId()?>',
	      cookie     : true,
	      xfbml      : true,
	      version    : 'v2.6'
	    });
	};

	(function(d, s, id){
	    var js, fjs = d.getElementsByTagName(s)[0];
	    if (d.getElementById(id)) {return;}
	    js = d.createElement(s); js.id = id;
	    js.src = "//connect.facebook.net/en_US/sdk.js";
	    fjs.parentNode.insertBefore(js, fjs);
	}(document, 'script', 'facebook-jssdk'));
</script>

<?php
	$_SESSION['alias']->js_load[] = 'js/'.$_SESSION['alias']->alias.'/site.js';
	$_SESSION['alias']->js_load[] = 'js/'.$_SESSION['alias']->alias.'/jquery.steps.js';
	$_SESSION['alias']->js_load[] = 'assets/jquery-ui/ui/minified/jquery-ui.min.js';
	$_SESSION['alias']->js_load[] = 'js/'.$_SESSION['alias']->alias.'/shopping-cart.js';
	$width = !$this->userIs() ? 19.25 : 24.3;
?>


<style>
	.wizard > .steps > ul > li {
	    width:  <?= $width?>%;
	    margin-left: 10px;
	}
	.wizard > .steps > ul > li:first-child {
	    margin-left: 0;
	}
</style>
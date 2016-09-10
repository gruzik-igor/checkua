<link href="<?=SITE_URL?>style/css/custom-wizard-steps.css" rel="stylesheet" type="text/css"/>	

<div class="container">
	<div class="shopping-cart" action="#">
		<div class="header-tags">
			<div class="overflow-h">
				<h2>Корзина</h2>
				<p>Перегляд і редагування</p>
				<i class="rounded-x fa fa-check"></i>
			</div>
		</div>
		<section>
			<div class="table-responsive">
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
						<tr id="mainCartProduct-<?= $product['productId']?>">
							<td class="product-in-table">
								<?php if(!empty($product['s_photo'])){ ?>
								<img class="img-responsive" src="<?= $product['s_photo']?>" alt="">
								<?php } ?>
								<div class="product-it-in">
									<h3><?= $product['name']?></h3>
								</div>
							</td>
							<td id="productPrice-<?= $product['productId']?>"><?= $product['price']?> грн.</td>
							<td>
								<button type="button" class="quantity-button" onclick="cart.update(<?= $product['productId']?>)" value="-">-</button>
								<input type="text" min="1" class="quantity-field" id="productQuantity-<?= $product['productId']?>" onkeyup="cart.update(<?= $product['productId']?>)" value="<?= $product['quantity']?>">
								<button type="button" class="quantity-button" onclick="cart.update(<?= $product['productId']?>)" value="+">+</button>
							</td>
							<td class="shop-red" id="productTotalPrice-<?= $product['productId']?>"><?= $product['price'] * $product['quantity']?> грн.</td>
							<td>
								<button type="button" class="close" onclick="cart.remove(<?= $product['productId'] ?>)" ><span>×</span><span class="sr-only">Close</span></button>
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
			<div class="overflow-h">
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
				                <div class="form-group required" id="clientPassword" hidden>
				                    <label class="col-md-2 control-label" for="password">Пароль</label>
				                    <div class="col-md-10">
				                        <input class="form-control" name="password" type="password" disabled>
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

		<div class="header-tags">
			<div class="overflow-h">
				<h2>Накладна</h2>
				<p>Перегляд</p>
				<i class="rounded-x fa fa-file"></i>
			</div>
		</div>
		<section data-mode="async" data-url="<?= SITE_URL?>cart/loadInvoice">
			
		</section>
	
		<?php if($_SESSION['option']->usePayments) { ?>
		<div class="header-tags">
			<div class="overflow-h">
				<h2>Payment</h2>
				<p>Select Payment method</p>
				<i class="rounded-x fa fa-credit-card"></i>
			</div>
		</div>
		<section>
			<div class="row">
				<div class="col-md-6 md-margin-bottom-50">
					<h2 class="title-type">Choose a payment method</h2>
					<!-- Accordion -->
					<div class="accordion-v2">
						<div class="panel-group" id="accordion">
							<div class="panel panel-default">
								<div class="panel-heading">
									<h4 class="panel-title">
										<a data-toggle="collapse" data-parent="#accordion" href="#collapseOne">
											<i class="fa fa-credit-card"></i>
											Credit or Debit Card
										</a>
									</h4>
								</div>
								<div id="collapseOne" class="panel-collapse collapse in">
									<div class="panel-body cus-form-horizontal">
										<div class="form-group">
											<label class="col-sm-4 no-col-space control-label">Cardholder Name</label>
											<div class="col-sm-8">
												<input type="text" class="form-control required" name="cardholder" placeholder="">
											</div>
										</div>
										<div class="form-group">
											<label class="col-sm-4 no-col-space control-label">Card Number</label>
											<div class="col-sm-8">
												<input type="text" class="form-control required" name="cardnumber" placeholder="">
											</div>
										</div>
										<div class="form-group">
											<label class="col-sm-4 no-col-space control-label">Payment Types</label>
											<div class="col-sm-8">
												<ul class="list-inline payment-type">
													<li><i class="fa fa-cc-paypal"></i></li>
													<li><i class="fa fa-cc-visa"></i></li>
													<li><i class="fa fa-cc-mastercard"></i></li>
													<li><i class="fa fa-cc-discover"></i></li>
												</ul>
											</div>
										</div>
										<div class="form-group">
											<label class="col-sm-4">Expiration Date</label>
											<div class="col-sm-8 input-small-field">
												<input type="text" name="mm" placeholder="MM" class="form-control required sm-margin-bottom-20">
												<span class="slash">/</span>
												<input type="text" name="yy" placeholder="YY" class="form-control required">
											</div>
										</div>
										<div class="form-group">
											<label class="col-sm-4 no-col-space control-label">CSC</label>
											<div class="col-sm-8 input-small-field">
												<input type="text" name="number" placeholder="" class="form-control required">
												<a href="#">What's this?</a>
											</div>
										</div>
									</div>
								</div>
							</div>
							<div class="panel panel-default">
								<div class="panel-heading">
									<h4 class="panel-title">
										<a data-toggle="collapse" data-parent="#accordion" href="#collapseTwo">
											<i class="fa fa-paypal"></i>
											Pay with PayPal
										</a>
									</h4>
								</div>
								<div id="collapseTwo" class="panel-collapse collapse">
									<div class="content margin-left-10">
										<a href="#"><img src="https://www.paypalobjects.com/webstatic/en_US/i/buttons/PP_logo_h_150x38.png" alt="PayPal"></a>
									</div>
								</div>
							</div>
						</div>
					</div>
					<!-- End Accordion -->
				</div>

				<div class="col-md-6">
					<h2 class="title-type">Frequently asked questions</h2>
					<!-- Accordion -->
					<div class="accordion-v2 plus-toggle">
						<div class="panel-group" id="accordion-v2">
							<div class="panel panel-default">
								<div class="panel-heading">
									<h4 class="panel-title">
										<a data-toggle="collapse" data-parent="#accordion-v2" href="#collapseOne-v2">
											What payments methods can I use?
										</a>
									</h4>
								</div>
								<div id="collapseOne-v2" class="panel-collapse collapse in">
									<div class="panel-body">
										Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nam hendrerit, felis vel tincidunt sodales, urna metus rutrum leo, sit amet finibus velit ante nec lacus. Cras erat nunc, pulvinar nec leo at, rhoncus elementum orci. Nullam ut sapien ultricies, gravida ante ut, ultrices nunc.
									</div>
								</div>
							</div>
							<div class="panel panel-default">
								<div class="panel-heading">
									<h4 class="panel-title">
										<a data-toggle="collapse" class="collapsed" data-parent="#accordion-v2" href="#collapseTwo-v2">
											Can I use gift card to pay for my purchase?
										</a>
									</h4>
								</div>
								<div id="collapseTwo-v2" class="panel-collapse collapse">
									<div class="panel-body">
										Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nam hendrerit, felis vel tincidunt sodales, urna metus rutrum leo, sit amet finibus velit ante nec lacus. Cras erat nunc, pulvinar nec leo at, rhoncus elementum orci. Nullam ut sapien ultricies, gravida ante ut, ultrices nunc.
									</div>
								</div>
							</div>
							<div class="panel panel-default">
								<div class="panel-heading">
									<h4 class="panel-title">
										<a data-toggle="collapse" class="collapsed" data-parent="#accordion-v2" href="#collapseThree-v2">
											Will I be charged when I place my order?
										</a>
									</h4>
								</div>
								<div id="collapseThree-v2" class="panel-collapse collapse">
									<div class="panel-body">
										Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nam hendrerit, felis vel tincidunt sodales, urna metus rutrum leo, sit amet finibus velit ante nec lacus. Cras erat nunc, pulvinar nec leo at, rhoncus elementum orci. Nullam ut sapien ultricies, gravida ante ut, ultrices nunc.
									</div>
								</div>
							</div>
							<div class="panel panel-default">
								<div class="panel-heading">
									<h4 class="panel-title">
										<a data-toggle="collapse" class="collapsed" data-parent="#accordion-v2" href="#collapseFour-v2">
											How long will it take to get my order?
										</a>
									</h4>
								</div>
								<div id="collapseFour-v2" class="panel-collapse collapse">
									<div class="panel-body">
										Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nam hendrerit, felis vel tincidunt sodales, urna metus rutrum leo, sit amet finibus velit ante nec lacus. Cras erat nunc, pulvinar nec leo at, rhoncus elementum orci. Nullam ut sapien ultricies, gravida ante ut, ultrices nunc.
									</div>
								</div>
							</div>
						</div>
					</div>
					<!-- End Accordion -->
				</div>
			</div>
		</section>
		<?php } ?>

		<div class="coupon-code">
			<div class="row">
				<div class="col-sm-3 col-sm-offset-9">
					<ul class="list-inline total-result">
						<li class="divider"></li>
						<li class="total-price">
							<h4>Разом:</h4>
							<div class="total-result-in">
								<span id="productsSubTotalPrice"><?= isset($_SESSION['cart']->subTotal) ? $_SESSION['cart']->subTotal : '0'?> грн.</span>
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
?>

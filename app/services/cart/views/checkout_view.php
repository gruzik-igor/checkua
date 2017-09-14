<link rel="stylesheet" type="text/css" href="<?=SERVER_URL.'style/'.$_SESSION['alias']->alias.'/checkout.css'?>">
<link href="<?=SITE_URL?>assets/jquery-ui/themes/base/minified/jquery-ui.min.css" rel="stylesheet" type="text/css"/>
<?php $_SESSION['alias']->js_load[] = 'js/'.$_SESSION['alias']->alias.'/checkout.js'; 
$_SESSION['alias']->js_load[] = 'assets/jquery-ui/ui/minified/jquery-ui.min.js'; ?>

<section class="section" id="cart-checkout">
	<div class="container">
		<div class="row">
			<h1 class="col-md-12"><?=$_SESSION['alias']->name?></h1>
		</div>
		<div class="row">

			<div class="alert alert-danger alert-dismissible fade hidden" role="alert">
				<strong>Помилка!</strong> Максимальна кількість доступних товарів <span id="maxQuantity"></span>
			</div>

			<?php if(!$this->userIs()) { ?>
			<div class="col-md-6">
				<div class="box">
					<strong><?=$this->text('Вже купували?')?></strong> <a href="#" class="effect" data-slide-toggle=".checkout-login-form"><?=$this->text('Клікніть щоб увійти')?></a> <?=$this->text('- це заощадить Ваш час')?>
					<p><?=$this->text('Якщо Ви новий покупець, перейдіть до розділів "Доставка та оплата"')?></p>
				</div>
			</div>

			<div class="clearfix"></div>
			<?php /*
			<div class="col-sm-6">
				<div class="alert">
					<strong>Have a coupon?</strong> <a href="#" class="effect" data-slide-toggle=".checkout-coupon-form">Click here to enter your code</a>
				</div>
			</div>
			*/ ?>
			<div class="col-sm-6">
				<div class="checkout-login-form box" <?=(empty($_SESSION['notify'])) ? 'style="display: none;"' : ''?>>
					<h2><?=$this->text('Увійти')?></h2>
					<?php if(!empty($_SESSION['notify']->error)) { ?>
					   <div class="alert alert-danger fade in">
					        <span class="close" data-dismiss="alert">×</span>
					        <h4><?=(isset($_SESSION['notify']->title)) ? $_SESSION['notify']->title : 'Помилка!'?></h4>
					        <p><?=$_SESSION['notify']->error?></p>
					    </div>
					<?php } ?>
					<form action="<?=SITE_URL.$_SESSION['alias']->alias?>/login" method="POST" class="login-form inputs-border inputs-bg">
						<div class="form-group">
							<label for="email"><?=$this->text('email або телефон')?>*</label>
							<input type="text" name="email" id="email" class="form-control" placeholder="<?=$this->text('email або телефон')?>*" value="<?=$this->data->re_post('email')?>">
						</div>
						<div class="form-group">
							<label for="password"><?=$this->text('Пароль')?>*</label>
							<input type="password" name="password" id="password" class="form-control" placeholder="<?=$this->text('Пароль')?>*">
						</div>
						<div class="form-group">
							<a href="<?=SITE_URL?>reset" class="effect pull-right"><?=$this->text('Забув пароль?')?></a>
							<button type="submit" class="btn btn-warning"><?=$this->text('Увійти')?></button>
						</div>
						<p><?=$this->text('Швидкий вхід:')?></p>
						<div class="form-group">
							<button class="facebookSignUp" onclick="facebookSignUp()">Facebook <i class="fa fa-facebook-square fa-lg pull-right" aria-hidden="true"></i></button>
						</div>
					</form>
				</div>
			</div>
			<?php /*
			<div class="col-sm-6">
				<div class="checkout-coupon-form box" style="display: none;">
					<form action="<?=SITE_URL.$_SESSION['alias']->alias?>/coupon" method="POST" class="coupon-form inputs-border inputs-bg">
						<div class="form-group">
							<label for="coupon_code">Coupon code</label>
							<input type="text" id="coupon_code" class="form-control" placeholder="Coupon code">
						</div>
						<div class="form-group text-right">
							<button type="button" class="btn btn-default">Apply Coupon</button>
						</div>
					</form>
				</div>
			</div>
			*/ }
			if(!empty($_SESSION['notify']->success)) { ?>
			    <div class="alert alert-success fade in">
			        <span class="close" data-dismiss="alert">×</span>
			        <i class="fa fa-check fa-2x pull-left"></i>
			        <h4><?=$_SESSION['notify']->success?></h4>
			    </div>
			<?php } unset($_SESSION['notify']); ?>
			<div class="clearfix"></div>

			<form action="<?=SITE_URL.$_SESSION['alias']->alias?>/confirm" method="POST" class="checkout-form inputs-border inputs-bg">
				<div class="col-md-6">
					<div class="billing-field box">
						<h3 class="title"><?=$this->text('Доставка')?></h3>

						<?php 
						$cooperation_where['alias1'] = $_SESSION['alias']->id;
						$cooperation_where['type'] = 'delivery';
						$cooperation = $this->db->getAllDataByFieldInArray('wl_aliases_cooperation', $cooperation_where);
				        if($cooperation)
				        {
				            foreach ($cooperation as $storage) {
				                $this->load->function_in_alias($storage->alias2, '__get_Shipping_to_cart');
				            }
				        }
				        else { ?>
					        <div class="form-group">
								<input type="text" class="form-control" placeholder="Name" required>
							</div>

							<div class="row">
								<div class="form-group col-sm-6">
									<div class="required">
										<input type="email" class="form-control" placeholder="Email Address" required="">
									</div>
								</div>
								<div class="form-group col-sm-6">
									<div class="required">
										<input type="tel" class="form-control" placeholder="Phone" required="">
									</div>
								</div>
							</div>
						<?php } ?>
						
						<h3 class="title"><?=$this->text('Побажання до замовлення')?></h3>
						<div class="form-group">
							<textarea class="form-control" placeholder="<?=$this->text('Побажання до замовлення, наприклад щодо доставки')?>" rows="5"></textarea>
						</div>
					</div>

					<div class="row">
				        <div id="map"></div>
				    </div>
				</div>

				<div class="col-md-6">
					<div class="review-order">
						<div class="box">
							<h3 class="title"><?=$this->text('Ваше замовлення')?></h3>
							<div class="table-responsive">
								<table class="table cart-table review-order-table">
								    <thead>
								        <tr>
								            <th class="product-name" width="50%"><?=$this->text('Товар')?></th>
								            <th class="product-total"><?=$this->text('Сума')?></th>
								        </tr>
								    </thead>
								    <tbody>
								    	<?php foreach($products as $product) { ?>
									        <tr class="item">
									            <td class="product-name">
									                <a href="<?=SITE_URL.$product->info->link?>"><?=$product->info->name?></a> <strong class="product-quantity">× <?= $product->quantity?></strong> 
									                <?php if(!empty($product->product_options))
													{
														$product->product_options = unserialize($product->product_options); ?>
														<table class="variation">
										                    <tbody>
										                    <?php foreach ($product->product_options as $key => $value) { ?>
										                    <tr>
									                            <th class="variation-size"><?=$key?>:</th>
									                            <td class="variation-size">
									                                <p><?=$value?></p>
									                            </td>
									                        </tr>
														<?php } ?>
															</tbody>
									                	</table>
													<?php } ?>
									            </td>
									            <td class="product-total">
									                <span class="amount" title="<?=$product->priceFormat ?> × <?= $product->quantity?>"><?=$this->cart_model->priceFormat($product->price * $product->quantity) ?></span>
									            </td>
									        </tr>
								        <?php } ?>
								    </tbody>
								    <tfoot>
								        <tr class="cart-subtotal">
								            <th><?=$this->text('Попередня сума')?></th>
								            <td><span class="amount"><?=$subTotal?></span></td>
								        </tr>
								        <tr class="shipping">
								            <th><?=$this->text('Доставка')?></th>
								            <td>
								                <p><?=$this->text('безкоштовно')?></p>
								            </td>
								        </tr>
								        <tr class="order-total">
								            <th><?=$this->text('До оплати')?></th>
								            <td>
									            <strong><span class="amount"><?=$subTotal?></span></strong>
								            </td>
								        </tr>
								    </tfoot>
								</table><!-- /.review-order-table -->
							</div>

							<h2><?=$this->text('Оплата')?></h2>
							<div id="payment" class="checkout-payment">
							    <ul class="payment-methods">
							        <li class="payment-method">
							            <input id="payment_method_cheque" type="radio" name="payment_method" value="0" checked="checked">
							            <label for="payment_method_cheque" class="radio" data-slide-toggle="#payment-cash" data-parent=".payment-methods"><?=$this->text('Готівкою при отриманні')?></label>

							            <div class="payment-box" id="payment-cash">
							                <p><?=$this->text('Оплата готівкою при доставці/отриманні товару.')?></p>
							            </div>
							        </li>
							        <?php
									$cooperation_where['alias1'] = $_SESSION['alias']->id;
									$cooperation_where['type'] = 'payment';
									$ntkd = array('alias' => '#c.alias2', 'content' => 0);
									if($_SESSION['language'])
										$ntkd['language'] = $_SESSION['language'];
									$cooperation = $this->db->select('wl_aliases_cooperation as c', 'alias2 as id', $cooperation_where)
															->join('wl_ntkd', 'name, list', $ntkd)
															->get('array');
							        if($cooperation)
							        {
							            foreach ($cooperation as $payment) { ?>
							            <li class="payment-method">
								            <input id="payment_method_cod" type="radio" name="payment_method" value="<?=$payment->id?>">
								            <label for="payment_method_cod" class="radio" data-slide-toggle="#payment-<?=$payment->id?>" data-parent=".payment-methods"><?=$payment->name?></label>

								            <div class="payment-box" id="payment-<?=$payment->id?>" style="display:none;">
								                <p><?=htmlspecialchars_decode($payment->list)?></p>
								            </div>
								        </li>
							            <?php }
							        }
									?>
							    </ul>
						    	<div class="text-right">
						    		<button type="submit" class="btn btn-buy"><?=$this->text('Оформити замовлення')?></button>
						    	</div>
							</div>
						</div>
					</div>
				</div>
			</form>
		</div>
	</div>
</section>
<div id="divLoading"></div>

<?php $this->load->library('facebook'); 
if($_SESSION['option']->facebook_initialise){ ?>
	<script>
		window.fbAsyncInit = function() {
			
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
<?php } ?>
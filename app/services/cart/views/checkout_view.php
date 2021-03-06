<script type="text/javascript" src="<?=SERVER_URL?>assets/jquery/jquery-1.9.1.min.js" ></script>
<link rel="stylesheet" type="text/css" href="<?=SERVER_URL.'style/'.$_SESSION['alias']->alias.'/checkout.css'?>">
<link href="<?=SERVER_URL?>assets/jquery-ui/themes/base/minified/jquery-ui.min.css" rel="stylesheet" type="text/css"/>
<?php $jss = array('assets/jquery-ui/ui/minified/jquery-ui.min.js', 'js/'.$_SESSION['alias']->alias.'/cities.js', 'js/'.$_SESSION['alias']->alias.'/checkout.js');
foreach ($jss as $js) {
	if(!in_array($js, $_SESSION['alias']->js_load))
		$_SESSION['alias']->js_load[] = $js;
} ?>

<div class="page-head content-top-margin">
	<div class="container">
		<div class="row">
			<h1 class="col-md-12"><?=$_SESSION['alias']->name?></h1>
		</div>
	</div>
</div>
<section class="section" id="cart-checkout">
	<div class="container">
		<?php if(!empty($_SESSION['notify-Cart'])) { ?>
			<div class="col-md-12">
			   <div class="alert alert-danger alert-dismissible fade in">
			        <span class="close" data-dismiss="alert">×</span>
			        <h4><?=(isset($_SESSION['notify-Cart']->title)) ? $_SESSION['notify-Cart']->title : $this->text('Помилка!')?></h4>
			        <?=$_SESSION['notify-Cart']->error?>
			    </div>
			</div>
		<?php unset($_SESSION['notify-Cart']); } ?>
		<div class="row">

			<?php if(!$this->userIs()) { ?>
			<div class="col-md-6">
				<div class="row">
					<div class="box boxLogo">
						<strong><?=$this->text('Вже купували?')?></strong> <a href="#" class="effect" data-slide-toggle=".checkout-login-form"><?=$this->text('Клікніть щоб увійти')?></a> <?=$this->text('- це заощадить Ваш час')?>
						<p><?=$this->text('Якщо Ви новий покупець, перейдіть до розділів "Доставка та оплата"')?></p>
					</div>
				</div>
			</div>
			<?php } if ($bonusCodes && $bonusCodes->showForm) { ?>
			<div class="col-sm-6">
				<div class="alert">
					<strong><?=$this->text('Have a coupon?')?></strong> <a href="#" class="effect" data-slide-toggle=".checkout-coupon-form"><?=$this->text('Click here to enter your code')?></a>
				</div>
			</div>
			<?php }
			else
				echo '<div class="clearfix"></div>';
			if(!$this->userIs()) { ?>
			<div class="col-sm-6">
				<div class="checkout-login-form box" <?=(empty($_SESSION['notify']->error)) ? 'style="display: none;"' : ''?>>
					<h2><?=$this->text('Увійти')?></h2>
					<?php if(!empty($_SESSION['notify']->error)) { ?>
					   <div class="alert alert-danger fade in">
					        <span class="close" data-dismiss="alert">×</span>
					        <h4><?=(isset($_SESSION['notify']->title)) ? $_SESSION['notify']->title : 'Помилка!'?></h4>
					        <p><?=$_SESSION['notify']->error?></p>
					    </div>
					<?php } ?>
					<form action="<?=SITE_URL.$_SESSION['alias']->alias?>/login" method="POST" class="login-form inputs-border inputs-bg">
						<p class="message"></p>
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
						<?php $this->load->library('facebook'); 
						if($_SESSION['option']->facebook_initialise){ ?>
							<p><?=$this->text('Швидкий вхід:')?></p>
							<div class="form-group">
								<button class="facebookSignUp" onclick="return facebookSignUp()">Facebook <i class="fab fa-facebook-f fa-lg pull-right" aria-hidden="true"></i></button>
							</div>
						<?php } ?>
					</form>
				</div>
			</div>
			<?php } if ($bonusCodes && $bonusCodes->showForm) { ?>
			<div class="col-sm-6">
				<div class="checkout-coupon-form box" style="display: none;">
					<form action="<?=SITE_URL.$_SESSION['alias']->alias?>/coupon" method="POST" class="coupon-form inputs-border inputs-bg">
						<div class="form-group">
							<label for="coupon_code"><?=$this->text('Coupon code')?></label>
							<input type="text" name="code" class="form-control" placeholder="<?=$this->text('Coupon code')?>" required>
						</div>
						<div class="form-group text-right">
							<button class="btn btn-default"><?=$this->text('Apply Coupon')?></button>
						</div>
					</form>
				</div>
			</div>
			<?php } if(!empty($_SESSION['notify']->success)) { ?>
				<div class="col-md-12">
				    <div class="alert alert-success fade in">
				        <span class="close" data-dismiss="alert">×</span>
				        <i class="fa fa-check fa-2x pull-left"></i>
				        <h4><?=$_SESSION['notify']->success?></h4>
				    </div>
				</div>
			<?php } unset($_SESSION['notify']); ?>
			<div class="clearfix"></div>

			<form action="<?=SITE_URL.$_SESSION['alias']->alias?>/confirm" method="POST" class="checkout-form inputs-border inputs-bg" onsubmit="divLoading.style.display='block'">
				<div class="col-md-6">
					<div class="billing-field">
						<?php if(!$this->userIs()) { ?>
							<div class="row">
								<h3 class="title mt0"><?=$this->text('Покупець')?></h3>
							</div>
							<div class="form-group">
				                <div class="required">
				                    <input type="email" name="email" value="<?=$this->data->re_post('email')?>" class="form-control" placeholder="Email" required>
				                </div>
				            </div>
							<div class="form-group">
						        <input type="text" name="name" value="<?=$this->data->re_post('name')?>" class="form-control" id="loginName" placeholder="<?=$this->text('Ім\'я Прізвище')?>" required>
						    </div>
					    <?php }
					    if($shippings)
					    	require_once '__shippings_subview.php';
					    ?>
						
						<div class="row">
							<h3 class="title"><?=$this->text('Побажання до замовлення')?></h3>
							<div class="form-group">
								<textarea name="comment" class="form-control" placeholder="<?=$this->text('Побажання до замовлення, наприклад щодо доставки')?>" rows="5"><?=$this->data->re_post('comment')?></textarea>
							</div>
						</div>
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
								    	<?php $discountAll = 0; foreach($products as $product) { ?>
									        <tr class="item">
									            <td class="product-name">
									            	<?php if($product->info->photo) { ?>
														<a href="<?=SITE_URL.$product->info->link?>">
															<img src="<?=IMG_PATH?><?=(isset($product->info->cart_photo)) ? $product->info->cart_photo : $product->info->photo ?>" alt="<?=$this->text('Фото'). ' '. $product->info->name ?>">
														</a>
													<?php } ?>
									                <a href="<?=SITE_URL.$product->info->link?>"><?=$product->info->name?></a> <strong class="product-quantity">× <?= $product->quantity?></strong> 
									                <?php $p = '';
													if(!empty($product->info->article)) {
														$p .= $this->text('Артикул', 0).": <strong>{$product->info->article}</strong>";
													} if(!empty($product->product_options))
														foreach ($product->product_options as $key => $value) {
															if(!empty($p))
																$p .= ', ';
															$p .= "{$key}: <strong>{$value}</strong>";
														}
													if(!empty($p))
														echo "<p>{$p}</p>";
													?>
									            </td>
									            <td class="product-total">
									            	<?php if(!empty($product->discount)) {
									            		$discountAll += $product->discount;
									            		echo '<span class="amount discount">'.$this->cart_model->priceFormat(($product->price * $product->quantity + $product->discount)).'</span><br>'; } ?>
									                <span class="amount" title="<?=$product->priceFormat ?> × <?= $product->quantity?>"><?=$this->cart_model->priceFormat($product->price * $product->quantity) ?></span>
									            </td>
									        </tr>
								        <?php } ?>
								    </tbody>
								    <tfoot>
								    	<?php if($discountAll || ($bonusCodes && !empty($bonusCodes->info)) || ($shippings && $shippings[0]->pay >= -1)) { ?>
								    		<tr class="cart-subtotal">
									            <th><?=$this->text('Сума')?></th>
									            <td><span class="amount"><?=$subTotal?></span></td>
									        </tr>
								    	<?php } if($discountAll) { ?>
								    		<tr class="cart-subtotal">
									            <th><?=$this->text('Економія')?></th>
									            <td><span class="amount"><?=$this->cart_model->priceFormat($discountAll)?></span></td>
									        </tr>
								    	<?php } if($bonusCodes && !empty($bonusCodes->info))
											foreach ($bonusCodes->info as $key => $discount) { ?>
								    		<tr class="cart-subtotal">
									            <th><?=$this->text('Бонус-код').': '.$key?></th>
									            <td><span class="amount"><?=$discount?></span></td>
									        </tr>
								        <?php } if($shippings && $shippings[0]->pay >= -1) { ?>
								        	<tr class="shipping">
									            <th><?=$this->text('Доставка')?></th>
									            <td>
									                <p><?=($shippings[0]->pay == -1)?$this->text('безкоштовно'):$this->cart_model->priceFormat($shippings[0]->price)?></p>
									            </td>
									        </tr>
								        <?php } ?>
								        <tr class="order-total">
								            <th><?=$this->text('До оплати')?></th>
								            <td>
									            <strong><span class="amount"><?=$total?></span></strong>
								            </td>
								        </tr>
								    </tfoot>
								</table><!-- /.review-order-table -->
							</div>

							<?php if($payments) { ?>
								<h2><?=$this->text('Оплата')?></h2>
								<div id="payment" class="checkout-payment">
								    <ul class="payment-methods">
								    	<?php if($payments) foreach ($payments as $payment) {
											$checked = ($payments[0]->id == $payment->id) ? 'checked' : '';
								    		?>
								    		<li class="payment-method">
									            <input id="payment_method_cod-<?=$payment->id?>" type="radio" name="payment_method" value="<?=$payment->id?>" <?=$checked?>>
									            <label for="payment_method_cod-<?=$payment->id?>" class="radio" data-slide-toggle="#payment-<?=$payment->id?>" data-parent=".payment-methods"><?=$payment->name?></label>

									            <div class="payment-box" id="payment-<?=$payment->id?>" style="display:none;">
									                <p><?=htmlspecialchars_decode($payment->info)?></p>
									            </div>
									        </li>
								    	<?php } ?>
								    </ul>
								</div>
							<?php } ?>

							<label><input type="checkbox" name="oferta" style="position: static !important; visibility: visible !important;" checked required> <?=$this->text('Я погоджуюся з')?> <a href="<?=SERVER_URL?>dogovir-oferti"><?=$this->text('Договором оферти')?></a></label>
							<br>

							<a href="<?=SITE_URL.$_SESSION['alias']->alias?>" class="btn btn-buy btn-warning pull-left"><?=$this->text('До корзини')?></a>
							<div class="text-right">
					    		<button type="submit" class="btn btn-buy" onclick="ga('send', 'event', 'cart', 'confirm');"><?=$this->text('Оформити замовлення')?></button>
					    	</div>
						</div>
					</div>
				</div>
			</form>
		</div>
	</div>
</section>
<div id="divLoading"></div>

<?php if(!$this->userIs() && !empty($_SESSION['option']->facebook_initialise) && $_SESSION['option']->facebook_initialise){ ?>
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
<?php if($print) { ?>
	<!DOCTYPE html>
	<html lang="uk">
		<head>
			<title>.</title>
		    <meta charset="utf-8">
		    <meta name="viewport" content="width=device-width, initial-scale=1.0">
			<meta name="viewport" content="width=device-width, maximum-scale=1, initial-scale=1, user-scalable=0" />
			<link href="<?=SERVER_URL?>assets/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
		</head>

		<body onload="window.print();">
<?php } else { ?>
		<div class="row">
			<div class="col-md-12">
		        <div class="panel panel-inverse">
		            <div class="panel-heading">
		                <div class="panel-heading-btn">
		                    <a href="<?=SITE_URL?>admin/cart/<?= $cart->id?>" class="btn btn-success btn-xs">Керувати замовленням</a>
							<a href="<?=SITE_URL?>admin/cart/<?= $cart->id?>/print?go" class="btn btn-danger btn-xs" target="_blank">Друкувати</a>
		                </div>
		                <h4 class="panel-title">Попередній перегляд до друку Замовлення #<?= $cart->id?> від <?= date('d.m.Y H:i', $cart->date_edit)?></h4>
		            </div>
					<div class="panel-body">
		    		<div class="clearfix">
<?php } ?>
    		<center><img src="<?=IMG_PATH?>logo.png" style="width: 100px"></center>
    		<?php /* <div class="pull-right" style="text-align: right;">
    			<p><strong>вул. Любінська 92 Будинок меблів</strong></p>
    			<p><strong>м. Львів, Україна, 79054</strong></p>
    		</div>
    		<p><strong><?=SITE_NAME?></strong></p>
    		<p><strong><?=SITE_EMAIL?>, +38 068 919 7241</strong></p>*/ ?>

    		<h1>Замовлення #<?= $cart->id?> від <?= date('d.m.Y H:i', $cart->date_edit)?></h1>

    		<?php /* <div class="pull-right" style="text-align: right;">
    			<p>Статус замовлення: <strong><?= $cart->status_name ?></strong></p>
    		</div> */ ?>

			<table class="cartUserinfo">
				<tr>
					<td rowspan="2">Покупець:</td>
					<th><?= $cart->user_name ." (#$cart->user)" ?></th>
				</tr>
				<tr>
					<th><?= $cart->user_email .", " . $cart->user_phone ?></th>
				</tr>
				<?php if($cart->shipping_id) {
					if(!empty($cart->shipping->name)) { ?>
						<tr>
							<td>Служба доставки: </td>
							<th><?= $cart->shipping->name ?></th>
						</tr>
					<?php } ?>
					<tr>
						<td colspan="2">
							<?php if(!empty($cart->shipping->text))
					            echo "<p>{$cart->shipping->text}</p>";
					        else
					        {
					            if(!empty($cart->shipping_info['city']))
					                echo "<p>Місто: <b>{$cart->shipping_info['city']}</b> </p>";
					            if(!empty($cart->shipping_info['department']))
					                echo "<p>Відділення: <b>{$cart->shipping_info['department']}</b> </p>";
					            if(!empty($cart->shipping_info['address']))
					                echo "<p>Адреса: <b>{$cart->shipping_info['address']}</b> </p>";
					        }
					        if(!empty($cart->shipping_info['recipient']))
					            echo "<p>Отримувач: <b>{$cart->shipping_info['recipient']}</b> </p>";
					        if(!empty($cart->shipping_info['phone']))
					            echo "<p>Контактний телефон: <b>{$cart->shipping_info['phone']}</b> </p>"; ?>
					    </td>
					</tr>
				<?php } ?>
			</table>
	    	<p></p>
    	</div>
   		<div class="table-responsive" >
    		<table class="table table-striped table-bordered nowrap" width="100%">
    			<thead>
	    			<tr>
	    				<th>#</th>
	    				<?php if(!empty($cart->products[0]->info->article)) { ?>
			    			<th>Артикул</th>
			    		<?php } ?>
	    				<th>Назва</th>
	    				<th>Кількість</th>
	    				<th>Ціна за од.</th>
	    				<th>Сума</th>
	    			</tr>
	    		</thead>
	    		<tbody>
					<?php $i = 1; if($cart->products) foreach($cart->products as $product) {?>
	    			<tr>
	    				<td><?=$i++?></td>
	    				<?php if(!empty($product->info->article)) { ?>
	    					<td><?= $product->info->article ?></td>
	    				<?php } ?>
	    				<td><?php if(!empty($product->info))
	    				echo '<strong>'.$product->info->name.'</strong>';
		    			if(!empty($product->product_options))
						{
							echo "<hr style='margin:2px'>";
							$i = 0;
							$product->product_options = unserialize($product->product_options);
							foreach ($product->product_options as $key => $value) {
								if($i++ > 0)
									echo "<br>";
								echo "{$key}: <strong>{$value}</strong>";
							}
						} ?></td>
	    				<td><?= $product->quantity ?></td>
	    				<td><?= $this->cart_model->priceFormat($product->price) ?></td>
	    				<td><?= $this->cart_model->priceFormat($product->price*$product->quantity) ?></td>
	    			</tr>
					<?php } 
					$cols = 4;
					if(!empty($cart->products[0]->info->article))
						$cols++;
					?>
					<tr>
						<td colspan="<?=$cols?>" style="text-align: right;">Сума</td><td><b><?= $this->cart_model->priceFormat($cart->total)?></b></td>
					</tr>
	    		</tbody>
    		</table>
    	</div>
<style>
	h1 {
	    margin: 40px 0 30px;
	}
	table.cartUserinfo tr td
	{
		padding: 5px;
	}
</style>

<?php if($print) { ?>
		<script type="text/javascript" src="<?=SERVER_URL?>assets/bootstrap/js/bootstrap.min.js"></script>
	</body>
</html>
<?php } else { ?>
    	</div>
    </div>
</div>
<?php } ?>

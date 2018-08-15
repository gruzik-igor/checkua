<div class="table-responsive" >
	<h4 class="left">Тип покупця: <?= $cart->user_type_name?></h4>
	<?php if($cart->status_weight == 0){ ?>
	<button class="btn btn-sm btn-warning pull-right" id="toggleNewProduct" onclick="$('#newProduct').toggle();">Додати товар</button><div class="clearfix"></div><br>
    <?php } ?>
    <table class="table table-striped table-bordered nowrap" width="100%">
	    <thead>
	    	<tr>
	    		<?php if(!empty($cart->products[0]->info->article)) { ?>
	    			<th>Артикул</th>
	    		<?php } ?>
	    		<th>Продукт</th>
	    		<?php if($_SESSION['option']->useStorage) { ?>
	    		<th>Склад</th>
	    		<?php } ?>
		    	<th>Ціна / од.</th>
		    	<th>Кількість</th>
		    	<th>Разом</th>
		    	<?php if($cart->status_weight == 0){ ?><th></th><?php } ?>
	    	</tr>
	    </thead>
	    <tbody>
	    	<?php if($cart->products) foreach($cart->products as $product) { ?>
	    	<tr id="productId-<?= $product->id ?>">
	    		<?php if(!empty($product->info->article)) { ?>
	    			<td><a href="<?=SITE_URL.$product->info->link?>" target="_blank"><?= $product->info->article ?></a></td>
	    		<?php } ?>

	    		<td>
	    			<?php if($cart->action == 'new' && !empty($product->info->options)) { ?>
	    				<button type="button" class="btn btn-xs btn-info right" onclick="$('#edit-product-options-<?=$product->id?>').slideToggle()">Редагувати</button>
	    			<?php } if($product->info->photo) { ?>
		    			<a href="<?=SITE_URL.$product->info->link?>" class="left">
		    				<img src="<?=IMG_PATH?><?=(isset($product->info->cart_photo)) ? $product->info->cart_photo : $product->info->photo ?>" alt="<?=$this->text('Фото'). ' '. $product->info->name ?>" width="90">
		    			</a>
	    			<?php }
	    			echo '<strong>'.$product->info->name.'</strong>';
	    			if(!empty($product->product_options))
					{
						$product->product_options = unserialize($product->product_options);
						foreach ($product->product_options as $key => $value) {
							echo "<p>{$key}: <strong>{$value}</strong></p>";
						}
					} 
					if(!empty($cart->action == 'new' && !empty($product->info->options))){
					?>
					<div class="clearfix"></div>
					<form class="form-horizontal m-t-10" id="edit-product-options-<?=$product->id?>" style="display: none;" action="<?= SITE_URL.'admin/'. $_SESSION['alias']->alias.'/updateproductoptions'?>" method="post">
						<input type="hidden" name="cart" value="<?=$cart->id?>">
						<input type="hidden" name="productRow" value="<?=$product->id?>">
						<?php foreach ($product->info->options as $option) {
							if($option->toCart) { ?>
							<div class="form-group">
		                        <label class="col-md-5 control-label"><strong><?=$option->name?></strong>
		                        	<?=($option->changePrice) ? '<br><small>Випливає на ціну</small>' : ''?>
		                        </label>
		                        <div class="col-md-7">
		                        	<select name="option-<?=$option->id?>" class="form-control" required>
		                        		<?php foreach ($option->value as $value) {
		                        			$selected = '';
		                        			if(isset($product->product_options[$option->name]) && $product->product_options[$option->name] == $value->name)
		                        				$selected = 'selected';
		                        			echo "<option value='{$value->id}' {$selected}>{$value->name}</option>";
		                        		} ?>
		                        	</select>
		                        </div>
		                    </div>	
						<?php } } ?>
						<div class="form-group">
	                        <label class="col-md-5 control-label"></label>
	                        <div class="col-md-7">
	                            <button type="submit" class="btn btn-sm btn-success">Зберегти</button>
	                            <button type="button" class="btn btn-sm btn-info m-r-10" onclick="$(this).closest('form').slideUp()">Скасувати</button>
	                        </div>
	                    </div>
					</form>
					<?php } ?>
	    		</td>

	    		<?php if($_SESSION['option']->useStorage) {?>
	    		<td width="20%">
	    			<?php if(isset($product->invoice)) {
	    				echo $product->invoice->storage_name;
	    				if($cart->status_weight < 50){
	    					echo $product->invoice->amount_free . 'од. ';
	    					echo "<button onclick='showProductInvoices(this, ".$product->alias.', '.$product->product.', '.$product->id.")' class='right'><i class='fa fa-exchange'></i></button>";
	    				}
	    			} elseif(!empty($product->invoices)){ ?>
						<select id="addInvoice-<?= $product->id ?>">
							<?php foreach($product->invoices as $invoice) {?>
							<option value="<?= $invoice->id.'/'.$invoice->storage.'/'.$invoice->price_out?>"><?= $invoice->storage_name . ' / ' . $invoice->amount_free . ' од. ' . ' / $' . $invoice->price_out . ' за од.' ?></option>
							<?php } ?>
						</select>
						<button onclick='changeProductInvoice(this,<?= $product->alias.','.$product->product.','.$product->id?>)' class='right'><i class='fa fa-save'></i></button>
	    			<?php } else echo "Товару немає на складі" ?>
	    		</td>
	    		<?php } ?>

	    		<td id="productPrice-<?= $product->id ?>"><?= $product->price?> грн</td>

	    		<td width="15%" >
	    			<?php if($cart->status_weight == 0){ ?>
	    			<form action="<?= SITE_URL.'admin/'. $_SESSION['alias']->alias.'/changeProductQuantity'?>" method="POST">
	    				<div class="input-group">
							<input type="text" name="quantity" id="productQuantity-<?= $product->id ?>" class="form-control" value="<?= $product->quantity?>">
							<input type="hidden" name="cart" value="<?= $product->cart ?>">
							<input type="hidden" name="id" value="<?= $product->id ?>">
							<input type="hidden" name="storageId" value="<?= $product->storage_alias ?>">
							<input type="hidden" name="invoiceId" value="<?= $product->storage_invoice ?>">
							<?php $toHistory = '';
							if(!empty($product->info->article))
								$toHistory = $product->info->article.' ';
							$toHistory .= $product->info->name.' ';
							$toHistory .= '. Зміна кількості з '.$product->quantity.' на ';
								?>
							<input type="hidden" name="toHistory" value="<?= $toHistory ?>">
							<span class="input-group-btn">
	    						<button type="submit" class="btn btn-secondary"><i class='fa fa-save'></i></button>
	    					</span>
	    				</div>
	    			</form>
	    			<?php } else echo $product->quantity; ?>
	    		</td>

	    		<td id="productTotalPrice-<?= $product->id ?>"><?= $product->price * $product->quantity?> грн</td>

	    		<?php if($cart->status_weight == 0){ ?>
	    		<td><button onclick="removeProduct(<?= $product->id?>, <?= $product->cart?>, <?= $product->price * $product->quantity?>)"><i class='fa fa-remove'></i></button></td>
	    		<?php } ?>
	    	</tr>
	    	<?php } ?>
	    	<tr>
	    		<td colspan="6" class="text-right" >
	    			<span id="totalPrice2"><?= $cart->total ?></span><span> грн</span>
	    		</td>
	    	</tr>
	    </tbody>
    </table>

    <?php if($cart->status == 0) { ?>
    	<form action="<?=SITE_URL.'admin/'.$_SESSION['alias']->alias.'/'?>finishAddCart" method="post">
    		<input type="hidden" name="cart" value="<?=$cart->id?>">
    		<button class="btn btn-sm btn-warning pull-right">Сформувати замовлення</button>
    		<div class="clearfix"></div>
    	</form>
    <?php } ?>
</div>
<?php require_once '_tabs-add_product.php'; ?>


<script>
	function removeProduct(id, cartId, totalPrice) {
		if(confirm('Ви впевнені, що хочете видалити цей товар?')){
			$.ajax({
				url: "<?= SITE_URL.'admin/'. $_SESSION['alias']->alias.'/remove'?>",
				type:"POST",
				data:{
					"id":id,
					"cartId":cartId,
					"totalPrice":totalPrice
				},
				success:function (res) {
					$("#totalPrice, #totalPrice2").text(parseFloat($("#totalPrice").text() - totalPrice));
					$("#productId-"+id).remove();
				}
			})
		}
	}

	function showProductInvoices(el,alias, product,id) {
		var userType = $("#userType").val();
		$.ajax({
			url: "<?= SITE_URL.'admin/'. $_SESSION['alias']->alias.'/showProductInvoices'?>",
			type:"POST",
			data:{
				"alias":alias,
				"userType":userType,
				"product":product
			},
			success:function (res) {
				if(res){
					var selectedInvoiceValues = $(el).parent().text().trim().split(" / ");
					$(el).parent().empty().html("<select id='addInvoice-"+id+"'></select><button onclick='changeProductInvoice(this, "+alias+', '+product+', '+id+")' class='right'><i class='fa fa-save'></i></button>");
					$.each(res, function (index,value) {
						var selected = (value.storage_name == selectedInvoiceValues[0]) && (value.amount_free+'од.' == selectedInvoiceValues[1]) ? 'selected' : '';
						$("#addInvoice-"+id).append("<option "+selected+" value="+value.id+'/'+value.storage+'/'+value.price_out+'/'+value.price_in+">"+value.storage_name+" / "+value.amount_free+"од. / $"+value.price_out+" за од. </option>")
					})
				}
			}
		})
	}

	function changeProductInvoice(el, alias, product, id) {
		var price = confirm('Поміняти ціну в корзині?') ? true : false;
		var value = $("#addInvoice-"+id).val();
		$.ajax({
			url: "<?= SITE_URL.'admin/'. $_SESSION['alias']->alias.'/changeProductInvoice'?>",
			type:"POST",
			data:{
				"alias":alias,
				"product":product,
				"id":id,
				"value":value,
				"price":price
			},
			success:function (res) {
				if(res){
					if(res['totalPrice']){
						var price = value.split('/')[2];
						$("#productPrice-"+id).text(price + ' грн');
						$("#productTotalPrice-"+id).text(price * $("#productQuantity-"+id).val() + ' грн');
						$("#totalPrice, #totalPrice2").text(res['totalPrice'] + ' грн');
					}
					var text = $(el).parent().find(':selected').text();
					$(el).parent().empty().html(text+"<button onclick='showProductInvoices(this, "+alias+', '+product+', '+id+")' class='right'><i class='fa fa-exchange'></i></button>");
				}
			}
		})
	}

</script>

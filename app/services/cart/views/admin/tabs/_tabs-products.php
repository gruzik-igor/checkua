<div class="table-responsive" >
	<h4 class="left">Тип покупця: <?= $cartInfo->user_type_name?></h4>
	<?php if($cartInfo->status_weight == 0){ ?>
	<button class="btn btn-sm btn-warning pull-right" id="toggleNewProduct" onclick="$('#newProduct').toggle();">Додати товар</button><div class="clearfix"></div><br>
    <?php } ?>
    <table class="table table-striped table-bordered nowrap" width="100%">
	    <thead>
	    	<tr>
	    		<th>Артикул</th>
	    		<th>Продукт</th>
	    		<?php if($_SESSION['option']->useStorage) { ?>
	    		<th>Склад</th>
	    		<?php } ?>
		    	<th>Ціна / од.</th>
		    	<th>Кількість</th>
		    	<th>Разом</th>
		    	<?php if($cartInfo->status_weight == 0){ ?><th></th><?php } ?>
	    	</tr>
	    </thead>
	    <tbody>
	    	<?php if($cartProducts) foreach($cartProducts as $product) {?>
	    	<tr id="productId-<?= $product->id ?>">
	    		<td><a href="<?=SITE_URL.'admin/'.$product->alias_name.'/search?id='.$product->product?>" target="_blank"><?= $product->product_article ?></a></td>
	    		<td><?= $product->product_name?></td>
	    		<?php if($_SESSION['option']->useStorage) {?>
	    		<td width="20%">
	    			<?php if(isset($product->invoice)) {
	    				echo $product->invoice->storage_name;
	    				if($cartInfo->status_weight < 50){
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
	    		<td id="productPrice-<?= $product->id ?>">$<?= $product->price?></td>
	    		<td width="15%" >
	    			<?php if($cartInfo->status_weight == 0){ ?>
	    			<form action="<?= SITE_URL.'admin/'. $_SESSION['alias']->alias.'/changeProductQuantity'?>" method="POST">
	    				<div class="input-group">
							<input type="text" name="quantity" id="productQuantity-<?= $product->id ?>" class="form-control" value="<?= $product->quantity?>">
							<input type="hidden" name="cart" value="<?= $product->cart ?>">
							<input type="hidden" name="id" value="<?= $product->id ?>">
							<input type="hidden" name="storageId" value="<?= $product->storage_alias ?>">
							<input type="hidden" name="invoiceId" value="<?= $product->storage_invoice ?>">
							<span class="input-group-btn">
	    						<button type="submit" class="btn btn-secondary"><i class='fa fa-save'></i></button>
	    					</span>
	    				</div>
	    			</form>
	    			<?php } else echo $product->quantity; ?>
	    		</td>
	    		<td id="productTotalPrice-<?= $product->id ?>">$<?= $product->price * $product->quantity?></td>
	    		<?php if($cartInfo->status_weight == 0){ ?>
	    		<td><button onclick="removeProduct(<?= $product->id?>, <?= $product->cart?>, <?= $product->price * $product->quantity?>)"><i class='fa fa-remove'></i></button></td>
	    		<?php } ?>
	    	</tr>
	    	<?php } ?>
	    	<tr>
	    		<td colspan="6" class="text-right" id="totalPrice2">$<?= $cartInfo->total ?></td>
	    	</tr>
	    	<tr>
	    		<?php $currency_USD = $cartInfo->currency == 0 ? $this->load->function_in_alias('currency', '__get_Currency', 'USD') : $cartInfo->currency; ?>
	    		<td colspan="6" class="text-right" id="totalPrice3"><?= $cartInfo->total * $currency_USD?> грн (1 USD = <?=$currency_USD?> UAH)</td>
	    		<?php if($cartInfo->status_weight == 0){ ?><td></td><?php } ?>
	    	</tr>
	    </tbody>
    </table>
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
					$("#totalPrice, #totalPrice2").text('$' + (parseFloat($("#totalPrice").text()) - totalPrice));
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
						$("#productPrice-"+id).text('$' + price);
						$("#productTotalPrice-"+id).text('$' + price * $("#productQuantity-"+id).val());
						$("#totalPrice, #totalPrice2").text('$' + res['totalPrice']);
					}
					var text = $(el).parent().find(':selected').text();
					$(el).parent().empty().html(text+"<button onclick='showProductInvoices(this, "+alias+', '+product+', '+id+")' class='right'><i class='fa fa-exchange'></i></button>");
				}
			}
		})
	}

</script>

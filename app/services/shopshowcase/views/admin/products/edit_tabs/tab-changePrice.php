<form action="<?=SITE_URL.'admin/'.$_SESSION['alias']->alias?>/saveChangePrice" method="POST" class="form-horizontal">
	<input type="hidden" name="id" value="<?=$product->id?>">

	<div class="row">
		<label class="col-md-3 control-label">Основна (базова) вартість товару</label>
	    <div class="col-md-9">
	    	<div class="input-group">
	            <input type="number" name="price" value="<?=$product->price?>" min="0" step="0.01" required class="form-control">
	            <span class="input-group-addon">y.o.</span>
	        </div>
	    </div>
    </div>
    <div class="row">
		<label class="col-md-3 control-label">Стара ціна (y.o.)</label>
	    <div class="col-md-9">
	    	<div class="input-group">
	            <input type="number" name="old_price" value="<?=$product->old_price?>" min="0" step="0.01" class="form-control">
	            <span class="input-group-addon">y.o.</span>
	        </div>
	    </div>
    </div>

	<h3>Зміна ціни відносно властивості товару</h3>
	<?php if (!empty($changePriceOptions)) {
		foreach ($changePriceOptions as $option) { ?>
		<div class="row">
		    <label class="col-md-2 control-label"><?=$option->name?></label>
		    <div class="col-md-10">
		    	<?php foreach ($option->values as $ov) {
		    		$action = $value = $valueDefault = $currency = 0;
		    		$actionDefault = '';
		    		if(!empty($ov->changePrice) && !is_numeric($ov->changePrice))
					{
						$changePrice = unserialize($ov->changePrice);
						$actionDefault = '('.$changePrice['action'].')';
						$value = $valueDefault = $changePrice['value'];
						$currency = $changePrice['currency'];
					}

					if(isset($product_options_changePrice[$option->id][$ov->id]))
					{
						$changePrice = $product_options_changePrice[$option->id][$ov->id];
						if(is_numeric($changePrice))
						{
			    			if($changePrice > 0)
			    			{
			    				$action = 1;
			    				$value = $changePrice;
			    				$currency = 0;
			    			}
			    			else
			    				$action = $changePrice;
			    		}
			    		else
			    		{
			    			$changePrice = $product_options_changePrice[$option->id][$ov->id];
							$action = $changePrice['action'];
							$value = $changePrice['value'];
							$currency = $changePrice['currency'];
			    		}
					}
				?>
		    	<div class="form-group">
		    		<label class="col-md-2 control-label"><?=$ov->name?></label>
			    	<div class="col-xs-2">
			    		<input type="hidden" name="changePrice-option-<?=$ov->id?>" value="<?=$option->id?>">
			    		<select class="form-control" name="changePrice-action-<?=$ov->id?>" onchange="setChangePrice('<?=$ov->id?>', this.value)">
			            	<option value="-1">відключено</option>
			            	<option value="0" <?=($action == 0) ? 'selected' : ''?>>за замовчуванням <?=$actionDefault?></option>
			            	<option value="1" <?=($action == 1) ? 'selected' : ''?>>точна ціна</option>
			            	<option value="+" <?=($action === '+') ? 'selected' : ''?>>+</option>
			            	<option value="-" <?=($action === '-') ? 'selected' : ''?>>-</option>
			            	<option value="*" <?=($action === '*') ? 'selected' : ''?>>*</option>
			            </select>
			    	</div>
					<div class="col-xs-6">    
			            <input type="number" name="changePrice-value-<?=$ov->id?>" value="<?=$value?>" class="form-control changePrice-set-<?=$ov->id?>" <?=(is_numeric($action) && $action <= 0) ? 'disabled' : ''?> placeholder="<?=$valueDefault?>" min="0" step="0.01" required>
			        </div>
			        <div class="col-xs-2">
			            <select class="form-control changePrice-set-<?=$ov->id?>" name="changePrice-currency-<?=$ov->id?>" id="changePrice-currency-<?=$ov->id?>" <?=(is_numeric($action) && $action <= 0) ? 'disabled' : ''?>>
			            	<option value="0">y.o.</option>
			            	<option value="p" <?=($currency === 'p') ? 'selected' : ''?>>%</option>
			            </select>
			        </div>
		        </div>
		        <?php } ?>
		    </div>
		</div>
	<?php } } else { ?>
		<div class="row">
			<div class="alert alert-warning fade in">
	        	<i class="fa fa-cog fa-2x pull-left"></i>
	          	<h4>Активуйте властивості до товару на вкладці "Загальні дані"</h4>
	      	</div>
	    </div>
	<?php } ?>

	<div class="form-group">
		<div class="col-md-5"></div>
		<button type="submit" class="btn btn-sm btn-success col-md-2">Зберегти</button>
	</div>
</form>

<script type="text/javascript">
	function setChangePrice(id, value) {
		if(value <= 0)
			$('.changePrice-set-'+id).prop('disabled', true);
		else
		{
			$('.changePrice-set-'+id).prop('disabled', false);
			if(value == 1 || value == '*')
			{
				$("#changePrice-currency-"+id).val('0');
				$('#changePrice-currency-'+id).prop('disabled', true);
			}
		}
	}
</script>
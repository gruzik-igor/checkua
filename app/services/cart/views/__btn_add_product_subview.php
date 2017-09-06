<?php $showSelectQuantity = true;
if($_SESSION['cart']->initJsStyle) {
	$_SESSION['alias-cache'][$product->wl_alias]->alias->js_load[] = 'js/'.$_SESSION['alias']->alias.'/cart.js';
	echo '<link rel="stylesheet" type="text/css" href="'.SITE_URL.'style/'.$_SESSION['alias']->alias.'/cart.css">';
	$_SESSION['cart']->initJsStyle = false;
}
$quantity = 1;
$productKey = $product->wl_alias . '-' . $product->id;
if(isset($product->storage_alias) && isset($product->storage_id) && $product->storage_id > 0)
	$productKey .= '-' . $product->storage_alias . '-' . $product->storage_id;
?>
<div class="row">
    <div class="form-group">
    	<?php if($showSelectQuantity) { $quantity = 0; ?>
	        <label class="control-label col-md-2"><?=$this->text('Кількість', 0)?>:</label>
	        <div class="col-md-4">
	            <div class="input-group">
	                <span class="input-group-btn"><button class="btn btn-success" onclick="changeQuantity(this)">-</button></span>
	                <input type="number" class="form-control" value="1" id="productQuantity">
	                <span class="input-group-btn"><button class="btn btn-success" onclick="changeQuantity(this)">+</button></span>
	            </div>
	        </div>
        <?php } ?>
        <div class="col-md-3">
           <button type="button" class="btn addToCartBtn" onclick="cart.add(<?= "'".$productKey."', ".$quantity?>)"><?=$this->text('Додати до корзини')?></button>
        </div>
    </div>
</div>
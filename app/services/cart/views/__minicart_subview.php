<?php /* <link rel="stylesheet" type="text/css" href="<?=SITE_URL.'style/'.$_SESSION['alias']->alias?>/mini-shopping-cart.css">

<li id="shopping-cart-in-menu">
    <a href="#!" data-toggle="dropdown" aria-haspopup="false" aria-expanded="true">
        <i class="fa fa-shopping-cart"></i>
        <?php if($products) echo('<span class="badge">'.count($products).'</span>')?>
    </a>
    <ul class="dropdown-menu">
    	<div id="slimScrollDiv" class="dropdown-wrap" slim-scroll="√">
	    	<?php if($products)
	    	{
	    		if(isset($_SESSION['alias']->alias_from) && $_SESSION['alias']->alias_from != $_SESSION['alias']->id)
	    		{
		    		$_SESSION['alias-cache'][$_SESSION['alias']->alias_from]->alias->js_load[] = 'assets/slimscroll/jquery.slimscroll.min.js';
					$_SESSION['alias-cache'][$_SESSION['alias']->alias_from]->alias->js_load[] = 'assets/slimscroll/slimscroll.init.js';
				}
				else
				{
					$_SESSION['alias']->js_load[] = 'assets/slimscroll/jquery.slimscroll.min.js';
					$_SESSION['alias']->js_load[] = 'assets/slimscroll/slimscroll.init.js';
				}
	    		foreach ($products as $product) { ?>
	            <li id="product-<?=$product->key?>">
	                <a href="<?=SITE_URL.$product->info->link?>"><img src="<?=IMG_PATH.$product->info->admin_photo?>" class="img-responsive product-img" alt="<?=$product->info->name?>"></a>
	                <div class="product-details">
	                    <p class="product-title clearfix"><a href="<?=SITE_URL.$product->info->link?>"><?=$product->info->name?></a></p>
	                    <p class="product-price clearfix">
							<span class="amount"><?=$product->priceFormat?> x <?=$product->quantity?></span>
						</p>
	                </div>
	            </li>
	            <?php } 
	        } ?>
	    </div>
        <li class="cart-footer">
        	<h4><?=$this->text('Попередня сума')?> <strong class="subTotal pull-right"><?=$subTotal?></strong></h4>
            <a href="<?=SITE_URL.$_SESSION['alias']->alias?>" class="btn btn-warning"><?=$this->text('До корзини', 0)?></a>
        </li>
    </ul>
</li>
*/ ?>

<li id="shopping-cart-in-menu" class="shopping-cart">
    <a href="#!" class="li-icon" data-toggle="dropdown" aria-haspopup="false" aria-expanded="false">
        <i class="lil-shopping_cart"></i><span class="badge" id="productsCount"><?= ($products) ? count($products) : 0?></span>
    </a>
    <ul class="dropdown-menu">
        <div id="slimScrollDiv" class="dropdown-wrap mCustomScrollbar" slim-scroll="√">
            <?php if($products)
	    	{
	    		if(isset($_SESSION['alias']->alias_from) && $_SESSION['alias']->alias_from != $_SESSION['alias']->id)
	    		{
		    		$_SESSION['alias-cache'][$_SESSION['alias']->alias_from]->alias->js_load[] = 'assets/slimscroll/jquery.slimscroll.min.js';
					$_SESSION['alias-cache'][$_SESSION['alias']->alias_from]->alias->js_load[] = 'assets/slimscroll/slimscroll.init.js';
				}
				else
				{
					$_SESSION['alias']->js_load[] = 'assets/slimscroll/jquery.slimscroll.min.js';
					$_SESSION['alias']->js_load[] = 'assets/slimscroll/slimscroll.init.js';
				}
	    		foreach ($products as $product) { ?>
            <li id="product-<?=$product->key?>">
                <?php if(!empty($product->info->admin_photo)){ ?>
                	<a href="<?=SITE_URL.$product->info->link?>"><img src="<?=IMG_PATH.$product->info->admin_photo?>" class="img-responsive product-img" alt="<?=$product->info->name?>"></a>
                <?php } ?>
                <div class="product-details">
                    <p class="product-title clearfix"><a href="<?=SITE_URL.$product->info->link?>"><?=html_entity_decode($product->info->name)?></a></p>
                    <p class="product-price clearfix">
						<span class="amount"><?=$product->priceFormat?> x <?=$product->quantity?></span>
					</p>
                </div>
            </li>
            <?php } } else {?>
            <li>
                <h6 class="text-center cart-empty"><?=$this->text('Корзина пуста', 0)?></h6>
            </li>
            <?php } ?>
        </div>
        <li class="dropdown-footer">
            <a href="<?= SITE_URL?>cart"><?=$this->text('Перейти до корзини', 0)?></a>
        </li>
    </ul>
</li>
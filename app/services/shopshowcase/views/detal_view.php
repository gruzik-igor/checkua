<link rel="stylesheet" href="<?=SERVER_URL?>assets/blueimp/css/blueimp-gallery.min.css">

<div class="shop-product">
    <div class="container content">
        <div class="row">
            <div class="col-md-6 md-margin-bottom-50">
            <?php if(!empty($_SESSION['alias']->images)) { ?>
                <div id="blueimp-gallery-items" class="blueimp-gallery">
                    <?php foreach ($_SESSION['alias']->images as $image) { 
                        $path = (isset($image->m_path)) ? $image->m_path : $image->path;
                        ?>
                        <a href="<?=IMG_PATH.$image->path?>">
                            <img src="<?=IMG_PATH.$path?>" alt="<?=$image->title?>">
                        </a>
                    <?php } ?>
                </div>
                <?php } ?>
                <div id="blueimp-gallery-carousel" class="blueimp-gallery blueimp-gallery-carousel">
                    <div class="slides"></div>
                    <h3 class="title"></h3>
                    <a class="prev">‹</a>
                    <a class="next">›</a>
                    <a class="play-pause"></a>
                    <ol class="indicator"></ol>
                </div>
            </div>

            <div class="col-md-6">
            	<div class="shop-product-heading">
            		<h2>
	                    <?= html_entity_decode(str_replace($product->article, '', $product->name))?>
	                </h2>
            	</div>
                <div class="clear-both"><p class="margin-top-20"><?=$_SESSION['alias']->list?></p></div>

                <ul class="list-inline shop-product-prices margin-bottom-30">
                    <li class="shop-red">
                        <?= $product->price ?> грн
                    </li>
                    <?php if($product->old_price != 0) { ?>
                    <li class="line-through"><?= $product->old_price ?> грн</li>
                    <?php } ?>
                    <li class="similar-products">
                        <?php if(isset($product->similarProducts) && $product->group != 4 && $product->similarProducts) {?>
                            <a href="<?= SITE_URL.$product->similarProducts[0]->link ?>">
                                <div class="col-md-4 pull-right" style="font-size:15px" ><?=$this->text('Обирайте комлект')?><br><?=$this->text('та отримайте знижку')?><span>10%</span></div>
                                <div class="col-md-2 pull-right padding-0">
                                    <img src="<?= IMG_PATH.$product->similarProducts[0]->photo ?>" class="img-responsive" alt="">
                                </div>
                            </a>
                        <?php } ?>
                    </li>
                </ul>

                <?php if($product->options) foreach($product->options as $option) { if($option->id == 4 || $option->id == 5) continue; ?>
                 <div class="wishlist-category">
                    <strong><?= $option->name ?>:</strong>
                    <?php if(is_array($option->value)) foreach ($option->value as $value) { ?>
                    <p><?= $value ?> </p> <?= end($option->value) == $value ? '' : ','; ?>
                    <?php } else { ?>
                    <p><?= $option->value ?> </p>
                    <?php } ?>
                </div>
                <?php } ?>

                <?php if(isset($product->options['4-rozmir'])) { ?>
                <div class="wishlist-category">
                    <strong><?= $product->options['4-rozmir']->name?>:</strong>
                    <p>
                        <select name="size" id="size">
                            <?php foreach ($product->options['4-rozmir']->value as $rozmir) { ?>
                                <option value=""><?= $rozmir?></option>
                            <?php } ?>
                        </select>
                    </p>
                </div>
                <?php } ?>

                <div class="wishlist-category">
                    <strong><?=$this->text('Артикул')?>:</strong>
                    <p><?= $product->article ?> </p>
                </div>
                <?php if($product->group) { ?>
                    <div class="wishlist-category">
                        <strong><?=$this->text('Категорія')?>:</strong>
                        <p><a href="<?=SITE_URL.$product->group_link?>"><?= $product->group_name ?></a></p>
                    </div>
                <?php } ?>

                <div class="margin-bottom-20">
                    <?php $this->load->function_in_alias('cart', '__show_btn_add_product', $product); ?>
                    
                    <div class="delivery-time"><?=$this->text('Орієнтований час')?><br><?=$this->text('доставки')?>: <br><strong>7-10 <?=$this->text('днів')?></strong></div>
                </div>

                <?php if(isset($product->options['5-dovzhyna'])) { ?>
                <div class="">
                    <div class="" style="vertical-align: middle;display: table-cell;padding-right: 5px; ;"><img src="<?= $product->options['5-dovzhyna']->photo ?>" /></div>
                    <div class="" style="vertical-align: middle;display: table-cell; text-align: justify;"><?=$this->text('Довжина плаття')?>:<br> <?= $product->options['5-dovzhyna']->value?></div>
                </div>
                 <?php } ?>

                <div class="margin-bottom-30"></div>
                <?=$_SESSION['alias']->text?>
            </div>
        </div>
    </div>
</div>

<?php $otherProductsByGroup = $this->load->function_in_alias($product->wl_alias, '__get_Products', array('group' => $product->group, 'limit' => 10));
if(!empty($otherProductsByGroup) && count($otherProductsByGroup) > 1) { ?>

<div class="container padding-top-40">
    <div class="heading heading-v1 margin-bottom-20">
        <h2><?=$this->text('Вас також може зацікавити')?></h2>
    </div>

    <div class="illustration-v2 margin-bottom-60">
        <ul class="list-inline owl-slider-v4">
        <?php
        foreach ($otherProductsByGroup as $otherProduct) {
            if($otherProduct->id != $product->id) {
        ?>
            <li class="item">
                <div class="product-img">
                    <?php if(isset($otherProduct->m_photo)){ ?>
                    <a href="<?=SERVER_URL.'shop/'.$otherProduct->alias?>">
                        <img class="full-width img-responsive" src="<?=IMG_PATH.$otherProduct->m_photo?>">
                    </a>
                    <?php } if($otherProduct->old_price != 0) { ?>
                    <div class="shop-rgba-red rgba-banner line-through"><?= $otherProduct->old_price ?> грн</div>
                    <?php } ?>
                </div>
                <div class="product-description product-description-brd">
                    <div class="overflow-h margin-bottom-5">
                        <div class="">
                            <h4 class="title-price product-name-overflow"><a href="<?=SERVER_URL.'shop/'.$otherProduct->alias?>"><?= str_replace($otherProduct->article, '', $otherProduct->name) ?></a></h4>
                            <a href="<?= SERVER_URL.$otherProduct->group_link ?>">
                                <span class="gender text-uppercase"><?= $otherProduct->group_name ?></span>
                            </a>
                        </div>
                        <div class="product-price pull-right">
                            <span class="title-price"><?= $otherProduct->price ?> грн.</span>
                        </div>
                    </div>
                </div>
            </li>
        <?php } } ?>
        </ul>
    </div>
</div>
<?php } ?>

<?php
$_SESSION['alias']->js_load[] = "assets/blueimp/js/jquery.blueimp-gallery.min.js";
$_SESSION['alias']->js_load[] = "assets/blueimp/js/blueimp-gallery.init.js";
?>
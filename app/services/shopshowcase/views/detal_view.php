<div class="shop-product">
    <div class="container content">
        <div class="row">
            <?php if(!empty($_SESSION['alias']->images)) { ?>
                <link rel="stylesheet" href="<?=SERVER_URL?>assets/blueimp/css/blueimp-gallery.min.css">
                <div class="col-md-6 md-margin-bottom-50">
                    <div id="blueimp-gallery-items" class="blueimp-gallery">
                        <?php foreach ($_SESSION['alias']->images as $image) { 
                            $path = (isset($image->m_path)) ? $image->m_path : $image->path;
                            ?>
                            <a href="<?=IMG_PATH.$image->path?>">
                                <img src="<?=IMG_PATH.$path?>" alt="<?=$image->title?>">
                            </a>
                        <?php } ?>
                    </div>
                    
                    <div id="blueimp-gallery-carousel" class="blueimp-gallery blueimp-gallery-carousel">
                        <div class="slides"></div>
                        <h3 class="title"></h3>
                        <a class="prev">‹</a>
                        <a class="next">›</a>
                        <a class="play-pause"></a>
                        <ol class="indicator"></ol>
                    </div>
                </div>
            <?php
                $_SESSION['alias']->js_load[] = "assets/blueimp/js/jquery.blueimp-gallery.min.js";
                $_SESSION['alias']->js_load[] = "assets/blueimp/js/blueimp-gallery.init.js";
            } ?>

            <div class="col-md-6">
            	<div class="shop-product-heading">
            		<h2><?= html_entity_decode(str_replace($product->article, '', $product->name))?></h2>
            	</div>

                <?php if(!empty($_SESSION['alias']->list)) { ?>
                    <div class="clear-both"><p class="margin-top-20"><?=$_SESSION['alias']->list?></p></div>
                <?php } ?>

                <ul class="list-inline shop-product-prices margin-bottom-30">
                    <li class="shop-red">
                        <?= $product->price ?> грн
                    </li>
                    <?php if($product->old_price != 0) { ?>
                        <li class="line-through"><?= $product->old_price ?> грн</li>
                    <?php } ?>
                </ul>

                <div class="wishlist-category">
                    <strong><?=$this->text('Артикул')?>:</strong>
                    <p><?= $product->article ?></p>
                </div>
                <?php if($product->group) { ?>
                    <div class="wishlist-category">
                        <strong><?=$this->text('Категорія')?>:</strong> <p>
                        <?php if(is_array($product->group)) {
                            foreach ($product->group as $group) {
                                echo '<a href="'.SITE_URL.$group->link.'">'.$group->name.'</a> ';
                            }
                        } else
                            echo '<a href="'.SITE_URL.$product->group_link.'">'.$product->group_name.'</a>';
                        ?></p>
                    </div>
                <?php } ?>

                <?php if(!empty($product->options))
                {
                    foreach($product->options as $option) {
                        if(!$option->toCart) { ?>
                        <div class="wishlist-category">
                            <strong><?= $option->name ?>:</strong>
                            <?php if(is_array($option->value))
                                    echo '<p>'.implode(', ', $option->value).'</p>';
                                  else
                                    echo "<p>{$option->value}</p>";
                            ?>
                        </div>
                    <?php } }
                    foreach($product->options as $option) {
                        if($option->toCart) { ?>
                        <div class="wishlist-category">
                            <strong><?= $option->name ?>:</strong>
                            <p>
                                <select id="product-option-<?=$option->id?>">
                                    <?php foreach ($option->value as $value) {
                                        echo "<option>{$value}</option>";
                                    } ?>
                                </select>
                            </p>
                        </div>
                    <?php } }
                } ?>

                <div class="margin-bottom-20">
                    <?php $this->load->function_in_alias('cart', '__show_btn_add_product', $product); ?>
                    
                    <div class="delivery-time"><?=$this->text('Орієнтований час')?><br><?=$this->text('доставки')?>: <br><strong>7-10 <?=$this->text('днів')?></strong></div>
                </div>

                <div class="margin-bottom-30"></div>
                <?=$_SESSION['alias']->text?>
            </div>
        </div>
    </div>
</div>

<?php $_SESSION['option']->paginator_per_page = 10;
$otherProductsByGroup = $this->shop_model->getProducts($product->group);
if(!empty($otherProductsByGroup) && count($otherProductsByGroup) > 1) { ?>
<div class="container padding-top-40">
    <div class="heading heading-v1 margin-bottom-20">
        <h2><?=$this->text('Вас також може зацікавити')?></h2>
    </div>

    <div class="illustration-v2 margin-bottom-60">
        <ul class="list-inline owl-slider-v4">
        <?php foreach ($otherProductsByGroup as $otherProduct) {
            if($otherProduct->id != $product->id) { ?>
            <li class="item">
                <div class="product-img">
                    <?php if(isset($otherProduct->m_photo)) { ?>
                        <a href="<?=SITE_URL.$otherProduct->link?>">
                            <img class="full-width img-responsive" src="<?=IMG_PATH.$otherProduct->m_photo?>">
                        </a>
                    <?php } if($otherProduct->old_price != 0) { ?>
                        <div class="shop-rgba-red rgba-banner line-through"><?= $otherProduct->old_price ?> грн</div>
                    <?php } ?>
                </div>
                <div class="product-description product-description-brd">
                    <div class="overflow-h margin-bottom-5">
                        <div class="">
                            <h4 class="title-price product-name-overflow"><a href="<?=SITE_URL.$otherProduct->link?>"><?= str_replace($otherProduct->article, '', $otherProduct->name) ?></a></h4>
                            <a href="<?= SITE_URL.$otherProduct->group_link ?>">
                                <span class="gender text-uppercase"><?= $otherProduct->group_name ?></span>
                            </a>
                        </div>
                        <div class="product-price pull-right">
                            <span class="title-price"><?= $otherProduct->price ?> грн</span>
                        </div>
                    </div>
                </div>
            </li>
        <?php } } ?>
        </ul>
    </div>
</div>
<?php } ?>
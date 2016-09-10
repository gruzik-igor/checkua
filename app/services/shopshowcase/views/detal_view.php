<link rel="stylesheet" href="<?=SERVER_URL?>assets/owl-carousel/owl-carousel/owl.carousel.css">
<link rel="stylesheet" href="<?=SERVER_URL?>assets/sky-forms-pro/skyforms/css/sky-forms.css">
<link rel="stylesheet" href="<?=SERVER_URL?>assets/sky-forms-pro/skyforms/custom/custom-sky-forms.css">
<link rel="stylesheet" href="<?=SERVER_URL?>assets/master-slider/masterslider/style/masterslider.css">
<link rel='stylesheet' href="<?=SERVER_URL?>assets/master-slider/masterslider/skins/default/style.css">

<!--=== Shop Product ===-->
<div class="shop-product">

    <div class="container">
        <div class="row">
            <?php if(!empty($product->photos)) { ?>
                <div class="col-md-6 md-margin-bottom-50">
                    <div class="ms-showcase2-template">
                        <!-- Master Slider -->
                        <div class="master-slider ms-skin-default" id="masterslider">
                            <?php foreach ($product->photos as $photo) { ?>
                                <div class="ms-slide">
                                    <img class="ms-brd" src="<?=SERVER_URL?>style/images/blank.gif" data-src="<?=IMG_PATH.$photo->detal_file_address?>" alt="<?=$product->article?> <?=$product->name?>">
                                    <img class="ms-thumb" src="<?=IMG_PATH.$photo->s_file_address?>" alt="thumb">
                                </div>
                            <?php } ?>
                        </div>
                        <!-- End Master Slider -->
                    </div>
                </div>
            <?php } ?>

            <div class="col-md-6">

                <h2>
                    <small class="label label-default adlistingtype">&nbsp;<?=$product->article?>&nbsp;</small>
                    <?= html_entity_decode($product->name)?>
                </h2>

                <div class="clear-both"><p class="margin-top-20"><?=$_SESSION['alias']->list?></p></div>

                <ul class="list-inline shop-product-prices margin-bottom-30">
                    <li class="">$<?=$product->price?></li>
                    <li class="shop-red"><?=$product->price * $currency?> грн</li>
                    <?php if($product->availability > 0) { ?>
                        <li><small class="shop-bg-red time-day-left"><?=$product->availability_name?></small></li>
                    <?php } ?>
                </ul><!--/end shop product prices-->

                <h3 class="shop-product-title">Кількість</h3>
                <div class="margin-bottom-40">
                    <form name="f1" class="product-quantity sm-margin-bottom-20">
                        <button type='button' class="quantity-button" name='subtract' onclick='javascript: subtractQty();' value='-'>-</button>
                        <input type='text' class="quantity-field" name='qty' value="1" id='qty'/>
                        <button type='button' class="quantity-button" name='add' onclick='javascript: document.getElementById("qty").value++;' value='+'>+</button>
                    </form>
                    <button type="button" class="btn-u btn-u-sea-shop btn-u-lg" onclick="cart.add(<?= $product->id.', '.$product->wl_alias?>)">Додати до корзини</button>
                </div><!--/end product quantity-->

                <?php if(isset($product->options['1-vyrobnyk']) && $product->options['1-vyrobnyk']->value != '') { ?>
                    <p class="wishlist-category"><strong>Виробник:</strong> <a href="#"><?=nl2br($product->options['1-vyrobnyk']->value)?></a></p>
                <?php } ?>
                <p class="wishlist-category"><strong>Категорія:</strong> <a href="<?=SITE_URL.$product->group_link?>"><?=$product->group_name?></a></p>
                <p class="wishlist-category"><strong>Товар додано:</strong> <?=date('d.m.Y H:i', $product->date_edit)?> by <?=$product->user_name?></p>

                <div class="margin-bottom-30"></div>
                <?=$_SESSION['alias']->text?>
            </div>
        </div><!--/end row-->
    </div>
</div>
<!--=== End Shop Product ===-->

<!--=== Content Medium ===-->
<div class="content-md container">
    <!--=== Product Service ===-->
    <div class="row margin-bottom-60">
        <div class="col-md-4 product-service md-margin-bottom-30">
            <div class="product-service-heading">
                <i class="fa fa-truck"></i>
            </div>
            <div class="product-service-in">
                <h3>Free Delivery</h3>
                <p>Integer mattis lacinia felis vel molestie. Ut eu euismod erat, tincidunt pulvinar semper veliUt porta, leo...</p>
                <a href="#">+Read More</a>
            </div>
        </div>
        <div class="col-md-4 product-service md-margin-bottom-30">
            <div class="product-service-heading">
                <i class="icon-earphones-alt"></i>
            </div>
            <div class="product-service-in">
                <h3>Customer Service</h3>
                <p>Integer mattis lacinia felis vel molestie. Ut eu euismod erat, tincidunt pulvinar semper veliUt porta, leo...</p>
                <a href="#">+Read More</a>
            </div>
        </div>
        <div class="col-md-4 product-service">
            <div class="product-service-heading">
                <i class="icon-refresh"></i>
            </div>
            <div class="product-service-in">
                <h3>Free Returns</h3>
                <p>Integer mattis lacinia felis vel molestie. Ut eu euismod erat, tincidunt pulvinar semper veliUt porta, leo...</p>
                <a href="#">+Read More</a>
            </div>
        </div>
    </div><!--/end row-->
    <!--=== End Product Service ===-->
</div><!--/end container-->
<!--=== End Content Medium ===-->

<!--=== Illustration v2 ===-->
<div class="container">
    <div class="heading heading-v1 margin-bottom-20">
        <h2>Product you may like</h2>
        <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed odio elit, ultrices vel cursus sed, placerat ut leo. Phasellus in magna erat. Etiam gravida convallis augue non tincidunt. Nunc lobortis dapibus neque quis lacinia. Nam dapibus tellus sit amet odio venenatis</p>
    </div>

    <div class="illustration-v2 margin-bottom-60">
        <div class="customNavigation margin-bottom-25">
            <a class="owl-btn prev rounded-x"><i class="fa fa-angle-left"></i></a>
            <a class="owl-btn next rounded-x"><i class="fa fa-angle-right"></i></a>
        </div>

        <ul class="list-inline owl-slider-v4">
            <li class="item">
                <a href="#"><img class="img-responsive" src="assets/img/thumb/09.jpg" alt=""></a>
                <div class="product-description-v2">
                    <div class="margin-bottom-5">
                        <h4 class="title-price"><a href="#">Double-breasted</a></h4>
                        <span class="title-price">$95.00</span>
                    </div>
                    <ul class="list-inline product-ratings">
                        <li><i class="rating-selected fa fa-star"></i></li>
                        <li><i class="rating-selected fa fa-star"></i></li>
                        <li><i class="rating-selected fa fa-star"></i></li>
                        <li><i class="rating fa fa-star"></i></li>
                        <li><i class="rating fa fa-star"></i></li>
                    </ul>
                </div>
            </li>
            <li class="item">
                <a href="#"><img class="img-responsive" src="assets/img/thumb/07.jpg" alt=""></a>
                <div class="product-description-v2">
                    <div class="margin-bottom-5">
                        <h4 class="title-price"><a href="#">Double-breasted</a></h4>
                        <span class="title-price">$60.00</span>
                        <span class="title-price line-through">$95.00</span>
                    </div>
                    <ul class="list-inline product-ratings">
                        <li><i class="rating-selected fa fa-star"></i></li>
                        <li><i class="rating-selected fa fa-star"></i></li>
                        <li><i class="rating-selected fa fa-star"></i></li>
                        <li><i class="rating fa fa-star"></i></li>
                        <li><i class="rating fa fa-star"></i></li>
                    </ul>
                </div>
            </li>
            <li class="item">
                <a href="#"><img class="img-responsive" src="assets/img/thumb/08.jpg" alt=""></a>
                <div class="product-description-v2">
                    <div class="margin-bottom-5">
                        <h4 class="title-price"><a href="#">Double-breasted</a></h4>
                        <span class="title-price">$95.00</span>
                    </div>
                    <ul class="list-inline product-ratings">
                        <li><i class="rating-selected fa fa-star"></i></li>
                        <li><i class="rating-selected fa fa-star"></i></li>
                        <li><i class="rating-selected fa fa-star"></i></li>
                        <li><i class="rating fa fa-star"></i></li>
                        <li><i class="rating fa fa-star"></i></li>
                    </ul>
                </div>
            </li>
            <li class="item">
                <a href="#"><img class="img-responsive" src="assets/img/thumb/06.jpg" alt=""></a>
                <div class="product-description-v2">
                    <div class="margin-bottom-5">
                        <h4 class="title-price"><a href="#">Double-breasted</a></h4>
                        <span class="title-price">$95.00</span>
                    </div>
                    <ul class="list-inline product-ratings">
                        <li><i class="rating-selected fa fa-star"></i></li>
                        <li><i class="rating-selected fa fa-star"></i></li>
                        <li><i class="rating-selected fa fa-star"></i></li>
                        <li><i class="rating fa fa-star"></i></li>
                        <li><i class="rating fa fa-star"></i></li>
                    </ul>
                </div>
            </li>
            <li class="item">
                <a href="#"><img class="img-responsive" src="assets/img/thumb/04.jpg" alt=""></a>
                <div class="product-description-v2">
                    <div class="margin-bottom-5">
                        <h4 class="title-price"><a href="#">Double-breasted</a></h4>
                        <span class="title-price">$95.00</span>
                    </div>
                    <ul class="list-inline product-ratings">
                        <li><i class="rating-selected fa fa-star"></i></li>
                        <li><i class="rating-selected fa fa-star"></i></li>
                        <li><i class="rating-selected fa fa-star"></i></li>
                        <li><i class="rating fa fa-star"></i></li>
                        <li><i class="rating fa fa-star"></i></li>
                    </ul>
                </div>
            </li>
            <li class="item">
                <a href="#"><img class="img-responsive" src="assets/img/thumb/03.jpg" alt=""></a>
                <div class="product-description-v2">
                    <div class="margin-bottom-5">
                        <h4 class="title-price"><a href="#">Double-breasted</a></h4>
                        <span class="title-price">$95.00</span>
                    </div>
                    <ul class="list-inline product-ratings">
                        <li><i class="rating-selected fa fa-star"></i></li>
                        <li><i class="rating-selected fa fa-star"></i></li>
                        <li><i class="rating-selected fa fa-star"></i></li>
                        <li><i class="rating fa fa-star"></i></li>
                        <li><i class="rating fa fa-star"></i></li>
                    </ul>
                </div>
            </li>
        </ul>
    </div>
</div>
<!--=== End Illustration v2 ===-->

<?php 
$_SESSION['alias']->js_load[] = 'assets/owl-carousel/owl-carousel/owl.carousel.js';
$_SESSION['alias']->js_load[] = 'assets/master-slider/masterslider/masterslider.min.js';
$_SESSION['alias']->js_load[] = 'assets/master-slider/masterslider/jquery.easing.min.js';
$_SESSION['alias']->js_load[] = 'js/plugins/owl-carousel.js';
$_SESSION['alias']->js_load[] = 'js/plugins/master-slider.js';
$_SESSION['alias']->js_load[] = 'js/forms/product-quantity.js';
$_SESSION['alias']->js_init[] = 'App.initScrollBar();';
$_SESSION['alias']->js_init[] = 'OwlCarousel.initOwlCarousel();';
$_SESSION['alias']->js_init[] = 'MasterSliderShowcase2.initMasterSliderShowcase2();';
?>
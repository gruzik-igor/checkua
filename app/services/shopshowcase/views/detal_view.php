<link rel="stylesheet" href="<?=SERVER_URL?>assets/gritter/css/jquery.gritter.css">

<div class="page-head content-top-margin">
    <div class="container">
        <div class="row">
            <div class="col-md-12 col-sm-12">
                <ol class="breadcrumb">
                    <li><a href="<?= SITE_URL?>"><?=$this->text('Головна', 0)?></a></li>
                    <li><a href="<?= SITE_URL?>category"><?=$this->text('Каталог', 0)?></a></li>
                   <!--  <?php if(count($product->group) < 2){ foreach ($product->group as $g) { ?>
                    <li><a href="<?= SITE_URL.$_SESSION['alias']->alias.'/'.$g->alias?>"><?=$g->name?></a></li>
                    <?php } }?> -->

                    <li class="active"><?=$_SESSION['alias']->name?></li>

                    <li><a href="<?= SITE_URL?>"></a></li>
                  
                </ol>
            </div>
        </div>
    </div>
</div>
<section class="section single-product-wrapper detalmargin">
    <div class="container">
        <div class="row">
            <div class="col-sm-5">
                <h3><?=$_SESSION['alias']->list?></h3><hr><br>
                <?php if(!empty($_SESSION['alias']->images)) { ?>
                <div class="product-images">
                    <div class="product-thumbnail">
                        <a href="<?=IMG_PATH.$_SESSION['alias']->images[0]->path?>" class="fancybox" rel="gallery">
                            <img src="<?=IMG_PATH.$_SESSION['alias']->images[0]->md_path?>" style="width:auto; margin: 0 auto" class="img-responsive">
                        </a>
                    </div>
                    <div class="product-images-carousel">
                        <?php for ($i = 1; $i < count($_SESSION['alias']->images); $i++) {  ?>
                        <div class="item">
                            <a href="<?=IMG_PATH.$_SESSION['alias']->images[$i]->path?>" class="fancybox" rel="gallery">
                                <img src="<?=IMG_PATH.$_SESSION['alias']->images[$i]->ld_path?>" class="img-responsive">
                            </a>
                        </div>
                        <?php } ?>
                    </div>
                </div>
                <?php } ?>
            </div>

            <div class="col-sm-6 col-sm-offset-1">
                <div class="product-details">
                   <!--  <div class="rating">
                        <span class="pull-right"><?=$this->text('Артикул')?>: <?= $product->article ?><span></span></span>
                    </div>
 -->

                    <?php $canBy = false;
                    if(!empty($product->group))
                        foreach ($product->group as $g) {
                            if($g->active)
                            {
                                $canBy = true;
                                break;
                            }
                        }
                     ?>
                    <div class="product-title">
                        <div class="row">
                            <h3 class="product-name"><span style="font-size: 16px"><?=$this->text('Артикул')?>:</span> <?= $_SESSION['alias']->name ?></h3>
                            <hr>
                            <br>
                            <br>
                            <br>
                            <?php if($canBy) { ?>
                                <p class="price">
                                    <?php if($product->old_price != 0) { ?>
                                    <del>
                                        <span class="amount"><?= $product->old_price ?> грн</span>
                                    </del>
                                    <?php } ?>
                                    <ins>
                                        <span class="amount" id="product-price"><?= $product->price ?> грн</span>
                                    </ins>
                                </p>
                            <?php } ?>
                        </div>
                        
                    </div>

                    

                    <div class="inputs-border">
         
                        <?php $productOptionsChangePrice = array();
                        if(!empty($product->options))
                        foreach ($product->options as $key => $option) {
                            if($option->changePrice)
                                $productOptionsChangePrice[] = $option->id;
                            $next = array($option->id.'-podushky-1-katehorii', $option->id.'-podushky-2-katehorii', $option->id.'-podushky-3-katehorii');
                            $key_array = explode('-', $key);
                            $tkanuny = (isset($key_array[1]) && $key_array[1] == 'tkanyny') ? true : false;
                            if($key == $option->id.'-kolir') { ?>
                                <div class="product-attributes row product-options">
                                    <h4 id="product-option-name-<?=$option->id?>"><?=$this->text('Колір')?></h4>
                                    <?php foreach ($option->value as $value) {
                                        if(!$value->photo) $value->photo = IMG_PATH.'noimg.jpg';
                                        ?>
                                        <div class="color-option">
                                            <label class="labelimg" style="background-image: url('<?=$value->photo?>');">
                                                <input type="radio" name="product-option-<?=$option->id?>" value="<?=$value->id?>" onchange="updateProductPrice()">
                                            </label>
                                            <h4><?=$value->name?></h4> 
                                        </div>
                                    <?php } ?>
                                </div>
                        <?php } else if($key == $option->id.'-rozmir') { ?>
                            <div class="row">
                                <h4 id="product-option-name-<?=$option->id?>"><?=$this->text('Розмір')?></h4>
                                <?php foreach ($option->value as $value) { ?>

                                <!-- <label class="size-option" style="float: left;">
                                    <input type="radio" name="rozmir" value=""  class="" >
                                    <p style="margin-top: -16px; margin-bottom: 16px"><?=$value->name?> см</p>
                                    <div class="underlineoption"></div>
                                </label> -->
                                <div class="color-optiond h60">
                                    <label class="labelimgd"  >
                                        <div class="rozmirr">
                                            <input type="radio" name="product-option-<?=$option->id?>" value="<?=$value->id?>" onchange="updateProductPrice()">
                                            <h4><?=$value->name?> см</h4>
                                        </div>
                                    </label> 
                                </div>
                                <?php } ?>
                            </div>
                        <?php } else if($key == $option->id.'-dyvanni-podushky') { ?>
                            <button type="button" class="btn pillow" data-toggle="modal" data-target="#myModal">
                                <?=$this->text('Оберіть диванні подушки')?>  
                            </button>
                        <?php } else if($tkanuny) { ?>
                            <div class="product-attributes row product-options">
                                <h4 id="product-option-name-0"><?=$option->name?> </h4>
                                <?php foreach ($option->value as $value) {
                                    if(!$value->photo) $value->photo = IMG_PATH.'noimg.jpg';
                                    ?>
                                    <div class="color-optiond" >
                                        <label class="labelimg" style="background-image: url('<?=$value->photo?>');background-size: 80px 100px;height: 100px;">
                                            <input type="radio" name="product-option-0" data-id="<?=$option->id?>" value="<?=$value->id?>" onchange="updateProductPrice()">
                                        </label>
                                        <h4 style="margin-top: -10px"><?=$value->name?></h4> 
                                    </div>
                                <?php } ?>
                            </div>
                        <?php } else if(!in_array($key, $next)) { ?>
                            <div class="product-attributes row product-options">
                                <h4 id="product-option-name-<?=$option->id?>"><?=$option->name?></h4>
                                <?php foreach ($option->value as $value) {
                                    if(!$value->photo) $value->photo = IMG_PATH.'noimg.jpg';
                                    ?>
                                    <div class="color-optiond" >
                                        <label class="labelimg" style="background-image: url('<?=$value->photo?>');background-size: 80px 100px;height: 100px;">
                                            <input type="radio" name="product-option-<?=$option->id?>" value="<?=$value->id?>" onchange="updateProductPrice()">
                                        </label>
                                        <h4 style="margin-top: -10px"><?=$value->name?></h4> 
                                    </div>
                                <?php } ?>
                            </div>
                        <?php } 
                    } ?>

                        <!-- Modal -->
                        <div id="myModal" class="modal fade" role="dialog">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                                        <h4 class="modal-title"><?=$this->text('Оберіть диванні подушки')?></h4>
                                    </div>
                                    <div class="modal-body" >
                                        <strong><?=$this->text('Ви можете обрати не більше двох кольорів диванних подушок')?></strong>
                                        <hr>
                                        <div style="padding-bottom: 100%;">
                                        <?php foreach ($product->options as $key => $option) {
                                            $ok = array($option->id.'-podushky-1-katehorii', $option->id.'-podushky-2-katehorii', $option->id.'-podushky-3-katehorii');
                                            if(in_array($key, $ok))
                                                foreach ($option->value as $value) { ?>
                                                <div class="color-optiond">
                                                    <?php if($value->photo){?>
                                                    <label class="labelimgd" style="background-image: url('<?=$value->photo?>');">
                                                        <input type="checkbox" name="pillowcolour">
                                                    </label>
                                                    <?php }else{ ?>
                                                    <label class="labelimgd" style="background-image: url('<?=IMG_PATH?>noimg.jpg');">
                                                        <input type="checkbox" name="pillowcolour">
                                                    </label>
                                                    <?php } ?>
                                                    <h4><?=$value->name?></h4> 
                                                </div>
                                            <?php } } ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <div class="row">
                                <label>
                                <?=$this->text('Вподобати сторінку',0)?> <?php
                                $likes = array();
                                $likes['content'] = $product->id;
                                $likes['name'] = $_SESSION['alias']->name;
                                $likes['link'] = $_SESSION['alias']->link;
                                $likes['image'] = (isset($_SESSION['alias']->images[0])) ? $_SESSION['alias']->images[0] : false;
                                $likes['additionall'] = "<p>{$product->price} грн</p>";
                                $this->load->function_in_alias('likes', '__show_Like_Btn', $likes); ?></label>
                            </div>
                            
                        </div>
                        <div class="row">
                            <?php $this->load->function_in_alias('cart', '__show_btn_add_product', $product); ?>
                        </div>
                    </div>
                </div>
            </div>
            <?php if($_SESSION['alias']->text || $_SESSION['alias']->list){ ?>
            <div class="col-sm-12">
                <div class="tabs-wrapper">
                    <!-- Nav tabs -->
                    <ul class="nav nav-tabs" role="tablist">
                        <?php if($_SESSION['alias']->text){ ?>
                        <li class="active">
                            <a href="#tab-description" aria-controls="tab-description" data-toggle="tab"><?=$this->text('Опис')?></a>
                        </li>
                        <?php } ?>
                    </ul>
                    <!-- Tab panes -->

                    <div class="tab-content">
                        
                        <div class="tab-pane active" id="tab-description">
                            <?=html_entity_decode($_SESSION['alias']->text)?>
                        </div>
                    </div>
                </div>
            </div>
            <?php } ?>
        </div>
    </div>
</section>

<?php if($otherProductsByGroup = $this->shop_model->getProducts($product->group, $product->id)) { ?>

<section class="section related-products second-style no-padding-top">
    <div class="container">
        <div class="section-title text-center">
            <h3><i class="line"></i><?=$this->text('Вас також може зацікавити')?><i class="line"></i></h3>
        </div>
        <div id="related-products">
            <?php foreach ($otherProductsByGroup as $otherProduct) { ?>
            <div class="product" data-product-id="1">
                <div class="inner-product">
                    <?php if($otherProduct->photo != '') { ?>
                    <div class="product-thumbnail">
                        <img src="<?=IMG_PATH.$otherProduct->m_photo?>" class="img-responsive" alt="<?=$otherProduct->name?>">
                    </div>
                    <?php } ?>
                    <div class="product-details text-center">
                        <div class="product-btns">
                            <span data-toggle="tooltip" data-placement="top" title="View">
                                <a href="<?=SITE_URL.$otherProduct->link?>" class="li-icon view-details"><i class="lil-search"></i></a>
                            </span>
                        </div>
                    </div>
                </div>
                <h3 class="product-title"><a href="<?=SITE_URL.$otherProduct->link?>"><?= $otherProduct->article ?></a></h3>
                <p class="product-price">
                    <ins>
                        <span class="amount"><?=$otherProduct->price?> грн </span>
                    </ins>
                    <?php if($otherProduct->old_price != 0 && $otherProduct->old_price > $otherProduct->price) { ?>
                    <del>
                        <span class="amount"><?=$otherProduct->old_price?> грн </span>
                    </del>
                    <?php } ?>
                </p>
            </div>
            <?php } ?>
        </div>
    </div>
</section>
<?php } ?>

<div class="g-popup-wrapper">
    <div class="g-popup g-popup--discount">
        <p class="g-image margin-bottom-15 clear-both"></p>
        <p class="g-name"></p>
        <div class="clear-both">
            <label class="input pull-left padding-right-5">
                <button class="btn btn-default g-popup__close__button" type="button"><?=$this->text('Продовжити')?></button>
            </label>
            <label class="input pull-right">
                <a href="<?=SERVER_URL?>cart" class="btn btn-default" type="button"><?=$this->text('До корзини')?></a>
            </label>
        </div>
    </div>
</div>
<script>
    var productID = <?=$product->id?>;
    var productOptionsChangePrice = [<?=implode(',', $productOptionsChangePrice)?>];
    var SHOP_URL = '<?= SITE_URL.$_SESSION['alias']->alias ?>/';
</script>
<?php
    $_SESSION['alias']->js_load[] = "assets/gritter/js/jquery.gritter.js";
    $_SESSION['alias']->js_load[] = 'js/'.$_SESSION['alias']->alias.'/product.js';
    $_SESSION['alias']->js_load[] = 'js/order.js';
?>
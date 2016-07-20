<link rel="stylesheet" href="<?=SITE_URL?>assets/cube-portfolio/cubeportfolio/custom/custom-cubeportfolio.css">
<link rel="stylesheet" href="<?=SITE_URL?>assets/cube-portfolio/cubeportfolio/css/cubeportfolio.min.css">

<!--=== Breadcrumbs v3 ===-->
<div class="breadcrumbs-v3 img-v1">
    <div class="container text-center">
        <p><?=$this->text('Проект')?></p>
        <h1 style="color:white !important;"><?=$_SESSION['alias']->name?></h1>
    </div>
</div>
<!--=== End Breadcrumbs v3 ===-->

<div class="container">
    <div class="content">
        <?php if(!empty($product->photos)) { ?>
        <!-- Magazine Slider -->
        <div class="carousel slide carousel-v2 margin-bottom-40" id="portfolio-carousel">
            <?php if(count($product->photos) > 1) { ?>
                <ol class="carousel-indicators">
                    <?php for ($i = 0; $i < count($product->photos); $i++) { ?>
                        <li class="<?=($i == 0) ? 'active' : ''?> rounded-x" data-target="#portfolio-carousel" data-slide-to="<?=$i?>"></li>
                    <?php } ?>
                </ol>
            <?php } ?>
            
            <div class="carousel-inner">
                <?php foreach ($product->photos as $photo) { ?>
                    <div class="item <?=($product->photos[0]->id == $photo->id) ? 'active' : ''?>">
                        <img src="<?=IMG_PATH.$_SESSION['option']->folder.'/'.$product->id.'/'.$photo->name?>" alt="<?=$photo->title?>" title="<?=$photo->title?>">
                    </div>
                <?php } ?>
            </div>         

            <?php if(count($product->photos) > 1) { ?>
                <a class="left carousel-control rounded-x" href="#portfolio-carousel" role="button" data-slide="prev">
                    <i class="fa fa-angle-left arrow-prev"></i>
                </a>
                <a class="right carousel-control rounded-x" href="#portfolio-carousel" role="button" data-slide="next">
                    <i class="fa fa-angle-right arrow-next"></i>
                </a>
            <?php } ?>
        </div>
        <!-- End Magazine Slider -->
        <?php } ?>

        <div class="row margin-bottom-60">
            <div class="col-sm-8">
                <div class="headline"><h2><?= html_entity_decode($product->name)?></h2></div>
                <?php if(isset($product->options['2-korotko-pro-proekt']) && $product->options['2-korotko-pro-proekt']->value != '') { ?>
                    <div class="tag-box tag-box-v2">
                        <p><?=nl2br($product->options['2-korotko-pro-proekt']->value)?></p>
                    </div>
                <?php } ?>
                <?php if(isset($product->options['3-zavdannja']) && $product->options['3-zavdannja']->value != '') { ?>
                    <div class="tag-box heading heading-v1 margin-bottom-40">
                        <h2><?=$product->options['3-zavdannja']->name?></h2>
                        <p><?=nl2br($product->options['3-zavdannja']->value)?></p>
                    </div>
                <?php } ?>
                <?php if(isset($product->options['4-skladnist']) && $product->options['4-skladnist']->value != '') { ?>
                    <div class="tag-box heading heading-v1 margin-bottom-40">
                        <h2><?=$product->options['4-skladnist']->name?></h2>
                        <p><?=nl2br($product->options['4-skladnist']->value)?></p>
                    </div>
                <?php } ?>
                <?= html_entity_decode($product->text)?>
            </div>
            <div class="col-sm-4">
                <div class="headline"><h2><?=$this->text('Вартість від: ')?>$<?= $product->price?></h2></div>
                
                <div class="headline"><h2><?=$this->text('Деталі:')?></h2></div>
                <p><?=$this->text('Рубрика: ')?></p>
                <?php if(is_array($product->group)) { foreach ($product->group as $g) {
                    echo "<p><a href=\"".SITE_URL."services/{$g->link}\"><strong>{$g->name}</strong></a> </p>";
                } } ?>
                <?php if(isset($product->options['1-vlasna-adresa']) && $product->options['1-vlasna-adresa']->value != '') { ?>
                    <p><?=$product->options['1-vlasna-adresa']->name?>: <a href="<?=$product->options['1-vlasna-adresa']->value?>" target="_blank"><strong><?=$product->options['1-vlasna-adresa']->value?></strong></a></p>
                <?php } ?>
                <?php if((isset($product->options['5-realizacija']) && $product->options['5-realizacija']->value != '') || (isset($product->options['18-realizacija']) && $product->options['18-realizacija']->value != '')) { ?>
                    <p><?=$product->options['5-realizacija']->name?>: <strong><?=$product->options['5-realizacija']->value.' '.$product->options['18-realizacija']->value?></strong></p>
                <?php } ?>
                <?php if(isset($product->options['19-orientovnyy-chas-realizacii']) && $product->options['19-orientovnyy-chas-realizacii']->value != '') { ?>
                    <p><?=$product->options['19-orientovnyy-chas-realizacii']->name?>: <strong><?=$product->options['19-orientovnyy-chas-realizacii']->sufix.' '.$product->options['19-orientovnyy-chas-realizacii']->value?></strong></p>
                <?php } ?>
                <p><?=$this->text('Статтю додано ')?><strong><?=date('d.m.Y', $product->date_add)?></strong></p>
                <p><?=$this->text('користувачем ')?><strong><?= html_entity_decode($product->user_name)?></strong></p>
            </div>
        </div>        

        <?php 
        if(!empty($otherProductsByGroup)) { ?>
            <div class="headline"><h2><?=$this->text('Схожі проекти:')?></h2></div>
            <?php 
            $limit = 8;
            $i = 0;
            foreach ($otherProductsByGroup as $otherProduct) { 
            ?>
                <div class="cube-portfolio col-lg-3 col-md-3 col-sm-6 col-xs-12">
                    <div id="grid-container"class="cbp-l-grid-agency cbp cbp-caption-active cbp-caption-zoom cbp-cols-4 cbp-ready">
                        <div class="cbp-item"> 
                            <div class="cbp-caption margin-bottom-20 ">
                                <div class="cbp-caption-defaultWrap">
                                    <img src="<?=IMG_PATH.$otherProduct->s_photo?>" alt="<?=$otherProduct->name?>">
                                </div>
                                <div class="cbp-caption-activeWrap">
                                    <div class="cbp-l-caption-alignCenter">
                                        <div class="cbp-l-caption-body">
                                            <ul class="link-captions no-bottom-space">
                                                <li><a href="<?=SITE_URL.$otherProduct->link?>"><i class="rounded-x fa fa-link"></i></a></li>                                        
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="cbp-title-dark">
                                <div class="cbp-l-grid-agency-title"><?=$otherProduct->name?></div>
                                <div class="cbp-l-grid-agency-desc">$<?= html_entity_decode($otherProduct->price)?></div>
                            </div>
                        </div>
                    </div>
                </div>  
            <?php 
                    $i++;
                    if($i == $limit) break;
                }
     
        } ?>
   </div>
</div>


<?php 
    $_SESSION['alias']->js_plugins[] = 'assets/cube-portfolio/cubeportfolio/js/jquery.cubeportfolio.min.js';
    $_SESSION['alias']->js_load[] = 'js/plugins/cube-portfolio/cube-portfolio-4.js';
?>

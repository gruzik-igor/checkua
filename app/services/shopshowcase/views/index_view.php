<link rel="stylesheet" href="<?=SITE_URL?>assets/cube-portfolio/cubeportfolio/custom/custom-cubeportfolio.css">
<link rel="stylesheet" href="<?=SITE_URL?>assets/cube-portfolio/cubeportfolio/css/cubeportfolio.min.css">
<!--=== Breadcrumbs v3 ===-->
<div class="breadcrumbs-v3 img-v1">
    <div class="container text-center">
        <p><?=$this->text('Portfolio', 0)?></p>
        <h1><?=$this->text('Наші Проекти')?></h1>
    </div>
</div>
<!--=== End Breadcrumbs v3 ===-->

<?php if(isset($products)){ ?>
<div class="cube-portfolio container margin-bottom-60">
    <div class="content-xs">
        <div id="filters-container" class="cbp-l-filters-text content-xs">
            <div data-filter="*" class="cbp-filter-item-active cbp-filter-item"> Всі123 </div> 
            <?php 
            if(isset($products)){ 
                $groups = array();
                foreach ($products as $project) {
                    if(!empty($project->group)){ 
                        foreach ($project->group as $group) {
                            $groups[$group->alias] = $group->name.' ';
                        }
                    }
                }
                foreach ($groups as $alias => $name) {
            ?>
                <div data-filter=".<?=$alias?>" class="cbp-filter-item"> <?=$name?> </div>
            <?php } 
            } ?>
        </div>

        <div id="grid-container" class="cbp-l-grid-agency">

            <?php
                foreach($products as $article){
                    $activeGroupsLink = '';
                    $activeGroupsName = '';
                    if(!empty($article->group)){ 
                        foreach ($article->group as $group) {
                            $activeGroupsLink .= $group->alias.' ';
                            $activeGroupsName .= $group->name.' ';
                        }
                    }
            ?>
                    <div class="cbp-item <?=$activeGroupsLink?>">
                        <div class="cbp-caption" style="border: 2px solid white;margin-top:2px">
                            <div class="cbp-caption-defaultWrap">
                                <img src="<?=IMG_PATH.$article->b_photo?>" alt="">
                            </div>
                            <div class="cbp-caption-activeWrap">
                                <div class="cbp-l-caption-alignCenter">
                                    <div class="cbp-l-caption-body">                            
                                        <ul class="link-captions">
                                            <li><a href="<?=SITE_URL.$article->link?>"><i class="rounded-x fa fa-link"></i></a></li>
                                        </ul>
                                        <div class="cbp-l-grid-agency-title"><?=$article->name?></div>
                                        <div class="cbp-l-grid-agency-desc"><?=$activeGroupsName?></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
            <?php } ?>
        </div>
    </div>
</div>
<?php 
    $_SESSION['alias']->js_plugins[] = 'assets/cube-portfolio/cubeportfolio/js/jquery.cubeportfolio.min.js';
    $_SESSION['alias']->js_load[] = 'assets/cube-portfolio/cubeportfolio/cube-portfolio-2.js';
} ?>
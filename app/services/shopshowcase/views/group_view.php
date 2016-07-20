<!--=== Breadcrumbs ===-->
<div class="breadcrumbs">
    <div class="container">
        <h1 class="pull-left"><?=$template['menu_services']?></h1>
        <ul class="pull-right breadcrumb">
            <li><a href="<?=SITE_URL?>"><?=$template['menu_main']?></a></li>
            <li><a href="<?=SITE_URL?>services"><?=$template['menu_services']?></a></li>
            <?php
            if(!empty($service->parents)) {
                foreach ($service->parents as $parent) {
                    echo '<li><a href="'.SITE_URL.$_SESSION['alias']->alias.'/'.$parent->link.'">'.$parent->name.'</a></li>';
                }
            }
            ?>
            <li class="active"><?=$_SESSION['alias']->name?></li>
        </ul>
    </div>
</div><!--/breadcrumbs-->
<!--=== End Breadcrumbs ===-->

<!--=== Content Part ===-->
<div class="container content">
    <div class="row">
        <div class="col-md-6">
            <h1><?=$_SESSION['alias']->name?></h1>
            <?php if($_SESSION['alias']->list != '') { ?>
                <div class="tag-box tag-box-v2">
                    <p><?=$_SESSION['alias']->list?></p>
                </div>
            <?php } ?>
            <p> <?= html_entity_decode($_SESSION['alias']->description)?></p>
            <p> <?= html_entity_decode($_SESSION['alias']->text)?></p>
        </div>
        <?php if($group->photo > 0) { ?>
            <div class="col-md-6">
                <img src="<?=SITE_URL?>images/portfolio/groups/b_<?=$group->photo?>.jpg" style="width:100%" alt="<?=$_SESSION['alias']->name?>">
            </div>
        <?php } ?>
    </div>
</div>

<?php
if($products){ 
?>
    <div class="bg-grey">
        <div class="container content-sm">
            <div class="text-center margin-bottom-50">
                <h2 class="title-v2 title-center">Наші проекти:</h2>
                <p class="space-lg-hor">
                    If you are going to use a <span class="color-green">passage of Lorem Ipsum</span>, you need to be sure there isn't anything embarrassing hidden in the middle of text.
                    <a href="<?=SITE_URL?>portfolio">До всіх проектів Web Spirit Creative Agency</a>
                </p>
            </div>

            <div class="row news-v1">
                <?php foreach ($products as $project) { ?>
                    <div class="col-md-4 md-margin-bottom-40">
                        <div class="news-v1-in bg-color-white">
                            <?php if($project->photo != '') { ?>
                                <a href="<?=SITE_URL.'portfolio/'.$project->link?>">
                                    <img class="img-responsive" src="<?=IMG_PATH?>portfolio/<?=$project->id.'/m_'.$project->photo?>" alt="<?=$project->name?>">
                                </a>
                            <?php } ?>
                            <h3 class="font-normal"><a href="<?=SITE_URL.'portfolio/'.$project->link?>"><?=$project->name?></a></h3>
                            <?php
                            if($project->list != ''){
                                echo $project->list;
                            } elseif($project->text != ''){
                                echo "<p>";
                                echo mb_substr(html_entity_decode($project->text), 0, 150, 'utf-8');
                                echo "</p>";
                            }
                            ?>
                            <ul class="list-inline news-v1-info no-margin-bottom">
                                <li><span>By</span> <a href="<?=SITE_URL?>about"><?=$project->user_name?></a></li>
                                <li>|</li>
                                <li><i class="fa fa-clock-o"></i> <?=date('d.m.Y H:i', $project->date_edit)?></li>
                            </ul>
                        </div>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>
<?php 
} else { 
    $groups = $this->shop_model->getGroups($group->id);
    if($groups){ 
        echo '<div class="container content-sm"><div class="row">';
        $i = 0;
        foreach($groups as $group){
            if($group->class != ''){
                if($i % 2 == 0 && $i > 0) echo "</div></div><div class='container content-sm'><div class='row'>";
    ?> 
                <div class="col-sm-6 sm-margin-bottom-40">
                    <div class="funny-boxes <?=$group->class?>">
                        <div class="row">
                            <?php if($group->photo > 0) { ?>
                            <div class="col-md-4 funny-boxes-img">
                                <a href="<?=SITE_URL.$_SESSION['alias']->alias.'/'.$group->link?>">
                                    <img class="img-responsive" src="<?=SITE_URL?>images/portfolio/groups/l_<?=$group->photo?>.jpg" alt="">
                                </a>
                            </div>
                            <div class="col-md-8">
                                <h2><a href="<?=SITE_URL.$_SESSION['alias']->alias.'/'.$group->link?>"> <?=html_entity_decode($group->name)?> </a></h2>
                                <?php
                                if($group->list != ''){
                                    echo $group->list;
                                } elseif($group->text != ''){
                                    echo "<p>";
                                    echo mb_substr(html_entity_decode($group->text), 0, 200, 'utf-8');
                                    if(mb_strlen(html_entity_decode($group->text), 'utf-8') > 200) echo "..";
                                    echo "</p>";
                                }
                                ?>                  
                            </div>
                            <?php } else { ?>
                                <h2><a href="<?=SITE_URL.$_SESSION['alias']->alias.'/'.$group->link?>"> <?=html_entity_decode($group->name)?> </a></h2>
                                <?php
                                if($group->list != ''){
                                    echo $group->list;
                                } elseif($group->text != ''){
                                    echo "<p>";
                                    echo mb_substr(html_entity_decode($group->text), 0, 300, 'utf-8');
                                    if(mb_strlen(html_entity_decode($group->text), 'utf-8') > 300) echo "..";
                                    echo "</p>";
                                }
                                ?>                        
                            <?php } ?>
                        </div>     
                                           
                    </div>      
                </div>
<?php
                $i++; 
            }
        }
        echo "</div></div>";
    }
}
?>
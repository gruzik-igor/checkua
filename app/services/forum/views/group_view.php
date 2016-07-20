    <div id="page-title" class="page-title has-bg">
        <div class="bg-cover"><img src="<?=SITE_URL?>assets/img/cover.jpg" alt="" /></div>
        <div class="container">
         <!--    <h1>Блог</h1>
            <p><?=$_SESSION['alias']->title?></p> -->
        </div>
    </div>

    <div id="content" class="content">
 
        <div class="container">
            <div class="row row-space-30">
                <div class="col-md-9">
                        <h1 class="post-title" style="padding-bottom: 40px">
                            <?=$_SESSION['alias']->name?>
                        </h1>
                    <ul class="post-list">

                        <?php if($articles) {
                            $i = 0;
                         foreach ($articles as $article) { ?>
<?php 
$month = array();
$month[1] = 'Січень';
$month[2] = 'Лютий';
$month[3] = 'Березень';
$month[4] = 'Квітень';
$month[5] = 'Травень';
$month[6] = 'Червень';
$month[7] = 'Липень';
$month[8] = 'Серпень';
$month[9] = 'Вересень';
$month[10] = 'Жовтень';
$month[11] = 'Листопад';
$month[12] = 'Грудень';
$m = $month[date( "n", $article->date_add)];
 ?>
                              <li>
                            <!-- begin post-left-info -->
                            <div class="post-left-info">
                                <div class="post-date">
                                    <span class="day"><?=date( "d", $article->date_add)?></span>
                                    <span class="month"><?=$m?></span>
                                </div>
                            </div>
                            <div class="post-content">
             
                                            <?php foreach ($article->photos as $photo) { ?>
                                            <div class="item <?php if($photo->id == $article->photos[0]->id){echo("active");}?>">
                                                <a href="<?=SITE_URL.$article->link?>">
                                                    <img src="<?=IMG_PATH.$photo->path?>" style="width:100%" alt="<?=$photo->title?>" title="<?=$photo->title?>"/>
                                                </a>
                                            </div>
                                            <?php } ?>
            
                                <div class="post-info">
                                    <h4 class="post-title">
                                        <a href="<?=SITE_URL.$article->link?>"><?=$article->name?></a>
                                    </h4>
                                    <div class="post-by">
                                        Автор <?= html_entity_decode($article->user_name)?><span class="divider">|</span> Категорія:
                                        <a href="<?=$group->alias?>"><?=html_entity_decode($article->group_name)?></a>

                                    </div>
                                </div>
                                <div class="read-btn-container">
                                    <a href="<?=SITE_URL.$article->link?>">Читати далі <i class="fa fa-angle-double-right"></i></a>
                                </div>
                            </div>
                        </li>
                        <?php } } ?>
                    </ul>
                </div>
                <div class="col-md-3">
                    <div class="section-container">
                        <form action="<?=SITE_URL?>search">
                            <div class="input-group sidebar-search">
                                <input type="text" name="by" class="form-control" placeholder="<?=$this->text('Шукати', 0)?>" required>
                                <span class="input-group-btn">
                                    <button class="btn btn-inverse" type="button"><i class="fa fa-search"></i></button>
                                </span>
                            </div>
                        </form>
                    </div>
                     <?php $groups = $this->load->function_in_alias('forum', '__get_Groups', array('limit' => 6));?>
                    <div class="section-container">
                        <h4 class="section-title"><span>Категорії</span></h4>
                        <ul class="sidebar-list">
                        <?php foreach ($groups as $group) {?>
                            <li><a href="<?=SITE_URL.'forum/'.$group->alias?>"><?=$group->name?></a></li>
                        <?php } ?>
                        </ul>
                    </div>
                    <div class="section-container">
                        <h4 class="section-title"><span>Останні записи</span></h4>
                        <?php $art = $this->load->function_in_alias('forum', '__get_Articles', array('limit' => 6));?>
                        <ul class="sidebar-recent-post">
                         <?php if($art) {
                         foreach ($art as $a) { ?>
                            <li>
                                <div class="info">
                                    <h4 class="title"><a href="<?=SITE_URL.$a->link?>"><?=$a->name?></a></h4>
                                    <div class="date"><?=date( "H:i d.m.Y", $a->date_add)?></div>
                                </div>
                            </li>
                            <?php } } ?>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

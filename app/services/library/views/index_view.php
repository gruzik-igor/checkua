    <div id="page-title" class="page-title has-bg">
        <div class="bg-cover"><img src="assets/img/cover.jpg" alt="" /></div>
        <div class="container">

        </div>
    </div>

    <div id="content" class="content">
        <!-- begin container -->
        <div class="container">
            <!-- begin row -->
            <div class="row row-space-30">
                <!-- begin col-9 -->
                <div class="col-md-9">
                    <!-- begin post-list -->
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
                               <!--  <div class="post-likes">
                                    <i class="fa fa-heart-o"></i>
                                    <span class="number">520</span>
                                </div>  -->
                            </div>
                            <!-- end post-left-info -->
                            <!-- begin post-content -->
                            <div class="post-content">
                                <!-- begin post-image -->
                                <div class="post-image post-image-with-carousel">
                                    <!-- begin carousel -->
                                    <div id="carousel-post-<?=$article->id?>" class="carousel slide" data-ride="carousel">
                                        <!-- begin carousel-indicators -->
                                        <ol class="carousel-indicators">
                                        <?php for ($i=0; $i < count($article->photos); $i++) { ?>
                                            <li data-target="#carousel-post-<?=$article->id?>" data-slide-to="<?=$i?>" class="<?php if($i == 0){echo("active");}?>"></li>
                                        <?php } ?>
                                        </ol>
                                        <!-- end carousel-indicators -->
                                        <!-- begin carousel-inner -->
                                        <div class="carousel-inner">
                                            <?php foreach ($article->photos as $photo) { ?>

                                            <div class="item <?php if($photo->id == $article->photos[0]->id){echo("active");}?>">
                                                <a href="<?=SITE_URL.$article->link?>">
                                                    <img src="<?=IMG_PATH.$photo->path?>" style="width:100%" alt="<?=$photo->title?>" title="<?=$photo->title?>" />
                                                </a>
                                            </div>
                                            <?php } ?>
                                        </div>
                                        <!-- end carousel-inner -->
                                        <!-- begin carousel-control -->
                                        <a class="left carousel-control" href="#carousel-post-<?=$article->id?>" role="button" data-slide="prev">
                                            <span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span>
                                        </a>
                                        <a class="right carousel-control" href="#carousel-post-<?=$article->id?>" role="button" data-slide="next">
                                            <span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span>
                                        </a>
                                        <!-- end carousel-control -->
                                    </div>
                                    <!-- end carousel -->
                                </div>
                                <div class="post-info">
                                    <h4 class="post-title">
                                        <a href="<?=SITE_URL.$article->link?>"><?=$article->name?></a>
                                    </h4>
                                    <div class="post-by">
                                        Автор <?= html_entity_decode($article->user_name)?><span class="divider">|</span> Категорія:
                                        <?php foreach ($groups as $group) {
                                            if ($article->group_name == $group->name){?>
                                        <a href="<?=SITE_URL.'blog/'.$group->alias?>"><?=html_entity_decode($article->group_name)?></a>
                                        <?php } } ?>

                                    </div>
                                    <div class="post-desc">
                                        <?=html_entity_decode($this->data->getShortText($article->list, 300))?>
                                    </div>
                                </div>
                                <!-- end post-info -->
                                <!-- begin read-btn-container -->
                                <div class="read-btn-container">
                                    <a href="<?=SITE_URL.$article->link?>">Читати далі <i class="fa fa-angle-double-right"></i></a>
                                </div>
                                <!-- end read-btn-container -->
                            </div>
                            <!-- end post-content -->
                        </li>
                        <?php } } ?>
                    </ul>

                </div>
                <!-- end col-9 -->
                <!-- begin col-3 -->
                <div class="col-md-3">
                    <!-- begin section-container -->
                    <div class="section-container">
                        <form action="<?=SITE_URL?>search">
                            <div class="input-group sidebar-search">
                                <input type="text" name="by" class="form-control" placeholder="<?=$this->text('Шукати', 0)?>" required>
                                <span class="input-group-btn">
                                    <button class="btn btn-inverse" type="submit"><i class="fa fa-search"></i></button>
                                </span>
                            </div>
                        </form>
                    </div>
                    <!-- end section-container -->
                    <!-- begin section-container -->
                    <div class="section-container">
                        <h4 class="section-title"><span>Категорії</span></h4>
                        <ul class="sidebar-list">
                        <?php foreach ($groups as $group) {?>
                            <li><a href="<?=SITE_URL.'blog/'.$group->alias?>"><?=$group->name?></a></li>
                        <?php } ?>
                        </ul>
                    </div>
                    <!-- end section-container -->
                    <!-- begin section-container -->
                    <div class="section-container">
                        <h4 class="section-title"><span>Останні записи</span></h4>
                        <ul class="sidebar-recent-post">
                         <?php if($articles) {
                         foreach ($articles as $article) { ?>
                            <li>
                                <div class="info">
                                    <h4 class="title"><a href="<?=SITE_URL.$article->link?>"><?=$article->name?></a></h4>
                                    <div class="date"><?=date( "H:i d.m.Y", $article->date_add)?></div>
                                </div>
                            </li>
                            <?php } } ?>
                        </ul>
                    </div>
                </div>
                <!-- end col-3 -->
            </div>
            <!-- end row -->
        </div>
        <!-- end container -->
    </div>

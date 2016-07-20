    <div id="page-title" class="page-title has-bg">
        <div class="bg-cover"><img src="<?=SITE_URL?>assets/img/cover.jpg" alt="" /></div>
        <div class="container">
           <!--  <h1>Блог</h1>
            <p><?=$_SESSION['alias']->name?></p> -->
        </div>
    </div>
    <div id="content" class="content">
        <!-- begin container -->
        <div class="container">
            <!-- begin row -->
            <div class="row row-space-30">
            <!-- forum All Posts -->
            <div class="col-md-9">
                <!-- News v3 -->
                <div class="post-detail section-container">
                        <ul class="breadcrumb">
                            <li><a href="<?=SITE_URL?>">Головна</a></li>
                            <li><a href="<?=SITE_URL?>forum">Форум</a></li>
                            <?php foreach ($article->parents as $link) {?>
                            <li><a href="<?=SITE_URL?>forum/<?=$link->alias?>"><?=$article->group_name?></a></li>
                            <?php } ?>
                            <li class="active"><?=$_SESSION['alias']->name?></li>
                        </ul>
                        <h1 class="post-title">
                            <?=$_SESSION['alias']->name?>
                        </h1>
                        <div class="post-by">
                        </div>
                            <div class="carousel-inner">
                                <?php foreach ($article->photos as $photo) { ?>
                                    <img src="<?=IMG_PATH.$photo->path?>" style="width:100%" alt="<?=$photo->title?>" title="<?=$photo->title?>"/>
                                <?php } ?>
                            </div>



                            <div class="post-desc" style="background: #F0EFEF;box-shadow: 0 0 20px rgba(0,0,0,0.6);padding: 10px;">
                                <div style="min-height: 100px">
                                    <div class="col-sm-2">
                                        <img src="<?=IMG_PATH?>Fake.png" style="width: 100%" alt="">
                                    </div>
                                    <div class="col-sm-10">
                                        <p style="font-size: 18px; color:#383838"><?= html_entity_decode($article->list)?></p>
                                        <p style="font-size: 15px"><?= html_entity_decode($article->text)?></p>
                                    </div>
                                </div>
                                    <?php if(!empty($article->videos)) {
                                         echo('<div class="margin-bottom-20 embed-responsive embed-responsive-16by9">');
                                    $this->load->forum('video');
                                    $this->video->show_many($article->videos);
                                    echo('</div>');
                                    } ?>
                            </div>



                    </div>
                        <hr>

                                <script type="text/javascript" src="//yastatic.net/share/share.js" charset="utf-8"></script><div class="yashare-auto-init" data-yashareL10n="ua" data-yashareType="small" data-yashareQuickServices="vkontakte,facebook,twitter,gplus" data-yashareTheme="counter"></div>    
                    </div>
            <div class="col-md-3">
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
                <?php if(!empty($otherArticlesByGroup)) { ?>
                        <h4 class="section-title"><span>Схожі обговорення</span></h4>                    
                        <?php 
                        $limit = 4; $i = 0;
                        foreach ($otherArticlesByGroup as $otherProduct) { 
                            if($otherProduct->id != $article->id){ ?>
                            <div class="cbp-item">
                                <div class="cbp-caption margin-bottom-20">
                                    <div class="cbp-caption-defaultWrap">
                                        <a href="<?=SITE_URL.$otherProduct->link?>">
                                        <img style="max-width:100%" src="<?=IMG_PATH.$otherProduct->photo?>" alt="<?=$otherProduct->name?>">
                                        </a>
                                    </div>
                                </div>
                                <div class="cbp-title-dark">
                                    <a href="<?=SITE_URL.$otherProduct->link?>">
                                    <div class="cbp-l-grid-agency-title" style="color:black; font-size:20px;"><?=$otherProduct->name?></div>
                                    </a>
                                    <div>Автор статті: <?=$article->author_add_name?></div>
                                    <div class="date"><?=date( "H:i d.m.Y", $article->date_add)?></div>
                                </div>
                            </div>
                            <hr>
                        <?php 
                        $i++;
                        if($i == $limit) break;
                    } }?>             
                <?php } ?>
            </div>            
        </div>
    </div>
</div>

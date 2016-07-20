    <div class="scrollToTop"><i class="icon-up-open-big"></i></div>

    <div id="page-title" class="page-title has-bg">
        <div class="bg-cover"><img src="assets/img/cover5.jpg" alt="" /></div>
        <div class="container">
            <h1><?=$_SESSION['alias']->name?></h1>
            <p>Автори блогу - досвідчені українські спортсмени та інструктори Ігор Загурний і Павло Портянко</p>
        </div>
    </div>


      <!-- main-container start -->
      <!-- ================ -->
    <div id="content" class="content">
        <!-- begin container -->
        <div class="container">
            <!-- begin row -->
            <div class="row row-space-30">
                <!-- begin col-9 -->

            <!-- main start -->
            <!-- ================ -->
                <div class="col-md-9">

                    <!-- end section-container -->
                    <!-- begin section-container -->

                    <div class="section-container">
                        <h4 class="section-title m-b-20"><span>Z&P GROUP</span></h4>


                    <div  class="col-sm-12" style="padding:30px 0">
                        <p>
                            <?=html_entity_decode($_SESSION['alias']->list)?>
                        </p>
                    </div>
                    <div class="col-sm-12">
                  <?php if ($page->photos != ''){ ?>
                    <?php foreach ($page->photos as $p ) { ?>
                    <div class="col-sm-6" style="margin-top:30px">
                        <img src="<?=IMG_PATH.$p->photo?>" style="width:100%" alt="<?=$p->title?>">
                        <p><?=html_entity_decode($p->title)?></p>
                    </div>
                    <?php } }?>
                    </div>
                    <div  class="col-sm-12" style="padding:30px 0">
                        <p>
                            <?=html_entity_decode($_SESSION['alias']->text)?>
                        </p>
                    </div>
                        <!-- begin row -->
                        <div class="row row-space-30 f-s-12 text-inverse">
                            <!-- begin col-8 -->
                            <div class="col-md-12">
                                <form class="form-horizontal">
                                    <div class="form-group">
                                        <label class="control-label col-md-3">Ім'я <span class="text-danger">*</span></label>
                                        <div class="col-md-9">
                                            <input type="text" class="form-control">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-md-3">Email <span class="text-danger">*</span></label>
                                        <div class="col-md-9">
                                            <input type="text" class="form-control">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-md-3">Повідомлення <span class="text-danger">*</span></label>
                                        <div class="col-md-9">
                                            <textarea class="form-control" rows="10"></textarea>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-md-3"></label>
                                        <div class="col-md-9 text-left">
                                            <button type="submit" class="btn btn-inverse btn-lg btn-block">Надіслати</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                            <!-- end col-4 -->
                        </div>
                        <!-- end row -->
                    </div>
                    <!-- end section-container -->
                </div>

            <!-- portfolio sidebar start -->
                <div class="col-md-3">
                    <!-- begin section-container -->
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
                    <!-- end section-container -->
                    <!-- begin section-container -->

                    <!-- end section-container -->
                    <!-- begin section-container -->
                     <?php $last_articles = $this->load->function_in_alias('blog', '__get_Articles', array('limit' => 10));?>
                    <div class="section-container">
                        <h4 class="section-title"><span>Останні записи</span></h4>
                        <ul class="sidebar-recent-post">
                         <?php if($last_articles) {
                         foreach ($last_articles as $article) { ?>
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
            </div>
          </div>
        </div>

 
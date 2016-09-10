<section>
    <div class="container">

        <div class="row">

            <!-- LEFT -->
            <div class="col-md-9 col-sm-9">

                <h1 class="blog-post-title"><?=$_SESSION['alias']->name?></h1>
                <ul class="blog-post-info list-inline">
                    <li>
                        <a href="#">
                            <i class="fa fa-clock-o"></i> 
                            <span class="font-lato"><?=date('d.m.Y H:i', $page->date_edit)?></span>
                        </a>
                    </li>
                    <li>
                        <a href="#">
                            <i class="fa fa-user"></i> 
                            <span class="font-lato"><?=$page->author_edit_name?></span>
                        </a>
                    </li>
                </ul>

                <?php if($_SESSION['alias']->list != '') {
                    echo ("<strong>{$_SESSION['alias']->list}</strong>");
                }
                if(!empty($page->photos)) { ?>

                <!-- OWL SLIDER -->
                <div class="owl-carousel buttons-autohide controlls-over" data-plugin-options='{"items": 1, "autoPlay": 4500, "autoHeight": false, "navigation": true, "pagination": true, "transitionStyle":"fadeUp", "progressBar":"false"}'>
                    <?php foreach ($page->photos as $photo) { ?>
                        <a class="lightbox" href="<?=IMG_PATH.$photo->path?>" data-plugin-options='{"type":"image"}'>
                            <img class="img-responsive" src="<?=IMG_PATH.$photo->b_path?>" alt="<?=$photo->title?>" />
                        </a>
                    <?php } ?>
                </div>
                <!-- /OWL SLIDER -->
                <?php }

                echo($_SESSION['alias']->text);

                if(!empty($page->videos)) {
                    echo('<div class="margin-bottom-20 embed-responsive embed-responsive-16by9">');
                    $this->load->library('video');
                    $this->video->show_many($page->videos);
                    echo('</div>');
                }
                ?>
            </div>

            <!-- RIGHT -->
            <div class="col-md-3 col-sm-3">

				<div class="fb-page margin-bootm-20" data-href="https://www.facebook.com/%D0%93%D0%9E%D0%A6%D0%B5%D0%BD%D1%82%D1%80-%D0%AE%D0%9D%D0%95%D0%A1%D0%9A%D0%9E-193253730718669/?fref=ts" data-tabs="timeline" data-width="250" data-small-header="false" data-adapt-container-width="true" data-hide-cover="false" data-show-facepile="true"></div>

                <hr>

                <!-- VK Widget -->
                <div id="vk_groups" class="margin-top-20"></div>
                <script type="text/javascript">
                    VK.Widgets.Group("vk_groups", {mode: 2, width: "250", height: "500"}, 3588995);
                </script>

            </div>

        </div>


    </div>
</section>
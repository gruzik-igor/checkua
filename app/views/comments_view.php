<div class="container padding-top-40" id="comments">
    <div class="heading heading-v1 margin-bottom-20">
        <h2><?=$_SESSION['alias']->name?></h2>
    </div>

    <div class="illustration-v2 margin-bottom-60">
        <?php $this->load->model("wl_comments_model");
        $content = 0;
        $alias = $_SESSION['alias']->id;
        $image_name = 'comment';
        $comments = false;
        require_once '@wl_comments/index_view.php';
         ?>
    </div>
</div>

<link rel="stylesheet" href="<?=SERVER_URL?>assets/blueimp/css/blueimp-gallery.min.css">
<?php $_SESSION['alias']->js_load[] = "assets/blueimp/js/jquery.blueimp-gallery.min.js"; ?>
<div id="blueimp-gallery" class="blueimp-gallery blueimp-gallery-controls" data-filter=":even">
    <div class="slides"></div>
    <h3 class="title"></h3>
    <a class="prev">‹</a>
    <a class="next">›</a>
    <a class="close">×</a>
    <a class="play-pause"></a>
    <ol class="indicator"></ol>
</div>
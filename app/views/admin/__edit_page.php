<div class="panel-body">
    <ul class="nav nav-tabs">
        <?php
        if($_SESSION['language']) {
            foreach ($_SESSION['all_languages'] as $lang) {
                echo("<li ");
                if($_SESSION['language'] == $lang)
                    echo ('class="active"');
                echo("><a href=\"#tab-{$lang}\" data-toggle=\"tab\" aria-expanded=\"true\">{$lang}</a></li>");
            }
        }
        else
            echo('<li class="active"><a href="#tab-ntkd" data-toggle="tab" aria-expanded="true">Назва та опис</a></li>');
        if(isset($_SESSION['option']->folder) && $_SESSION['option']->folder != '') {
        ?>
            <li><a href="#tab-photo" data-toggle="tab" aria-expanded="true">Фото</a></li>
        <?php } ?>
        <li><a href="#tab-video" data-toggle="tab" aria-expanded="true">Відео</a></li>
        <?php if(isset($_SESSION['option']->folder) && $_SESSION['option']->folder != '') { ?>
            <li><a href="#tab-audio" data-toggle="tab" aria-expanded="true">Аудіо</a></li>
        <?php } ?>
    </ul>

    <div class="tab-content">
        <?php if($_SESSION['language']) { foreach ($_SESSION['all_languages'] as $language) { ?>
            <div class="tab-pane fade <?=($_SESSION['language'] == $language) ? 'active in' : ''?>" id="tab-<?=$language?>">
                <?php require 'wl_ntkd/__tab_ntkdt.php'; ?>
            </div>
        <?php } } else { ?>
      		<div class="tab-pane fade active in" id="tab-ntkd">
      			<?php require 'wl_ntkd/__tab_ntkdt.php'; ?>
      		</div>
        <?php } if(isset($_SESSION['option']->folder) && $_SESSION['option']->folder != '') { ?>
            <div class="tab-pane fade" id="tab-photo">
                <?php require_once 'wl_images/__tab-photo.php'; ?>
            </div>
        <?php } ?>
        <div class="tab-pane fade" id="tab-video">
            <?php require_once 'wl_video/__tab-video.php'; ?>
        </div>
        <?php if(isset($_SESSION['option']->folder) && $_SESSION['option']->folder != '') { ?>
            <div class="tab-pane fade" id="tab-audio">
                <?php require_once 'wl_audio/__tab-audio.php'; ?>
            </div>
        <?php } ?>
    </div>
</div>

<script type="text/javascript">
    var ALIAS_ID = <?=$_SESSION['alias']->id?>;
    var CONTENT_ID = <?=$_SESSION['alias']->content?>;
    var ALIAS_FOLDER = '<?=$_SESSION['option']->folder?>';
    var PHOTO_FILE_NAME = '<?=(isset($PHOTO_FILE_NAME)) ? $PHOTO_FILE_NAME : $_SESSION['alias']->alias?>';
    var PHOTO_TITLE = '<?=$_SESSION['alias']->name?>';
    <?php if(!isset($ADDITIONAL_TABLE)) { ?>
        var ADDITIONAL_TABLE = false;
        var ADDITIONAL_TABLE_ID = false;
        var ADDITIONAL_FIELDS = false;
    <?php } else { ?>
        var ADDITIONAL_TABLE = '<?=$ADDITIONAL_TABLE?>';
        var ADDITIONAL_TABLE_ID = <?=$ADDITIONAL_TABLE_ID?>;
        var ADDITIONAL_FIELDS = '<?=$ADDITIONAL_FIELDS?>';
    <?php }
    $_SESSION['alias']->js_load[] = 'assets/ckeditor/ckeditor.js';
    $_SESSION['alias']->js_load[] = 'assets/ckfinder/ckfinder.js';
    $_SESSION['alias']->js_load[] = 'assets/white-lion/__edit_page.js';
    ?>
</script>
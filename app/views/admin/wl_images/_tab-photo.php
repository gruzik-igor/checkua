<!-- The fileupload-buttonbar contains buttons to add/delete files and start/cancel the upload -->
<div id="fileupload" class="fileupload-buttonbar">
    <!-- The fileinput-button span is used to style the file input field as button -->
    <span class="btn btn-success fileinput-button f-left">
        <i class="glyphicon glyphicon-plus"></i>
        <span>Додати фото</span>
        <input type="file" name="photos[]" multiple>
    </span>
    
    <!-- The global file processing state -->
    <span class="fileupload-process"></span>


    <!-- The global progress state -->
    <div class="col-lg-5 fileupload-progress fade">
        <!-- The global progress bar -->
        <div class="progress progress-striped active" role="progressbar" aria-valuemin="0" aria-valuemax="100">
            <div class="progress-bar progress-bar-success" style="width:0%;"></div>
        </div>
        <!-- The extended global progress state -->
        <div class="progress-extended">&nbsp;</div>
    </div>

    <!-- The table listing the files available for upload/download -->
    <table role="presentation" class="table table-striped"><tbody class="files"></tbody></table>
</div>

<div class="clear"> </div>

<?php if(!empty($PHOTOS)) { ?>
    <table id="PHOTOS" class="table">
        <tbody class="files">
            <?php foreach($PHOTOS as $photo) { ?>
                <tr id="photo-<?=$photo->id?>" class="template-download fade in">
                    <td class="preview">
                        <a href="<?=IMG_PATH.$photo->file_address?>">
                            <img src="<?=IMG_PATH.$photo->a_file_address?>">
                        </a>
                    </td>
                    <td>
                        <textarea name="title" onChange="savePhoto(<?=$photo->id?>, this)"><?=$photo->title?></textarea>
                    </td>
                    <td class="navigation">
    	                <button name="main" class="btn btn-warning PHOTO_MAIN" onClick="savePhoto(<?=$photo->id?>, this)" <?= (isset($PHOTO_MAIN) && $PHOTO_MAIN == $photo->file_address) ? 'disabled="disabled"' : '' ?>>
    					    <i class="glyphicon glyphicon-eye-open"></i>
    					    <span>Головне</span>
    					</button>
                        <button class="btn btn-danger" onClick="deletePhoto(<?=$photo->id?>)">
                            <i class="glyphicon glyphicon-trash"></i>
                            <span>Видалити</span>
                        </button>
                        <br>
                        Додано: <?=date('d.m.Y H:i', $photo->date_add)?>
                        <?=mb_substr($photo->user_name, 0, 16, 'utf-8')?>
                        <br>
                        <span id="pea-saveing-<?=$photo->id?>" class="saveing"><img src="<?=SITE_URL?>style/admin/images/icon-loading.gif">Зберігання..</span>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
<?php } ?>

<!-- The blueimp Gallery widget -->
<div id="blueimp-gallery" class="blueimp-gallery blueimp-gallery-controls" data-filter=":even">
    <div class="slides"></div>
    <h3 class="title"></h3>
    <a class="prev">‹</a>
    <a class="next">›</a>
    <a class="close">×</a>
    <a class="play-pause"></a>
    <ol class="indicator"></ol>
</div>
<!-- The template to display files available for upload -->
<script id="template-upload" type="text/x-tmpl">
{% for (var i=0, file; file=o.files[i]; i++) { %}
    <tr class="template-upload fade">
        <td>
            <span class="preview"></span>
        </td>
        <td>
            <p class="name">{%=file.name%}</p>
            <strong class="error text-danger"></strong>
        </td>
        <td>
            <p class="size">Processing...</p>
            <div class="progress progress-striped active" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0"><div class="progress-bar progress-bar-success" style="width:0%;"></div></div>
        </td>
        <td>
            {% if (!i && !o.options.autoUpload) { %}
                <button class="btn btn-primary start" disabled style="display:none">
                    <i class="glyphicon glyphicon-upload"></i>
                    <span>Start</span>
                </button>
            {% } %}
            {% if (!i) { %}
                <button class="btn btn-warning cancel">
                    <i class="glyphicon glyphicon-ban-circle"></i>
                    <span>Скасувати</span>
                </button>
            {% } %}
        </td>
    </tr>
{% } %}
</script>
<!-- The template to display files available for download -->
<script id="template-download" type="text/x-tmpl">
{% for (var i=0, file; file=o.files[i]; i++) { %}
    <tr id="photo-{%=file.id%}" class="template-download fade">
        <td class="preview">
            {% if (file.thumbnailUrl) { %}
                <a href="{%=file.url%}" data-gallery><img src="{%=file.thumbnailUrl%}"></a>
            {% } %}
        </td>
        <td>
            {% if (file.error) { %}
                <div><span class="label label-danger">Error</span> {%=file.error%}</div>
            {% } else { %}
                <textarea name="title" onChange="savePhoto({%=file.id%}, this)"><?=$_SESSION['alias']->name?></textarea>
            {% } %}
        </td>
        <td class="navigation">
        	<button name="main" class="btn btn-warning PHOTO_MAIN" onClick="savePhoto({%=file.id%}, this)" disabled="disabled">
					    <i class="glyphicon glyphicon-eye-open"></i>
					    <span>Головне</span>
					</button>
            <button class="btn btn-danger delete" onClick="deletePhoto({%=file.id%})">
                <i class="glyphicon glyphicon-trash"></i>
                <span>Видалити</span>
            </button>
            <br>
            Додано: {%=file.date%}
            <a href="<?=SITE_URL?>profile/<?=$_SESSION['user']->id?>"><?=mb_substr($_SESSION['user']->name, 0, 16, 'utf-8')?></a>
            <br>
            <span id="pea-saveing-{%=file.id%}" class="saveing"><img src="<?=SITE_URL?>style/admin/images/icon-loading.gif">Зберігання..</span>
        </td>
    </tr>
{% } %}
</script>

<?php 
$_SESSION['alias']->js_load[] = "assets/blueimp/js/vendor/jquery.ui.widget.js";
// $_SESSION['alias']->js_load[] = "assets/blueimp/js/jquery.iframe-transport.js";
$_SESSION['alias']->js_load[] = "assets/blueimp/js/jquery.fileupload.js";
$_SESSION['alias']->js_load[] = "assets/blueimp/js/jquery.fileupload-process.js";
$_SESSION['alias']->js_load[] = "assets/blueimp/js/jquery.fileupload-image.js";
$_SESSION['alias']->js_load[] = "assets/blueimp/js/jquery.fileupload-validate.js";
$_SESSION['alias']->js_load[] = "assets/blueimp/js/jquery.fileupload-ui.js";
$_SESSION['alias']->js_load[] = "assets/blueimp/js/jquery.blueimp-gallery.min.js";
// $_SESSION['alias']->js_load[] = "assets/blueimp/js/jquery.fileupload-audio.js";
// $_SESSION['alias']->js_load[] = "assets/blueimp/js/jquery.fileupload-video.js";
// $_SESSION['alias']->js_load[] = "assets/blueimp/js/cors/jquery.xdr-transport.js";
$_SESSION['alias']->js_load[] = 'js/admin/_tab-photo.js';
?>


<!-- The Templates plugin is included to render the upload/download listings -->
<script src="http://blueimp.github.io/JavaScript-Templates/js/tmpl.min.js"></script>
<!-- The Load Image plugin is included for the preview images and image resizing functionality -->
<script src="http://blueimp.github.io/JavaScript-Load-Image/js/load-image.all.min.js"></script>
<!-- The Canvas to Blob plugin is included for image resizing functionality -->
<script src="http://blueimp.github.io/JavaScript-Canvas-to-Blob/js/canvas-to-blob.min.js"></script>


<script type="text/javascript">
    var ALIAS_ID = <?=$_SESSION['alias']->id?>;
    var ALIAS_FOLDER = '<?=$_SESSION['option']->folder?>';
    var CONTENT_ID = <?=$CONTENT_ID?>;
    var PHOTO_MAIN = '<?=$PHOTO_MAIN?>';
    var PHOTO_FILE_NAME = '<?=$PHOTO_FILE_NAME?>';
    var PHOTO_TITLE = '<?=$_SESSION['alias']->name?>';
    var PHOTO_UPDATE_TABLE = '<?=(isset($PHOTO_UPDATE_TABLE)) ? $PHOTO_UPDATE_TABLE : ''?>';
    var PHOTO_UPDATE_FIELD = '<?=(isset($PHOTO_UPDATE_FIELD)) ? $PHOTO_UPDATE_FIELD : 'photo'?>';

    document.getElementById('PHOTOS').onclick = function (event) {
        event = event || window.event;
        var target = event.target || event.srcElement;
        if(target.tagName == "IMG")
        {
            var link = target.src ? target.parentNode : target,
                options = {index: link, event: event},
                links = this.getElementsByTagName('a');
            blueimp.Gallery(links, options);
        }
    };
</script>

<style type="text/css">
    .fileupload-progress .progress-extended {
        margin-top: 5px;
    }
    .error {
        color: red;
    }
    td.preview {
        width: 150px;
    }
    td.preview a img {
        width: 150px;
    }
    td textarea {
        width: 100%;
        height: 100%;
    }
    td.navigation {
        width: 150px;
        font-size: 15px;
    }
    td.navigation button.btn {
    	margin: 2px;
    	width: 150px;
    	font-size: 16px;
    }
    .saveing {
        display: none;
        font-size: 95%;
    }
    .saveing img {
        width: 35px;
    }

    @media (min-width: 481px) {
      .navigation {
        list-style: none;
        padding: 0;
      }
      .navigation li {
        display: inline-block;
      }
      .navigation li:not(:first-child):before {
        content: "| ";
      }
    }
</style>

<!-- blueimp Gallery styles -->
<link rel="stylesheet" href="http://blueimp.github.io/Gallery/css/blueimp-gallery.min.css">
<!-- CSS to style the file input field as button and adjust the Bootstrap progress bars -->
<link rel="stylesheet" href="<?=SITE_URL?>assets/blueimp/css/jquery.fileupload.css">
<link rel="stylesheet" href="<?=SITE_URL?>assets/blueimp/css/jquery.fileupload-ui.css">
<!-- CSS adjustments for browsers with JavaScript disabled -->
<noscript><link rel="stylesheet" href="<?=SITE_URL?>assets/blueimp/css/jquery.fileupload-noscript.css"></noscript>
<noscript><link rel="stylesheet" href="<?=SITE_URL?>assets/blueimp/css/jquery.fileupload-ui-noscript.css"></noscript>
<h1 id="h1-album-name"><?=($album->name != '')?$album->name:'Редагувати альбом'?></h1>

<button id="btn-go-to-album" class="btn btn-warning" onClick="goToAlbum()">
    <i class="glyphicon glyphicon-eye-open"></i>
    <span>До альбому</span>
</button>

<button style="float:right" class="btn btn-danger" onClick="showUninstalForm()">
	<i class="glyphicon glyphicon-trash"></i>
	<span>Видалити альбом</span>
</button>
<br>
<br>
<div id="uninstall-form" style="background: rgba(236, 0, 0, 0.68); padding: 10px; display: none;">
	<form action="<?=SITE_URL.'admin/'.$_SESSION['alias']->alias?>/delete_album" method="POST">
		Ви впевнені що бажаєте видалити альбом і всі фотографії у ньому? Увага! Дані відновити неможливо!
		<br><br>
		<input type="hidden" name="id" value="<?=$album->id?>">
		<button type="submit" style="margin-left:25px; float:left;">Видалити</button>
	</form>
	<button style="margin-left:25px" onClick="showUninstalForm()">Скасувати</button>
	<div class="clear"></div>
</div>

<form method="POST" action="<?=SITE_URL.$_SESSION['alias']->alias?>/save_album" enctype="multipart/form-data">
	<table id="album-data">
        <?php if($this->userCan('photo')){ $link = explode('-', $album->link); ?>
            <tr>
                <td title="Обов'язкове поле">link*:</td>
                <td><?=array_shift($link)?>-<input type="text" name="link" value="<?=implode('-', $link)?>" required style="width:85%"></td>
            </tr>
            <?php if($_SESSION['language']){
                foreach ($ntkd as $lng => $value) { ?>
                    <tr>
                        <td title="Обов'язкове поле">Назва альбому <?=$lng?>*:</td>
                        <td class="edit-text"><input type="text" name="name_<?=$lng?>" value="<?=$value->name?>" required></td>
                    </tr>
                    <tr>
                        <td>Опис альбому <?=$lng?>:</td>
                        <td class="edit-text"><textarea name="text_<?=$lng?>"><?=$value->text?></textarea></td>
                    </tr>
                    <tr>
                        <td title="Обов'язкове поле">title <?=$lng?>*:</td>
                        <td class="edit-text"><input type="text" name="title_<?=$lng?>" value="<?=$value->title?>" required></td>
                    </tr>
                    <tr>
                        <td>description <?=$lng?>:</td>
                        <td class="edit-text"><textarea name="description_<?=$lng?>"><?=$value->description?></textarea></td>
                    </tr>
            <?php } } else { ?>
                <tr>
                    <td title="Обов'язкове поле">title*:</td>
                    <td class="edit-text"><input type="text" name="title" value="<?=$ntkd->title?>" required></td>
                </tr>
                <tr>
                    <td>description <?=$lng?>:</td>
                    <td class="edit-text"><textarea name="description"><?=$ntkd->description?></textarea></td>
                </tr>
        <?php } } else { ?>
		<tr>
			<td title="Обов'язкове поле">Назва альбому*:</td>
			<td class="edit-text"><input type="text" name="name" value="<?=$album->name?>" required></td>
		</tr>
		<tr>
			<td>Опис альбому:</td>
			<td class="edit-text"><textarea name="text"><?=$album->text?></textarea></td>
		</tr>
        <?php } ?>
        <tr>
            <td colspan="2">
                <input type="radio" name="active" value="1" id="active-1" <?=($album->active == 1)?'checked':''?>><label for="active-1">Альбом активний</label>
                <input type="radio" name="active" value="0" id="active-0" <?=($album->active == 0)?'checked':''?>><label for="active-0">Альбом тимчасово відключений для показу</label>
            </td>
        </tr>
		<tr>
			<td title="Задати унікальну обкладинку альбому або виберіть серед завантажених фотографій">Обкладинка альбому:</td>
			<td><input type="file" name="photo" title="Задати унікальну обкладинку альбому або виберіть серед завантажених фотографій"></td>
		</tr>
        <tr>
            <td colspan="2">
                <center>
                    <button type="submit" class="btn btn-primary">
                        <i class="glyphicon glyphicon-upload"></i>
                        <span>Зберегти</span>
                    </button>
                </center>
            </td>
        </tr>
	</table>
    <div style="float:left">
        <?php if($album->photo >= 0){ ?>
            <center>Головне фото:</center>
            <a id="main-image" rel="photos" href="<?=IMG_PATH.$_SESSION['option']->folder.'/'.$album->id.'/'.$album->photo?>.jpg" title="<?=$album->name?>">
                <img src="<?=IMG_PATH.$_SESSION['option']->folder.'/'.$album->id.'/s_'.$album->photo?>.jpg" style="width: 150px;" alt="<?=$album->name?>"/>
            </a>  
        <?php } ?>
        <br>
        Головне фото альбому можна завантажити <br>індивідуально або ж вибрати серед <br>завантажених.
    </div>
    <div class="clear"></div>
    <input type="hidden" name="album" value="<?=$album->id?>">
    <input type="hidden" name="active" value="1">
</form>

<br>


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

<?php if(isset($photos) && count($photos) > 0){ ?>
    <table class="table table-striped"><tbody class="files">
        <?php foreach($photos as $p){ ?>
            <tr id="photo-<?=$p->id?>" class="template-download fade in">
                <td class="preview">
                    <a href="<?=IMG_PATH.$_SESSION['alias']->alias?>/<?=$album->id.'/'.$p->id?>.jpg" data-gallery rel="photos">
                        <img src="<?=IMG_PATH.$_SESSION['alias']->alias?>/<?=$album->id.'/s_'.$p->id?>.jpg">
                    </a>
                </td>
                <td>
                    <textarea onChange="savePhoto(<?=$p->id?>, this)"><?=$p->name?></textarea>
                </td>
                <td class="navigation">
                    <?php if($p->user == $_SESSION['user']->id || $this->userCan($_SESSION['alias']->alias)){ ?>
                        <button class="btn btn-danger delete" onClick="deletePhoto(<?=$p->id?>)">
                            <i class="glyphicon glyphicon-trash"></i>
                            <span>Видалити</span>
                        </button>
                        <br>
                        <br>
                        <button class="btn btn-warning" onClick="setAlbumPhoto(<?=$p->id?>)" title="Зробити головним фото альбому">
                            <i class="glyphicon glyphicon-eye-open"></i>
                            <span>Головне фото</span>
                        </button>
                        <br>
                    <?php } ?>
                    Додано: <?=date('d.m.Y H:i', $p->date)?>
                    <a href="<?=SITE_URL?>profile/<?=$p->user?>"><?=mb_substr($p->user_name, 0, 16, 'utf-8')?></a>
                    <br>
                    <span id="photo-saveing-<?=$p->id?>" class="saveing"><img src="<?=SITE_URL?>style/images/icon-loading.gif">Зберігання..</span>
                </td>
            </tr>
        <?php } ?>
    </tbody></table>
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
                <a href="{%=file.url%}" data-gallery rel="photos"><img src="{%=file.thumbnailUrl%}"></a>
            {% } %}
        </td>
        <td>
            {% if (file.error) { %}
                <div><span class="label label-danger">Error</span> {%=file.error%}</div>
            {% } else { %}
                <textarea onChange="savePhoto({%=file.id%}, this)">{%=file.name%}</textarea>
            {% } %}
        </td>
        <td class="navigation">
            <button class="btn btn-danger delete" onClick="deletePhoto({%=file.id%})">
                <i class="glyphicon glyphicon-trash"></i>
                <span>Видалити</span>
            </button>
            <br>
            <br>
            <button class="btn btn-warning" onClick="setAlbumPhoto({%=file.id%})" title="Зробити головним фото альбому">
                <i class="glyphicon glyphicon-eye-open"></i>
                <span>Головне фото</span>
            </button>
            <br>
            Додано: {%=file.date%}
            <a href="<?=SITE_URL?>profile/<?=$_SESSION['user']->id?>"><?=mb_substr($_SESSION['user']->name, 0, 16, 'utf-8')?></a>
            <br>
            <span id="photo-saveing-{%=file.id%}" class="saveing"><img src="<?=SITE_URL?>style/images/icon-loading.gif">Зберігання..</span>
            <?php //<span class="size">{%=o.formatFileSize(file.size)%}</span> ?>
        </td>
    </tr>
{% } %}
</script>


<!-- The jQuery UI widget factory, can be omitted if jQuery UI is already included -->
<script src="<?=SITE_URL?>assets/blueimp/js/vendor/jquery.ui.widget.js"></script>
<!-- The Templates plugin is included to render the upload/download listings -->
<script src="http://blueimp.github.io/JavaScript-Templates/js/tmpl.min.js"></script>
<!-- The Load Image plugin is included for the preview images and image resizing functionality -->
<script src="http://blueimp.github.io/JavaScript-Load-Image/js/load-image.min.js"></script>
<!-- The Canvas to Blob plugin is included for image resizing functionality -->
<script src="http://blueimp.github.io/JavaScript-Canvas-to-Blob/js/canvas-to-blob.min.js"></script>
<!-- Bootstrap JS is not required, but included for the responsive demo navigation -->
<script src="//netdna.bootstrapcdn.com/bootstrap/3.0.3/js/bootstrap.min.js"></script>
<!-- blueimp Gallery script -->
<script src="http://blueimp.github.io/Gallery/js/jquery.blueimp-gallery.min.js"></script>
<!-- The Iframe Transport is required for browsers without support for XHR file uploads -->
<script src="<?=SITE_URL?>assets/blueimp/js/jquery.iframe-transport.js"></script>
<!-- The basic File Upload plugin -->
<script src="<?=SITE_URL?>assets/blueimp/js/jquery.fileupload.js"></script>
<!-- The File Upload processing plugin -->
<script src="<?=SITE_URL?>assets/blueimp/js/jquery.fileupload-process.js"></script>
<!-- The File Upload image preview & resize plugin -->
<script src="<?=SITE_URL?>assets/blueimp/js/jquery.fileupload-image.js"></script>
<!-- The File Upload audio preview plugin - ->
<script src="<?=SITE_URL?>assets/blueimp/js/jquery.fileupload-audio.js"></script>
<!-- The File Upload video preview plugin - ->
<script src="<?=SITE_URL?>assets/blueimp/js/jquery.fileupload-video.js"></script>
<!-- The File Upload validation plugin -->
<script src="<?=SITE_URL?>assets/blueimp/js/jquery.fileupload-validate.js"></script>
<!-- The File Upload user interface plugin -->
<script src="<?=SITE_URL?>assets/blueimp/js/jquery.fileupload-ui.js"></script>
<!-- The XDomainRequest Transport is included for cross-domain file deletion for IE 8 and IE 9 -->
<!--[if (gte IE 8)&(lt IE 10)]>
<script src="<?=SITE_URL?>assets/blueimp/js/cors/jquery.xdr-transport.js"></script>
<![endif]-->

<script type="text/javascript">
    /*
 * jQuery File Upload Plugin JS Example 8.9.1
 * https://github.com/blueimp/jQuery-File-Upload
 *
 * Copyright 2010, Sebastian Tschan
 * https://blueimp.net
 *
 * Licensed under the MIT license:
 * http://www.opensource.org/licenses/MIT
 */

/* global $, window */



$(function () {
    'use strict';

    // Initialize the jQuery File Upload widget:
    $('#fileupload').fileupload({
        url: '<?=SITE_URL.'admin/'.$_SESSION['alias']->alias?>/upload/<?=$album->id?>',
        autoUpload: true,
        acceptFileTypes: /(\.|\/)(jpe?g|png)$/i
    });

});

</script>

<script type="text/javascript">
    function goToAlbum () {
        top.document.location.href = "<?=SITE_URL.$_SESSION['alias']->alias.'/'.$album->link?>";
    }
    function showUninstalForm () {
        if($('#uninstall-form').is(":hidden")){
            $('#uninstall-form').slideDown("slow");
        } else {
            $('#uninstall-form').slideUp("fast");
        }
    }
    function setAlbumPhoto(id){
        $('#photo-saveing-'+id).css("display", "block");
        $.ajax({
            url: "<?=SITE_URL.'admin/'.$_SESSION['alias']->alias?>/setAlbumPhoto",
            type: 'POST',
            data: {
                album: <?=$album->id?>,
                photo :  id,
                json : true
            },
            success: function(res){
                if(res['result'] == false){
                    alert(res['error']);
                } else {
                    $('#main-image').attr('href', '<?=IMG_PATH.$_SESSION['option']->folder.'/'.$album->id?>/' + id + '.jpg');
                    $('#main-image').html('<img src="<?=IMG_PATH.$_SESSION['option']->folder.'/'.$album->id?>/s_' + id + '.jpg" style="width: 150px;" alt="<?=$album->name?>"/>');
                    alert("Фото встановлено");
                }
                $('#photo-saveing-'+id).css("display", "none");
            },
            error: function(){
                alert("Помилка! Спробуйте ще раз!");
                $('#photo-saveing-'+id).css("display", "none");
            },
            timeout: function(){
                alert("Помилка! Спробуйте ще раз!");
                $('#photo-saveing-'+id).css("display", "none");
            }
        });
    }
    
    function savePhoto(id, e){
        $('#photo-saveing-'+id).css("display", "block");
        $.ajax({
            url: "<?=SITE_URL.'admin/'.$_SESSION['alias']->alias?>/save_photo",
            type: 'POST',
            data: {
                photo: id,
                name: e.value,
                json: true
            },
            success: function(res){
                if(res['result'] == false){
                    alert(res['error']);
                }
                $('#photo-saveing-'+id).css("display", "none");
            },
            error: function(){
                alert("Помилка! Спробуйте ще раз!");
                $('#photo-saveing-'+id).css("display", "none");
            },
            timeout: function(){
                alert("Помилка: Вийшов час очікування! Спробуйте ще раз!");
                $('#photo-saveing-'+id).css("display", "none");
            }
        });
    }
    function deletePhoto(id){
        if (confirm("Ви впевнені, що хочете видалити фотографію? \nУВАГА, інформація відновленню НЕ ПІДЛЯГАЄ!")) {
            $('#photo-saveing-'+id).css("display", "block");
            $.ajax({
                url: "<?=SITE_URL.'admin/'.$_SESSION['alias']->alias?>/delete_photo",
                type: 'POST',
                data: {
                    photo: id,
                    json: true
                },
                success: function(res){
                    if(res['result'] == false){
                        alert(res['error']);
                    } else $("#photo-"+id).remove();
                },
                error: function(){
                    alert("Помилка! Спробуйте ще раз!");
                    $('#photo-saveing-'+id).css("display", "none");
                },
                timeout: function(){
                    alert("Помилка: Вийшов час очікування! Спробуйте ще раз!");
                    $('#photo-saveing-'+id).css("display", "none");
                }
            });
        }
    }
</script>
<STYLE type="text/css">
    .f-left {
        float: left;
    }
    .fileupload-progress .progress-extended {
        margin-top: 5px;
    }
    .error {
        color: red;
    }
    td.edit-text {
        width: 650px;
    }
    td.edit-text textarea{
        height: 70px;
    }
    td.preview {
        width: 200px;
    }
    td.preview img {
        height: 140px;
    }
    td input {
        width: 100%;
    }
    td input[type="radio"] {
        width: auto;
    }
    td textarea {
        width: 100%;
        height: 140px;
    }
    td.navigation {
        width: 150px;
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

<!--
<script type="text/javascript" src="<?=SITE_URL?>assets/fancybox/jquery.fancybox-1.3.4.pack.js"></script>
<link rel="stylesheet" href="<?=SITE_URL?>assets/fancybox/jquery.fancybox-1.3.4.css" type="text/css" media="screen" />
<script type="text/javascript" src="<?=SITE_URL?>assets/fancybox/jquery.easing-1.3.pack.js"></script>
<script type="text/javascript" src="<?=SITE_URL?>assets/fancybox/jquery.mousewheel-3.0.4.pack.js"></script>
<script type="text/javascript">
    $("a[rel=photos]").fancybox({
        'transitionIn'      : 'elastic',
        'transitionOut'     : 'elastic',
        'titlePosition'     : 'over',
        'speedIn'       :   600, 
        'speedOut'      :   200, 
        'titleFormat'       : function(title, currentArray, currentIndex, currentOpts) {
            return '<span id="fancybox-title-over">Image ' + (currentIndex + 1) + ' / ' + currentArray.length + (title.length ? ' &nbsp; ' + title : '') + '</span>';
        }
    });
</script>
-->

<!-- Bootstrap styles -->
<link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.0.3/css/bootstrap.min.css">
<!-- blueimp Gallery styles -->
<link rel="stylesheet" href="http://blueimp.github.io/Gallery/css/blueimp-gallery.min.css">
<!-- CSS to style the file input field as button and adjust the Bootstrap progress bars -->
<link rel="stylesheet" href="<?=SITE_URL?>assets/blueimp/css/jquery.fileupload.css">
<link rel="stylesheet" href="<?=SITE_URL?>assets/blueimp/css/jquery.fileupload-ui.css">
<!-- CSS adjustments for browsers with JavaScript disabled -->
<noscript><link rel="stylesheet" href="<?=SITE_URL?>assets/blueimp/css/jquery.fileupload-noscript.css"></noscript>
<noscript><link rel="stylesheet" href="<?=SITE_URL?>assets/blueimp/css/jquery.fileupload-ui-noscript.css"></noscript>

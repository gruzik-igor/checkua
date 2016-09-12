<?php if(isset($_SESSION['notify'])) require APP_PATH.'views/admin/notify_view.php'; ?>
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
        ?>
        <li><a href="#tab-photo" data-toggle="tab" aria-expanded="true">Фото</a></li>
        <li><a href="#tab-video" data-toggle="tab" aria-expanded="true">Відео</a></li>
        <li><a href="#tab-audio" data-toggle="tab" aria-expanded="true">Аудіо</a></li>
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
        <?php } ?>
        <div class="tab-pane fade" id="tab-photo">
            <?php require_once 'wl_images/__tab-photo.php'; ?>
        </div>
        <div class="tab-pane fade" id="tab-video">
            <?php require_once 'wl_video/__tab-video.php'; ?>
        </div>
        <div class="tab-pane fade" id="tab-audio">
            <?php require_once 'wl_audio/__tab-audio.php'; ?>
        </div>
    </div>
</div>

<script type="text/javascript" src="<?=SITE_URL?>assets/ckeditor/ckeditor.js"></script>
<script type="text/javascript" src="<?=SITE_URL?>assets/ckfinder/ckfinder.js"></script>
<script type="text/javascript">
  <?php if($_SESSION['language']) foreach($_SESSION['all_languages'] as $lng) echo "CKEDITOR.replace( 'editor-{$lng}' ); "; else echo "CKEDITOR.replace( 'editor' ); "; ?>
    CKFinder.setupCKEditor( null, {
    basePath : '<?=SITE_URL?>assets/ckfinder/',
    filebrowserBrowseUrl : '<?=SITE_URL?>assets/ckfinder/ckfinder.html',
    filebrowserImageBrowseUrl : '<?=SITE_URL?>assets/ckfinder/ckfinder.html?type=Images',
    filebrowserFlashBrowseUrl : '<?=SITE_URL?>assets/ckfinder/ckfinder.html?type=Flash',
    filebrowserUploadUrl : '<?=SITE_URL?>assets/ckfinder/core/connector/asp/connector.asp?command=QuickUpload&type=Files',
    filebrowserImageUploadUrl : '<?=SITE_URL?>assets/ckfinder/core/connector/asp/connector.asp?command=QuickUpload&type=Images',
    filebrowserFlashUploadUrl : '<?=SITE_URL?>assets/ckfinder/core/connector/asp/connector.asp?command=QuickUpload&type=Flash',
  });
</script>

<script type="text/javascript">
  var data;
  function save (field, e, lang) {
    $('#saveing').css("display", "block");
    var value = '';
    if(e != false) value = e.value;
    else value = data;

    $.ajax({
      url: "<?=SITE_URL?>admin/wl_ntkd/save",
      type: 'POST',
      data: {
        alias: <?=$_SESSION['alias']->id?>,
        content: <?=$_SESSION['alias']->content?>,
        field: field,
        data: value,
        language: lang,
        <?php if(isset($ADDITIONAL_TABLE)) { ?>
            additional_table : '<?=$ADDITIONAL_TABLE?>',
            additional_table_id : '<?=$ADDITIONAL_TABLE_ID?>',
            additional_fields : '<?=$ADDITIONAL_FIELDS?>',
        <?php } ?>
        json: true
      },
      success: function(res){
        if(res['result'] == false){
            $.gritter.add({title:"Помилка!",text:res['error']});
        } else {
          language = '';
          if(lang) language = lang;
          $.gritter.add({title:field+' '+language,text:"Дані успішно збережено!"});
        }
        $('#saveing').css("display", "none");
      },
      error: function(){
        $.gritter.add({title:"Помилка!",text:"Помилка! Спробуйте ще раз!"});
        $('#saveing').css("display", "none");
      },
      timeout: function(){
        $.gritter.add({title:"Помилка!",text:"Помилка: Вийшов час очікування! Спробуйте ще раз!"});
        $('#saveing').css("display", "none");
      }
    });
  }
  function saveText(lang){
    if(lang != false){
      data = CKEDITOR.instances['editor-'+lang].getData();
    } else {
      data = CKEDITOR.instances['editor'].getData();
    }
    save('text', false, lang);
  }
  function showEditTKD (lang) {
    if($('#tkd-'+lang).is(":hidden")){
      $('#tkd-'+lang).slideDown("slow");
      } else {
      $('#tkd-'+lang).slideUp("fast");
      }
  }
</script>
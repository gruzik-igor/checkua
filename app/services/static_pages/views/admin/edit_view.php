<?php
	$h1 = '';
  $ntkd = array();
  $where_ntkd['alias'] = $_SESSION['alias']->id;
  $where_ntkd['content'] = 0;
  $wl = $this->db->getAllDataByFieldInArray('wl_ntkd', $where_ntkd);
  if($wl){
  	if($_SESSION['language']){
  		foreach ($wl as $nt) {
	      	$ntkd[$nt->language] = $nt;
	      	if($_SESSION['language'] == $nt->language) $h1 = $nt->name;
	    }
  	} else {
  		$ntkd = NULL;
  		$ntkd = $wl[0];
  		$h1 = $ntkd->name;
  	}

  }

?>
<div class="row">
  <div class="col-md-12">
    <div class="panel panel-inverse">
      <div class="panel-heading">
        <div class="panel-heading-btn">
          <a href="<?=SITE_URL.$_SESSION['alias']->alias?>" class="btn btn-info btn-xs">На сторінку</a>
        </div>

        <h5 class="panel-title">
          Додано: <?=date('d.m.Y H:i', $article->date_add)?>
          Редаговано: <?=date('d.m.Y H:i', $article->date_edit)?>
        </h5>
      </div>

      <?php if(isset($_SESSION['notify'])){ 
        require APP_PATH.'views/admin/notify_view.php';
      } ?>

      <div class="panel-body">
        <ul class="nav nav-tabs">
          <?php if($_SESSION['language']) { foreach ($_SESSION['all_languages'] as $lang) { ?>
          	<li <?=($_SESSION['language'] == $lang) ? 'class="active"' : ''?>><a href="#tab-<?=$lang?>" data-toggle="tab" aria-expanded="true"><?=$lang?></a></li>
          <?php } } else { ?>
          	<li class="active"><a href="#tab-ntkd" data-toggle="tab" aria-expanded="true">Назва та опис</a></li>
          <?php } ?>
          <li><a href="#tab-photo" data-toggle="tab" aria-expanded="true">Фото</a></li>
          <li><a href="#tab-video" data-toggle="tab" aria-expanded="true">Відео</a></li>
        </ul>
        <div class="tab-content">
          <?php if($_SESSION['language']) { foreach ($_SESSION['all_languages'] as $lang) { ?>
            <div class="tab-pane fade <?=($_SESSION['language'] == $lang) ? 'active in' : ''?>" id="tab-<?=$lang?>">
              <?php require '_tab-ntkd.php'; ?>
            </div>
          <?php } } else { ?>
        		<div class="tab-pane fade active in" id="tab-ntkd">
        			<?php require '_tab-ntkd.php'; ?>
        		</div>
          <?php } ?>
          <div class="tab-pane fade" id="tab-photo">
            <?php require_once '_tab-photo.php'; ?>
          </div>
          <div class="tab-pane fade" id="tab-video">
            <?php require_once '_tab-video.php'; ?>
          </div>
        </div>

      </div>
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
        alias: '<?=$_SESSION['alias']->id?>',
        content: 0,
        field: field,
        data: value,
        language: lang,
        additional_table : '<?=$_SESSION['service']->table?>',
        additional_table_id : '<?=$article->id?>',
        additional_fields : 'author_edit=>user,date_edit=>time',
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
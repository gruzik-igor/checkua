<?php
	$h1 = '';
  $ntkd = array();
  $where_ntkd['alias'] = $_SESSION['alias']->id;
  $where_ntkd['content'] = $product->id;
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

  $options_parents = array();
  if($_SESSION['option']->useGroups && isset($list)){
    $parent = $product->group;
    while ($parent != 0) {
      array_unshift($options_parents, $parent);
      $parent = $list[$parent]->parent;
    }
  }
  if(empty($options_parents)) {
    $options = $this->shop_model->getOptions();
    if($options) array_unshift($options_parents, 0);
  } else {
    array_unshift($options_parents, 0);
  }
  
  $product_options = array();
  $options = $this->db->getAllDataByFieldInArray($this->shop_model->table('_product_options'), $product->id, 'product');
  if($options){
    foreach ($options as $option) {
      if($option->language != '' && in_array($option->language, $_SESSION['all_languages'])){
        $product_options[$option->option][$option->language] = $option->value;
      } else {
        $product_options[$option->option] = $option->value;
      }
    }
  }

?>
<div class="row">
  <div class="col-md-12">
    <div class="panel panel-inverse">
      <div class="panel-heading">
        <div class="panel-heading-btn">
          <a href="<?=SITE_URL.$_SESSION['alias']->alias.'/'.$product->id?>" class="btn btn-info btn-xs"><?=$_SESSION['admin_options']['word:product_to']?></a>
          <?php
            $url = $this->data->url();
            array_shift($url);
            array_pop ($url);
            $url = implode('/', $url);
          ?>
          <a href="<?=SITE_URL.'admin/'.$url?>" class="btn btn-success btn-xs">До каталогу</a>
          <button onClick="showUninstalForm()" class="btn btn-danger btn-xs">Видалити <?=$_SESSION['admin_options']['word:product_to_delete']?></button>
        </div>

          <h5 class="panel-title">
            Додано: <?=date('d.m.Y H:i', $product->date_add)?>
            Редаговано: <?=date('d.m.Y H:i', $product->date_edit)?>
          </h5>
      </div>

      <div id="uninstall-form" class="alert alert-danger fade in" style="display: none;">
        <i class="fa fa-trash fa-2x pull-left"></i>
        <form action="<?=SITE_URL.'admin/'.$_SESSION['alias']->alias?>/delete" method="POST">
          <p>Ви впевнені що бажаєте видалити <?=$_SESSION['admin_options']['word:product_to_delete']?>?</p>
          <input type="hidden" name="id" value="<?=$product->id?>">
          <input type="submit" value="Видалити" class="btn btn-danger">
          <button type="button" style="margin-left:25px" onClick="showUninstalForm()" class="btn btn-info">Скасувати</button>
        </form>
      </div>

      <?php if(isset($_SESSION['notify'])){ 
        require APP_PATH.'views/admin/notify_view.php';
      } ?>

      <div class="panel-body">
        <ul class="nav nav-tabs">
          <li class="active"><a href="#tab-main" data-toggle="tab" aria-expanded="true">Загальні дані</a></li>
          <?php if($_SESSION['language']) { foreach ($_SESSION['all_languages'] as $lang) { ?>
          	<li><a href="#tab-<?=$lang?>" data-toggle="tab" aria-expanded="true"><?=$lang?></a></li>
          <?php } } else { ?>
          	<li><a href="#tab-ntkd" data-toggle="tab" aria-expanded="true">Назва та опис</a></li>
          <?php } ?>
          <li><a href="#tab-photo" data-toggle="tab" aria-expanded="true">Фото</a></li>
          <li><a href="#tab-video" data-toggle="tab" aria-expanded="true">Відео</a></li>
        </ul>
        <div class="tab-content">
          <div class="tab-pane fade active in" id="tab-main">
            <?php require_once 'edit_tabs/tab-main.php'; ?>
          </div>
          <?php if($_SESSION['language']) { foreach ($_SESSION['all_languages'] as $lang) { ?>
            <div class="tab-pane fade" id="tab-<?=$lang?>">
              <?php require 'edit_tabs/tab-ntkd.php'; ?>
            </div>
          <?php } } else { ?>
        		<div class="tab-pane fade" id="tab-ntkd">
        			<?php require 'edit_tabs/tab-ntkd.php'; ?>
        		</div>
          <?php } ?>
          <div class="tab-pane fade" id="tab-photo">
            <?php require_once 'edit_tabs/tab-photo.php'; ?>
          </div>
          <div class="tab-pane fade" id="tab-video">
            <?php require_once 'edit_tabs/edit_videos_view.php'; ?>
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
        content: '<?=$product->id?>',
        field: field,
        data: value,
        language: lang,
        additional_table : '<?=$this->shop_model->table('_products')?>',
        additional_table_id : '<?=$product->id?>',
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
  function saveOption (e, label) {
    $('#saveing').css("display", "block");
    $.ajax({
      url: "<?=SITE_URL?>admin/<?=$_SESSION['alias']->alias?>/saveOption",
      type: 'POST',
      data: {
        id: '<?=$product->id?>',
        option: e.name,
        data: e.value,
        json: true
      },
      success: function(res){
        if(res['result'] == false){
            $.gritter.add({title:"Помилка!", text:label + ' ' + res['error']});
        } else {
          $.gritter.add({title:label, text:"Дані успішно збережено!"});
        }
        $('#saveing').css("display", "none");
      },
      error: function(){
        $.gritter.add({title:"Помилка!", text:"Помилка! Спробуйте ще раз!"});
        $('#saveing').css("display", "none");
      },
      timeout: function(){
        $.gritter.add({title:"Помилка!", text:"Помилка: Вийшов час очікування! Спробуйте ще раз!"});
        $('#saveing').css("display", "none");
      }
    });
  }
</script>
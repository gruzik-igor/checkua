<?php
  $ntkd = array();
  $where_ntkd['alias'] = $_SESSION['alias']->id;
  $where_ntkd['content'] = $product->id;
  $wl = $this->db->getAllDataByFieldInArray('wl_ntkd', $where_ntkd);
  if($wl)
  {
  	if($_SESSION['language'])
  		foreach ($wl as $nt) {
	      $ntkd[$nt->language] = $nt;
	    }
    else
  		$ntkd = $wl[0];
  }
  
  $product_options = array();
  $options = $this->db->getAllDataByFieldInArray($this->shop_model->table('_product_options'), $product->id, 'product');
  if($options)
  {
    foreach ($options as $option) {
      if($option->language != '' && in_array($option->language, $_SESSION['all_languages']))
        $product_options[$option->option][$option->language] = $option->value;
      else
        $product_options[$option->option] = $option->value;
    }
  }

  $storages = array();
  if($cooperation = $this->db->getAllDataByFieldInArray('wl_aliases_cooperation', $_SESSION['alias']->id, 'alias1'))
    foreach ($cooperation as $c) {
      if($c->type == 'storage') $storages[] = $c->alias2;
    }
?>
<div class="row">
  <div class="col-md-12">
    <div class="panel panel-inverse">
      <div class="panel-heading">
        <div class="panel-heading-btn">
          <a href="<?=SITE_URL.$_SESSION['alias']->alias.'/'.$product->alias?>" class="btn btn-info btn-xs"><?=$_SESSION['admin_options']['word:product_to']?></a>
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
            Додав: <?=$product->author_add_name .' '.date('d.m.Y H:i', $product->date_add)?>.
            Редаговано: <?=$product->author_edit_name .' '.date('d.m.Y H:i', $product->date_edit)?>
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

      <?php if(isset($_SESSION['notify'])) {
        require APP_PATH.'views/admin/notify_view.php';
      } ?>

      <div class="panel-body">
        <ul class="nav nav-tabs">
          <?php if(!empty($storages)) { ?>
            <li class="active"><a href="#tab-storages" data-toggle="tab" aria-expanded="true">Склад</a></li>
            <li>
          <?php } else echo '<li class="active">'; ?>
            <a href="#tab-main" data-toggle="tab" aria-expanded="true">Загальні дані</a></li>
          <?php if($_SESSION['language']) { foreach ($_SESSION['all_languages'] as $lang) { ?>
          	<li><a href="#tab-<?=$lang?>" data-toggle="tab" aria-expanded="true"><?=$lang?></a></li>
          <?php } } else { ?>
          	<li><a href="#tab-ntkd" data-toggle="tab" aria-expanded="true">Назва та опис</a></li>
          <?php } ?>
          <li><a href="#tab-photo" data-toggle="tab" aria-expanded="true">Фото</a></li>
          <li><a href="#tab-video" data-toggle="tab" aria-expanded="true">Відео</a></li>
          <li><a href="#tab-audio" data-toggle="tab" aria-expanded="true">Аудіо</a></li>
        </ul>
        <div class="tab-content">
          <?php if(!empty($storages)) { ?>
            <div class="tab-pane fade active in" id="tab-storages">
              <?php require 'edit_tabs/tab-storages.php'; ?>
            </div>
            <div class="tab-pane fade" id="tab-main">
          <?php } else { ?>
            <div class="tab-pane fade active in" id="tab-main">
            <?php } require_once 'edit_tabs/tab-main.php'; ?>
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
            <?php
            $ADDITIONAL_TABLE_ID = $product->id;
            $ADDITIONAL_FIELDS = 'author_edit=>user,date_edit=>time';
            $ADDITIONAL_TABLE = $this->shop_model->table('_products');
            require_once APP_PATH.'views/admin/wl_images/__tab-photo.php'; ?>
          </div>
          <div class="tab-pane fade" id="tab-video">
            <?php require_once APP_PATH.'views/admin/wl_video/__tab-video.php'; ?>
          </div>
          <div class="tab-pane fade" id="tab-audio">
            <?php require_once APP_PATH.'views/admin/wl_audio/__tab-audio.php'; ?>
          </div>
        </div>

      </div>
    </div>
  </div>
</div>

<script type="text/javascript">
  var ALIAS_ID = <?=$_SESSION['alias']->id?>;
  var CONTENT_ID = <?=$_SESSION['alias']->content?>;
  var ALIAS_FOLDER = '<?=$_SESSION['option']->folder?>';
  var PHOTO_FILE_NAME = '<?=$product->alias?>';
  var PHOTO_TITLE = '<?=$_SESSION['alias']->name?>';
  var ADDITIONAL_TABLE = '<?=$ADDITIONAL_TABLE?>';
  var ADDITIONAL_TABLE_ID = <?=$ADDITIONAL_TABLE_ID?>;
  var ADDITIONAL_FIELDS = '<?=$ADDITIONAL_FIELDS?>';
  <?php
  $_SESSION['alias']->js_load[] = 'assets/ckeditor/ckeditor.js';
  $_SESSION['alias']->js_load[] = 'assets/ckfinder/ckfinder.js';
  $_SESSION['alias']->js_load[] = 'assets/white-lion/__edit_page.js';
  ?>

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
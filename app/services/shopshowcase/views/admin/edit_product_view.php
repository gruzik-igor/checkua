<?php require_once '_admin_words.php'; ?>

<div class="f-right inline">
	<a href="<?=SITE_URL.'admin/'.$_SESSION['alias']->alias?>/all">До всіх <?=$admin_words['products_to_all']?></a>
	<a href="<?=SITE_URL.'admin/'.$_SESSION['alias']->alias?>/groups">До всіх <?=$admin_words['groups_to_all']?></a>
</div>

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
  array_unshift($options_parents, 0);
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

<h1><?=$h1?></h1>

<span class="f-r">
	Додано: <?=date('d.m.Y H:i', $product->date_add)?>
	Редаговано: <?=date('d.m.Y H:i', $product->date_edit)?>
</span>

<?php
	$url = $this->data->url();
	array_shift($url);
	array_pop ($url);
	$url = implode('/', $url);
?>
<a href="<?=SITE_URL.'admin/'.$url?>">До каталогу</a>
<a href="<?=SITE_URL.$_SESSION['alias']->alias.'/'.$product->id?>"><?=$admin_words['product_to']?></a>
<button onClick="showUninstalForm()">Видалити <?=$admin_words['product_to_delete']?></button>
<br>
<div id="uninstall-form" style="background: rgba(236, 0, 0, 0.68); padding: 10px; display: none;">
	<form action="<?=SITE_URL.'admin/'.$_SESSION['alias']->alias?>/delete" method="POST">
		Ви впевнені що бажаєте видалити <?=$admin_words['product']?>?
		<br><br>
		<input type="hidden" name="id" value="<?=$product->id?>">
		<input type="submit" value="Видалити" style="margin-left:25px; float:left;">
	</form>
	<button style="margin-left:25px" onClick="showUninstalForm()">Скасувати</button>
	<div class="clear"></div>
</div>

<?php if(isset($_SESSION['notify']->success)){ 
	$success = $_SESSION['notify']->success;
	require APP_PATH.'views/notify_view.php';
} ?>

<div class="clear"></div>
<br>

<div id="tabs">
  <ul>
    <li><a href="#tab-main">Загальні дані</a></li>
    <?php if($_SESSION['language']) { foreach ($_SESSION['all_languages'] as $lang) { ?>
    	<li><a href="#tab-<?=$lang?>"><?=$lang?></a></li>
    <?php } } else { ?>
    	<li><a href="#tab-ntkd">Назва та опис</a></li>
    <?php } ?>
    <li><a href="#tab-photo">Фото</a></li>
  </ul>
  <div id="tab-main">
    <?php require_once 'edit_product_tabs/tab-main.php'; ?>
  </div>
  <?php if($_SESSION['language']) { foreach ($_SESSION['all_languages'] as $lang) { ?>
  		<div id="tab-<?=$lang?>">
  			<?php require 'edit_product_tabs/tab-ntkd.php'; ?>
  		</div>
  	<?php } } else { ?>
  		<div id="tab-ntkd">
  			<?php require 'edit_product_tabs/tab-ntkd.php'; ?>
  		</div>
  	<?php } ?>
  <div id="tab-photo">
    <?php require_once 'edit_product_tabs/tab-photo.php'; ?>
  </div>
</div>


<?php 
	// $content = $product->id;
	// require_once('_edit_ntkdt_view.php');
?>

<link rel="stylesheet" href="//code.jquery.com/ui/1.11.0/themes/smoothness/jquery-ui.css">
<script src="//code.jquery.com/ui/1.10.4/jquery-ui.js"></script>
<script>
	$(function() {
    	$( "#tabs" ).tabs();
  	});
</script>

<script type="text/javascript" src="<?=SITE_URL?>assets/ckeditor/ckeditor.js"></script>
<script type="text/javascript" src="<?=SITE_URL?>assets/ckfinder/ckfinder.js"></script>
<script type="text/javascript">
  <?php if($_SESSION['all_languages']) foreach($_SESSION['all_languages'] as $lng) echo "CKEDITOR.replace( 'editor-{$lng}' ); "; else echo "CKEDITOR.replace( 'editor' ); "; ?>
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
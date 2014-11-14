<div style="padding:15px">

	<a href="<?=SITE_URL?>admin" style="float:right">До панелі управління</a>
	<a href="<?=SITE_URL.$_SESSION['alias']->alias?>/<?=$article->id?>">До публікації</a>
	<a href="<?=SITE_URL.$_SESSION['alias']->alias?>/all">До загального списку</a>

<form method="post" action="<?=SITE_URL.$_SESSION['alias']->alias?>/save" enctype="multipart/form-data">

	<img class="s-kartinka2" src="<?=IMG_PATH.$_SESSION['alias']->alias?>/s_<?=$article->id?>.jpg" />
	Публікація активна <input type="checkbox" name="active" value="1" <?=($article->active == 1) ? 'checked' : '';?>> Так/Ні<br>
	<?php $checked = (isset($article->special) && $article->special == 1) ? 'checked' : '';
		echo ($_SESSION['alias']->alias == 'articles')?'Спеціальна пропозиція <input type=checkbox name="special" value=1 '.$checked.'>Так/Ні<br>':''; 
	?>
	Власна адреса посилання <input type="text" name="link" value="<?=$article->link?>" required>
	<p>Змінити обкладинку: <input type="file" name="photo"></p>
	<?php if($_SESSION['alias']->alias == 'library'){ ?>
	Група: 
		<select name="group">
			<option value="1" <?=($article->group == 1)?'selected':''?>>Статті</option>
			<option value="2" <?=($article->group == 2)?'selected':''?>>Книги</option>
			<option value="3" <?=($article->group == 3)?'selected':''?>>Музика</option>
			<option value="4" <?=($article->group == 4)?'selected':''?>>Відео</option>
		</select>
		<br>
		<br>
	<?php }
	echo '<p>Назва: '; $field = 'name'; $content = explode('|||', $article->$field);
	foreach($_SESSION['all_languages'] as $lng){
		$text = ''; 
		foreach($content as $f){ $f = explode(':::', $f); if($f[0] == $lng && isset($f[1]) && $f[1] != '') $text = $f[1]; }
		echo $lng.': <input type=text name="'.$field.'_'.$lng.'" value="'.$text.'" required>'; 
	}
	echo '<p>title: '; $field = 'title'; $content = explode('|||', $article->$field);
	foreach($_SESSION['all_languages'] as $lng){
		$text = ''; 
		foreach($content as $f){ $f = explode(':::', $f); if($f[0] == $lng && isset($f[1]) && $f[1] != '') $text = $f[1]; }
		echo $lng.': <input type=text name="'.$field.'_'.$lng.'" value="'.$text.'" required>'; 
	}
	echo '<p>keywords: '; $field = 'keywords'; $content = explode('|||', $article->$field);
	foreach($_SESSION['all_languages'] as $lng){
		$text = ''; 
		foreach($content as $f){ $f = explode(':::', $f); if($f[0] == $lng && isset($f[1]) && $f[1] != '') $text = $f[1]; }
		echo $lng.': <input type=text name="'.$field.'_'.$lng.'" value="'.$text.'">'; 
	}
	echo '<p>description: '; $field = 'description'; $content = explode('|||', $article->$field);
	foreach($_SESSION['all_languages'] as $lng){
		$text = ''; 
		foreach($content as $f){ $f = explode(':::', $f); if($f[0] == $lng && isset($f[1]) && $f[1] != '') $text = $f[1]; }
		echo $lng.': <input type=text name="'.$field.'_'.$lng.'" value="'.$text.'">'; 
	}
	echo '<p>Короткий опис: '; $content = explode('|||', $article->short_text);
	foreach($_SESSION['all_languages'] as $lng){
		$text = ''; foreach($content as $f){ $f = explode(':::', $f); if($f[0] == $lng && isset($f[1]) && $f[1] != '') $text = $f[1]; }
		echo $lng.': <textarea name="short_text_'.$lng.'">'.$text.'</textarea>';
	}
	echo "<p>Повний текст: ";
	$simple = explode('|||', $article->text);
	foreach($_SESSION['all_languages'] as $lng){
		$text = '';
		foreach($simple as $s){
			$s = explode(':::', $s);
			if($s[0] == $lng && isset($s[1])) $text = $s[1];
		}
		echo '<center><h2>'.$lng.'</h2></center><textarea id="editor'.$lng.'" name="text_'.$lng.'" >'.$text.'</textarea><br>';
		echo '<input type="submit" value="Зберегти">';
	}
?>
   <input type=hidden name=type value=edit>
   <input type=hidden name=id value=<?=$article->id?>>
  </form>
</div>


<script type="text/javascript" src="<?=SITE_URL?>js/ckeditor/ckeditor.js"></script>
<script type="text/javascript" src="<?=SITE_URL?>js/ckfinder/ckfinder.js"></script>
<script type="text/javascript">
	<?php foreach($_SESSION['all_languages'] as $lng) echo "CKEDITOR.replace( 'editor{$lng}' ); "; ?>
	CKFinder.setupCKEditor( null, {
		basePath : '<?=SITE_URL?>js/ckfinder/',
		filebrowserBrowseUrl : '<?=SITE_URL?>js/ckfinder/ckfinder.html',
		filebrowserImageBrowseUrl : '<?=SITE_URL?>js/ckfinder/ckfinder.html?type=Images',
		filebrowserFlashBrowseUrl : '<?=SITE_URL?>js/ckfinder/ckfinder.html?type=Flash',
		filebrowserUploadUrl : '<?=SITE_URL?>js/ckfinder/core/connector/asp/connector.asp?command=QuickUpload&type=Files',
		filebrowserImageUploadUrl : '<?=SITE_URL?>js/ckfinder/core/connector/asp/connector.asp?command=QuickUpload&type=Images',
		filebrowserFlashUploadUrl : '<?=SITE_URL?>js/ckfinder/core/connector/asp/connector.asp?command=QuickUpload&type=Flash',
	});
</script>

<style>
	img.s-kartinka2  {
		margin: 5px;
		float:left;
		max-height: 150px;
		max-width: 250px;
	}
</style>
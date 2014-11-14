<div class="f-right inline">
	<a href="<?=SITE_URL.'admin/'.$_SESSION['alias']->alias?>/all">До всіх статей</a>
	<a href="<?=SITE_URL.'admin/'.$_SESSION['alias']->alias?>/categories">До категорій</a>
</div>

<button onClick="showUninstalForm()">Видалити статтю</button>
<br>
<div id="uninstall-form" style="background: rgba(236, 0, 0, 0.68); padding: 10px; display: none;">
	<form action="<?=SITE_URL.'admin/'.$_SESSION['alias']->alias?>/delete" method="POST">
		Ви впевнені що бажаєте видалити статтю?
		<br><br>
		<input type="hidden" name="id" value="<?=$article->id?>">
		<input type="submit" value="Видалити" style="margin-left:25px; float:left;">
	</form>
	<button style="margin-left:25px" onClick="showUninstalForm()">Скасувати</button>
	<div class="clear"></div>
</div>

<form action="<?=SITE_URL.'admin/'.$_SESSION['alias']->alias?>/save" method="POST" enctype="multipart/form-data">
	<input type="hidden" name="referer" value="<?=$_SERVER['HTTP_REFERER']?>">
	<input type="hidden" name="id" value="<?=$article->id?>">
	<table>
		<tr>
			<td>Фото</td>
			<td>
				<?php if($article->photo > 0){ ?>
					<img src="<?=IMG_PATH.$_SESSION['option']->folder.'/'.$article->photo?>.jpg" class="f-left">
					Змінити фото:<br>
				<?php } ?>
				<input type="file" name="photo" class="border-0">
			</td>
		</tr>
		<?php if($_SESSION['option']->useCategories){
			if($categories){
				echo "<tr><td>Категорія</td><td>";
				if($_SESSION['option']->articleMultiCategory){
					foreach ($categories as $c) { ?>
						<input type="checkbox" name="category[]" value="<?=$c->id?>" id="category-<?=$c->id?>" <?=(in_array($c->id, $article->category))?'checked':''?>>
						<label for="category-<?=$c->id?>"><?=$c->name?></label>
						<br>
					<?php }
				} else {
					echo('<select name="category">');
					foreach ($categories as $c) { ?>
						<option value="<?=$c->id?>" <?=($c->id == $article->category)?'selected':''?>><?=$c->name?></option>
					<?php }
					echo('</select>');
				}
				echo "</td></tr>";
			}
		} ?>
		<tr>
			<td>link</td>
			<?php $article->link = explode('-', $article->link); array_shift($article->link); $article->link = implode('-', $article->link); ?>
			<td><?=$article->id?>-<input type="text" name="link" value="<?=$article->link?>" required style="width:90%"></td>
		</tr>
		<tr>
			<td>active</td>
			<td>
				<input type="radio" name="active" value="1" <?=($article->active == 1)?'checked':''?> id="active-1"><label for="active-1">Так</label>
				<input type="radio" name="active" value="0" <?=($article->active == 0)?'checked':''?> id="active-0"><label for="active-0">Ні</label>
			</td>
		</tr>
		<tr>
			<td colspan="2" class="center"><input type="submit" value="Зберегти"></td>
		</tr>
	</table>
</form>

<?php 
	$content = $article->id;
	require_once('_edit_ntkdt_view.php');
?>

<style type="text/css">
	input[type="radio"]{
		min-width: 15px;
		height: 15px;
		margin-left: 15px;
		margin-right: 5px;
	}
	img.f-left {
		margin-right: 10px;
		height: 80px;
	}
</style>
<script type="text/javascript">
	function showUninstalForm () {
		if($('#uninstall-form').is(":hidden")){
			$('#uninstall-form').slideDown("slow");
		} else {
			$('#uninstall-form').slideUp("fast");
		}
	}
</script>
<div class="f-right inline">
	<a href="<?=SITE_URL.'admin/'.$_SESSION['alias']->alias?>/all">До всіх статей</a>
	<a href="<?=SITE_URL.'admin/'.$_SESSION['alias']->alias?>/categories">До категорій</a>
</div>

<button onClick="showUninstalForm()">Видалити категорію</button>
<br>
<div id="uninstall-form" style="background: rgba(236, 0, 0, 0.68); padding: 10px; display: none;">
	<form action="<?=SITE_URL.'admin/'.$_SESSION['alias']->alias?>/delete_category" method="POST">
		Ви впевнені що бажаєте видалити категорію?
		<br><br>
		<input type="checkbox" name="content" value="1" id="content" onChange="setContentUninstall(this)"><label for="content">Видалити всі статті, що пов'язані з категорією</label>
		<br>
		<input type="hidden" name="id" value="<?=$category->id?>">
		<input type="submit" value="Видалити" style="margin-left:25px; float:left;">
	</form>
	<button style="margin-left:25px" onClick="showUninstalForm()">Скасувати</button>
	<div class="clear"></div>
</div>

<form action="<?=SITE_URL.'admin/'.$_SESSION['alias']->alias?>/save_category" method="POST" enctype="multipart/form-data">
	<input type="hidden" name="id" value="<?=$category->id?>">
	<table>
		<tr>
			<td>Фото</td>
			<td>
				<?php if($category->photo > 0){ ?>
					<img src="<?=IMG_PATH.$_SESSION['option']->folder.'/categories/'.$category->photo?>.jpg" class="f-left">
					Змінити фото:<br>
				<?php } ?>
				<input type="file" name="photo" class="border-0">
			</td>
		</tr>
		<tr>
			<td>link</td>
			<td><input type="text" name="link" value="<?=$category->link?>" required></td>
		</tr>
		<tr>
			<td>active</td>
			<td>
				<input type="radio" name="active" value="1" <?=($category->active == 1)?'checked':''?> id="active-1"><label for="active-1">Так</label>
				<input type="radio" name="active" value="0" <?=($category->active == 0)?'checked':''?> id="active-0"><label for="active-0">Ні</label>
			</td>
		</tr>
		<tr>
			<td colspan="2" class="center"><input type="submit" value="Зберегти"></td>
		</tr>
	</table>
</form>

<?php 
	$content = $category->id * -1;
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
	function setContentUninstall (e){
		if(e.checked){
			if(confirm("Увага! Будуть видалені всі статті, що пов'язані з даною категорією! Ви впевнені що хочете видалити?")){
				e.checked = true;
			} else e.checked = false;
		}
	}
</script>
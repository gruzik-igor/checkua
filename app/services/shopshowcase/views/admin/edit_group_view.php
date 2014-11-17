<div class="f-right inline">
	<a href="<?=SITE_URL.'admin/'.$_SESSION['alias']->alias?>/all">До всіх товарів</a>
	<a href="<?=SITE_URL.'admin/'.$_SESSION['alias']->alias?>/groups">До всіх груп</a>
</div>

<button onClick="showUninstalForm()">Видалити групу</button>
<br>
<div id="uninstall-form" style="background: rgba(236, 0, 0, 0.68); padding: 10px; display: none;">
	<form action="<?=SITE_URL.'admin/'.$_SESSION['alias']->alias?>/delete_group" method="POST">
		Ви впевнені що бажаєте видалити групу?
		<br><br>
		<input type="checkbox" name="content" value="1" id="content" onChange="setContentUninstall(this)"><label for="content">Видалити всі товари і підгрупи, що пов'язані з даною групою</label>
		<br>
		<input type="hidden" name="id" value="<?=$group->id?>">
		<input type="submit" value="Видалити" style="margin-left:25px; float:left;">
	</form>
	<button style="margin-left:25px" onClick="showUninstalForm()">Скасувати</button>
	<div class="clear"></div>
</div>

<form action="<?=SITE_URL.'admin/'.$_SESSION['alias']->alias?>/save_group" method="POST" enctype="multipart/form-data">
	<input type="hidden" name="id" value="<?=$group->id?>">
	<table>
		<tr>
			<td>Фото</td>
			<td>
				<?php if($group->photo > 0){ ?>
					<img src="<?=IMG_PATH.$_SESSION['option']->folder.'/groups/'.$group->photo?>.jpg" class="f-left">
					Змінити фото:<br>
				<?php } ?>
				<input type="file" name="photo" class="border-0">
			</td>
		</tr>
		<tr>
			<td>Батьківська група</td>
			<td>
				<select name="parent" required>
					<option value="0">Немає</option>
					<?php if(isset($groups)){
						$list = array();
						$emptyParentsList = array();
						foreach ($groups as $g) {
							$list[$g->id] = $g;
							$list[$g->id]->child = array();
							if(isset($emptyParentsList[$g->id])){
								foreach ($emptyParentsList[$g->id] as $c) {
									$list[$g->id]->child[] = $c;
								}
							}
							if($g->parent > 0) {
								if(isset($list[$g->parent]->child)) $list[$g->parent]->child[] = $g->id;
								else {
									if(isset($emptyParentsList[$g->parent])) $emptyParentsList[$g->parent][] = $g->id;
									else $emptyParentsList[$g->parent] = array($g->id);
								}
							}
						}
						if(!empty($list)){
							function showList($group_id, $group_parent, $all, $list, $parent = 0, $level = 0)
							{
								$prefix = '';
								for ($i=0; $i < $level; $i++) { 
									$prefix .= '- ';
								}
								foreach ($list as $g) if($g->parent == $parent) {
									if($group_id == $g->id){
										echo('<optgroup label="'.$prefix.$g->name.'"></optgroup>');
									} else {
										$selected = '';
										if($g->id == $group_parent) $selected = 'selected';
										echo('<option value="'.$g->id.'" '.$selected.'>'.$prefix.$g->name.'</option>');
									}
									if(!empty($g->child)) {
										$l = $level + 1;
										$childs = array();
										foreach ($g->child as $c) {
											$childs[] = $all[$c];
										}
										showList ($group_id, $group_parent, $all, $childs, $g->id, $l);
									}
								}
								return true;
							}
							showList($group->id, $group->parent, $list, $list);
						}
					} ?>
				</select>
			</td>
		</tr>
		<tr>
			<td>link</td>
			<td><input type="text" name="link" value="<?=$group->link?>" required></td>
		</tr>
		<tr>
			<td>active</td>
			<td>
				<input type="radio" name="active" value="1" <?=($group->active == 1)?'checked':''?> id="active-1"><label for="active-1">Так</label>
				<input type="radio" name="active" value="0" <?=($group->active == 0)?'checked':''?> id="active-0"><label for="active-0">Ні</label>
			</td>
		</tr>
		<tr>
			<td colspan="2" class="center"><input type="submit" value="Зберегти"></td>
		</tr>
	</table>
</form>

<?php 
	$content = $group->id * -1;
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
			if(confirm("Увага! Будуть видалені всі товари даної групи та товари груп, що пов'язані з даною категорією! Ви впевнені що хочете видалити?")){
				e.checked = true;
			} else e.checked = false;
		}
	}
</script>
<div class="f-right inline">
	<a href="<?=SITE_URL.'admin/'.$_SESSION['alias']->alias?>/add">Додати товар</a>
	<a href="<?=SITE_URL.'admin/'.$_SESSION['alias']->alias?>/all">До всіх товарів</a>
	<a href="<?=SITE_URL.'admin/'.$_SESSION['alias']->alias?>/groups">До всіх груп</a>
</div>

<h1>Додати групу товарів</h1>

<form action="<?=SITE_URL.'admin/'.$_SESSION['alias']->alias?>/save_group" method="POST" enctype="multipart/form-data">
	<input type="hidden" name="id" value="0">
	<table>
		<tr>
			<td>Фото</td>
			<td><input type="file" name="photo" class="border-0"></td>
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
							function showList($all, $list, $parent = 0, $level = 0)
							{
								$prefix = '';
								for ($i=0; $i < $level; $i++) { 
									$prefix .= '- ';
								}
								foreach ($list as $g) if($g->parent == $parent) {
									echo('<option value="'.$g->id.'">'.$prefix.$g->name.'</option>');
									if(!empty($g->child)) {
										$l = $level + 1;
										$childs = array();
										foreach ($g->child as $c) {
											$childs[] = $all[$c];
										}
										showList ($all, $childs, $g->id, $l);
									}
								}
								return true;
							}
							showList($list, $list);
						}
					} ?>
				</select>
			</td>
		</tr>
		<?php if($_SESSION['language']) foreach ($_SESSION['all_languages'] as $lang) { ?>
			<tr>
				<td>Назва <?=$lang?></td>
				<td><input type="text" name="name_<?=$lang?>" value="" required></td>
			</tr>
		<?php } else { ?>
			<tr>
				<td>Назва</td>
				<td><input type="text" name="name" value="" required></td>
			</tr>
		<?php } ?>
		<tr>
			<td colspan="2" class="center"><input type="submit" value="Додати"></td>
		</tr>
	</table>
</form>
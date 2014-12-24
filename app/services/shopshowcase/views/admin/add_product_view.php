<?php require_once '_admin_words.php'; ?>

<div class="f-right inline">
	<a href="<?=SITE_URL.'admin/'.$_SESSION['alias']->alias?>/all">До всіх <?=$admin_words['products_to_all']?></a>
	<a href="<?=SITE_URL.'admin/'.$_SESSION['alias']->alias?>/groups">До всіх <?=$admin_words['groups_to_all']?></a>
</div>

<h1><?=$admin_words['product_add']?></h1>

<form action="<?=SITE_URL.'admin/'.$_SESSION['alias']->alias?>/save" method="POST" enctype="multipart/form-data">
	<input type="hidden" name="id" value="0">
	<table>
		<tr>
			<td>Фото</td>
			<td><input type="file" name="photo" class="border-0"></td>
		</tr>
		<?php if($_SESSION['option']->useGroups){
			$this->load->smodel('shop_model');
			$groups = $this->shop_model->getGroups(-1);
			if($groups){

				$list = array();
				$emptyChildsList = array();
				foreach ($groups as $g) {
					$list[$g->id] = $g;
					$list[$g->id]->child = array();
					if(isset($emptyChildsList[$g->id])){
						foreach ($emptyChildsList[$g->id] as $c) {
							$list[$g->id]->child[] = $c;
						}
					}
					if($g->parent > 0) {
						if(isset($list[$g->parent]->child)) $list[$g->parent]->child[] = $g->id;
						else {
							if(isset($emptyChildsList[$g->parent])) $emptyChildsList[$g->parent][] = $g->id;
							else $emptyChildsList[$g->parent] = array($g->id);
						}
					}
				}

				echo "<tr><td>Оберіть групу</td><td>";
				if($_SESSION['option']->ProductMultiGroup && !empty($list)){
					function showList($all, $list, $parent = 0, $level = 0, $parents = array())
					{

						$ml = 15 * $level;
						foreach ($list as $g) if($g->parent == $parent) {
							$class = '';
							if($g->parent > 0 && !empty($parents)){
								$class = 'class="';
								foreach ($parents as $p) {
									$class .= ' parent-'.$p;
								}
								$class .= '"';
							}
							if(empty($g->child)){
								$checked = '';
								if(isset($_GET['group']) && $_GET['group'] == $g->id) $checked = 'checked';
								echo ('<input type="checkbox" name="group[]" value="'.$g->id.'" id="group-'.$g->id.'" '.$class.' '.$checked.'>');
								echo ('<label for="group-'.$g->id.'">'.$g->name.'</label>');
								echo ('<br>');
							} else {
								echo ('<input type="checkbox" id="group-'.$g->id.'" '.$class.' onChange="setChilds('.$g->id.')">');
								echo ('<label for="group-'.$g->id.'">'.$g->name.'</label>');
								$l = $level + 1;
								$childs = array();
								foreach ($g->child as $c) {
									$childs[] = $all[$c];
								}
								$ml = 15 * $l;
								echo ('<div style="margin-left: '.$ml.'px">');
								$parents2 = $parents;
								$parents2[] = $g->id;
								showList ($all, $childs, $g->id, $l, $parents2);
								echo('</div>');
							}
						}

						return true;
					}
					showList($list, $list);
				} else {
					echo('<select name="group">');
					echo ('<option value="0">Немає</option>');
					if(!empty($list)){
						function showList($all, $list, $parent = 0, $level = 0)
						{
							$prefix = '';
							for ($i=0; $i < $level; $i++) { 
								$prefix .= '- ';
							}
							foreach ($list as $g) if($g->parent == $parent) {
								if(empty($g->child)){
									$selected = '';
									if(isset($_GET['group']) && $_GET['group'] == $g->id) $selected = 'selected';
									echo('<option value="'.$g->id.'" '.$selected.'>'.$prefix.$g->name.'</option>');
								} else {
									echo('<optgroup label="'.$prefix.$g->name.'">');
									$l = $level + 1;
									$childs = array();
									foreach ($g->child as $c) {
										$childs[] = $all[$c];
									}
									showList ($all, $childs, $g->id, $l);
									echo('</optgroup>');
								}
							}
							return true;
						}
						showList($list, $list);
					}
					echo('</select>');
				}
				echo "</td></tr>";
			}
		}
		if($_SESSION['language']) foreach ($_SESSION['all_languages'] as $lang) { ?>
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
			<td>Ціна</td>
			<td><input type="number" name="price" value="0" min="0" required></td>
		</tr>
		<tr>
			<td colspan="2" class="center"><input type="submit" value="Додати"></td>
		</tr>
	</table>
</form>

<script>
	function setChilds (parent) {
		if($('#group-'+parent).prop('checked')){
			$('.parent-'+parent).prop('checked', true);
		} else {
			$('.parent-'+parent).prop('checked', false);
		}
	}
</script>
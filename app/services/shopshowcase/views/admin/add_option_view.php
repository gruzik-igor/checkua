<?php require_once '_admin_words.php'; ?>

<div class="f-right inline">
	<a href="<?=SITE_URL.'admin/'.$_SESSION['alias']->alias?>/options">До всіх <?=$admin_words['options_to_all']?></a>
	<a href="<?=SITE_URL.'admin/'.$_SESSION['alias']->alias?>/groups">До всіх <?=$admin_words['groups_to_all']?></a>
</div>

<h1><?=$admin_words['option_add']?></h1>

<form action="<?=SITE_URL.'admin/'.$_SESSION['alias']->alias?>/save_option" method="POST">
	<input type="hidden" name="id" value="0">
	<table>
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
							$selected = '';
							if(isset($_GET['group']) && $_GET['group'] == $g->id) $selected = 'selected';
							echo('<option value="'.$g->id.'" '.$selected.'>'.$prefix.$g->name.'</option>');
							if(!empty($g->child)){
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
				echo('</select>');
			}
			echo "</td></tr>";
		}
		if($_SESSION['language']) foreach ($_SESSION['all_languages'] as $lang) { ?>
			<tr>
				<td>Назва <?=$lang?></td>
				<td><input type="text" name="name_<?=$lang?>" value="" required></td>
			</tr>
			<tr>
				<td>Суфікс (розмірність) <?=$lang?></td>
				<td><input type="text" name="sufix_<?=$lang?>" value=""></td>
			</tr>
		<?php } else { ?>
			<tr>
				<td>Назва</td>
				<td><input type="text" name="name" value="" required></td>
			</tr>
			<tr>
				<td>Суфікс (розмірність)</td>
				<td><input type="text" name="sufix" value=""></td>
			</tr>
		<?php } ?>
		<tr>
			<td>Тип</td>
			<td><select name="type" required>
				<?php $types = $this->db->getAllData('wl_input_types');
					foreach ($types as $type) {
						echo("<option value='{$type->id}'>{$type->name}</option>");
					}
				?>
				</select>
			</td>
		</tr>
		<tr>
			<td colspan="2" class="center"><input type="submit" value="Додати"></td>
		</tr>
	</table>
</form>
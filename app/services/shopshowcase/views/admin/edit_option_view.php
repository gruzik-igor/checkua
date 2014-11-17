<div class="f-right inline">
	<a href="<?=SITE_URL.'admin/'.$_SESSION['alias']->alias?>/options">До всіх параметрів</a>
	<a href="<?=SITE_URL.'admin/'.$_SESSION['alias']->alias?>/groups">До всіх груп</a>
</div>

<h1>Редагувати параметр товару</h1>

<button onClick="showUninstalForm()">Видалити параметр</button>
<br>
<div id="uninstall-form" style="background: rgba(236, 0, 0, 0.68); padding: 10px; display: none;">
	<form action="<?=SITE_URL.'admin/'.$_SESSION['alias']->alias?>/delete_option" method="POST">
		Ви впевнені що бажаєте видалити параметр із всіма властивостями, якщо такі є?
		<br><br>
		<input type="hidden" name="id" value="<?=$option->id?>">
		<input type="submit" value="Видалити" style="margin-left:25px; float:left;">
	</form>
	<button style="margin-left:25px" onClick="showUninstalForm()">Скасувати</button>
	<div class="clear"></div>
</div>

<form action="<?=SITE_URL.'admin/'.$_SESSION['alias']->alias?>/save_option" method="POST">
	<input type="hidden" name="id" value="<?=$option->id?>">
	<table>
		<tr>
			<td>active</td>
			<td>
				<input type="radio" name="active" value="1" <?=($option->active == 1)?'checked':''?> id="active-1"><label for="active-1">Так</label>
				<input type="radio" name="active" value="0" <?=($option->active == 0)?'checked':''?> id="active-0"><label for="active-0">Ні</label>
			</td>
		</tr>
		<tr>
			<td>link</td>
			<?php $option->link = explode('-', $option->link); array_shift($option->link); $option->link = implode('-', $option->link); ?>
			<td><?=$option->id?>-<input type="text" name="link" value="<?=$option->link?>" required style="width:90%"></td>
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
				echo('<select name="group">');
				echo ('<option value="0">Немає</option>');
				if(!empty($list)){
					function showList($option_id, $all, $list, $parent = 0, $level = 0)
					{
						$prefix = '';
						for ($i=0; $i < $level; $i++) { 
							$prefix .= '- ';
						}
						foreach ($list as $g) if($g->parent == $parent) {
							$selected = '';
							if($option_id == $g->id) $selected = 'selected';
							echo('<option value="'.$g->id.'" '.$selected.'>'.$prefix.$g->name.'</option>');
							if(!empty($g->child)){
								$l = $level + 1;
								$childs = array();
								foreach ($g->child as $c) {
									$childs[] = $all[$c];
								}
								showList ($option_id, $all, $childs, $g->id, $l);
							}
						}
						return true;
					}
					showList($option->group, $list, $list);
				}
				echo('</select>');
			}
			echo "</td></tr>";
		}
		$ns = $this->db->getAllDataByFieldInArray($this->shop_model->table('_options_name'), $option->id, 'option');
		if($_SESSION['language']){
			$names = array();
			foreach ($ns as $n) {
				$names[$n->language] = $n;
			}
		 foreach ($_SESSION['all_languages'] as $lang) { 
		 	if(empty($names[$lang])){
				$data = array();
				$data['option'] = $option->id;
				$data['language'] = $lang;
				$data['name'] = '';
				if($this->db->insertRow($this->shop_model->table('_options_name'), $data)){
					@$names[$lang]->name = '';
					$names[$lang]->sufix = '';
				}
		 	}
		 	?>
			<tr>
				<td>Назва <?=$lang?></td>
				<td><input type="text" name="name_<?=$lang?>" value="<?=$names[$lang]->name?>" required></td>
			</tr>
			<tr>
				<td>Суфікс (розмірність) <?=$lang?></td>
				<td><input type="text" name="sufix_<?=$lang?>" value="<?=$names[$lang]->sufix?>"></td>
			</tr>
		<?php } } else { ?>
			<tr>
				<td>Назва</td>
				<td><input type="text" name="name" value="<?=$ns[0]->name?>" required></td>
			</tr>
			<tr>
				<td>Суфікс (розмірність)</td>
				<td><input type="text" name="sufix" value="<?=$ns[0]->sufix?>"></td>
			</tr>
		<?php } ?>
		<tr>
			<td>Тип</td>
			<td><select name="type" required>
				<?php $types = $this->db->getAllData('wl_input_types');
						$options = false;
					foreach ($types as $type) {
						$selected = '';
						if($type->id == $option->type){
							$selected = 'selected';
							if($type->options == 1) $options = true;
						}
						echo("<option value='{$type->id}' {$selected}>{$type->name}</option>");
					}
				?>
				</select>
			</td>
		</tr>
		<?php if($options){ ?>
		<tr>
			<td>filter</td>
			<td>
				<input type="radio" name="filter" value="1" <?=($option->filter == 1)?'checked':''?> id="filter-1"><label for="filter-1">Так</label>
				<input type="radio" name="filter" value="0" <?=($option->filter == 0)?'checked':''?> id="filter-0"><label for="filter-0">Ні</label>
			</td>
		</tr>
		<?php
			echo('</table><table id="options">');
			echo('<tr><td colspan="');
			$colspan = 2;
			if($_SESSION['language']) $colspan += count($_SESSION['all_languages']);
			else $colspan++;
			echo($colspan.'" class="center">Властивості параметру <span onClick="addOptionRow()" class="f-right pointer">Додати властивість</span></td></tr>');
			$options = array();
			if($_SESSION['language']){
				$options = $this->db->getAllDataByFieldInArray($this->shop_model->table('_group_options'), ($option->id * -1), 'group');
				echo("<tr><td></td>");
				foreach ($_SESSION['all_languages'] as $lang) {
					echo("<td>{$lang}</td>");
				}
				echo("<td></td></tr>");
			} else {
				$this->db->executeQuery("SELECT o.*, n.id as name_id, n.name FROM `{$this->shop_model->table('_group_options')}` as o LEFT JOIN `{$this->shop_model->table('_options_name')}` as n ON n.option = o.id WHERE o.group = '-{$option->id}'");
				if($this->db->numRows() > 0){
                    $options = $this->db->getRows('array');
                }
			}
			
			if($options){
				$i = 1;
				if($_SESSION['language']){
					foreach ($options as $opt){
						$names_db = $this->db->getAllDataByFieldInArray($this->shop_model->table('_options_name'), $opt->id, 'option');
						$names = array();
						if($names_db){
							foreach ($names_db as $name) {
								@$names[$name->language]->id = $name->id;
								$names[$name->language]->name = $name->name;
							}
						}
						echo('<tr id="option_'.$opt->id.'">');
						echo("<td>#{$i}</td>");
						foreach ($_SESSION['all_languages'] as $lang) {
							$value = '';
							$value_id = 0;
							if(isset($names[$lang])){
								$value = $names[$lang]->name;
								$value_id = $names[$lang]->id;
							} else {
								$data = array();
								$data['option'] = $opt->id;
								$data['language'] = $lang;
								$data['name'] = '';
								$this->db->insertRow($this->shop_model->table('_options_name'), $data);
								$value_id = $this->db->getLastInsertedId();
							}
							if($value_id > 0) {
								echo("<td><input type='text' name='option_{$value_id}' value='{$value}'></td>");
							} else {
								echo("<td>Error {$lang}</td>");
							}
						}
						echo('<td><span onClick="deleteOptionRow('.$opt->id.')" class="pointer">Видалити властивість</span>');
						echo('</tr>');
						$i++;
					}
				} else {
					foreach ($options as $opt) {
						echo('<tr id="option_'.$opt->id.'">');
						echo("<td>#{$i}</td>");
						echo("<td><input type='text' name='option_{$opt->name_id}' value='{$opt->name}'></td>");
						echo('<td><span onClick="deleteOptionRow('.$opt->id.')" class="pointer">Видалити властивість</span>');
						echo('</tr>');
						$i++;
					}
				}
			} else {
				echo("<tr>");
				if($_SESSION['language']){
					echo("<tr><td>#1</td>");
					foreach ($_SESSION['all_languages'] as $lang) {
						echo("<td><input type='text' name='option_0_{$lang}[]' required></td>");
					}
				} else {
					echo("<td>#1</td><td><input type='text' name='option_0[]' required></td>");
				}
				echo("<td></td>");
				echo("</tr>");
			}
		} ?>
	</table>
	<div class="center"><input type="submit" value="Зберегти"></div>
</form>

<script type="text/javascript">
	function addOptionRow () {
		var countRows = $('#options tr').length;
		<?php if($_SESSION['language']){ ?>
			countRows--;
			var appendText = '<tr><td>#' + countRows + '</td>';
			<?php foreach ($_SESSION['all_languages'] as $lang) { ?>
				appendText += '<td><input type="text" name="option_0_<?=$lang?>[]"></td>';
		<?php } } else { ?>
			var appendText = '<tr><td>#' + countRows + '</td>';
			appendText += '<td><input type="text" name="option_0[]"></td>';
		<?php } ?>
		appendText += '<td>*Пустий рядок зараховуватися не буде</td></tr>';
		$('#options').append(appendText);
	}

	function deleteOptionRow (id) {
		if(confirm("Ви впевнені що бажаєте видалити властивість?")){
			$.ajax({
				url: "<?=SITE_URL.'admin/'.$_SESSION['alias']->alias?>/deleteOptionProperty",
				type: 'POST',
				data: {
					id :  id,
					json : true
				},
				success: function(res){
					if(res['result'] == false){
						alert('Помилка! Спробуйте щераз');
					} else {
						$('#option_'+id).slideUp("fast");
					}
				}
			});
		}
	}

	function showUninstalForm () {
		if($('#uninstall-form').is(":hidden")){
			$('#uninstall-form').slideDown("slow");
		} else {
			$('#uninstall-form').slideUp("fast");
		}
	}
</script>
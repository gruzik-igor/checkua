<form action="<?=SITE_URL.'admin/'.$_SESSION['alias']->alias?>/save" method="POST" enctype="multipart/form-data" id="editProductForm">
	<input type="hidden" name="id" value="<?=$product->id?>">
	<table class="table table-striped table-bordered">
		<tr>
			<th>Id на сайті</th>
			<td><?=$product->id?></td>
		</tr>
		<tr>
			<th style="width:25%">Власна адреса посилання</th>
			<td>
				<div class="input-group">
					<?php
					if($_SESSION['option']->ProductUseArticle) {
						$product->article = $this->data->latterUAtoEN($product->article);
						$alias = substr($product->alias, strlen($product->article) + 1);
						echo('<span class="input-group-addon">/'.$url.'/'.$product->article.'-</span>');
					} else {
						$alias = explode('-', $product->alias); array_shift($alias); $alias = implode('-', $alias);
						echo('<span class="input-group-addon">/'.$url.'/'.$product->id.'-</span>');
					}
					?>
                    <input type="text" name="alias" value="<?=$alias?>" required class="form-control">
                </div>
            </td>
		</tr>
		<?php if($_SESSION['option']->ProductUseArticle) { ?>
    		<tr>
				<th>Артикул</th>
				<td>
					<input type="text" name="article" value="<?=$product->article?>" class="form-control" required>
					<input type="hidden" name="article_old" value="<?=$product->article?>">
				</td>
			</tr>
		<?php }
		if($_SESSION['option']->useGroups && $groups)
		{
			$list = array();
			$emptyChildsList = array();
			foreach ($groups as $g) {
				$list[$g->id] = $g;
				$list[$g->id]->child = array();
				if(isset($emptyChildsList[$g->id]))
					foreach ($emptyChildsList[$g->id] as $c) {
						$list[$g->id]->child[] = $c;
					}
				if($g->parent > 0)
				{
					if(isset($list[$g->parent]->child))
						$list[$g->parent]->child[] = $g->id;
					else
					{
						if(isset($emptyChildsList[$g->parent])) $emptyChildsList[$g->parent][] = $g->id;
						else $emptyChildsList[$g->parent] = array($g->id);
					}
				}
			}

			if($_SESSION['option']->ProductMultiGroup)
			{
	            $_SESSION['alias']->js_load[] = 'assets/switchery/switchery.min.js';
				echo '<link rel="stylesheet" href="'.SITE_URL.'assets/switchery/switchery.min.css" />';
				$_SESSION['alias']->js_load[] = 'assets/jstree/jstree.min.js';
				echo '<link rel="stylesheet" href="'.SITE_URL.'assets/jstree/themes/default/style.min.css" />';
				echo "<tr><td colspan=2><table class=\"table table-striped table-bordered\">";
				echo "<tr>
						<th style='width:80px'>Стан</th>
						<th>Група <a href=\"#modal-groupsTree\" data-toggle=\"modal\" class=\"btn btn-success btn-xs right\"><i class=\"fa fa-list\"></i> Керувати групами товару</a></th>";
				$order = explode(' ', $_SESSION['option']->productOrder);
				if($order[0] == 'position')
					echo "<th style='width:225px'>Позиція (Режим: <i>{$_SESSION['option']->productOrder}</i>)</th>";
				echo "</tr><tr>";
				if($product->group)
				{
					function parentsLink(&$parents, $all, $parent, $link)
					{
						if($parent > 0)
						{
							$link = $all[$parent]->alias .'/'.$link;
							$parents[] = $parent;
							if($all[$parent]->parent > 0) $link = parentsLink ($parents, $all, $all[$parent]->parent, $link);
							return $link;
						}
					}
					function makeLink($all, $parent, $link)
					{
						if($parent > 0)
						{
							$link = $all[$parent]->alias .'/'.$link;
							if($all[$parent]->parent > 0) $link = parentsLink ($parents, $all, $all[$parent]->parent, $link);
						}
						return $link;
					}
					foreach ($product->group as $g) {
						$g = $list[$g]; 
						$checked = ($g->product_active) ? 'checked' : '';
		            	echo '<td><input name="active-group-'.$g->id.'" type="checkbox" data-render="switchery" class="switchery-small" '.$checked.' value="1" /></td>';

						echo "<td>"; reset($_SESSION['alias']->breadcrumb);
						$link = SITE_URL.'admin/'.$_SESSION['alias']->alias;
						$name = key($_SESSION['alias']->breadcrumb);
		            	echo "<a href=\"{$link}\" target=\"_blank\">{$name}</a>/";
		            	if($g->parent > 0) {
		            		$parents = array();
		            		$g->link = SITE_URL.'admin/'.$_SESSION['alias']->alias . '/' . parentsLink($parents, $list, $g->parent, $g->alias);
		            		if($parents)
		            		{
		            			rsort($parents);
		            			foreach ($parents as $parent) {
		            				$link = SITE_URL.'admin/'.$_SESSION['alias']->alias . '/' . makeLink($list, $list[$parent]->parent, $list[$parent]->alias);
		            				echo "<a href=\"{$link}\" target=\"_blank\">{$list[$parent]->name}</a>/";
		            			}
		            		}
		            	}
		            	else
		            		$g->link = SITE_URL.'admin/'.$_SESSION['alias']->alias . '/' . $g->alias;
		            	echo "<a href=\"{$g->link}\" target=\"_blank\"><strong>{$g->name}</strong></a></td>";
		            	
		            	if($order[0] == 'position')
		            		echo '<td><input type="number" name="position-group-'.$g->id.'" title="Обережно при зміні" value="'.$g->product_position.'" min="1" max="'.$g->product_position_max.'" class="form-control" required></td>';
		            	echo "</tr>";
		            }
		        } else {
		        	echo "<tr><td colspan=3 class='center'>
		        		<a href=\"#modal-groupsTree\" data-toggle=\"modal\" class=\"btn btn-success\"><i class=\"fa fa-list\"></i> Керувати групами товару</a>
		        		</td></tr>";
		        }
				echo "</table>";
				echo '<input type="hidden" name="product_groups" id="selected" value="'.implode(',', $product->group).'" />';
				echo "</td></tr>";
			}
			else
			{
				echo "<tr><th>Оберіть {$_SESSION['admin_options']['word:groups_to_delete']}</th><td>";
				echo('<input type="hidden" name="group_old" value="'.$product->group.'">');
				echo('<select name="group" class="form-control">');
				echo ('<option value="0">Немає</option>');
				if(!empty($list))
				{
					function showList($product_group, $all, $list, $parent = 0, $level = 0)
					{
						$prefix = '';
						for ($i=0; $i < $level; $i++) {
							$prefix .= '- ';
						}
						foreach ($list as $g) if($g->parent == $parent) {
							if(empty($g->child)) {
								$selected = '';
								if($product_group == $g->id) $selected = 'selected';
								echo('<option value="'.$g->id.'" '.$selected.'>'.$prefix.$g->name.'</option>');
							} else {
								echo('<optgroup label="'.$prefix.$g->name.'">');
								$l = $level + 1;
								$childs = array();
								foreach ($g->child as $c) {
									$childs[] = $all[$c];
								}
								showList ($product_group, $all, $childs, $g->id, $l);
								echo('</optgroup>');
							}
						}
						return true;
					}
					showList($product->group, $list, $list);
				}
				echo('</select>');
				echo "</td></tr>"; ?>
				<tr>
					<th>Позиція в групі</th>
					<td>
						<input type="hidden" name="position_old" value="<?=$product->position?>">
						<div class="input-group">
							<input type="number" name="position" title="Обережно при зміні" value="<?=$product->position?>" min="1" required class="form-control">
							<span class="input-group-addon">Режим: <i><?=$_SESSION['option']->productOrder?></i></span>
						</div>
					</td>
				</tr>
				<tr>
					<th>Стан</th>
					<td>
						<input type="radio" name="active" value="1" <?=($product->active == 1)?'checked':''?> id="active-1"><label for="active-1">Публікація активна</label>
						<input type="radio" name="active" value="0" <?=($product->active == 0)?'checked':''?> id="active-0"><label for="active-0">Публікацію тимчасово відключено</label>
					</td>
				</tr>
			<?php }

			if(!empty($list))
			{
				if($_SESSION['option']->ProductMultiGroup)
				{
					foreach ($product->group as $parent) {
						while ($parent != 0) {
							if(!in_array($parent, $options_parents))
								array_unshift($options_parents, $parent);
							$parent = $list[$parent]->parent;
						}
					}
				}
				else
				{
					$parent = $product->group;
					while ($parent != 0) {
						array_unshift($options_parents, $parent);
						$parent = $list[$parent]->parent;
					}
				}

			}
		} if($_SESSION['option']->useAvailability) { ?>
			<tr>
				<th>Наявність</th>
				<td>
					<?php $where_language = '';
	            	if($_SESSION['language']) $where_language = "AND n.language = '{$_SESSION['language']}'";
					$this->db->executeQuery("SELECT a.*, n.name FROM {$_SESSION['service']->table}_availability as a LEFT JOIN {$_SESSION['service']->table}_availability_name as n ON n.availability = a.id {$where_language} WHERE a.active = 1 ORDER BY a.position ASC");
					if($this->db->numRows() > 0){
	            		$availabilities = $this->db->getRows('array');
	            		foreach ($availabilities as $availability) { ?>
	            			<input type="radio" name="availability" value="<?=$availability->id?>" <?=($product->availability == $availability->id)?'checked':''?> id="availability-<?=$availability->id?>"><label for="availability-<?=$availability->id?>"><?=$availability->name?></label>
	            		<?php }
	            	}
	            	?>
				</td>
			</tr>
		<?php } ?>
		<tr>
			<th>Вартість (y.o.)</th>
			<td>
				<div class="input-group">
                    <input type="number" name="price" value="<?=$product->price?>" min="0" step="0.01" required class="form-control">
                    <span class="input-group-addon">y.o.</span>
                </div>
			</td>
		</tr>
		<tr>
			<th>Стара ціна (y.o.)</th>
			<td>
				<div class="input-group">
                    <input type="number" name="old_price" value="<?=$product->old_price?>" min="0" step="0.01" class="form-control">
                    <span class="input-group-addon">y.o.</span>
                </div>
			</td>
		</tr>
		<?php array_unshift($options_parents, 0);
		$showh3 = $init_select2 = true; 
		$this->load->smodel('options_model');
		foreach ($options_parents as $option_id) {
			if($options = $this->options_model->getOptions($option_id))
				foreach ($options as $option)
					if($_SESSION['language'] == false || ($option->type_name != 'text' && $option->type_name != 'textarea'))
					{
						if($showh3)
						{
							echo "<tr><td colspan=\"2\"><h3>Властивості {$_SESSION['admin_options']['word:products']}</h3></td></tr>";
							$showh3 = false;
						}

						$value = '';
						if(isset($product_options[$option->id])) $value = $product_options[$option->id];
						echo('<tr>');
						echo('<th>'.$option->name);
						if($option->sufix != '') echo " ({$option->sufix})";
						echo('</th><td>');
						if($option->toCart && $option->type_name != 'checkbox')
						{
							echo 'Обирає клієнт перед додачею в корзину (для ручного керування оберіть тип властивісті checkbox): ';
							$where = ($_SESSION['language']) ? "AND n.language = '{$_SESSION['language']}'" : '';
							$option_values = $this->db->getQuery("SELECT o.*, n.id as name_id, n.name FROM `{$this->shop_model->table('_options')}` as o LEFT JOIN `{$this->shop_model->table('_options_name')}` as n ON n.option = o.id {$where} WHERE o.group = '-{$option->id}'", 'array');
							if(!empty($option_values))
							{
								$names = array();
								foreach ($option_values as $ov) {
									$names[] = '<strong>'.$ov->name.'</strong>';
								}
								echo implode(', ', $names);
							}

						}
						if($option->type_name == 'checkbox' || $option->type_name == 'checkbox-select2')
						{
							$where = '';
							if($_SESSION['language']) $where = "AND n.language = '{$_SESSION['language']}'";
							$option_values = array();
							$this->db->executeQuery("SELECT o.*, n.id as name_id, n.name FROM `{$this->shop_model->table('_options')}` as o LEFT JOIN `{$this->shop_model->table('_options_name')}` as n ON n.option = o.id {$where} WHERE o.group = '-{$option->id}'");
							if($this->db->numRows() > 0)
			                    $option_values = $this->db->getRows('array');

							if(!empty($option_values))
							{
								$value = explode(',', $value);
								if($option->type_name == 'checkbox')
									foreach ($option_values as $ov) {
										$checked = '';
										if(in_array($ov->id, $value)) $checked = ' checked';
										echo('<input type="checkbox" name="option-'.$option->id.'[]" value="'.$ov->id.'" id="option-'.$ov->id.'" '.$checked.'> <label for="option-'.$ov->id.'">'.$ov->name.'</label> ');
									}
								else
								{
									echo('<select name="option-'.$option->id.'[]" class="form-control select2" multiple="multiple"> ');
									foreach ($option_values as $ov) {
										$selected = '';
										if(in_array($ov->id, $value)) $selected = ' selected';
										echo("<option value='{$ov->id}'{$selected}>{$ov->name}</option>");
									}
									echo("</select> ");
									
									if($init_select2)
									{
										$init_select2 = false;
										echo '<link rel="stylesheet" href="'.SITE_URL.'assets/select2/select2.min.css" />';
										$_SESSION['alias']->js_load[] = 'assets/select2/select2.min.js';
									}
								}
							}
						}
						elseif($option->type_name == 'radio' && !$option->toCart)
						{
							$where = ($_SESSION['language']) ? "AND n.language = '{$_SESSION['language']}'" : '';
							$option_values = $this->db->getQuery("SELECT o.*, n.id as name_id, n.name FROM `{$this->shop_model->table('_options')}` as o LEFT JOIN `{$this->shop_model->table('_options_name')}` as n ON n.option = o.id {$where} WHERE o.group = '-{$option->id}'", 'array');
							if(!empty($option_values))
							{
								$checked = ($value == '' || $value == 0) ? ' checked' : '';
								echo('<input type="radio" name="option-'.$option->id.'" value="0" id="option-'.$option->id.'-0" '.$checked.'> <label for="option-'.$option->id.'-0">Не вказано</label> ');
								foreach ($option_values as $ov) {
									$checked = ($value == $ov->id) ? ' checked' : '';
									echo('<input type="radio" name="option-'.$option->id.'" value="'.$ov->id.'" id="option-'.$ov->id.'" '.$checked.'> <label for="option-'.$ov->id.'">'.$ov->name.'</label> ');
								}
							}
						}
						elseif($option->type_name == 'select' && !$option->toCart)
						{
							$where = '';
							if($_SESSION['language']) $where = "AND n.language = '{$_SESSION['language']}'";
							$option_values = array();
							$this->db->executeQuery("SELECT o.*, n.id as name_id, n.name FROM `{$this->shop_model->table('_options')}` as o LEFT JOIN `{$this->shop_model->table('_options_name')}` as n ON n.option = o.id {$where} WHERE o.group = '-{$option->id}'");
							if($this->db->numRows() > 0){
			                    $option_values = $this->db->getRows('array');
			                }
							echo('<select name="option-'.$option->id.'" class="form-control select2"> ');
							echo("<option value='0'>Не вказано</option>");
							if(!empty($option_values)){
								foreach ($option_values as $ov) {
									$selected = '';
									if($value == $ov->id) $selected = ' selected';
									echo("<option value='{$ov->id}'{$selected}>{$ov->name}</option>");
								}
							}
							echo("</select> ");
							if($init_select2)
							{
								$init_select2 = false;
								echo '<link rel="stylesheet" href="'.SITE_URL.'assets/select2/select2.min.css" />';
								$_SESSION['alias']->js_load[] = 'assets/select2/select2.min.js';
							}
						}
						elseif($option->type_name == 'textarea' && !$option->toCart)
						{
							echo('<textarea onChange="saveOption(this, \''.$option->name.'\')" name="option-'.$option->id.'">'.$value.'</textarea>');
						}
						elseif(!$option->toCart)
						{
							if($option->sufix != '')
								echo('<div class="input-group">');
							echo('<input type="'.$option->type_name.'" name="option-'.$option->id.'" value="'.$value.'"  class="form-control" onChange="saveOption(this, \''.$option->name.'\')"> ');
							if($option->sufix != '')
							{
								echo("<span class=\"input-group-addon\">{$option->sufix}</span>");
								echo('</div>');
							}
						}
						echo('</td></tr>');
					}
		}
		?>
		<tr>
			<td>
				Після збереження:
			</td>
			<td id="after_save">
				<input type="radio" name="to" value="edit" id="to_edit" checked="checked"><label for="to_edit">продовжити редагування</label>
				<input type="radio" name="to" value="category" id="to_category"><label for="to_category">до списку <?=$_SESSION['admin_options']['word:products_to_all']?></label>
				<input type="radio" name="to" value="new" id="to_new"><label for="to_new"><?=$_SESSION['admin_options']['word:product_add']?></label>
			</td>
		</tr>
		<tr>
			<td></td>
			<td><button type="submit" class="btn btn-sm btn-success col-md-6">Зберегти</button></td>
		</tr>
	</table>
</form>

<div class="modal fade" id="modal-groupsTree">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
				<h4 class="modal-title">Керувати групами товару</h4>
			</div>
			<div class="modal-body">
				<img src="<?=SITE_URL?>style/admin/images/icon-loading.gif" width=40> Завантаження груп...
			</div>
			<div class="modal-footer">
				<div class="col-md-6">
					<input type="search" id="search" class="form-control col-md-6" placeholder="Пошук" />
				</div>
				
				<a href="javascript:;" class="btn btn-sm btn-white" data-dismiss="modal">Закрити</a>
				<input type="submit" class="btn btn-sm btn-success" value="Зберегти" form="editProductForm">
			</div>
		</div>
	</div>
</div>

<style type="text/css">
	input[type="radio"]{
		min-width: 15px;
		height: 15px;
		margin-left: 15px;
		margin-right: 5px;
	}
	input[type="checkbox"]{
		margin-right: 5px;
	}
	img.f-left {
		margin-right: 10px;
		height: 80px;
	}
	#after_save label {
		font-weight: normal;
		width: auto;
		padding-right: 10px;
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
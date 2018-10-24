<form action="<?=SITE_URL.'admin/'.$_SESSION['alias']->alias?>/save" method="POST" enctype="multipart/form-data" id="editProductForm" class="form-horizontal">
	<input type="hidden" name="id" value="<?=$product->id?>">
	<div class="form-group">
		<label class="col-md-3 control-label">Id на сайті</label>
	    <div class="col-md-9">
	    	<strong><?=$product->id?></strong>
	    </div>
    </div>
    <div class="form-group">
		<label class="col-md-3 control-label">Власна адреса посилання</label>
	    <div class="col-md-9">
	    	<div class="input-group">
				<?php
				if($_SESSION['option']->ProductUseArticle) {
					$article = $this->data->latterUAtoEN($product->article);
					$alias = substr($product->alias, strlen($article) + 1);
					echo('<span class="input-group-addon">/'.$url.'/'.$article.'-</span>');
				} else {
					$alias = explode('-', $product->alias); array_shift($alias); $alias = implode('-', $alias);
					echo('<span class="input-group-addon">/'.$url.'/'.$product->id.'-</span>');
				}
				?>
                <input type="text" name="alias" value="<?=$alias?>" class="form-control">
            </div>
	    </div>
    </div>

	<?php if($_SESSION['option']->ProductUseArticle) { ?>
		<div class="form-group">
			<label class="col-md-3 control-label">Артикул</label>
		    <div class="col-md-9">
		    	<input type="text" name="article" value="<?=$product->article?>" class="form-control" required>
				<input type="hidden" name="article_old" value="<?=$product->article?>">
		    </div>
	    </div>
	<?php } if(!$changePriceTab) { ?>
		<div class="form-group">
			<label class="col-md-3 control-label">Вартість (y.o.)</label>
		    <div class="col-md-9">
		    	<div class="input-group">
		            <input type="number" name="price" value="<?=$product->price?>" min="0" step="0.01" required class="form-control">
		            <span class="input-group-addon">y.o.</span>
		        </div>
		    </div>
	    </div>
	    <div class="form-group">
			<label class="col-md-3 control-label">Стара ціна (y.o.)</label>
		    <div class="col-md-9">
		    	<div class="input-group">
		            <input type="number" name="old_price" value="<?=$product->old_price?>" min="0" step="0.01" class="form-control">
		            <span class="input-group-addon">y.o.</span>
		        </div>
		    </div>
	    </div>
	<?php } if($_SESSION['option']->useAvailability) { ?>
		<div class="form-group">
			<label class="col-md-3 control-label">Наявність</label>
		    <div class="col-md-9">
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
		    </div>
	    </div>
	<?php } if($_SESSION['option']->useGroups && $groups)
	{
		if($_SESSION['option']->ProductMultiGroup)
		{
            $_SESSION['alias']->js_load[] = 'assets/switchery/switchery.min.js';
			echo '<link rel="stylesheet" href="'.SITE_URL.'assets/switchery/switchery.min.css" />';
			$_SESSION['alias']->js_load[] = 'assets/jstree/jstree.min.js';
			echo '<link rel="stylesheet" href="'.SITE_URL.'assets/jstree/themes/default/style.min.css" />';
			echo "<div class=\"form-group\"><table class=\"table table-striped table-bordered\">";
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
					if (empty($list[$g]))
						continue;
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
	            			krsort ($parents);
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
			echo "</div>";
		}
		else
		{
			echo '<div class="form-group"><label class="col-md-3 control-label">Оберіть групу</label><div class="col-md-9">';
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
			echo "</div></div>"; ?>
			<div class="form-group">
				<label class="col-md-3 control-label">Позиція в групі</label>
			    <div class="col-md-9">
			    	<input type="hidden" name="position_old" value="<?=$product->position?>">
					<div class="input-group">
						<input type="number" name="position" title="Обережно при зміні" value="<?=$product->position?>" min="1" required class="form-control">
						<span class="input-group-addon">Режим: <i><?=$_SESSION['option']->productOrder?></i></span>
					</div>
			    </div>
		    </div>
		    <div class="form-group">
				<label class="col-md-3 control-label">Стан</label>
			    <div class="col-md-9">
			    	<input type="radio" name="active" value="1" <?=($product->active == 1)?'checked':''?> id="active-1"><label for="active-1">Публікація активна</label>
					<input type="radio" name="active" value="0" <?=($product->active == 0)?'checked':''?> id="active-0"><label for="active-0">Публікацію тимчасово відключено</label>
			    </div>
		    </div>
		<?php }
	}
	else { ?>
		<div class="form-group">
			<label class="col-md-3 control-label">Стан</label>
		    <div class="col-md-9">
		    	<input type="radio" name="active" value="1" <?=($product->active == 1)?'checked':''?> id="active-1"><label for="active-1">Публікація активна</label>
				<input type="radio" name="active" value="0" <?=($product->active == 0)?'checked':''?> id="active-0"><label for="active-0">Публікацію тимчасово відключено</label>
		    </div>
	    </div>
	<?php }
	$changePriceOptions = array();
	$showh3 = $init_select2 = true;
	foreach ($options_parents as $option_id) {
		if(isset($productOptions[$option_id]))
			foreach ($productOptions[$option_id] as $option)
				if($_SESSION['language'] == false || ($option->type_name != 'text' && $option->type_name != 'textarea'))
				{
					if($showh3)
					{
						echo "<h3>Властивості товару</h3>";
						$showh3 = false;
					}

					$value = '';
					if(isset($product_options_values[$option->id]))
						$value = $product_options_values[$option->id];

					echo('<div class="form-group">');
					echo('<label class="col-md-3 control-label">'.$option->name);
					if($option->sufix != '') echo " ({$option->sufix})";
					echo('</label> <div class="col-md-9">');

					if($option->toCart && $option->type_name != 'checkbox' && $option->type_name != 'checkbox-select2')
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
							if($option->changePrice)
							{
								$changePriceOptions[$option->id] = clone $option;
								$changePriceOptions[$option->id]->values = $option_values;
							}
						}

					}
					if($option->type_name == 'checkbox' || $option->type_name == 'checkbox-select2')
					{
						$where = '';
						if($_SESSION['language']) $where = "AND n.language = '{$_SESSION['language']}'";
						$option_values = array();
						$this->db->executeQuery("SELECT o.*, n.id as name_id, n.name FROM `{$this->shop_model->table('_options')}` as o LEFT JOIN `{$this->shop_model->table('_options_name')}` as n ON n.option = o.id {$where} WHERE o.group = '-{$option->id}'");
						if($this->db->numRows() > 0)
						{
		                    $option_values = $this->db->getRows('array');
		                    if($option->changePrice)
							{
								$changePriceOptions[$option->id] = clone $option;
								$changePriceOptions[$option->id]->values = array();
							}

							$value = explode(',', $value);
							if($option->type_name == 'checkbox')
								foreach ($option_values as $ov) {
									$checked = '';
									if(in_array($ov->id, $value))
									{
										$checked = ' checked';
										if($option->changePrice)
											$changePriceOptions[$option->id]->values[] = $ov;
									}
									echo('<input type="checkbox" name="option-'.$option->id.'[]" value="'.$ov->id.'" id="option-'.$ov->id.'" '.$checked.'> <label for="option-'.$ov->id.'">'.$ov->name.'</label> ');
								}
							else
							{
								echo('<select name="option-'.$option->id.'[]" class="form-control select2" multiple="multiple"> ');
								foreach ($option_values as $ov) {
									$selected = '';
									if(in_array($ov->id, $value))
									{
										$selected = ' selected';
										if($option->changePrice)
											$changePriceOptions[$option->id]->values[] = $ov;
									}
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
					echo('</div></div>');
				}
	}
	?>
	<div class="form-group">
		<label class="col-md-3 control-label">Після збереження:</label>
		<div class="col-md-9" id="after_save">
			<input type="radio" name="to" value="edit" id="to_edit" checked="checked"><label for="to_edit">продовжити редагування</label>
			<input type="radio" name="to" value="category" id="to_category"><label for="to_category">до списку <?=$_SESSION['admin_options']['word:products_to_all']?></label>
			<input type="radio" name="to" value="new" id="to_new"><label for="to_new"><?=$_SESSION['admin_options']['word:product_add']?></label>
		</div>
    </div>
	<div class="form-group">
		<div class="col-md-5"></div>
		<button type="submit" class="btn btn-sm btn-success col-md-2">Зберегти</button>
	</div>
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
	input[type="radio"] {
		min-width: 15px;
		height: 15px;
		margin-left: 15px;
		margin-right: 5px;
	}
	input[type="checkbox"] {
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
	h3 {
		text-align: center;
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
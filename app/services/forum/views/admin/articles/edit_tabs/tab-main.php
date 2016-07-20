<form action="<?=SITE_URL.'admin/'.$_SESSION['alias']->alias?>/save" method="POST" enctype="multipart/form-data">
	<input type="hidden" name="id" value="<?=$article->id?>">
	<table class="table table-striped table-bordered">
		<?php if($_SESSION['option']->useGroups){
			$this->load->smodel('forum_model');
			$groups = $this->forum_model->getGroups(-1);
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

				echo "<tr><th>Оберіть {$_SESSION['admin_options']['word:groups_to_delete']}</th><td>";
				if($_SESSION['option']->ArticleMultiGroup && !empty($list)){
					function showList($article_group, $all, $list, $parent = 0, $level = 0, $parents = array())
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
								if(in_array($g->id, $article_group)) $checked = 'checked';
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
								showList ($article_group, $all, $childs, $g->id, $l, $parents2);
								echo('</div>');
							}
						}

						return true;
					}
					showList($article->group, $list, $list);
				} else {
					echo('<select name="group">');
					echo ('<option value="0">Немає</option>');
					if(!empty($list)){
						function showList($article_group, $all, $list, $parent = 0, $level = 0)
						{
							$prefix = '';
							for ($i=0; $i < $level; $i++) { 
								$prefix .= '- ';
							}
							foreach ($list as $g) if($g->parent == $parent) {
								if(empty($g->child)){
									$selected = '';
									if($article_group == $g->id) $selected = 'selected';
									echo('<option value="'.$g->id.'" '.$selected.'>'.$prefix.$g->name.'</option>');
								} else {
									echo('<optgroup label="'.$prefix.$g->name.'">');
									$l = $level + 1;
									$childs = array();
									foreach ($g->child as $c) {
										$childs[] = $all[$c];
									}
									showList ($article_group, $all, $childs, $g->id, $l);
									echo('</optgroup>');
								}
							}
							return true;
						}
						showList($article->group, $list, $list);
					}
					echo('</select>');
				}
				echo "</td></tr>";
			}
		} ?>
		<tr>
			<th style="width:25%">Власна адреса посилання</th>
			<?php $article->alias = explode($_SESSION['option']->idExplodeLink, $article->alias); array_shift($article->alias); $article->alias = implode($_SESSION['option']->idExplodeLink, $article->alias); ?>
			<td>
				<div class="input-group">
                    <span class="input-group-addon">/<?=$url.'/'.$article->id.$_SESSION['option']->idExplodeLink?></span>
                    <input type="text" name="alias" value="<?=$article->alias?>" required class="form-control">
                </div>
            </td>
		</tr>
		<tr>
			<th>Стан</th>
			<td>
				<input type="radio" name="active" value="1" <?=($article->active == 1)?'checked':''?> id="active-1"><label for="active-1">Публікація активна</label>
				<input type="radio" name="active" value="0" <?=($article->active == 0)?'checked':''?> id="active-0"><label for="active-0">Публікацію тимчасово відключено</label>
			</td>
		</tr>
		<tr>
			<th>Додано</th>
			<td><?=$article->author_add .'. ' . $article->author_add_name . date(' d.m.Y H:i', $article->date_add)?></td>
		</tr>
		<tr>
			<th>Востаннє редагувано</th>
			<td><?=$article->author_edit .'. ' . $article->author_edit_name . date(' d.m.Y H:i', $article->date_edit)?></td>
		</tr>
		<tr>
			<th>
				Після збереження:
			</th>
			<td id="after_save">
				<input type="radio" name="to" value="edit" id="to_edit" checked="checked"><label for="to_edit">продовжити редагування</label>
				<input type="radio" name="to" value="category" id="to_category"><label for="to_category">до списку <?=$_SESSION['admin_options']['word:articles_to_all']?></label>
				<input type="radio" name="to" value="new" id="to_new"><label for="to_new"><?=$_SESSION['admin_options']['word:article_add']?></label>
			</td>
		</tr>
		<tr>
			<td></td>
			<td><button type="submit" class="btn btn-sm btn-success col-md-6">Зберегти</button></td>
		</tr>
	</table>
</form>

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
	function setChilds (parent) {
		if($('#group-'+parent).prop('checked')){
			$('.parent-'+parent).prop('checked', true);
		} else {
			$('.parent-'+parent).prop('checked', false);
		}
	}
</script>
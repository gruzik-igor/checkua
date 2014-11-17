<div class="f-right inline">
	<a href="<?=SITE_URL.'admin/'.$_SESSION['alias']->alias?>/add_group">Додати групу</a>
	<a href="<?=SITE_URL.'admin/'.$_SESSION['alias']->alias?>/all">До всіх товарів</a>
	<a href="<?=SITE_URL.'admin/'.$_SESSION['alias']->alias?>/options">До всіх параметрів</a>
</div>

<h1>Керування групами товарів</h1>

<table cellspacing="0">
	<tr class="top">
		<th>Id</th>
		<th>Name</th>
		<th>Link</th>
		<th>Author</th>
		<th>Date</th>
		<th>Active</th>
		<th>Змінити порядок</th>
	</tr>
	<?php if(!empty($groups)){
		$list = array();
		$emptyParentsList = array();
		$count_level_0 = 0;
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
			if($g->parent == 0) $count_level_0++;
		}
		if(!empty($list)){
			function showList($all, $list, $count_childs, $parent = 0, $level = 0)
			{
				$pl = 15 * $level;
				$ml = 10 * $level;
				foreach ($list as $g) if($g->parent == $parent) { ?>
					<tr>
						<td style="padding-left: <?=$pl?>px"><?=$g->id?></td>
						<td><a href="<?=SITE_URL.'admin/'.$_SESSION['alias']->alias?>/edit_group/<?=$g->id?>"><?=$g->name?></td>
						<td><a href="<?=SITE_URL.$_SESSION['alias']->alias.'/'.$g->link?>">/<?=$_SESSION['alias']->alias.'/'.$g->link?></a></td>
						<td><a href="<?=SITE_URL.'admin/wl_users/'.$g->user?>"><?=$g->user_name?></a></td>
						<td><?=date("d.m.Y H:i", $g->date)?></td>
						<td bgcolor="<?=($g->active == 1)?'green':'red'?>" style="color:white"><center><?=$g->active?></center></td>
						<td style="padding: 1px 5px;">
							<form method="POST" action="<?=SITE_URL.'admin/'.$_SESSION['alias']->alias?>/change_group_position">
								<input type="hidden" name="id" value="<?=$g->id?>">
								<input type="number" name="position" min="1" max="<?=$count_childs?>" value="<?=$g->position?>" onchange="this.form.submit();" autocomplete="off" style="margin-left: <?=$ml?>px">
							</form>
						</td>
					</tr>
				<?php
					if(!empty($g->child)) {
						$l = $level + 1;
						$childs = array();
						foreach ($g->child as $c) {
							$childs[] = $all[$c];
						}
						showList ($all, $childs, count($childs), $g->id, $l);
					}
				}
				return true;
			}
			showList($list, $list, $count_level_0);
		}
	} ?>
</table>

<style type="text/css">
	input[type="number"]{
		min-width: 50px;
	}
</style>
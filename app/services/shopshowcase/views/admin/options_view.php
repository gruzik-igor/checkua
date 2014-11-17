<div class="f-right inline">
	<a href="<?=SITE_URL.'admin/'.$_SESSION['alias']->alias?>/add_option<?=(isset($group))?'?group='.$group->id:''?>">Додати параметр</a>
	<?php if($_SESSION['option']->useGroups == 1){ ?>
		<a href="<?=SITE_URL.'admin/'.$_SESSION['alias']->alias?>/all">До всіх товарів</a>
		<a href="<?=SITE_URL.'admin/'.$_SESSION['alias']->alias?>/groups">До груп товарів</a>
	<?php } ?>
</div>

<h1><?=(isset($group))?$_SESSION['alias']->name .'. Керування властивостями товарів':'Керування властивостями товарів'?></h1>

<table cellspacing="0">
	<?php if(isset($group)){ ?>
		<tr class="f_title"><td colspan="9">
		<a href="<?=SITE_URL.'admin/'.$_SESSION['alias']->alias?>/options"><?=$group->alias_name?></a> -> 
		<?php if(!empty($group->parents)){
			$link = SITE_URL.'admin/'.$_SESSION['alias']->alias.'/options';
			foreach ($group->parents as $parent) { $link .= '/'.$parent->link; ?>
				<a href="<?=$link?>"><?=$parent->name?></a> -> 
	<?php } } echo($_SESSION['alias']->name); echo("</td></tr>"); }?>
	<tr class="top">
		<th>Id</th>
		<th>Name</th>
		<th>Group</th>
		<th>Type</th>
		<th>Filter</th>
		<th>Active</th>
		<th>Змінити порядок</th>
		<th></th>
	</tr>
	<?php if(!empty($options)){ $max = count($options); foreach($options as $a){ ?>
	<tr>
		<td><?=$a->id?></td>
		<td><a href="<?=SITE_URL.'admin/'.$_SESSION['alias']->alias?>/options/<?=$a->link?>"><?=$a->name?></td>
		<td><?=$a->group_name?></td>
		<td><?=$a->type_name?></td>
		<td><?=$a->filter?></td>
		<td bgcolor="<?=($a->active == 1)?'green':'red'?>" style="color:white"><center><?=$a->active?></center></td>
		<td><form method="POST" action="<?=SITE_URL.'admin/'.$_SESSION['alias']->alias?>/change_option_position"><input type="hidden" name="id" value="<?=$a->id?>"><input type="number" name="position" min="1" max="<?=$max?>" value="<?=$a->position?>" onchange="this.form.submit();" autocomplete="off"></form></td>
	</tr>
	<?php } } ?>
</table>

<table cellspacing="0">
	<tr class="top">
		<th>Name</th>
		<th>До товарів</th>
		<th>Active</th>
	</tr>
	<?php if(!empty($groups)){ foreach($groups as $g){ ?>
	<tr>
		<td><a href="<?=SITE_URL.'admin/'.$_SESSION['alias']->alias?>/options/<?=$g->link?>"><?=$g->name?></td>
		<td><a href="<?=SITE_URL.'admin/'.$_SESSION['alias']->alias.'/'.$g->link?>">admin/<?=$_SESSION['alias']->alias.'/'.$g->link?>/*</a></td>
		<td bgcolor="<?=($g->active == 1)?'green':'red'?>" style="color:white"><center><?=$g->active?></center></td>
	</tr>
	<?php } } ?>
</table>

<style type="text/css">
	input[type="number"]{
		min-width: 50px;
	}
	select {
		width: 250px;
	}
</style>
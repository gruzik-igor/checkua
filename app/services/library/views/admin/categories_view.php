<div class="f-right inline">
	<a href="<?=SITE_URL.'admin/'.$_SESSION['alias']->alias?>/add_category">Додати категорію</a>
	<?php if($_SESSION['option']->useCategories == 1){ ?>
		<a href="<?=SITE_URL.'admin/'.$_SESSION['alias']->alias?>/all">До всіх статей</a>
	<?php } ?>
</div>

<h1>Список всіх категорій</h1>

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
	<?php if(!empty($categories)){ $max = $categories[count($categories)-1]->id; foreach($categories as $a){ ?>
	<tr>
		<td><?=$a->id?></td>
		<td><a href="<?=SITE_URL.'admin/'.$_SESSION['alias']->alias?>/edit_category/<?=$a->id?>"><?=$a->name?></td>
		<td><a href="<?=SITE_URL.$_SESSION['alias']->alias.'/'.$a->link?>">/<?=$_SESSION['alias']->alias.'/'.$a->link?></a></td>
		<td><a href="<?=SITE_URL.'admin/wl_users/'.$a->user?>"><?=$a->user_name?></a></td>
		<td><?=date("d.m.Y H:i", $a->date)?></td>
		<td bgcolor="<?=($a->active == 1)?'green':'red'?>" style="color:white"><center><?=$a->active?></center></td>
		<td><form method="POST" action="<?=SITE_URL.'admin/'.$_SESSION['alias']->alias?>/change_category_position"><input type="hidden" name="id" value="<?=$a->id?>"><input type="number" name="position" min="1" max="<?=$max?>" value="<?=$a->position?>" onchange="this.form.submit();" autocomplete="off"></form></td>
	</tr>
	<?php } } ?>
</table>

<style type="text/css">
	input[type="number"]{
		min-width: 50px;
	}
</style>
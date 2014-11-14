<a href="<?=SITE_URL.'admin/'.$_SESSION['alias']->alias?>/add_album" style="float:right">Додати альбом</a>
<h1>Список всіх альбомів</h1>
<table><tbody>
	<tr class="top">
		<th>Порядок</th>
		<th>Id</th>
		<th>Назва</th>
		<th>Адреса</th>
		<th>Стан</th>
	</tr>
	<?php if($albums){ $max = count($albums); foreach($albums as $group){ ?>
	<tr>
		<td><form method="post" action="<?=SITE_URL.$_SESSION['alias']->alias?>/change_position"><input type="hidden" name="id" value="<?=$group->id?>"><input type="number" name="position" min="1" max="<?=$max?>" value="<?=$group->position?>" onchange="this.form.submit();" autocomplete="off"></form></td>
		<td><?=$group->id?></td>
		<td><a href="<?=SITE_URL.$_SESSION['alias']->alias?>/edit/<?=$group->id?>"><?=$group->name?></td>
		<td>/<?=$group->link?></td>
		<td bgcolor="<?=($group->active == 1)?'green':'red'?>" style="color:white" title="<?=($group->active == 1)?'Альбом активний':'Альбом відключений'?>"><center><?=$group->active?></center></td>
	</tr>
	<?php } } ?>
</tbody></table>
<div class="f-right inline">
	<a href="<?=SITE_URL.'admin/'.$_SESSION['alias']->alias?>/add">Додати статтю</a>
	<a href="<?=SITE_URL.'admin/'.$_SESSION['alias']->alias?>/all">До всіх статей</a>
	<a href="<?=SITE_URL.'admin/'.$_SESSION['alias']->alias?>/categories">До всіх категорій</a>
</div>

<h1><?=$_SESSION['alias']->name?> Статті по категоріях</h1>

<table cellspacing="0">
	<tr class="top">
		<th>Name</th>
		<th>Link</th>
		<th>Active</th>
	</tr>
	<?php if(!empty($categories)){ $max = count($categories); foreach($categories as $a){ ?>
	<tr>
		<td><a href="<?=SITE_URL.'admin/'.$_SESSION['alias']->alias?>/<?=$a->link?>"><?=$a->name?></td>
		<td><a href="<?=SITE_URL.$_SESSION['alias']->alias.'/'.$a->link?>">/<?=$_SESSION['alias']->alias.'/'.$a->link?>/*</a></td>
		<td bgcolor="<?=($a->active == 1)?'green':'red'?>" style="color:white"><center><?=$a->active?></center></td>
	</tr>
	<?php } } ?>
</table>

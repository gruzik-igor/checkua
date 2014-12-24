<?php require_once '_admin_words.php'; ?>

<div class="f-right inline">
	<a href="<?=SITE_URL.'admin/'.$_SESSION['alias']->alias?>/add<?=(isset($group))?'?group='.$group->id:''?>"><?=$admin_words['product_add']?></a>
	<?php if($_SESSION['option']->useGroups == 1){ ?>
		<a href="<?=SITE_URL.'admin/'.$_SESSION['alias']->alias?>/all">До всіх <?=$admin_words['products_to_all']?></a>
		<a href="<?=SITE_URL.'admin/'.$_SESSION['alias']->alias?>/groups">До всіх <?=$admin_words['groups_to_all']?></a>
	<?php } ?>
	<a href="<?=SITE_URL.'admin/'.$_SESSION['alias']->alias?>/options">До всіх <?=$admin_words['options_to_all']?></a>
</div>

<h1><?=$_SESSION['alias']->name?>. Групи/підгрупи</h1>

<table cellspacing="0">
	<?php if(isset($group)){ ?>
		<tr class="f_title"><td colspan="3">
		<a href="<?=SITE_URL.'admin/'.$_SESSION['alias']->alias?>"><?=$group->alias_name?></a> -> 
		<?php if(!empty($group->parents)){
			$link = SITE_URL.'admin/'.$_SESSION['alias']->alias;
			foreach ($group->parents as $parent) { $link .= '/'.$parent->link; ?>
				<a href="<?=$link?>"><?=$parent->name?></a> -> 
	<?php } } echo($_SESSION['alias']->name."</td></tr>"); }?>
	<tr class="top">
		<th>Name</th>
		<th>Link</th>
		<th>Active</th>
	</tr>
	<?php if(!empty($groups)){ $max = count($groups); foreach($groups as $g){ ?>
	<tr>
		<td><a href="<?=SITE_URL.'admin/'.$_SESSION['alias']->alias?>/<?=$g->link?>"><?=$g->name?></td>
		<td><a href="<?=SITE_URL.$_SESSION['alias']->alias.'/'.$g->link?>">/<?=$_SESSION['alias']->alias.'/'.$g->link?>/*</a></td>
		<td bgcolor="<?=($g->active == 1)?'green':'red'?>" style="color:white"><center><?=$g->active?></center></td>
	</tr>
	<?php } } ?>
</table>

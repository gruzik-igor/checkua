<?php 
if(isset($list)){
	foreach ($list as $group) { ?>	
		<a href="<?=SITE_URL.$_SESSION['alias']->alias.'/'.$group->link?>" class="groups" title="<?=$group->name?>">
			<img src="<?=IMG_PATH?>tours/<?=(isset($group))?$group->link:'tours'?>.jpg" alt="<?=$group->name?>">
			<br>
			<?=$group->name?>
		</a>
<?php 
	}
	unset($group);
}

?>

<div class="clear"></div>

<style>
	a.groups {
		text-align: center;
		width: 380px;
		padding: 10px;
		float: left;
		color: #000;
	}
	a.groups img {
		width: 380px !important;
		height: 200px !important;
	}
</style>
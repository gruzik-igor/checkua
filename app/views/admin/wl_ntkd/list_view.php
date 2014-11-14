<?php

	$wl_aliases = false;
	if(isset($alias)){
		$wl_aliases = $this->db->getAllDataByFieldInArray('wl_ntkd', $alias->id, 'alias');
	}
	
?>

<h1>Налаштування SEO (name, title, descriptions)</h1>
 
Наявні адреси:
<a href="<?=SITE_URL.'admin/wl_ntkd/'.$alias->alias?>/edit" class="f-right">Головна сторінка</a>
<table cellspacing="0">
	<tr class="top">
		<th>alias</th>
		<th>id</th>
		<th>name</th>
		<th>language</th>
	</tr>
	<?php if($wl_aliases) foreach ($wl_aliases as $a) { ?>
		<tr>
			<td><a href="<?=SITE_URL.'admin/wl_ntkd/'.$alias->alias?>"><?=$alias->alias?></a></td>
			<td><a href="<?=SITE_URL.'admin/wl_ntkd/'.$alias->alias.'/'.$a->content?>"><?=$a->content?></a></td>
			<td><a href="<?=SITE_URL.'admin/wl_ntkd/'.$alias->alias.'/'.$a->content?>"><?=$a->name?></a></td>
			<td><?=$a->language?></td>
		</tr>
	<?php } ?>
</table>
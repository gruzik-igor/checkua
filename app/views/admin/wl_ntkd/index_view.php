<?php

	$wl_aliases = $this->db->getAllData('wl_aliases');
	$wl_services = $this->db->getAllData('wl_services');
	$services_title = array(0 => '');
	if($wl_services){
		foreach ($wl_services as $s) if($s->active == 1) {
			$services_title[$s->id] = $s->title;
		}
	}
	
?>

<h1>Налаштування SEO (name, title, descriptions)</h1>

Наявні адреси:
<table cellspacing="0">
	<tr class="top">
		<th>id</th>
		<th>alias</th>
		<th>service</th>
		<th>active</th>
	</tr>
	<?php if($wl_aliases) foreach ($wl_aliases as $alias) { ?>
		<tr>
			<td><?=$alias->id?></td>
			<td><a href="<?=SITE_URL.'admin/wl_ntkd/'.$alias->alias?>"><?=$alias->alias?></a></td>
			<td><?=$services_title[$alias->service]?></td>
			<td><?=$alias->active?></td>
		</tr>
	<?php } ?>
</table>
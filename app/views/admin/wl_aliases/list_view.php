<?php

	$wl_aliases = $this->db->getAllData('wl_aliases');
	$wl_services = $this->db->getAllData('wl_services');
	$services_name = array(0 => '');
	$services_title = array(0 => '');
	if($wl_services){
		foreach ($wl_services as $s) if($s->active == 1) {
			$services_name[$s->id] = $s->name;
			$services_title[$s->id] = $s->title;
		}
	}
	
?>

Наявні адреси:
<table cellspacing="0">
	<tr class="top">
		<th>id</th>
		<th>alias</th>
		<th>service</th>
		<th>alias table</th>
		<th>options</th>
		<th>active</th>
		<th></th>
	</tr>
	<?php if($wl_aliases) foreach ($wl_aliases as $alias) { ?>
		<tr>
			<td><?=$alias->id?></td>
			<td><a href="<?=SITE_URL.$alias->alias?>"><?=$alias->alias?></a></td>
			<td><a href="<?=SITE_URL?>wl_services/<?=$services_name[$alias->service]?>"><?=$services_title[$alias->service]?></a></td>
			<td><?=$alias->table?></td>
			<td><?=$alias->options?></td>
			<td><?=$alias->active?></td>
			<td><a href="<?=SITE_URL?>admin/wl_aliases/<?=$alias->alias?>">Редагувати</a></td>
		</tr>
	<?php } ?>
</table>

<br>
<form action="<?=SITE_URL?>admin/wl_aliases/add" method="GET">
	<span title="Адреса повинною бути унікальною!">Додати адресу*:</span>
	<input type="text" name="alias" placeholder="alias" required>
	<?php 
		if(!empty($wl_services)){
			echo "<select name='service' required>";
			echo "<option value='0'>відсутній</option>";
			foreach ($wl_services as $s) if($s->active == 1 && $s->multi_alias == 1) {
				echo "<option value='{$s->id}'>{$s->title}</option>";
			}
			echo "</select>";
		} else echo "<input type='hidden' id='service' value='0'>";
	?>
	<button>Додати</button>
</form>
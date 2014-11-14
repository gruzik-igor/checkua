<?php

	$services = $this->db->getAllData('wl_services');
	$services_name = array();

?>

Інстальовані сервіси:
<table cellspacing="0">
	<tr class="top">
		<th>id</th>
		<th>name</th>
		<th>title</th>
		<th>service table</th>
		<th>version</th>
		<th>active</th>
	</tr>
	<?php if($services) foreach ($services as $s) { $services_name[] = $s->name; ?>
		<tr>
			<td><?=$s->id?></td>
			<td><a href="<?=SITE_URL?>admin/wl_services/<?=$s->name?>"><?=$s->name?></a></td>
			<td title="<?=$s->description?>"><?=$s->title?></td>
			<td><?=$s->table?></td>
			<td><?=$s->version?></td>
			<td><?=$s->active?></td>
		</tr>
	<?php } ?>
</table>

<br>
<br>
Неінстальовані сервіси:
<br>
<form action="<?=SITE_URL?>admin/wl_services/install" method="POST">
<?php 
	$services = '';
	$files = scandir(APP_PATH.'services');
	$files[0] = null;
	$files[1] = null;
	foreach ($files as $dir) {
		if(!in_array($dir, $services_name) && $dir){
			$description = APP_PATH.'services'.DIRSEP.$dir.DIRSEP.'description.txt';
			if(file_exists($description)) $description = file_get_contents($description); else $description = '';
			$services .= '<span id="wl-non-'.$dir.'" title="'.$description.'">'.$dir.' <button onclick="install(\''.$dir.'\')">Інсталювати</button><br></span>';
		}
	};
	if($services == '') echo('Сервіси відсутні. Скопіюйте у папку APP_PATH/services'); else echo($services);
?>
	<input type="hidden" id="name" name="name" value="">
</form>

<script type="text/javascript">
	function install (name) {
		$("#name").val(name);
	}
</script>
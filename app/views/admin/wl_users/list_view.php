<?php

	$wl_users = $this->db->getAllData('wl_users');
	
?>
<a href="<?=SITE_URL?>admin/wl_users/add" style="float: right">Додати користувача</a>
<h1>Список користувачів:</h1>
<table cellspacing="0">
	<tr class="top">
		<th>id</th>
		<th>email</th>
		<th>name</th>
		<th>type</th>
		<th>status</th>
		<th>active</th>
		<th>registered</th>
	</tr>
	<?php if($wl_users) foreach ($wl_users as $user) { ?>
		<tr>
			<td><?=$user->id?></td>
			<td><a href="<?=SITE_URL.'admin/wl_users/'.$user->email?>"><?=$user->email?></a></td>
			<td><?=$user->name?></td>
			<td><?=$user->type?></td>
			<td><?=$user->status?></td>
			<td><?=$user->active?></td>
			<td><?=date("d.m.Y H:i", $user->registered)?></td>
		</tr>
	<?php } ?>
</table>

<!-- <br>
<form action="<?=SITE_URL?>admin/wl_aliases/add" method="GET">
	<span title="Адреса повинною бути унікальною!">Додати адресу*:</span>
	<input type="text" name="alias" placeholder="alias" required>
	<?php 
		if(!empty($wl_services)){
			echo "<select name='service' required>";
			echo "<option value='0'>відсутній</option>";
			foreach ($wl_services as $s) if($s->active == 1) {
				echo "<option value='{$s->id}'>{$s->title}</option>";
			}
			echo "</select>";
		} else echo "<input type='hidden' id='service' value='0'>";
	?>
	<button>Додати</button>
</form> -->
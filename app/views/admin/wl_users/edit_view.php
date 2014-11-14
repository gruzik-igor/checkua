<a href="<?=SITE_URL?>admin/wl_users" style="float: right">До всіх користувачів</a>
<h1><?=$user->name?></h1>

<form action="<?=SITE_URL?>admin/wl_users/save" method="POST">
	<input type="hidden" name="id" value="<?=$user->id?>">
	<table>
		<tr>
			<td title="Обов'язкове поле">email*</td>
			<td><input type="text" name="email" value="<?=$user->email?>" required></td>
		</tr>
		<tr>
			<td>name</td>
			<td><input type="text" name="name" value="<?=$user->name?>" required></td>
		</tr>
		<tr>
			<td>password</td>
			<td><input type="checkbox" name="active_password" value="1"><input type="text" name="password"><?=$user->password?></td>
		</tr>
		<tr>
			<td>type</td>
			<td><select name="type" onchange="chengeType(this)" required>
		<?php $types = $this->db->getAllDataByFieldInArray('wl_user_types', 1, 'active');
			foreach ($types as $type) {
				echo('<option value="'.$type->id.'"');
				if($type->id == $user->type) echo " selected";
				echo('>'.$type->name.'</option>');
			} ?>
			</select>
			</td>
		</tr>
		<tr id="permissions" <?=($user->type == 2)?'':'style="display: none"'?>>
			<td>user permissions</td>
			<td>
				<?php $permissions = $this->db->getAllData('wl_aliases');
					$up = $this->db->getAllDataByFieldInArray('wl_user_permissions', $user->id, 'user');
					$user_permissions = array();
					if(!empty($up)) foreach ($up as $upp) {
						$user_permissions[] = $upp->permission;
					}
					foreach ($permissions as $p) { ?>
						<input type="checkbox" id="<?=$p->alias?>" name="permissions[]" value="<?=$p->id?>" <?=(in_array($p->id, $user_permissions))?'checked':''?>><label for="<?=$p->alias?>"><?=$p->alias?></label>
				<?php } ?>
			</td>
		</tr>
		<tr>
			<td>status</td>
			<td><select name="status" required>
		<?php $status = $this->db->getAllData('wl_user_status');
			foreach ($status as $s) {
				echo('<option value="'.$s->id.'"');
				if($s->id == $user->status) echo " selected";
				echo('>'.$s->name.'</option>');
			} ?>
			</select>
			</td>
		</tr>
		<tr>
			<td>registered</td>
			<td><?=date("d.m.Y H:i", $user->registered)?></td>
		</tr>
		<tr>
			<td colspan="2"><input type="submit" value="<?=($user->id == 0)?'Додати':'Зберегти'?>"></td>
		</tr>
	</table>
</form>

<script type="text/javascript">
	function chengeType (e) {
		if(e.value == 2){
			$('#permissions').slideDown('slow');
		} else {
			$('#permissions').slideUp('slow');
		}
	}
</script>

<?php

$this->db->executeQuery("SELECT r.*, d.title, d.help_additionall as help FROM wl_user_register as r LEFT JOIN wl_user_register_do as d ON d.id = r.do WHERE r.user = {$user->id}");
if($this->db->numRows() > 0){
	$register = $this->db->getRows('array');
	?>
	<h3>Реєстр дій:</h3>
	<table cellspacing="0">
	<tr class="top">
		<th>id</th>
		<th>date</th>
		<th>do</th>
		<th>additionally</th>
	</tr>
	<?php foreach ($register as $r) { ?>
		<tr>
			<td><?=$r->id?></td>
			<td><?=date("d.m.Y H:i", $r->date)?></td>
			<td><?=$r->title?></td>
			<td title="<?=$r->help?>"><?=$r->additionally?></td>			
		</tr>
	<?php } ?>
</table>
<?php } ?>
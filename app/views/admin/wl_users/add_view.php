<a href="<?=SITE_URL?>admin/wl_users" style="float: right">До всіх користувачів</a>
<h1>Додати користувача</h1>

<form action="<?=SITE_URL?>admin/wl_users/save" method="POST">
	<input type="hidden" name="id" value="0">
	<table>
		<tr>
			<td title="Обов'язкове поле">email*</td>
			<td><input type="text" name="email" value="<?=(isset($_POST['email']))?$_POST['email']:''?>" required></td>
		</tr>
		<tr>
			<td>name*</td>
			<td><input type="text" name="name" value="<?=(isset($_POST['name']))?$_POST['name']:''?>" required></td>
		</tr>
		<tr>
			<td>password*</td>
			<td><input type="text" name="password" value="<?=(isset($_POST['password']))?$_POST['password']:''?>" required></td>
		</tr>
		<tr>
			<td>type*</td>
			<td><select name="type" onchange="chengeType(this)" required>
		<?php $types = $this->db->getAllDataByFieldInArray('wl_user_types', 1, 'active');
			foreach ($types as $type) {
				echo('<option value="'.$type->id.'"');
				if($type->id == 2) echo " selected";
				echo('>'.$type->name.'</option>');
			} ?>
			</select>
			</td>
		</tr>
		<tr id="permissions">
			<td>user permissions</td>
			<td>
				<?php $permissions = $this->db->getAllData('wl_aliases');
					foreach ($permissions as $p) { ?>
						<input type="checkbox" id="<?=$p->alias?>" name="permissions[]" value="<?=$p->id?>"><label for="<?=$p->alias?>"><?=$p->alias?></label>
				<?php } ?>
			</td>
		</tr>
		<tr>
			<td colspan="2"><input type="submit" value="Додати"></td>
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
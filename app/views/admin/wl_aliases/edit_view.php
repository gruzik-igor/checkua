<?php if($alias->id == 0){ 
	$text = '';
	if($alias->service > 0) $text = "на основі сервісу ".$alias->title;
?>
	<h1>Додати сторінку <?=$text?></h1>
<?php } else { 
	$text = '';
	if($alias->service > 0) $text = "(на основі сервісу ".$alias->title.")";
	?>
	<h1>Редагувати <?=$alias->alias?> <?=$text?></h1>
<?php } ?>

<form action="<?=SITE_URL?>admin/wl_aliases/save" method="POST">
	<input type="hidden" name="id" value="<?=$alias->id?>">
	<input type="hidden" name="service" value="<?=$alias->service?>">
	<table>
		<tr>
			<td title="Обов'язкове поле">Адреса посилання*</td>
			<td><input type="text" name="alias" value="<?=$alias->alias?>" required></td>
		</tr>
		<tr>
			<td>Назва сторінки</td>
			<td><?php if($alias->id > 0){ ?>
					<input type="text" value="<?=$alias->name?>" disabled>
					<input type="hidden" name="name" value="<?=$alias->name?>">
				<?php } else { ?>
					<input type="text" name="name" value="<?=$alias->name?>">
				<?php } ?>
			</td>
		</tr>
		<tr>
			<td title="Обов'язкове поле">ADMIN ico</td>
			<td><input type="text" name="admin_ico" value="<?=$alias->admin_ico?>"></td>
		</tr>
		<?php if(isset($options)) foreach ($options as $key => $value) { ?>
			<tr>
				<td><?=$key?></td>
				<td><input type="text" name="<?=$key?>" value="<?=$value?>" required></td>
			</tr>
		<?php } ?>
		<tr>
			<td colspan="2"><input type="submit" value="<?=($alias->id == 0)?'Додати':'Зберегти'?>"></td>
		</tr>
	</table>
</form>
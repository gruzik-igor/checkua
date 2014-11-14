<link href="<?=SITE_URL?>style/style-notify.css" rel="stylesheet" />

<div style="width: 370px; margin: 0 auto">
<br>
<br>

<br>
<h1 style="float: none; font-size: 1.5em; margin: 35px"><?=$_SESSION['alias']->name?></h1>

<?php if(!empty($login_error)) : ?>
    <div class="notify-error" style="max-width: 240px;"><?=$login_error?></div>
<?php endif; ?>
<?php if(!empty($success)) :?>
	<div class="notify-success" style="max-width: 240px;"><?=$success?></div>
<?php endif; ?>
<br>
<br>

<form method="POST" action="<?=SITE_URL?>login/process" style="margin-left: 65px">
	<table>
		<tr>
           <td><label for="email">Пошта: </label>
           <td><input type="email" name="email" required class="border-1"/>
		</tr>
		<tr>
           <td><label for="password">Пароль: </label>
           <td><input type="password" name="password" required class="border-1"/>
		</tr>
		<tr>
			<td colspan="2" style="text-align: center">
                <input class="button border-1 pointer f-none" type="submit" value="Увійти"/>
		   </td>
		</tr>
	</table>
</form>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
</div>

<style type="text/css">
	table tr td {
		padding: 3px 5px;
		vertical-align: middle;
		min-height: 5px;
	}
</style>
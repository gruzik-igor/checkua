<a href="<?=SITE_URL?>admin/<?=$_SESSION['alias']->alias?>" style="float:right">До всіх альбомів</a>
<h1>Додати новий альбом</h1>
<form id="fileupload" method="POST" action="<?=SITE_URL.'admin/'.$_SESSION['alias']->alias?>/create_album" enctype="multipart/form-data">
	<table>
		<tr>
			<td title="Обов'язкове поле">Назва альбому*:</td>
			<td><input type="text" name="name" value="<?=$_SESSION['user']->name?>" required></td>
		</tr>
		<tr>
			<td>Опис альбому:</td>
			<td><textarea name="description"></textarea></td>
		</tr>
		<tr>
			<td colspan="2">
				<center><input type="submit" value="Перейти до додавання фотографій"></center>
			</td>
		</tr>
	</table>
</form>


<style type="text/css">
    form input,textarea {
        background-color: #fff;
        width: 440px;
        padding: 5px;
        border: 1px solid #d4d4d4;
        line-height: 1.5em;
        box-shadow: inset 0px 2px 2px #ececec;
    }
</style>

<br>
<br>
<br>
<br>
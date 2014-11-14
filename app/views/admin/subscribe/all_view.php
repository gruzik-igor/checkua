<a href="<?=SITE_URL?>admin/subscribe/mail" style="float:right">зробити розсилку</a>
<h1>Список всіх підписників</h1>
<table><tbody>
	<tr>
		<th>Дата підписки</th>
		<th>Джерело</th>
		<th>email</th>
		<th>Ім'я</th>
		<th>Телефон</th>
		<th></th>
		<th></th>
	</tr>
	<?php if(!empty($mails)){ foreach($mails as $mail){ ?>
	<tr <?=($mail->active == 1)?'':'bgcolor="red"'?>>
		<td><?=date('d.m.Y i:H', $mail->add_date)?></td>
		<td><?=($mail->add_from == 1)?'Підписка':'Бронь'?></td>
		<td><?=$mail->email?></td>
		<td><?=$mail->name?></td>
		<td><?=$mail->tel?></td>
		<td><span style="color:red; cursor:pointer" onclick="deleteArticle(<?=$mail->id . ", '" . $mail->email ."'"?>)">Видалити!</span></td>
		<td><a href="<?=SITE_URL?>admin/subscribe/edit/<?=$mail->id?>?active=<?=$mail->active?>">Включити/виключити!</a></td>
	</tr>
	<?php } } ?>
</tbody></table>
<script type="text/javascript">
	function deleteArticle(id, name){
		if (confirm("Ви впевнені, що хочете видалити \"" + name + "\"? \nУВАГА, інформація відновленню НЕ ПІДЛЯГАЄ!")) {
			top.document.location.href = "<?=SITE_URL?>admin/subscribe/delete?id=" + id;
		}
	}
</script>


<style>
td {
	max-width: 160px;
	font-size: 12px;
}
</style>
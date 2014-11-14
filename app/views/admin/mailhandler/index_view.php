<div class="site1">
	<div class="title site">Зворотній зв'язок</div>
</div>
<table cellspacing="0">
	<tr class="top">
		<th></th><th>Дата</th><th>Ім'я</th><th>Телефон</th><th>email</th>
	</tr>
		<?php
		$messages = $this->db->getAllData('mail_handler', 'date DESC');
		if($messages){
			foreach ($messages as $message) {
				?>
				<tr>
    				<td><a href="<?=SITE_URL?>admin/mailhandler/<?=$message->id?>"><?=$message->id?></a></td>
    				<td><?=date("d.m.Y H:i", $message->date)?></td>
    				<td><a href="<?=SITE_URL?>admin/mailhandler/<?=$message->id?>"><?=$message->name?></a></td>
    				<td><?=$message->phone?></td>
    				<td><?=$message->email?></td>
   				</tr>
				<?php
			}
		} else {
	?>
		<tr>
			<td colspan="5">Листи відсутні!</td>
		</tr>
	<?php } ?>
</table>
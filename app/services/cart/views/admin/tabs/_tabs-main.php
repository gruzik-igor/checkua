<div class="table-responsive">
    <table class="table table-striped table-bordered nowrap" width="100%">
    	<tbody>
    		<tr>
				<th>ID</th>
				<td><?= $cartInfo->id?></td>
			</tr>
    		<tr>
				<th>Покупець</th>
				<td><?= $cartInfo->user_name?></td>
			</tr>
    		<tr>
				<th>Тип покупця</th>
				<td><?= $cartInfo->user_type_name?></td>
			</tr>
    		<tr>
				<th>E-mail</th>
				<td><?= $cartInfo->user_email?></td>
			</tr>
    		<tr>
				<th>Телефон</th>
				<td><?= $cartInfo->user_phone .' '. $cartInfo->user_phone2 ?></td>
			</tr>
    		<tr>
				<th>Статус</th>
				<td><?= $cartInfo->status_name?></td>
			</tr>
    		<tr>
				<th>Загальна сума</th>
				<td id="totalPrice"><?= $cartInfo->total?> $</td>
			</tr>
    		<tr>
				<th>Дата заявки</th>
				<td><?= date('d.m.Y H:i', $cartInfo->date_add) ?></td>
			</tr>
    		<tr>
				<th>Дата обробки</th>
				<td><?= $cartInfo->date_edit > 0 ? date('d.m.Y H:i', $cartInfo->date_edit) : '' ?></td>
			</tr>
			<tr>
				<th>1С синхронізація</th>
				<td><?php if($cartInfo->s1c > 0) echo(date('d.m.Y H:i', $cartInfo->s1c));
										elseif($cartInfo->status == 6) echo('Очікуємо');
										else echo('Не синхронізуємося'); ?></td>
			</tr>
    	</tbody>
    </table>
</div>
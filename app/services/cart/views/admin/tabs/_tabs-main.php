<div class="table-responsive">
    <table class="table table-striped table-bordered nowrap" width="100%">
    	<tbody>
    		<tr>
				<th>ID</th>
				<td><?= $cartInfo->id?></td>
			</tr>
    		<tr>
				<th>Покупець</th>
				<td><a href="<?=SITE_URL?>admin/wl_users/<?= $cartInfo->user_email?>" class="btn btn-success btn-xs"><?= $cartInfo->user_name?></a></td>
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
				<td><?= $cartInfo->user_phone?></td>
			</tr>
    		<tr>
				<th>Статус</th>
				<td><?= $cartInfo->status_name?></td>
			</tr>
    		<tr>
				<th>Загальна сума</th>
				<td><span id="totalPrice"><?= $cartInfo->total?> </span> грн</td>
			</tr>
    		<tr>
				<th>Дата заявки</th>
				<td><?= date('d.m.Y H:i', $cartInfo->date_add) ?></td>
			</tr>
    		<tr>
				<th>Дата останньої операції (обробки)</th>
				<td><?= $cartInfo->date_edit > 0 ? date('d.m.Y H:i', $cartInfo->date_edit) : '' ?></td>
			</tr>
			<tr>
				<th>Поточний статус заявки</th>
				<td><?= $cartInfo->status_name ?></td>
			</tr>
    	</tbody>
    </table>
</div>
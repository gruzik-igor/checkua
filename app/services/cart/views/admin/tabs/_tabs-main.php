<div class="table-responsive">
    <table class="table table-striped table-bordered nowrap" width="100%">
    	<tbody>
    		<tr>
				<th>ID</th>
				<td><?= $cart->id?></td>
			</tr>
    		<tr>
				<th>Покупець</th>
				<td><?php if($cart->user) { ?>
						<a href="<?=SITE_URL?>admin/wl_users/<?= ($cart->user_email) ? $cart->user_email : $cart->user?>" class="btn btn-success btn-xs"><?= ($cart->user_name) ? $cart->user_name : 'Гість'?></a>
					<?php } else echo "Гість"; ?>
				</td>
			</tr>
    		<tr>
				<th>Тип покупця</th>
				<td><?= $cart->user_type_name?></td>
			</tr>
    		<tr>
				<th>E-mail</th>
				<td><?= $cart->user_email?></td>
			</tr>
    		<tr>
				<th>Телефон</th>
				<td><?= $cart->user_phone?></td>
			</tr>
    		<tr>
				<th>Статус</th>
				<td><?= $cart->status_name?></td>
			</tr>
    		<tr>
				<th>Загальна сума</th>
				<td><span id="totalPrice"><?= $cart->total?> </span> грн</td>
			</tr>
    		<tr>
				<th>Дата заявки</th>
				<td><?= date('d.m.Y H:i', $cart->date_add) ?></td>
			</tr>
    		<tr>
				<th>Дата останньої операції (обробки)</th>
				<td><?= $cart->date_edit > 0 ? date('d.m.Y H:i', $cart->date_edit) : '' ?></td>
			</tr>
    	</tbody>
    </table>
</div>
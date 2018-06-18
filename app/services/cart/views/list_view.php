	<h2><?=$this->text('Історія замовлень')?></h2>
	<div class="table-responsive" >
		<table class="table table-striped table-bordered nowrap" width="100%">
			<thead>
				<tr>
					<th></th>
					<th>Номер</th>
					<th>Статус</th>
					<th>Сума</th>
					<th>Дата</th>
				</tr>
			</thead>
			<tbody>
				<?php if($orders) foreach($orders as $order) {?>
				<tr>
					<td>
						<a class="btn-u" href="<?= SITE_URL.'cart/'.$order->id ?>"><?=$this->text('Перегляд')?></a>
						<?php if($order->status == 2) { ?>
							<a href="<?= SITE_URL.'cart/pay/'.$order->id ?>" class="btn btn-warning btn-sm">Оплатити</a>
						<?php } ?>
					</td>
					<td><?= $order->id ?></td>
					<td><?= $order->status_name ?></td>
					<td><?= $order->total ?> грн</td>
					<td><?= date('d.m.Y H:i', $order->date_add) ?></td>
				</tr>
				<?php } ?>
			</tbody>
		</table>
	</div>
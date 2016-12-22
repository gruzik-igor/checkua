<div class="container">
    <div class="row">
    	<div class="clearfix">
    		<a href="<?=SITE_URL?>cart/<?= $cartInfo->id?>" class="btn btn-success btn-sm pull-right">До замовлення</a>
    		<h1>Замовлення #<?= $cartInfo->id?> від <?= date('d.m.Y H:i', $cartInfo->date_edit)?></h1>
	    	<h4 class="pull-right"><div align='right'><?= $cartInfo->user_name ."</div>". $cartInfo->user_email .", " . $cartInfo->user_phone ?> </h4>
	    	<h4> Статус: <?= $cartInfo->status_name ."<br>Тип покупця: ". $cartInfo->user_type_name ?></h4>
    	</div>
    </div>
    <?php if($cartInfo->status == 2) { ?>
	    <div class="row">
	    	<div class="col-md-6">
	    	<table class="table">
	    		<tr>
	    			<th>Сума USD:</th>
	    			<td>$<?=$cartInfo->total?></td>
	    		</tr>
	    		<tr>
	    			<th>Позитивний баланс:</th>
	    			<td>$<?=round($cartInfo->user_ballance, 2)?></td>
	    		</tr>
	    		<?php if($cartInfo->total - $cartInfo->user_ballance > 0) { ?>
		    		<tr>
		    			<th>Сума UAH:</th>
		    			<td>$<?=round($cartInfo->total - $cartInfo->user_ballance, 2)?> x <?=round($currency_USD, 2)?>$/грн = <?=round(($cartInfo->total - $cartInfo->user_ballance) * $currency_USD, 2)?> грн</td>
		    		</tr>
		    		<tr>
		    			<th>Комісія банку 1%:</th>
		    			<td><?=round(($cartInfo->total - $cartInfo->user_ballance) * $currency_USD * 0.01, 2)?> грн</td>
		    		</tr>
		    		<tr>
		    			<th>До оплати:</th>
		    			<th><?=round(($cartInfo->total - $cartInfo->user_ballance) * $currency_USD * 1.01, 2)?> грн</th>
		    		</tr>
	    		<?php } else { ?>
		    		<tr>
		    			<th>До оплати з позитивного балансу:</th>
		    			<th>$<?=round($cartInfo->total, 2)?></th>
		    		</tr>
		    		<tr>
		    			<th>Залишок на позитивному балансі після оплати:</th>
		    			<td>$<?=round($cartInfo->user_ballance - $cartInfo->total, 2)?></td>
		    		</tr>
		    		<tr>
		    			<td colspan="2"><a href="<?=SITE_URL?>account/pay_by_ballance/<?=$cartInfo->id?>" class="btn btn-warning">Оплатити з позитивного балансу</a></td>
		    		</tr>
	    		<?php } ?>
	    	</table>
			</div>
	    </div>
	    <?php if($cartInfo->total - $cartInfo->user_ballance > 0) { ?>
			<div class="row">
				<div class="col-md-6 md-margin-bottom-50">
					<h2 class="title-type">Оберіть платіжний механізм</h2>
					<!-- Accordion -->
					<div class="accordion-v2">
						<div class="panel-group" id="accordion">
							<?php 
							$cartInfo->total = round(($cartInfo->total - $cartInfo->user_ballance) * $currency_USD * 1.01, 2);
							$cartInfo->return_url = $_SESSION['alias']->alias.'/'.$cartInfo->id;
							$cartInfo->wl_alias = $_SESSION['alias']->id;
							$cooperation_where['alias1'] = $_SESSION['alias']->id;
							$cooperation_where['type'] = 'payment';
							$cooperation = $this->db->getAllDataByFieldInArray('wl_aliases_cooperation', $cooperation_where);
					        if($cooperation)
					        {
					            foreach ($cooperation as $storage) {
					                $this->load->function_in_alias($storage->alias2, '__get_Payment', $cartInfo);
					            }
					        }
							?>
							<div class="panel panel-default">
								<div class="panel-heading">
									<h4 class="panel-title">
										<a data-toggle="collapse" data-parent="#accordion" href="#collapseTwo">
											<i class="fa fa-bank"></i>
											Реквізити для оплати
										</a>
									</h4>
								</div>
								<div id="collapseTwo" class="panel-collapse collapse">
									<div class="panel-body cus-form-horizontal">
										<div>4731 2171 1434 0597</div>
										<div>Папайло Юрій Богдановий</div>
									</div>
								</div>
							</div>
						</div>
					</div>
					<!-- End Accordion -->
				</div>

				<div class="col-md-6">
					<h2 class="title-type">Повідомити про оплату</h2>
					<form action="<?=SITE_URL?>account/pay" method="POST">
						<input type="hidden" name="cart" value="<?=$cartInfo->id?>">
						<div class="form-group">
			                <label for="name">Сума, грн</label>
			                <input type="number" class="form-control" name="amount" min="1" step="0.01" required>
			            </div>
			            <div class="form-group">
			                <label for="name">Примітка</label>
			                <textarea name="info" class="form-control"></textarea>
			            </div>
			            <div class="form-group text-center">
				            <input type="submit" class="btn btn-success" value="Відправити на перевірку">
				        </div>
					</form>
				</div>
			</div>
		<?php } ?>
	<?php } else { ?>
		<div class="row">
			<h2 class="text-center">Замовлення оплачено</h2>
		</div>
	<?php } ?>
</div>
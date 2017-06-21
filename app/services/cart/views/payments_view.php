<div class="row">
	<div class="col-md-6 md-margin-bottom-50">
		<h2 class="title-type"><?=$this->text('Оберіть платіжний механізм')?></h2>

		<form action="<?= SERVER_URL?>cart/pay" method="POST">
			<input type="hidden" name="cart" value="<?=$_SESSION['cart']->id?>">

			<?php
			$cooperation_where['alias1'] = $_SESSION['alias']->id;
			$cooperation_where['type'] = 'payment';
			$ntkd = array('alias' => '#c.alias2', 'content' => 0);
			if($_SESSION['language'])
				$ntkd['language'] = $_SESSION['language'];
			$cooperation = $this->db->select('wl_aliases_cooperation as c', 'alias2 as id', $cooperation_where)
									->join('wl_ntkd', 'list', $ntkd)
									->get('array');
	        if($cooperation)
	        {
	            foreach ($cooperation as $payment) { ?>
	                <div class="form-group">
					    <button type="submit" name="method" value="<?=$payment->id?>" class="btn btn-success"><?=htmlspecialchars_decode($payment->list)?></button>
					</div>
	            <?php }
	        }
			?>
			<div class="form-group">
		      	<button type="submit" name="method" value="0" class="btn btn-success"><i class="fa fa-money"></i> <?=$this->text('Готівкою при отриманні')?></button>
			</div>
		</form>
	</div>
</div>
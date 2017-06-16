<div class="row">
	<div class="col-md-6 md-margin-bottom-50">
		<h2 class="title-type"><?=$this->text('Оберіть платіжний механізм')?></h2>
		<!-- Accordion -->
		<div class="accordion-v2">
			<div class="panel-group" id="accordion">
				<?php
				$cart = $this->db->getAllDataById('s_cart', $_SESSION['cart']->id);
				if($cart)
				{
					$cart->return_url = $_SESSION['alias']->alias.'/order/'.$cart->id;
					$cart->wl_alias = $_SESSION['alias']->id;
					$cooperation_where['alias1'] = $_SESSION['alias']->id;
					$cooperation_where['type'] = 'payment';
					$cooperation = $this->db->getAllDataByFieldInArray('wl_aliases_cooperation', $cooperation_where);
			        if($cooperation)
			        {
			            foreach ($cooperation as $storage) {
			                $this->load->function_in_alias($storage->alias2, '__get_Payment', $cart);
			            }
			        }
				}
				?>
				<div class="panel panel-default ">
					<div class="panel-heading">
						<h4 class="panel-title">
							<a data-toggle="collapse" data-parent="#accordion" href="#collapseTwo">
								<i class="fa fa-money"></i>
								<?=$this->text('Готівка')?>
							</a>
						</h4>
					</div>
					<div id="collapseTwo" class="panel-collapse collapse in">
						<div class="panel-body">
							<a class="btn-u btn-u-sea-shop" href="<?= SERVER_URL?>cart/payCash"><?=$this->text('Оплатити готівкою')?></a>
						</div>
					</div>
				</div>
			</div>
		</div>
		<!-- End Accordion -->
	</div>
</div>
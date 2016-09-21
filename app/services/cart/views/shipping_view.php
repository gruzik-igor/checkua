<div class="alert alert-danger alert-dismissible " hidden role="alert" id="deliveryError">
	<strong>Помилка!</strong> Виберіть доставку
</div>
<div class="row" id="shipping">
	<div class="col-md-6 md-margin-bottom-50">
		<h2 class="title-type">Оберіть службу та вкажіть адресу доставки товару</h2>
			<?php 
				$cooperation_where['alias1'] = $_SESSION['alias']->id;
				$cooperation_where['type'] = 'delivery';
				$cooperation = $this->db->getAllDataByFieldInArray('wl_aliases_cooperation', $cooperation_where);
		        if($cooperation)
		        {
		            foreach ($cooperation as $storage) {
		                $this->load->function_in_alias($storage->alias2, '__get_Shipping_to_cart');
		            }
		        }
			?>
	</div>
</div>
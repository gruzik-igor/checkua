<div class="col-md-6">
	<div class="panel panel-inverse" data-sortable-id="form-stuff-1">
	    <div class="panel-heading">
	        <h4 class="panel-title">Валюти виводу</h4>
	    </div>
	    <div class="panel-body">
			<?php 
			$cooperation_where['alias1'] = $alias->id;
			$cooperation_where['type'] = 'currency';
			$cooperation = $this->db->getAllDataById('wl_aliases_cooperation', $cooperation_where);
			if($cooperation && false) { $currencies = $this->load->function_in_alias($cooperation->alias2, '__get_All_Currencies'); if($currencies) { ?>
			<form action="<?= SERVER_URL?>admin/<?= $alias->alias?>/saveCurrency">
				<?php foreach($currencies as $currency) {?>

				<div class="form-group">
					<label for=""><?= $currency->code?></label>
					<input type="checkbox" name="currency[]" data-render="switchery" value="<?= $currency->code?>">
				</div>
			<?php } ?>
			</form>
			<?php
			 } }
			else {
				echo "<h6>Якщо ви хочете використовувати декілька валют налаштуйте сервіс currency</h6>";
			}
			 ?>
		</div>
	</div>
</div>
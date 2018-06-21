<div class="col-md-6">
	<div class="panel panel-inverse" data-sortable-id="form-stuff-1">
	    <div class="panel-heading">
	        <h4 class="panel-title">Валюти виводу, Формат виводу ціни</h4>
	    </div>
	    <div class="panel-body">
	    	<h4>Валюти виводу</h4>
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


		<?php if(!empty($options))
		foreach ($options as $option) if($option->name == 'price_format')
		{
			$before = $after = '';
			$round = 2;
			if(!empty($option->value))
			{
				$price_format = unserialize($option->value);
				if(isset($price_format['before']))
					$before = $price_format['before'];
				if(isset($price_format['after']))
					$after = $price_format['after'];
				if(isset($price_format['round']))
					$round = $price_format['round'];
			}
		?>
			<hr>

			<h4>Формат виводу ціни (<strong>Перед ціною</strong> <i>ціна (число)</i> <strong>Після ціни</strong>)</h4>
			<form action="<?=SITE_URL?>admin/<?=$alias->alias?>/save_price_format" method="POST" class="form-horizontal">
				<input type="hidden" name="service" value="<?=$alias->service?>">
				<div class="form-group">
					<label class="col-md-4 control-label">Перед ціною</label>
					<div class="col-md-8">
						<input type="text" name="before" value="<?=$before?>" class="form-control">
					</div>
				</div>
				<div class="form-group">
					<label class="col-md-4 control-label">Точність ціни <br><small>Кількість знаків після коми, копійки</small></label>
					<div class="col-md-8">
						<input type="number" name="round" value="<?=$round?>" class="form-control" min="0" max="2">
					</div>
				</div>
				<div class="form-group">
					<label class="col-md-4 control-label">Після ціни</label>
					<div class="col-md-8">
						<input type="text" name="after" value="<?=$after?>" class="form-control">
					</div>
				</div>
				<div class="form-group">
					<label class="col-md-4 control-label"></label>
					<div class="col-md-8">
						<input type="submit" class="btn btn-success" value="Зберегти">
					</div>
				</div>
			</form>
		<?php } ?>
		</div>
	</div>
</div>
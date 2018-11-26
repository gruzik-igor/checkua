<div class="row">
	<div class="row search-row">
        <form>
            <div class="col-lg-8 col-sm-8 search-col">
                <input type="number" name="id" class="form-control" placeholder="№ Замовлення" value="<?=$this->data->get('id')?>" required="required">
            </div>
            <div class="col-lg-4 col-sm-4 search-col">
                <button class="btn btn-primary btn-search btn-block"><i class="fa fa-search"></i><strong> Знайти</strong></button>
            </div>
        </form>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="panel panel-inverse">
            <div class="panel-heading">
                <div class="panel-heading-btn">
                	<a href="<?= SITE_URL.'admin/'.$_SESSION['alias']->alias?>/add" class="btn btn-warning btn-xs"><i class="fa fa-plus"></i> Додати покупку</a>
                	<a href="<?= SITE_URL.'admin/'.$_SESSION['alias']->alias?>/settings" class="btn btn-info btn-xs"><i class="fa fa-cogs"></i> Налаштування</a>
                    <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-default" data-click="panel-expand"><i class="fa fa-expand"></i></a>
                    <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-success" data-click="panel-reload"><i class="fa fa-repeat"></i></a>
                </div>
                <h4 class="panel-title"><?=$_SESSION['alias']->name?></h4>
            </div>
			<div class="panel-body">
                <div class="table-responsive">
                    <table id="data-table" class="table table-striped table-bordered nowrap" width="100%">
                        <thead>
                            <tr>
                            	<th>ID</th>
                            	<th>Статус</th>
                            	<th>Товар</th>
                            	<th>Покупець</th>
								<th>Загальна сума</th>
                            </tr>
                        </thead>
                        <tbody>
						<?php if(!empty($carts)){ $activeDay = false;
							foreach($carts as $cart){ 
								$day = date('d.m.Y', $cart->date_add);
								if($activeDay != $day)
								{
									echo "<tr><th colspan=5>{$day}</th></tr>";
									$activeDay = $day;
								}
							$color = 'default';
							switch ($cart->status) {
								case 1:
								case 4:
									$color = 'warning';
									break;
								case 2:
									$color = 'success';
									break;
								case 3:
									$color = 'primary';
									break; 
								case 5:
									$color = 'danger';
									break;
							}
							?>
						<tr class="<?=$color?>">
							<td title="<?= date('d.m.Y H:i', $cart->date_add)?>">
								<a href="<?=SITE_URL.'admin/'.$_SESSION['alias']->alias?>/<?=$cart->id?>" class="btn btn-<?=$color?> btn-xs"><?=$cart->id?></a>
								<br>
								<?= date('H:i', $cart->date_add)?>
							</td>
							<td><strong><?= $cart->status_name?></strong> <?= $cart->date_edit > 0 ? '<br>від '.date('d.m.Y H:i', $cart->date_edit) : '' ?></td>
							<td><?php if($cart->products)
                            foreach ($cart->products as $product) {
                            	if($product->info->photo) { ?>
					    			<a href="<?=SITE_URL.$product->info->link?>" class="left">
					    				<img src="<?=IMG_PATH?><?=(isset($product->info->cart_photo)) ? $product->info->cart_photo : $product->info->photo ?>" alt="<?=$this->text('Фото'). ' '. $product->info->name ?>" width="90">
					    			</a>
				    			<?php } if(!empty($product->info->article)) { ?>
					    			<a href="<?=SITE_URL.$product->info->link?>" target="_blank"><?= $product->info->article ?></a> <br>
					    		<?php } 
	    						echo '<strong>'.$product->info->name.'</strong>';
	    						if(!empty($product->product_options))
								{
									$product->product_options = unserialize($product->product_options);
									$opttext = '';
									foreach ($product->product_options as $key => $value) {
										$opttext .= "{$key}: <strong>{$value}</strong>, ";
									}
									$opttext = substr($opttext, 0, -2);
									echo "<p>{$opttext}</p>";
								}
								else
									echo "<br><br>";
	    						break;
	    					} if(count($cart->products) > 1) { ?>
	    						<p><a href="<?=SITE_URL.'admin/'.$_SESSION['alias']->alias?>/<?=$cart->id?>" class="btn btn-<?=$color?> btn-xs">+ <?=count($cart->products) - 1?> товар</a></p>
	    					<?php } ?>
							</td>
							<td>
								<strong><?= ($cart->user_name != '') ? $cart->user_name : 'Гість'?></strong>
								<br>
								<?= $cart->user_phone?>
							</td>
							<th><?= $cart->total?> грн</th>
						</tr>
						<?php } } else { ?>
							<tr>
								<td colspan="8">Замовлення відсутні</td>
							</tr>
						<?php } ?>
						</tbody>
					</table>
				</div>
				<?php $this->load->library('paginator');
                echo $this->paginator->get(); ?>
			</div>
		</div>
	</div>
</div>


<style type="text/css">
	.search-row {
	    max-width: 800px;
	    margin-left: auto;
	    margin-right: auto;
	}
	.search-row .search-col {
	    padding: 0;
	    position: relative;
	}
	.search-row .search-col:first-child .form-control {
	    border: 1px solid #16A085;
	    border-radius: 3px 0 0 3px;
	    margin-bottom: 20px;
	}
</style>

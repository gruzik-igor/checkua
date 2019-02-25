<div class="row">
    <div class="col-md-12">
        <div class="panel panel-inverse">
            <div class="panel-heading">
                <div class="panel-heading-btn">
                	<a href="<?= SITE_URL.'admin/'.$_SESSION['alias']->alias?>/bonus/add" class="btn btn-warning btn-xs"><i class="fa fa-plus"></i> Додати бонус-код</a>
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
                            	<th>Знижка</th>
                            	<th>Залишок, термін</th>
								<th>Інформація</th>
                            </tr>
                        </thead>
                        <tbody>
						<?php if(!empty($bonuses)){
							foreach($bonuses as $bonus){
							$color = 'default';
							switch ($bonus->status) {
								case 0:
									$color = 'warning';
									break;
								case 1:
									$color = 'success';
									break;
							}
							?>
						<tr class="<?=$color?>">
							<td>
								<?=$bonus->id?>
								<a href="<?=SITE_URL.'admin/'.$_SESSION['alias']->alias?>/bonus/<?=$bonus->id?>" class="btn btn-<?=$color?> btn-xs"><?=$bonus->code?></a>
							</td>
							<td><strong><?= $bonus->status_name?></strong> <?= $bonus->date_edit > 0 ? '<br>від '.date('d.m.Y H:i', $bonus->date_edit) : '' ?></td>
							<td><?php if($bonus->products)
                            foreach ($bonus->products as $product) {
                            	if(empty($product->info))
                            		continue;
                            	if($product->info->photo) { ?>
					    			<a href="<?=SITE_URL.'admin/'.$_SESSION['alias']->alias?>/<?=$bonus->id?>" class="left">
					    				<img src="<?=IMG_PATH?><?=(isset($product->info->cart_photo)) ? $product->info->cart_photo : $product->info->photo ?>" alt="<?=$this->text('Фото'). ' '. $product->info->name ?>" width="90">
					    			</a>
				    			<?php } if(!empty($product->info->article)) { ?>
					    			<a href="<?=SITE_URL.'admin/'.$_SESSION['alias']->alias?>/<?=$bonus->id?>" target="_blank"><?= $product->info->article ?></a> <br>
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
	    					} if(count($bonus->products) > 1) { ?>
	    						<p><a href="<?=SITE_URL.'admin/'.$_SESSION['alias']->alias?>/<?=$bonus->id?>" class="btn btn-<?=$color?> btn-xs">+ <?=count($bonus->products) - 1?> товар</a></p>
	    					<?php } ?>
							</td>
							<td>
								<strong><?= ($bonus->user_name != '') ? $bonus->user_name : 'Гість'?></strong>
								<br>
								<?= $bonus->user_phone?>
							</td>
							<th><?= $bonus->total?> грн</th>
						</tr>
						<?php } } else { ?>
							<tr>
								<td colspan="5">Бонус-коди відсутні
									<a href="<?= SITE_URL.'admin/'.$_SESSION['alias']->alias?>/bonus/add" class="btn btn-warning btn-xs"><i class="fa fa-plus"></i> Додати</a>
								</td>
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
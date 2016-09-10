<?php if(isset($_SESSION['notify'])){ 
require APP_PATH.'views/admin/notify_view.php';
} ?>

<div class="row">
    <div class="col-md-12">
        <div class="panel panel-inverse">
            <div class="panel-heading">
                <div class="panel-heading-btn">
                	<a href="<?=SITE_URL.'admin/'.$_SESSION['alias']->alias?>/add" class="btn btn-warning btn-xs"><i class="fa fa-plus"></i> Додати накладну</a>
                </div>
                <h4 class="panel-title"><?=$_SESSION['alias']->name?>. Список всіх накладних</h4>
            </div>
            <div class="panel-body">
                <div class="table-responsive">
                    <table id="data-table" class="table table-striped table-bordered nowrap" width="100%">
                        <thead>
                            <tr>
                                <th>Накладна №</th>
                                <th></th>
                                <th><?=($_SESSION['option']->productUseArticle) ? 'Артикул' : 'ID'?></th>
								<th>Назва</th>
								<th>Ціна прихідна</th>
								<th>Кількість / Залишок</th>
								<?php if($_SESSION['option']->markUpByUserTypes == 1) {
									$groups = $this->db->getAllDataByFieldInArray('wl_user_types', 1, 'active');
									foreach($groups as $group){
									?>
									<th>Ціна для <?=$group->title?></th>
								<?php } } else { ?>
									<th>Ціна вихідна</th>
								<?php } ?>
								<th>Остання операція</th>
								<th>Дата приходу</th>
                            </tr>
                        </thead>
                        <tbody>
                        	<?php
                        	if(!empty($products)) { 
                        		foreach($products as $product) { ?>
									<tr>
										<td><?=$product->id?></td>
										<td><a href="<?=SITE_URL.'admin/'.$_SESSION['alias']->alias.'/'.$product->id?>" class="btn btn-info btn-xs">Детальніше</a></td>
										<td><?=($_SESSION['option']->productUseArticle) ? $product->info->article : $product->id?></td>
										<td><?=$product->info->name?></td>
										<td><?=$product->price_in?></td>
										<td><?=$product->amount?></td>
										<?php if($_SESSION['option']->markUpByUserTypes == 1) {
											$price_out = 0;
											if(is_numeric($product->price_out)) $price_out = $product->price_out;
											else $product->price_out = unserialize($product->price_out);
											foreach($groups as $group){ ?>
												<td><?=(isset($product->price_out[$group->id])) ? $product->price_out[$group->id] : $price_out?></td>
										<?php } } else {
											echo("<td>{$product->price_out}</td>");
                                        	}
                                        ?>
										<td><?=($product->date_out > 0) ? date("d.m.Y H:i", $product->date_out) : 'Відсутня'?></td>
										<td><?=date("d.m.Y H:i", $product->date_in)?></td>
									</tr>
							<?php } } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
	function changeAvailability(e, id) {
		$.ajax({
			url: "<?=SITE_URL.'admin/'.$_SESSION['alias']->alias?>/changeAvailability",
			type: 'POST',
			data: {
				availability :  e.value,
				id :  id,
				json : true
			},
			success: function(res){
				if(res['result'] == false){
					alert('Помилка! Спробуйте щераз');
				}
			}
		});
	}
</script>

<style type="text/css">
	input[type="number"]{
		min-width: 50px;
	}
	select {
		max-width: 200px;
	}
</style>
<div class="row">
	<div class="col-md-6">
        <div class="panel panel-inverse">
            <div class="panel-heading">
                <h4 class="panel-title">Поточні налаштування</h4>
            </div>
            <?php
            if(isset($_SESSION['notify'])){ 
	        	require APP_PATH.'views/admin/notify_view.php';
	        }
	        ?>

            <div class="panel-body">
                <form action="<?=SITE_URL?>admin/<?=$_SESSION['alias']->alias?>/options_save" method="POST" class="form-horizontal">
	                <table class="table table-striped table-bordered">
	                    <tbody>
							<tr>
								<td>Адреса посилання</td>
								<td><?=$_SESSION['alias']->alias?></td>
							</tr>
							<tr>
								<td>Службова назва складу</td>
								<td><input type="text" name="name" value="<?=$storage->name?>" class="form-control"></td>
							</tr>
							<?php if(isset($options)) foreach ($options as $option) { ?>
								<tr>
									<td><?=$option->title?></td>
									<td>
										<?php
										if($option->type == 'bool') { 
											if($option->value == 1) echo('Так');
											else echo('Ні');
										} else { 
											echo($option->value);
										}
										?>
									</td>
								</tr>
							<?php } ?>
								<tr>
									<td colspan="2">
										<a href="<?=SITE_URL?>admin/wl_aliases/<?=$_SESSION['alias']->alias?>" target="_blank">Для зміни параметрів перейдіть <?=SITE_URL?>admin/wl_aliases/<?=$_SESSION['alias']->alias?></a>
									</td>
								</tr>
							<?php if($_SESSION['option']->markUpByUserTypes == 0) { ?>
								<tr>
									<td>Стандартна націнка товару відносно приходу (20%)</td>
									<td><input type="number" name="markup" value="<?=$storage->markup?>" min="0" class="form-control"></td>
								</tr>
							<?php } ?>
							<tr>
								<td>Склад додано</td>
								<td><?=date('d.m.Y H:i', $storage->date_add) .' '.$storage->user_name.' ('.$storage->user_add.')'?></td>
							</tr>
							<tr>
								<td></td>
								<td><button type="submit" class="btn btn-sm btn-success col-md-6">Зберегти</button></td>
							</tr>
						</tbody>
                	</table>
                </form>
			</div>
		</div>
	</div>


<?php if(!empty($_SESSION['option']->markUpByUserTypes)){ ?>
	<div class="col-md-6">
        <div class="panel panel-inverse">
            <div class="panel-heading">
                <h4 class="panel-title">Індивідуальна націнка товару відносно приходу</h4>
            </div>
            <div class="panel-body">
                <div class="table-responsive">
                	<form action="<?=SITE_URL?>admin/<?=$_SESSION['alias']->alias?>/markup_save" method="POST" class="form-horizontal">
                    <table class="table table-striped table-bordered nowrap" width="100%">
                        <thead>
                            <tr>
								<th>Група</th>
								<th>Націнка (Наприклад 20 %)</th>
                            </tr>
                        </thead>
                        <tbody>
							<?php 
							$groups = $this->db->getAllDataByFieldInArray('wl_user_types', 1, 'active');
							foreach($groups as $group){ ?>
								<tr>
									<td><?=$group->title?></td>
									<td>
										<input type="number" name="markup-<?=$group->id?>" value="<?=(isset($storage->markup[$group->id]))?$storage->markup[$group->id] : 0?>" min="0" class="form-control">
									</td>
								</tr>
							<?php } ?>
							<tr>
								<td></td>
								<td><button type="submit" class="btn btn-sm btn-success col-md-6">Зберегти</button></td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
<?php } ?>

</div>
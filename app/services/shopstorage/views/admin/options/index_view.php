<div class="row">
	<div class="col-md-6">
        <div class="panel panel-inverse">
            <div class="panel-heading">
                <h4 class="panel-title">Оновити інформацію</h4>
            </div>
            <div class="panel-body">
                <div class="table-responsive">
                	<form action="<?=SITE_URL?>admin/<?=$_SESSION['alias']->alias?>/update" enctype="multipart/form-data" method="POST" class="form-horizontal">
                		<input type="hidden" name="checkPrice" value="-1">
                    <table class="table table-striped table-bordered nowrap" width="100%">
                        <tbody>
                        	<tr>
								<th>Вхідний прайс (xls, xlsx, csv)</th>
								<td>
									<input type="file" name="price" required="required" class="form-control">
								</td>
							</tr>
							<?php /*
							<tr>
								<th>Вхідна ціна</th>
								<td>
									<select name="checkPrice" class="form-control">
										<option value="-1">Собівартість</option>
										<?php if(!empty($_SESSION['option']->markUpByUserTypes)){ 
											foreach($groups as $group) if($group->id > 1) { ?>
												<option value="<?=$group->id?>"><?=$group->title?></option>
										<?php } } else { ?>
											<option value="1">Продажна (з націнкою)</option>
										<?php } ?>
									</select>
								</td>
							</tr>
							<?php */
			            	$cooperation = $this->db->getQuery("SELECT c.*, a1.alias as alias1_name FROM wl_aliases_cooperation as c LEFT JOIN wl_aliases as a1 ON c.alias1 = a1.id WHERE c.alias2 = {$storage->id}", 'array');
							if($cooperation) { ?>
							<tr>
								<th>Валюта у прайсі</th>
								<td>
									<select name="currency" class="form-control">
										<option value="USD">$ USD</option>
										<option value="UA">грн UA</option>
									</select>
								</td>
							</tr>
							<tr>
								<th>Курс валют 1$ = ? грн</th>
								<td>
									<input type="number" name="currency_to_1" value="1" step="0.01" required="required" class="form-control">
								</td>
							</tr>
							<tr>
								<th>Відсутні товари додати до магазину</th>
								<td>
									<label><input type="radio" checked="checked" name="insert" value="1"> Так (довше)</label>
									<label><input type="radio" name="insert" value="0"> Ні (швидше)</label>
								</td>
							</tr>
							<tr>
								<th>Наявні товари, що відсутні у прайсі видаляти зі складу</th>
								<td>
									<label><input type="radio" checked="checked" name="delete" value="1"> Так (довше)</label>
									<label><input type="radio" name="delete" value="0"> Ні (швидше)</label>
								</td>
							</tr>
							<tr>
								<td>Магазин</td>
								<td>
									<select name="shop" class="form-control">
										<?php foreach($cooperation as $shop) { ?>
											<option value="<?=$shop->alias1?>"><?=$shop->alias1_name?></option>
										<?php } ?>
									</select>
									</td>
								</tr>
							<?php } ?>
	
							<tr>
								<td></td>
								<td><button type="submit" class="btn btn-sm btn-success col-md-6">Оновити</button></td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
	<div class="col-md-6">
	<?php
	$this->db->select('s_shopstorage_updates as s', '*', $_SESSION['alias']->id, 'storage');
	$this->db->join('wl_users', 'name, email', '#s.manager');
	$this->db->order('date DESC');
	$this->db->limit(10);
	$history = $this->db->get('array');
	if($history)
	{
	?>
		<div class="panel panel-inverse">
            <div class="panel-heading">
                <h4 class="panel-title">Історія оновлення</h4>
            </div>
            <div class="panel-body">
                <div class="table-responsive">
	                <table class="table table-striped table-bordered nowrap" width="100%">
	                	<tr>
	                		<th>Дата</th>
	                		<th>Менеджер</th>
	                		<th>Валюта</th>
	                		<th>Курс</th>
	                		<th>Додано</th>
	                		<th>Оновлено</th>
	                		<th>Видалено</th>
	                		<th>Файл</th>
	                	</tr>
	                	<?php foreach ($history as $update) { ?>
		                	<tr>
		                		<td><?=date('d.m.Y H:i', $update->date)?></td>
		                		<td><?php if($update->manager > 0) { ?>
		                			<a href="<?=SITE_URL.'admin/wl_users/'.$update->email?>"><?=$update->manager.'. '.$update->name?></a>
		                			<?php } else echo "Автооновлення"; ?>
		                		</td>
		                		<td><?=$update->currency?></td>
		                		<td><?=$update->price_for_1?></td>
		                		<td><?=$update->inserted?></td>
		                		<td><?=$update->updated?></td>
		                		<td><?=$update->deleted?></td>
		                		<td><?=$update->file?></td>
		                	</tr>
	                	<?php } ?>
	                </table>
                </div>
                Виведено 10 останніх оновлень. Для повного архіву звертайтеся до розробників.
            </div>
        </div>
    <?php
	}
	if(isset($_SESSION['import'])) { ?>
		<div class="alert alert-success fade in">
	        <span class="close" data-dismiss="alert">×</span>
	        <i class="fa fa-check fa-2x pull-left"></i>
	        <h4>Інформацію успішно імпортовано!</h4>
	        <?=$_SESSION['import']?>
	    </div>
	<?php unset($_SESSION['import']); } ?>
	</div>
</div>

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
							foreach($groups as $group) if($group->id > 1) { ?>
								<tr>
									<td><?=$group->title?></td>
									<td>
										<input type="number" name="markup-<?=$group->id?>" value="<?=(isset($storage->markup[$group->id]))?$storage->markup[$group->id] : 0?>" min="0" class="form-control">
									</td>
								</tr>
							<?php } ?>
								<tr>
									<td>Неавторизований користувач / гість</td>
									<td>
										<input type="number" name="markup-0" value="<?=(isset($storage->markup[0]))?$storage->markup[0] : 0?>" min="0" class="form-control">
									</td>
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
	</div>
<?php } ?>

</div>
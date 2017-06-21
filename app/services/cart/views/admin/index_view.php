<div class="row">
	<div class="row search-row">
        <form>
            <div class="col-lg-8 col-sm-8 search-col">
                <input type="text" name="id" class="form-control" placeholder="№ Замовлення" value="<?=$this->data->get('id')?>" required="required">
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
                	<a href="<?= SITE_URL.'admin/'.$_SESSION['alias']->alias?>/add" class="btn btn-warning btn-xs"><i class="fa fa-plus"></i> Додати товар</a>
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
                            	<th></th>
                            	<th>Покупець</th>
                            	<th>Контактний номер</th>
								<th>Статус</th>
								<th>Загальна сума</th>
								<th>Дата заявки</th>
								<th>Дата обробки</th>
                            </tr>
                        </thead>
                        <tbody>
						<?php if(!empty($carts)){ foreach($carts as $cart){ 
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
						<tr>
							<td><a href="<?=SITE_URL.'admin/'.$_SESSION['alias']->alias?>/<?=$cart->id?>"><?=$cart->id?></a></td>
							<td><a href="<?=SITE_URL.'admin/'.$_SESSION['alias']->alias?>/<?=$cart->id?>" class="btn btn-<?=$color?> btn-xs">Детальніше</a></td>
							<td><?= $cart->user_name?></td>
							<td><?= $cart->user_phone?></td>
							<td><?= $cart->status_name?></td>
							<td><?= $cart->total?> грн</td>
							<td><?= date('d.m.Y H:i', $cart->date_add)?></td>
							<td><?= $cart->date_edit > 0 ? date('d.m.Y H:i', $cart->date_edit) : '' ?></td>
						</tr>
						<?php } } ?>
						</tbody>
					</table>
				</div>
				<?php
                $this->load->library('paginator');
                echo $this->paginator->get();
                ?>
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

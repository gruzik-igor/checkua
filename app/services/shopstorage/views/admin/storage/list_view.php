<?php if(isset($_SESSION['notify'])){ 
require APP_PATH.'views/admin/notify_view.php';
} ?>

<div class="row">
	<div class="row search-row">
        <form>
            <div class="col-lg-8 col-sm-8 search-col">
                <input type="text" name="<?=($_SESSION['option']->productUseArticle) ? 'article' : 'id'?>" class="form-control" placeholder="<?=($_SESSION['option']->productUseArticle) ? 'Артикул' : 'ID'?>" value="<?=$this->data->get('article')?>" required="required">
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
                                <th><?=($_SESSION['option']->productUseArticle) ? 'Артикул' : 'ID'?></th>
								<th>Назва</th>
								<th>Загальна наявність / Доступно</th>
								<th>Резервовано</th>
								<?php if($_SESSION['option']->markUpByUserTypes == 1) {
									$groups = $this->db->getAllDataByFieldInArray('wl_user_types', 1, 'active');
									foreach($groups as $group) if($group->id > 2) {
									?>
									<th>Ціна для <?=$group->title?></th>
								<?php }
									echo("<th>Ціна для Неавторизованого</th>");
								 } else { ?>
									<th>Ціна вихідна</th>
								<?php } ?>
								<th>Остання операція</th>
								<th>Оновлено</th>
                            </tr>
                        </thead>
                        <tbody>
                        	<?php
                        	if(!empty($invoices)) { 
                        		foreach($invoices as $product) { ?>
									<tr>
										<td>#<?=$product->id?> <br>
											<a href="<?=SITE_URL.'admin/'.$_SESSION['alias']->alias.'/'.$product->id?>" class="btn btn-info btn-xs">Детальніше</a>
										</td>
										<td><?=($_SESSION['option']->productUseArticle) ? $product->info->article : $product->id?></td>
										<td><?=$product->info->name?></td>
										<td><?=$product->amount?> / <b><?=$product->amount_free?></b></td>
										<td><?=$product->amount_reserved?></td>
										<?php if($_SESSION['option']->markUpByUserTypes == 1) {
											$price_out = 0;
											if(is_numeric($product->price_out)) $price_out = $product->price_out;
											else $product->price_out = unserialize($product->price_out);
											foreach($groups as $group) if($group->id > 2) { ?>
												<td><?=(isset($product->price_out[$group->id])) ? $product->price_out[$group->id] : $price_out?></td>
										<?php } ?>
										<td><?=(isset($product->price_out[0])) ? $product->price_out[0] : $price_out?></td>
									<?php } else {
											echo("<td>{$product->price_out}</td>");
                                        	}
                                        ?>
										<td><?=($product->date_out > 0) ? date("d.m.Y H:i", $product->date_out) : 'Відсутня'?></td>
										<td><?=date("d.m.Y H:i", $product->date_edit)?></td>
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
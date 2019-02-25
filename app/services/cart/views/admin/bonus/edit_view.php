<?php if(isset($_SESSION['notify'])) { 
require APP_PATH.'views/admin/notify_view.php';
} ?>
      
<div class="row">
    <div class="col-md-12">
        <div class="panel panel-inverse">
            <div class="panel-heading">
                	<div class="panel-heading-btn">
						<a href="<?=SITE_URL.'admin/'.$_SESSION['alias']->alias?>" class="btn btn-info btn-xs"><i class="fa fa-list"></i> До замовлень</a>
						<a href="<?=SITE_URL.'admin/'.$_SESSION['alias']->alias?>/bonus" class="btn btn-success btn-xs"><i class="fa fa-ravelry"></i> До бонус-кодів</a>
                	</div>
                <h4 class="panel-title">Додати бонус-код</h4>
            </div>
            <div class="panel-body">
            	<form action="<?=SITE_URL.'admin/'.$_SESSION['alias']->alias?>/save_bonus" method="POST" enctype="multipart/form-data" class="form-horizontal" name="bonusForm">
            		<input type="hidden" name="id" value="<?=$bonus->id?>">
            		<?php if($bonus->id > 0){ ?>
        			<div class="form-group">
                        <label class="col-md-3 control-label">Статус</label>
                        <div class="col-md-9">
                            <label>
                            	<input type="radio" name="mode" value="1" <?=($bonus->status == 1) ? 'checked':''?>> Активний
                            </label>
                            <label>
                            	<input type="radio" name="mode" value="0" <?=($bonus->status == 0) ? 'checked':''?>> Відключено
                            </label>
                            <label>
                            	<input type="radio" name="mode" value="-1" <?=($bonus->status == -1) ? 'checked':''?>> Архів
                            </label>
                        </div>
                    </div>
            		<?php } ?>
            		<div class="form-group">
                        <label class="col-md-3 control-label">Режим бонусу</label>
                        <div class="col-md-9">
                            <label>
                            	<input type="radio" name="mode" value="0" <?=($bonus->mode == 0) ? 'checked':''?>> Без коду (діє зразу для всіх замовлень)
                            </label>
                            <label>
                            	<input type="radio" name="mode" value="1" <?=($bonus->mode == 1) ? 'checked':''?>> Бонус-код
                            </label>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-3 control-label">Бонус-код</label>
                        <div class="col-md-9">
                            <input type="text" class="form-control" name="code" value="<?=$bonus->code?>" <?=($bonus->id == 0) ? 'disabled':''?> required minlength="4">
                            <?php if($bonus->id == 0){ ?>
	                            <br>
	                            <label>
	                            	<input type="checkbox" checked name="generate" value="1">
	                            	Автогенерація бонус-коду у <input type="number" min="4" max="12" value="8" name="generateLength"> символів
	                            </label>
	                        <?php } ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-3 control-label">Службова інформація</label>
                        <div class="col-md-9">
                            <textarea name="info" class="form-control" placeholder="Джерела поширення коду, тощо"></textarea>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-3 control-label">Код діє <br><strong>рази</strong></label>
                        <div class="col-md-9">
                            <label>
                            	<input type="radio" name="count_do" value="0" <?=($bonus->count_do >= 0) ? 'checked':''?>>
                            	<input type="number" min="1" value="<?=($bonus->count_do >= 0) ? $bonus->count_do:1?>" <?=($bonus->count_do == -1) ? 'disabled':''?> name="count_do_numbers"> раз
                            </label>
                            <br>
                            <label>
                            	<input type="radio" name="count_do" value="-1" <?=($bonus->count_do == -1) ? 'checked':''?>> Необмежену кількість разів
                            </label>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-3 control-label">Код діє <br><strong>дати</strong></label>
                        <div class="col-md-9">
                            <div class="input-group">
                            	<span class="input-group-addon">від</span>
					            <input type="datetime-local" name="from" min="<?=date('Y-m-d\TH:s')?>" value="<?=date('Y-m-d\TH:s')?>" class="form-control">
					        </div>
					        <div class="input-group m-t-5">
                            	<span class="input-group-addon">до</span>
					            <input type="datetime-local" name="to" min="<?=date('Y-m-d\TH:s')?>" value="" class="form-control">
					        </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-3 control-label">Знижка</label>
                        <div class="col-md-9">
                            <div class="input-group">
                            	<label class="input-group-addon"> <input type="radio" name="type_do" value="persent" required> %</label>
					            <input type="number" name="persent" value="" min="0" step="0.01" required class="form-control" disabled>
					            <span class="input-group-addon">від суми у корзині</span>
					        </div>
					        <div class="input-group m-t-5">
                            	<label class="input-group-addon"> <input type="radio" name="type_do" value="fixsum" required> фіксована сума</label>
					            <input type="number" name="fixsum" value="" min="0" step="0.01" required class="form-control" disabled>
					            <span class="input-group-addon"><?php
					            if(!is_array($_SESSION['option']->price_format) && !empty($_SESSION['option']->price_format))
									$price_format = unserialize($_SESSION['option']->price_format);
								$echo_price_format = "y.o.";
								if(isset($price_format) && is_array($price_format))
									$echo_price_format = $price_format['before'].' '.$price_format['after'];
								echo $echo_price_format;
					            ?></span>
					        </div>
					        <br>
					        <label>
                            	<input type="checkbox" name="maxActive" value="1">
                            	Не більше ніж <input type="number" min="4" max="12" value="8" name="maxDiscount" disabled> <?=$echo_price_format?>
                            </label><br>
					        <label>
                            	<input type="checkbox" name="minActive" value="1">
                            	Мінімальна сума замовлення <input type="number" min="4" max="12" value="8" name="minSum" disabled> <?=$echo_price_format?>
                            </label>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-3 control-label"></label>
                        <div class="col-md-9">
                            <button type="submit" class="btn btn-sm btn-success"><?=($bonus->id == 0) ? 'Додати':'Зберегти'?></button>
                        </div>
                    </div>
            	</form>
            </div>
        </div>
    </div>
</div>

<?php $_SESSION['alias']->js_load[] = 'js/'.$_SESSION['alias']->alias.'/admin_bonus.js'; ?>
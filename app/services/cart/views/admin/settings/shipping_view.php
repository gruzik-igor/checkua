<div class="row">
    <div class="col-md-6">
        <div class="panel panel-inverse">
            <div class="panel-heading">
            	<div class="panel-heading-btn">
            		<?php if($shipping) { ?>
	            		<button onClick="$('#uninstall-form').slideToggle()" class="btn btn-danger btn-xs"><i class="fa fa-trash"></i> Видалити доставку</button>
	            	<?php } ?>
					<a href="<?=SITE_URL.'admin/'.$_SESSION['alias']->alias?>/settings" class="btn btn-info btn-xs"><i class="fa fa-cogs"></i> Налаштування корзини</a>
            	</div>
                <h4 class="panel-title">Деталі доставки</h4>
            </div>
            <?php if($shipping) { ?>
            	<div id="uninstall-form" class="alert alert-danger fade in" style="display: none;">
					<form action="<?=SITE_URL.'admin/'.$_SESSION['alias']->alias?>/delete_shipping" method="POST">
						<p><i class="fa fa-trash"></i> Ви впевнені що бажаєте видалити доставку?</p>
						<input type="hidden" name="id" value="<?=$shipping->id?>">
						<button class="btn btn-danger btn-xs"><i class="fa fa-trash"></i> Видалити</button>
						<button type="button" style="margin-left:15px" onClick="$('#uninstall-form').slideUp()" class="btn btn-info btn-xs">Скасувати</button>
					</form>
				</div>
			<?php } ?>
            <div class="panel-body">
            	<form action="<?=SITE_URL.'admin/'.$_SESSION['alias']->alias?>/save_shipping" method="POST" class="form-horizontal">
					<input type="hidden" name="id" value="<?= $shipping ? $shipping->id : 0?>">
					<div class="form-group">
						<label class="col-md-3 control-label">Тип доставки</label>
						<div class="col-md-9">
							<?php $selected = 0;
							 if($shipping)
							 {
							 	if($shipping->wl_alias)
							 	{
							 		$selected = -1;
							 		echo "Спеціальна (власні налаштування)";
							 	}
							 	else
							 		$selected = $shipping->type;
							 }
							 if($selected >= 0) { ?>
								<select name="type" class="form-control">
									<option value="1" <?=$selected == 1 ? 'selected' : ''?>>за адресою</option>
									<option value="2" <?=$selected == 2 ? 'selected' : ''?>>у відділення</option>
									<option value="3" <?=$selected == 3 ? 'selected' : ''?>>без адреси</option>
								</select>
							<?php } ?>
						</div>
					</div>
					<?php if($shipping) { ?>
						<div class="form-group">
							<label class="col-md-3 control-label">Стан</label>
							<label class="col-md-4">
								<input type="radio" name="active" value="1" <?= $shipping->active ? 'checked' : ''?>>
								Доставка активна
							</label>
							<label class="col-md-4">
								<input type="radio" name="active" value="0" <?= $shipping->active ? '' : 'checked'?>>
								Доставка НЕ активна
							</label>
						</div>
					<?php }

					if($_SESSION['language'])
					{
						if($shipping)
						{
							@$name = unserialize($shipping->name);
							@$info = unserialize($shipping->info);
						}
						foreach ($_SESSION['all_languages'] as $lang) {
							if($shipping)
							{
								if(!isset($name[$lang]))
									$name[$lang] = $shipping->name;
								if(!isset($info[$lang]))
									$info[$lang] = $shipping->info;
							}
							?>
						<div class="form-group">
	                        <label class="col-md-3 control-label">Назва <?=$lang?></label>
	                        <div class="col-md-9">
	                            <input type="text" class="form-control" name="name_<?=$lang?>" value="<?=isset($name[$lang]) ? $name[$lang] : ''?>" placeholder="Назва <?=$lang?>" required>
	                        </div>
	                    </div>
	                    <div class="form-group">
	                        <label class="col-md-3 control-label">Опис <?=$lang?></label>
	                        <div class="col-md-9">
	                        	<textarea class="form-control" name="info_<?=$lang?>"><?=isset($info[$lang]) ? $info[$lang] : ''?></textarea>
	                        </div>
	                    </div>
					<?php } } else { ?>
						<div class="form-group">
	                        <label class="col-md-3 control-label">Назва</label>
	                        <div class="col-md-9">
	                            <input type="text" class="form-control" name="name" value="<?= $shipping ? $shipping->name : ''?>" placeholder="Назва" required>
	                        </div>
	                    </div>
	                    <div class="form-group">
	                        <label class="col-md-3 control-label">Опис</label>
	                        <div class="col-md-9">
	                        	<textarea class="form-control" name="info"><?= $shipping ? $shipping->info : ''?></textarea>
	                        </div>
	                    </div>
					<?php } ?>
						
                    <div class="form-group">
                        <label class="col-md-3 control-label"></label>
                        <div class="col-md-9">
                            <button type="submit" class="btn btn-sm btn-success"><i class="fa fa-save"></i> <?= $shipping ? 'Зберегти' : 'Додати'?></button>
                        </div>
                    </div>
	            </form>
            </div>
        </div>
    </div>
</div>
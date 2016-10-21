<div class="row">
    <div class="col-md-6">
        <div class="panel panel-inverse" data-sortable-id="form-stuff-1">
            <div class="panel-heading">
        		<div class="panel-heading-btn">
                	<a href="<?=SITE_URL?>admin/wl_aliases" class="btn btn-info btn-xs"><i class="fa fa-bank"></i> До всіх адрес</a>
                </div>
                <h4 class="panel-title">Редагувати загальні налаштування сайту</h4>
            </div>
            <div class="panel-body">
	            <form action="<?=SITE_URL?>admin/wl_aliases/save_all" method="POST" class="form-horizontal">
					<?php if(isset($options)) { 
						foreach ($options as $option) { ?>
							<div class="form-group">
		                        <label class="col-md-3 control-label"><?=$option->name?></label>
		                        <div class="col-md-9">
		                            <input type="text" class="form-control" name="option-<?=$option->id?>" value="<?=$option->value?>" placeholder="<?=$option->name?>">
		                        </div>
		                    </div>
		                <?php } ?>
		                <div class="form-group">
	                        <label class="col-md-3 control-label"></label>
	                        <div class="col-md-9">
	                            <button type="submit" class="btn btn-sm btn-success">Зберегти</button>
	                        </div>
	                    </div>
                    <?php } else { ?>
                    	<div class="note note-info">
							<h4>Увага! Загальні налаштування сайту відсутні</h4>
							<p>
							    За допомогою форми праворуч додайте налаштування.
		                    </p>
						</div>
                    <?php } ?>
                </form>
                <p>Загальні налаштування мають найнижчий пріорітет (можуть переоголошуватися для alias), проте доступні для цілого сайту.</p>
            </div>
        </div>
    </div>
	<div class="col-md-6">
        <div class="panel panel-inverse" data-sortable-id="form-stuff-1">
            <div class="panel-heading">
                <h4 class="panel-title">Додати загальне налаштування сайту</h4>
            </div>
            <div class="panel-body">
	            <form action="<?=SITE_URL?>admin/wl_aliases/add_all" method="POST" class="form-horizontal">
					<div class="form-group">
                        <label class="col-md-3 control-label">Назва налаштування</label>
                        <div class="col-md-9">
                            <input type="text" class="form-control" name="name" placeholder="Назва (анг)" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-3 control-label">Дані властивості</label>
                        <div class="col-md-9">
                            <input type="text" class="form-control" name="value" placeholder="Властивість" required>
                        </div>
                    </div>
	                <div class="form-group">
                        <label class="col-md-3 control-label"></label>
                        <div class="col-md-9">
                            <button type="submit" class="btn btn-sm btn-success">Додати</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
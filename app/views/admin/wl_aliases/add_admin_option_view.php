<div class="row">
	<div class="col-md-6">
		<div class="panel panel-inverse">
            <div class="panel-heading">
                <h4 class="panel-title">Додати основне налаштування</h4>
            </div>
            <div class="panel-body">
            	<form action="<?=SITE_URL?>admin/wl_aliases/saveOption" method="POST" class="form-horizontal">
					<input type="hidden" name="alias_id" value="<?=$alias->id?>">
					<input type="hidden" name="alias_link" value="<?=$alias->alias?>">
					<input type="hidden" name="service" value="<?=$alias->service?>">
					<div class="form-group">
                        <label class="col-md-3 control-label">Тип налаштування</label>
                        <div class="col-md-9">
                            <label class="radio-inline">
                                <input type="radio" name="type" value="all" checked="checked">
                                Загальний
                            </label>
                            <label class="radio-inline">
                                <input type="radio" name="type" value="admin">
                                Для панелі керування
                            </label>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-3 control-label">Назва параметру</label>
                        <div class="col-md-9">
                            <input type="text" class="form-control" name="name" placeholder="name" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-3 control-label">Значення</label>
                        <div class="col-md-9">
                            <input type="text" class="form-control" name="value" placeholder="value" required>
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
	<div class="col-md-6">
		<div class="panel panel-inverse">
            <div class="panel-heading">
                <h4 class="panel-title">Додати підменю в панелі керування до <?=$alias->alias?></h4>
            </div>
            <div class="panel-body">
            	<form action="<?=SITE_URL?>admin/wl_aliases/saveOption" method="POST" class="form-horizontal">
					<input type="hidden" name="alias_id" value="<?=$alias->id?>">
					<input type="hidden" name="alias_link" value="<?=$alias->alias?>">
					<input type="hidden" name="service" value="<?=$alias->service?>">
					<input type="hidden" name="type" value="sub-menu">
                    <div class="form-group">
                        <label class="col-md-3 control-label">Назва підменю</label>
                        <div class="col-md-9">
                            <input type="text" class="form-control" name="name" placeholder="name" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-3 control-label">Адреса</label>
                        <div class="col-md-9">
                        	<?=SITE_URL.'admin/'.$alias->alias.'/'?>
                            <input type="text" class="form-control" name="alias" placeholder="alias" required>
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
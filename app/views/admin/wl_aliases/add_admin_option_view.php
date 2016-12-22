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
                            <label class="radio-inline">
                                <input type="radio" name="type" value="all" onclick="$('#name').val('uniqueDesign')">
                                Унікальний дизайн
                            </label>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-3 control-label">Назва параметру</label>
                        <div class="col-md-9">
                            <input type="text" class="form-control" id="name" name="name" placeholder="name" required>
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
<?php if($service && !empty($service->cooperation_types)) { ?>
    <div class="row">
        <div class="col-md-6">
            <div class="panel panel-inverse">
                <div class="panel-heading">
                    <h4 class="panel-title">Додати адресу співпраці</h4>
                </div>
                <div class="panel-body">
                    <form action="<?=SITE_URL?>admin/wl_aliases/saveCooperation" method="POST" class="form-horizontal">
                        <input type="hidden" name="alias_id" value="<?=$alias->id?>">
                        <input type="hidden" name="alias_link" value="<?=$alias->alias?>">
                        <input type="hidden" name="alias_index" value="<?=isset($service->cooperation_index)?$service->cooperation_index:0?>">
                        <div class="form-group">
                            <label class="col-md-3 control-label">Тип співпраці</label>
                            <div class="col-md-9">
                                <select name="type" class="form-control">
                                    <?php foreach ($service->cooperation_types as $type => $name) {
                                        echo("<option value='{$type}'>{$name}</option>");
                                    } ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-3 control-label">Адреса співпраці</label>
                            <div class="col-md-9">
                                <select name="alias" class="form-control">
                                    <?php $wl_aliases = $this->db->getAllData('wl_aliases');
                                    foreach ($wl_aliases as $wl_alias) if($wl_alias->active && $wl_alias->id != $alias->id) {
                                        echo("<option value='{$wl_alias->id}'>{$wl_alias->alias}</option>");
                                    } ?>
                                </select>
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
<?php } ?>
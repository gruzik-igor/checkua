<div class="row">
    <div class="col-md-6">
        <div class="panel panel-inverse">
            <div class="panel-heading">
            	<div class="panel-heading-btn">
                	<a href="<?=SITE_URL?>admin/wl_images/<?=$alias->alias?>" class="btn btn-info btn-xs">До зображень <?=$alias->alias?></a>
                </div>
                <h4 class="panel-title">Редагувати зміну розміру</h4>
            </div>
            <div class="panel-body">
    	        <form action="<?=SITE_URL?>admin/wl_images/save" method="POST" class="form-horizontal">
    	        	<input type="hidden" name="id" value="<?=$wl_image->id?>">
    	        	<input type="hidden" name="alias_name" value="<?=$alias->alias?>">
    	        	<div class="form-group">
                        <label class="col-md-3 control-label">Стан</label>
                        <div class="col-md-9">
                            <label><input type="checkbox" name="active" value="1" <?=($wl_image->active)?'checked':''?>> зміну розміру активна</label>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-3 control-label">Назва</label>
                        <div class="col-md-9">
                            <input type="text" name="name" class="form-control" value="<?=$wl_image->name?>" required placeholder="Назва/признчення мініатюри" />
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-3 control-label">Префікс</label>
                        <div class="col-md-9">
                            <input type="text" name="prefix" class="form-control" value="<?=$wl_image->prefix?>" required placeholder="Префікс мініатюри" />
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-3 control-label">Тип</label>
                        <div class="col-md-9">
                            <select name="type" class="form-control">
                            	<option value="1" <?=($wl_image->type == 1)?'selected':''?>>resize</option>
                            	<option value="2" <?=($wl_image->type == 2)?'selected':''?>>preview</option>
                            </select>
                        </div>
                    </div>                
                    <div class="form-group">
                        <label class="col-md-3 control-label">Ширина (px)</label>
                        <div class="col-md-9">
                            <input type="number" name="width" class="form-control" value="<?=$wl_image->width?>" required placeholder="Ширина" />
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-3 control-label">Висота (px)</label>
                        <div class="col-md-9">
                            <input type="number" name="height" class="form-control" value="<?=$wl_image->height?>" required placeholder="Висота" />
                        </div>
                    </div>
                    <div class="form-group">
                    	<div class="col-md-3"></div>
                        <div class="col-md-9">
                            <button type="submit" class="btn btn-sm btn-success ">Зберегти</button>
                        </div>
                    </div>
    	        </form>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="panel panel-inverse">
            <div class="panel-heading">
                <h4 class="panel-title">Видалити зміну розміру</h4>
            </div>
            <div class="panel-body">
                <form action="<?=SITE_URL?>admin/wl_images/delete" method="POST" class="form-horizontal">
                    <input type="hidden" name="id" value="<?=$wl_image->id?>">
                    <input type="hidden" name="alias_name" value="<?=$alias->alias?>">
                    <?php $number = rand(0, 1000); ?>
                    <input type="hidden" name="close_number" value="<?=$number?>">
                    <div class="text-center">Захисту від випадкового видалення:</div>
                    <?php if(isset($_SESSION['notify_error_delete'])) { ?>
                        <div class="alert alert-danger fade in m-b-15">
                        <strong>Помилка!</strong>
                        <?=$_SESSION['notify_error_delete']?>
                        <span class="close" data-dismiss="alert">&times;</span>
                    </div>
                    <?php unset($_SESSION['notify_error_delete']); } ?>
                    <div class="form-group">
                        <label class="col-md-3 control-label">Число <b><?=$number?></b></label>
                        <div class="col-md-9">
                            <input type="number" name="user_namber" class="form-control" min="0" max="1000" required placeholder="Введіть число зліва" />
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-md-3"></div>
                        <div class="col-md-9">
                            <button type="submit" class="btn btn-sm btn-danger ">Видалити</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="panel panel-inverse">
            <div class="panel-heading">
                <h4 class="panel-title">Скопіювати зміну розміру</h4>
            </div>
            <div class="panel-body">
                <form action="<?=SITE_URL?>admin/wl_images/copy" method="POST" class="form-horizontal">
                    <input type="hidden" name="id" value="<?=$wl_image->id?>">
                    <div class="form-group">
                        <label class="col-md-3 control-label">Копія для головної адреси</label>
                        <div class="col-md-9">
                            <select name="alias" class="form-control">
                                <?php 
                                $aliases = $this->db->getAllDataByFieldInArray('wl_aliases', 1, 'active');
                                foreach ($aliases as $a) {
                                    $selected = '';
                                    if($a->id == $alias->id) $selected = 'selected';
                                    echo "<option value='{$a->id}' {$selected}>{$a->alias}</option>";
                                }
                                ?>
                            </select>
                        </div>
                    </div>  
                    <div class="form-group">
                        <label class="col-md-3 control-label">Нова назва</label>
                        <div class="col-md-9">
                            <input type="text" name="name" class="form-control" value="<?=(isset($_POST['name'])) ? $this->data->post('name') : $wl_image->name?>" required placeholder="Назва/признчення мініатюри" />
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-3 control-label">Новий префікс</label>
                        <div class="col-md-9">
                            <input type="text" name="prefix" class="form-control" value="<?=(isset($_POST['prefix'])) ? $this->data->post('prefix') : $wl_image->prefix?>" required placeholder="Префікс мініатюри" />
                        </div>
                    </div>
                    <?php $number = rand(0, 1000); ?>
                    <input type="hidden" name="close_number" value="<?=$number?>">
                    <div class="text-center">Захисту від випадкового копіювання:</div>
                    <?php if(isset($_SESSION['notify_error_copy'])) { ?>
                        <div class="alert alert-danger fade in m-b-15">
                        <strong>Помилка!</strong>
                        <?=$_SESSION['notify_error_copy']?>
                        <span class="close" data-dismiss="alert">&times;</span>
                    </div>
                    <?php unset($_SESSION['notify_error_copy']); } ?>
                    <div class="form-group">
                        <label class="col-md-3 control-label">Число <b><?=$number?></b></label>
                        <div class="col-md-9">
                            <input type="number" name="user_namber" class="form-control" min="0" max="1000" required placeholder="Введіть число зліва" />
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-md-3"></div>
                        <div class="col-md-9">
                            <button type="submit" class="btn btn-sm btn-info ">Скопіювати</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
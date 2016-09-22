<div class="col-md-7">
	<div class="panel panel-inverse" data-sortable-id="form-stuff-1">
	    <div class="panel-heading">
	        <h4 class="panel-title">Редагувати користувача</h4>
	    </div>
	    <div class="panel-body">
			<form action="<?=SITE_URL?>admin/wl_users/save" method="POST" class="form-horizontal">
				<input type="hidden" name="id" value="<?=$user->id?>">
			    <div class="form-group" title="УВАГА! Зміна email призведе до ліквідації паролю користувача (необхідно встановити заново)">
			        <label class="col-md-3 control-label">Email користувача</label>
			        <div class="col-md-9">
			            <input type="email" name="email" class="form-control" value="<?=$user->email?>" required placeholder="email" />
			        </div>
			    </div>
			    <div class="form-group">
			        <label class="col-md-3 control-label">Ім'я користувача</label>
			        <div class="col-md-9">
			            <input type="text" name="name" class="form-control" value="<?=$user->name?>" required placeholder="Ім'я користувача" />
			        </div>
			    </div>
			    <div class="form-group">
			        <label class="col-md-3 control-label">Змінити пароль користувача</label>
			        <div class="col-md-9">
			        	<input type="checkbox" name="active_password" id="active_password" value="1"> <label for="active_password">Встановити новий пароль:</label>
			            <input type="text" name="password" class="form-control" placeholder="Новий пароль" />
			            (Поточний: <?=$user->password?>)
			        </div>
			    </div>
			    <div class="form-group">
			        <label class="col-md-3 control-label">Тип користувача</label>
			        <div class="col-md-9">
			            <select class="form-control" name="type" onchange="chengeType(this)" required>
						<?php foreach ($types as $type) {
								echo('<option value="'.$type->id.'"');
								if($type->id == $user->type) echo " selected";
								echo('>'.$type->title.'</option>');
							} ?>
			            </select>
			        </div>
			    </div>
			    <div id="permissions" class="form-group" <?=($user->type == 2)?'':'style="display: none"'?>>
			        <label class="col-md-3 control-label">Сторінки доступу</label>
			        <div class="col-md-9">
			            <?php $permissions = $this->db->getAllData('wl_aliases');
							$up = $this->db->getAllDataByFieldInArray('wl_user_permissions', $user->id, 'user');
							$user_permissions = array();
							if(!empty($up)) foreach ($up as $upp) {
								$user_permissions[] = $upp->permission;
							}
							foreach ($permissions as $p) { ?>
								<input type="checkbox" id="<?=$p->alias?>" name="permissions[]" value="<?=$p->id?>" <?=(in_array($p->id, $user_permissions))?'checked':''?>><label for="<?=$p->alias?>"><?=$p->alias?></label>
						<?php } ?>
			        </div>

			        <script type="text/javascript">
						function chengeType (e) {
							if(e.value == 2){
								$('#permissions').slideDown('slow');
							} else {
								$('#permissions').slideUp('slow');
							}
						}
					</script>
			    </div>
			    <div class="form-group">
			        <label class="col-md-3 control-label">Статус акаунта</label>
			        <div class="col-md-9">
			        	<select class="form-control" name="status" required>
			            <?php foreach ($status as $s) {
								echo('<option value="'.$s->id.'"');
								if($s->id == $user->status) echo " selected";
								echo('>'.$s->title.'</option>');
							} ?>
						</select>
			        </div>
			    </div>

				<?php if(!empty($user->info)) foreach($user->info as $key => $value) { ?>
				<div class="form-group">
			        <label class="col-md-3 control-label"><?= $key ?></label>
			        <div class="col-md-9">
			            <input type="text" name="info[<?= $key ?>]" class="form-control" value="<?= $value ?>" />
			        </div>
			    </div>
				<?php } ?>

			    <div class="form-group">
			    	<label class="col-md-3 control-label">Дата реєстрації</label>
			        <div class="col-md-9">
			            <?=date("d.m.Y H:i", $user->registered)?>
			        </div>
			    </div>
			    <div class="form-group">
			        <label class="col-md-3 control-label">Пароль адміністратора</label>
			        <div class="col-md-9">
			            <input type="password" name="admin-password" class="form-control" required placeholder="Пароль адміністратора для підтвердження" />
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
<div class="col-md-5">
	<div class="panel panel-inverse" data-sortable-id="form-stuff-1">
	    <div class="panel-heading">
	        <h4 class="panel-title">Видалити користувача</h4>
	    </div>
	    <div class="panel-body">
	        <form action="<?=SITE_URL?>admin/wl_users/delete" method="POST" class="form-horizontal">
	            <input type="hidden" name="id" value="<?=$user->id?>">
	            <div class="form-group">
	                <label class="col-md-3 control-label">Пароль адміністратора</label>
	                <div class="col-md-9">
	                    <input type="password" name="admin-password" class="form-control" required placeholder="Пароль адміністратора для підтвердження" />
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
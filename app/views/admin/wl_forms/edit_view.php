<div class="row">
    <div class="col-md-12">
        <div class="panel panel-inverse">
            <div class="panel-heading">
                <div class="panel-heading-btn">
                	<a href="javascript:;" class="btn btn-warning btn-xs" onclick="toggle(hidden_form)"><i class="fa fa-plus"></i> Додати поле</a>
                </div>
                <h4 class="panel-title">Наявні поля:</h4>
            </div>
            <div class="panel-body">
                <div class="table-responsive">
                    <table id="data-table" class="table table-striped table-bordered nowrap" width="100%">
                        <thead>
                            <tr>
                            	<th>Порядок</th>
                                <th width="100px" nowrap>ID</th>
								<th>Тип користувачів</th>
								<th title="name">Назва поля</th>
								<th>Тип поля</th>
								<th title="required">Обов'язкове поле</th>
								<th>Можна редагувати</th>
								<th title="title">Заголовок</th>
                            </tr>
                        </thead>
                        <tbody>
							<?php if($fields) foreach ($fields as $field) { ?>
								<tr>
									<td><?=$field->position?></td>
									<td><?=$field->id?></td>
									<td><?=($field->user_type>0)?$field->user_type_name: 'Всі'?></td>
									<td><a href="<?=SITE_URL?>admin/wl_forms/<?php echo $form->name.'/'.$field->name?>"><?=$field->name?></td></a>
									<td><?=$field->input_type_name?></td>
									<td><?=($field->required)?'Так':'Ні'?></td>
									<td><?=($field->can_change)?'Так':'Ні'?></td>
									<td><?=$field->title?></td>
								</tr>
							<?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ДОДАТИ ПОЛЕ -->
<div class="col-lg-6" id="hidden_form" style="display: none;" >
	<div class="panel panel-inverse">
        <div class="panel-heading">
            <h4 class="panel-title">Додати поле</h4>
        </div>
		<div  class="panel-body">
			<form action="<?=SITE_URL?>admin/wl_forms/add_field" method="POST" class="form-horizontal">
				<input type="text" name="form" value="<?= $form->id ?>" hidden>
				<input type="text" name="form_name" value="<?= $form->name ?>" hidden>
				<table>
					<div class="form-group">
						<label class="col-md-3 control-label">user_type</label>
						<div class="col-md-9">
							<select name="user_type" class="form-control" required>
								<option value="0">Всі</option>
								<?php $user_types = $this->db->getAllDataByFieldInArray('wl_user_types', 1, 'active');
								foreach ($user_types as $type) {
									echo('<option value="'.$type->id.'"');
									echo('>'.$type->name.'</option>');
								} ?>
							</select>
						</div>
					</div>
					<div class="form-group">
						<label class="col-md-3 control-label">name*</label>
						<div class="col-md-9">
							<input type="text" class="form-control" name="name" id="name" required>
						</div>
					</div>
					<div class="form-group">
						<label class="col-md-3 control-label">input_type*</label>
						<div class="col-md-9">
							<select name="input_type" class="form-control" onchange="changeInputType(this)" id="input_type" required>
							<?php $input_types = $this->db->getAllData('wl_input_types');
								foreach ($input_types as $type) {
									echo('<option value="'.$type->id.'"');
									echo('>'.$type->name.'</option>');
								} ?>
							</select>
						</div>
					</div>
					<div class="form-group" id="hiddenValue" hidden>
						<label class="col-md-3 control-label">value</label>
						<div class="col-md-9">
							<input type="text" class="form-control" name="value[]" required>
							<input type="text" class="form-control" name="value[]">
							<button class="btn btn-sm btn-warning" onclick="addAnotherValue()"> Додати ще поле</button>
						</div>
					</div>
					<div class="form-group">
						<label class="col-md-3 control-label">required</label>
						<div class="col-md-9">
							<input type="radio" name="required" value="1">Так
							<input type="radio" name="required" value="0" checked>Ні
						</div>
					</div>
					<div class="form-group">
						<label class="col-md-3 control-label">can_change</label>
						<div class="col-md-9">
							<input type="radio" name="can_change" value="1">Так
							<input type="radio" name="can_change" value="0" checked>Ні
						</div>
					</div>
					<div class="form-group">
						<label class="col-md-3 control-label">title*</label>
						<div class="col-md-9">
							<input type="text" class="form-control" name="title" required>
						</div>
					</div>
					<div class="form-group">
						<div class="col-lg-3"></div>
						<div class="col-md-9">
							<input type="submit" value="Додати" class="btn btn-sm btn-warning" onclick="checkName()" id="submit">
							<span id="name_error" style="color:red"></span>
						</div>
					</div>
				</table>
			</form>
		</div>
	</div>
</div>

<script>
	function toggle(el) {
		el.style.display = (el.style.display == 'none') ? '' : 'none'
	}

	function changeInputType (t) {
		if(t.value == 8 || t.value == 9 || t.value == 10){
			$("#hiddenValue").show();
			$("div #hiddenValue input[type='text']").removeAttr("disabled");
		}
		else{
			$("#hiddenValue").hide();
			$("div").filter(":hidden").children("input[type='text']").attr("disabled", "disabled");
		}
	}

	function addAnotherValue () {
		event.preventDefault();
		$("#hiddenValue button").before('<input type="text" class="form-control" name="value[]">')
	}

	function checkName () {
		var name = $("#name").val();
		var names = ["<?= $names?>"]
		for(var n in names){
			if(names[n] == name){
				$('#submit').prop('disabled', true);
				$('#name_error').html("Співпадають імена");
				setTimeout(function() {
				$('#submit').prop('disabled', false);
			}, 100)
			}
		}
	}

</script>

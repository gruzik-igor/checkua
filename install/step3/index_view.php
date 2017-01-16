<h4 style="color:#fff">3. Реєстрація адміністратора</h4>
<form action="<?=SITE_URL?>step3" method="POST" class="margin-bottom-0">
	<div class="form-group">
        <input type="text" name="name" value="developer" class="form-control input-lg" placeholder="Ім'я яке відображається на сайті" required />
    </div>
    <div class="form-group">
        <input type="email" name="email" value="developer@webspirit.com.ua" class="form-control input-lg" placeholder="email адміністратора (використовується при логінації)" required />
    </div>
    <div class="form-group">
        <input type="password" name="admin_password" id="admin_password" value="" class="form-control input-lg" placeholder="Пароль" required />
    </div>
    <div class="form-group">
        <input type="password" name="admin_password_repeat" id="admin_password_repeat" value="" class="form-control input-lg" placeholder="Повторити пароль" required />
        <span id="password_error" style="color:red"></span>
    </div>
    <div class="form-group">
    	<p>Вільна реєстрація користувачів: </p>
        <label><input name="userSignUp" type="radio" value="1" checked="checked" required> Дозволена </label> 
		<label><input name="userSignUp" type="radio" value="0"> Заборонена</label>
    </div>

	<button type="submit" onclick="checkPassword()" id="submitButtonStep3" class="btn btn-success btn-block btn-lg">Далі</button>
</form>

<script>
	function checkPassword ()
	{
		var pass1 = $('#admin_password').val();
		var pass2 = $('#admin_password_repeat').val();
		if (pass1 != pass2 && pass1 != '')
		{
			$('#submitButtonStep3').prop('disabled', true);
			$('#password_error').html("Паролі не співпадають");
			setTimeout(function() {
				$('#submitButtonStep3').prop('disabled', false);
			}, 100);
			return false;
		}
		else
		{
			return true;
		}
	}
</script>
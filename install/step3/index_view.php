<h1>3. Реєстрація адміністратора</h1>
<form action="<?=SITE_URL?>install/step3/step3.php" method="POST">
	<p>Name: <br/><input name="name" id="name" type="text" required>
	<span>Ім'я яке відображається на сайті</span><br/>
	E-mail: <br/><input name="email" id="email" type="email" required><br/>
	Password: <br/><input name="admin_password" id="admin_password" type="password" required><br/>
	Repeat password: <br/><input name="admin_password_repeat" id="admin_password_repeat" type="password" required>
	<span id="password_error" style="color:red"></span><br/></p>
	<input onclick="checkPassword()" id="submitButtonStep3" type="submit" value="Далі"></form>
</form>

<script>
	function checkPassword () {
		var pass1 = $('#admin_password').val();
		var pass2 = $('#admin_password_repeat').val();
		if (pass1 != pass2){
			$('#submitButtonStep3').prop('disabled', true);
			$('#password_error').html("Паролі не співпадають");
			setTimeout(function() {
				$('#submitButtonStep3').prop('disabled', false);
			}, 100)
		}
	}
</script>
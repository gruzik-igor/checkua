<h2 style="color:#fff">1. Налаштування БД</h2>
<form action="<?=SITE_URL?>step1" method="POST">
	<input type="hidden" name="step" value="1">
	<input type="hidden" name="check" value="2">
		<p> Host: <br/><input name="host" id="host" type="text" value="localhost" required><br/>
			User: <br/><input name="user" id="user" type="text" required><br/>
			Password: <br/><input name="password" id="password" type="text" ><br/>
			<button type="button" onclick="checkConnection(1)">Перевірити з'єднання</button>
			<span id="checkResult1"></span>
			<br/>
			DataBase: <br/><input name="db" id="db" type="text" required><br/>
			<button type="button" onclick="checkConnection(2)">Перевірити db</button>
			<span id="checkResult2"></span></p>
			<br/>
			<input id="submitButtonStep1" type="submit" disabled="disabled" value="Далі"></form>
<script>
function checkConnection (step){
	var host = $('#host').val();
	var user = $('#user').val();
	var password = $('#password').val();
	var db = $('#db').val();
	var go = false;
	if(host != '' && user != '') go = true;
	if(step == 2 && db == '') go = false;

	if(go){
		$('#icon-loading').css('dispaly', 'inline');
		$.ajax({
			url: "<?=SITE_URL?>install/step1/checkConnection.php",
			type: 'POST',
			data: {
				check :  step,
				host :  host,
				user : user,
				password : password,
				db : db,
				json : true
			},
			success: function(res){
				if(res['result'] == true) {
					$('#checkResult'+step).css("color", "green");	
					if (step == 2) {
						$('#submitButtonStep1').prop('disabled', false);
					};
				} else {
					$('#checkResult'+step).css("color", "red");	
				}
				$('#checkResult'+step).html(res['content']);
				$('#panel').fadeIn("slow");
				$('#icon-loading').css('dispaly', 'none');
			}
		});
	} else {
		if (step == 2) {
			alert('Введіть назву бази даних!');
		} else {
			alert('Поля host, user є обов\'язковими!');
		}
	}
	return false;
}
</script>
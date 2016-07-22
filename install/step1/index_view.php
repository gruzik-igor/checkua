<h2 style="color:#fff">1. Налаштування БД</h2>
<form action="<?=SITE_URL?>step1" method="POST" class="margin-bottom-0">
	<input type="hidden" name="step" value="1">
	<input type="hidden" name="check" value="2">

	<div class="form-group">
		<span> Host: </span>
        <input type="text" name="host" id="host" value="localhost" class="form-control input-lg" placeholder="host" required />
    </div>
	<div class="form-group">
		<span> User: </span>
        <input type="text" name="user" id="user" value="root" class="form-control input-lg" placeholder="user" required />
    </div>
    <div class="form-group m-b-20">
		<span> Password: </span>
        <input type="text" name="password" id="password" value="" class="form-control input-lg" placeholder="password" />
    </div>
    <div class="login-buttons">
        <button type="button" onclick="checkConnection(1)" class="btn btn-info btn-block btn-lg">1. Перевірити з'єднання</button>
    </div>
<span id="checkResult1"></span>
<div class="form-group m-b-20">
		<span> Data base: </span>
        <input type="text" name="db" id="db" value="" class="form-control input-lg" placeholder="db" required />
    </div>
    <div class="login-buttons">
        <button type="button" onclick="checkConnection(2)" class="btn btn-info btn-block btn-lg">2. Перевірити db</button>
    </div>
    <span id="checkResult2"></span>

			<input id="submitButtonStep1" type="submit" disabled="disabled" value="Далі" class="btn btn-success btn-block btn-lg">
</form>

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
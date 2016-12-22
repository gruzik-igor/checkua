<h4 style="color:#fff">1. Налаштування БД</h4>
<form action="<?=SITE_URL?>step1" method="POST" class="margin-bottom-0" id="form-step-1">
	<div class="form-group">
        <input type="text" name="host" id="host" value="localhost" class="form-control input-lg" placeholder="Host" required />
    </div>
	<div class="form-group">
        <input type="text" name="user" id="user" value="root" class="form-control input-lg" placeholder="User" required />
    </div>
    <div class="form-group">
        <input type="text" name="password" id="password" value="" class="form-control input-lg" placeholder="Password" />
    </div>
	<div class="form-group m-b-20">
        <input type="text" name="db" id="db" value="<?=SITE_NAME?>" class="form-control input-lg" placeholder="Data base" required />
    </div>
	<button type="button" onclick="checkConnection()" class="btn btn-success btn-block btn-lg">Далі</button>
</form>

<script>
function checkConnection ()
{
	$('#saveing').css("display", "block");
	$.ajax({
		url: "<?=SITE_URL?>install/step1/checkConnection.php",
		type: 'POST',
		data: {
			host :  $('#host').val(),
			user : $('#user').val(),
			password : $('#password').val(),
			db : $('#db').val(),
			json : true
		},
		success: function(res)
		{
			if(res['result'] == true) {
				$('#form-step-1').submit();
			} else {
				alert(res['content']);
			}
			$('#saveing').css("display", "none");
		},
		error: function(){
			alert("Помилка! Спробуйте ще раз!");
			$('#saveing').css("display", "none");
		},
		timeout: function(){
			alert("Помилка: Вийшов час очікування! Спробуйте ще раз!");
			$('#saveing').css("display", "none");
		}
	});
	return false;
}
</script>
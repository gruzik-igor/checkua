<h1>2. Налаштування сайту</h1>
<form action="<?=SITE_URL?>install/step2/step2.php" method="POST">
	<p>Адреса сайту: <?php print_r($_SERVER['HTTP_HOST']."/".$uri[1])?></p>
	Назва сайту: <br/><input name="site_name" id="site_name" type="text" required><br/>
	Пароль системи: <br/><input name="sys_password" id="sys_password" type="text" pattern=".{8,12}" title="Пароль повинен складатись з 8-12 символів" required>
	<a href="https://www.random.org/passwords" target="_blank" style="text-decoration: none;">=> Random.org/password</a><br/>
	Емейл сайту: <br/><input name="site_email" id="site_email" type="email" required><br/>
	Системний емейл: <br/><input name="sys_email" id="sys_email" type="email"><br/>
	Сайт:</br> 
	<input onclick="multiLang(1)" name="language" id="monolingual" type="radio" value="one" required><label for="monolingual">Одномовний</label><br/>
	<input onclick="multiLang(2)" name="language" id="multilingual" type="radio" value="multi"><label for="multilingual">Багатомовний</label>

	<div id="anotherLanguage" style="display:none" >
		<input type="checkbox" name="languages[]" id="ua" value="ua"><label for="ua">ua</label> 
		<input type="checkbox" name="languages[]" id="ru" value="ru"><label for="ru">ru</label> 
		<input type="checkbox" name="languages[]" id="en" value="en"><label for="en">en</label> 
		<input type="checkbox" name="languages[]" id="pl" value="pl"><label for="pl">pl</label> </br>
		<input type="text" id="new_lang1" name="languages[]" pattern="[a-z]{2}" title="Введіть 2 букви в нижньому регістрі" style="width: 25px">   
		<input type="text" id="new_lang2" name="languages[]" pattern="[a-z]{2}" title="Введіть 2 букви в нижньому регістрі" style="width: 25px">
	</div><p></p>
	<input id="submitButtonStep2" type="submit" value="Далі"></form>
</form>

<script>
	function multiLang(step)
	{
		if (step == 1) {
			$("#anotherLanguage").hide();
		}
		else
			$("#anotherLanguage").show();
	}
</script>
<h4 style="color:#fff">4. Налаштування сайту</h4>
<form action="<?=SITE_URL?>install/step4/finish.php" method="POST" class="margin-bottom-0">

	<div class="form-group">
		<p>Робоча адреса сайту: (без www)</p>
        <input type="text" name="site_name" value="<?=SITE_NAME?>" class="form-control input-lg" placeholder="Робоча адреса сайту" required />
    </div>
    <div class="form-group">
        <label title="Відбуватиметься автовиправлення адреси без www"><input type="checkbox" name="useWWW" value="1" /> Використовувати www</label>
    </div>
	<div class="form-group">
		<p>Емейл сайту: </p>
        <input type="email" name="site_email" value="info@<?=SITE_NAME?>" class="form-control input-lg" placeholder="Емейл сайту" title="Від даного email будуть відсилатися системні листи" required />
    </div>
    
    <div class="form-group">
        <label title="Прискорення роботи сайту "><input type="checkbox" name="cache" value="1" /> Використовувати cache</label>
    </div>
	<div class="form-group m-b-20">
        <p>Мультимовність: </p>
        <label><input onclick="multiLang(1)" name="language" type="radio" value="one" checked="checked" required> Одномовний </label> 
		<label><input onclick="multiLang(2)" name="language" type="radio" value="multi"> Багатомовний</label>

		<div id="anotherLanguage" style="display:none" >
			<label><input type="checkbox" name="languages[]" value="ua"> ua </label> 
			<label><input type="checkbox" name="languages[]" value="ru"> ru </label> 
			<label><input type="checkbox" name="languages[]" value="en"> en </label> 
			<label><input type="checkbox" name="languages[]" value="pl"> pl </label> 
			Нова мова: <input type="text" id="new_lang1" name="languages[]" class="form-control languages" pattern="[a-z]{2}" title="Введіть 2 букви в нижньому регістрі" style="width: 25px"> 
			Нова мова: <input type="text" id="new_lang2" name="languages[]" class="form-control languages" pattern="[a-z]{2}" title="Введіть 2 букви в нижньому регістрі" style="width: 25px">
			<p><label><input name="language_type" type="radio" value="maindomain" checked="checked" required> В адресі site.com/*en/link </label> 
		<label><input name="language_type" type="radio" value="subdomain"> Автопіддомен *en.site.com</label>
		</div>
    </div>
	<button type="submit" class="btn btn-success btn-block btn-lg">До панелі керування</button>
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

<style>
	.languages
	{
		display: inline;
		width: 40px !important;
	}
</style>
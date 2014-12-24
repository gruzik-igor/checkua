<?php require_once(APP_PATH.'views/language/lng_signup_'.$_SESSION['language'].'.php'); ?>

<?php if(isset($errors)) : ?>
	<link href="<?=SITE_URL?>css/notify.css" rel="stylesheet" />
	<div class="notify-error"><?=$errors?></div>
<?php endif; 

$name = '';
$src_photo_200 = '';

if($method == 'vk'){
	$name = $_SESSION['vk_user']->first_name .' '.$_SESSION['vk_user']->last_name;
	$src_photo_200 = $_SESSION['vk_user']->photo_200_orig;
}
if($method == 'fb'){
	$name = $_SESSION['fb_user']['name'];
	$src_photo_200 = 'https://graph.facebook.com/'.$_SESSION['fb_user']['id'].'/picture?type=large';
}

?>

<h2>Увага! Незареєстрований користувач!</h2>

<p>Доброго дня, <b><?= $name ?>.</b></p>
<p>Ми Виявили, що Ви не зареєстровані* в системі <?= SITE_NAME ?>!</p>
<p><i>*Якщо Ви раніше реєструвалися, введіть нижче Ваш емейл і пароль. Соціальна мережа автоматично приєднається до Вашого профілю у <?= SITE_NAME ?></i></p>
<?php if(empty($recovery)){ ?>
	<h3>Пропонуємо Вам зареєструватися! Після реєстрації Ви зможете:</h3>
		<div class="misto5">
			<h5>Для наречених: </h5> <br>
			<p>- Створення власної сторінки</p> 	
			<p>- Найбільший вибір весільних спеціалістів</p>
			<p>- Фото та відео роботи</p>
		</div>
		<div class="misto5"> 
			<h5>Для професіоналів: </h5><br>
			<p>- Відсутні ТОП позиції  </p>
			<p>- Спілкування з молодятами в мережі сайту  </p>
			<p>- Безкоштовна можливість подання акцій  </p>
		</div>
	<div class="clear">
<?php } ?>
<img src="<?= $src_photo_200 ?>" style="float:left; padding:25px 15px">
<div style="padding:25px 15px">
<form method="post" action="<?=SITE_URL.$_SESSION['alias']->alias?>/signup" id="register">
	
	<h3>Для реєстрації/приєднання соціального профілю введіть наступні дані:</h3>
	
	<p><label for="email" style="margin-right:54px;"><?=$lng_signup['email']?>: </label>
		<input  class="ipt" type="email" id="useremail" name="email" onblur="checkEmail(this.value,'')" value="<?= (isset($recovery)) ? $recovery['email'] : '' ?>" required /></p>
		
	<p><label for="password" style="margin-right:125px;">Пароль: </label>
		<input class="ipt" type="password" name="password" required/></p>
		<p class="fld-desk"><?=$lng_signup['help_password']?></p>
		
	<p id=userOnSite style="display:none"><i>Введіть Ваш пароль і соціальний профіль автоматично приєднається до профілю на <?= SITE_NAME ?></i></p>
	<p id=usertype><label for="type"><?=$lng_signup['type']?>: </label>
		<label><input type=radio class="ipt" id="type" name="type" required value="m" <?= (isset($recovery) && $recovery['type'] == 'm') ? 'checked' : ''?> onclick="checkType()"><?=$lng_signup['m']?></input></label>
		<label><input type=radio class="ipt" name="type" value="s" <?= (isset($recovery) && $recovery['type'] == 's') ? 'checked' : ''?> onclick="checkType()"> <?=$lng_signup['s']?> </input></label>
		<div id=showType <?= (isset($recovery) && $recovery['type'] == 's') ? '' : 'style="display:none"'; ?> >
			<select class="ipt" name="typeS">
				<?php 
					foreach($types as $t){ $selected = ''; if(isset($recovery) && $recovery['typeS'] == $t->id) $selected = 'selected';
						echo "<option value={$t->id} {$selected}>{$t->name}</option>";
					}
				?>
			</select>
		</div>
	</p>
	<br>
	
	<input type=hidden name=method value="<?= (isset($recovery) && $recovery['method'])?$recovery['method']:$method ?>">
	<label><input class="button" type="submit" value="<?=$lng_signup['go']?>"></label>
	
</form>
</div>

	<script>
		function checkType(){
			var type = getCheckedRadioValue('type');
			if(type == 's' && $('#showType').is(":hidden")){
				$('#showType').slideDown("slow");
			} else {
				$('#showType').slideUp("slow");
			}
		}
		
		function getCheckedRadioValue(radioGroupName) {
			var rads = document.getElementsByName(radioGroupName),
			i;
			for (i=0; i < rads.length; i++)
			if (rads[i].checked)
			return rads[i].value;
			return null;
		}
		
		function checkEmail(input, response){
			if (response != ''){
				// Режим ответа
				usertype = document.getElementById('usertype');
				userOnSite = document.getElementById('userOnSite');
				
				if (response == 1){
					usertype.style.display="none";
					userOnSite.style.display="block";
					document.getElementById('type').required = false;
				}else{
					usertype.style.display="block";
					userOnSite.style.display="none";
					document.getElementById('type').required = true;
				}
			} else {
				// Режим приема
				url  = '<?=SITE_URL?>signup/checkEmail?q=' + input;
				loadXMLDoc(url);
			}
		}
		
		var req;
		
		function loadXMLDoc(url) {
			// ветка для родного XMLHttpRequest объекта
			if (window.XMLHttpRequest) {
				req = new XMLHttpRequest();
				req.onreadystatechange = processReqChange;
				req.open("GET", url, true);
				req.send(null);
				// ветка для IE/Windows ActiveX версии
			} else if (window.ActiveXObject) {
				req = new ActiveXObject("Microsoft.XMLHTTP");
				if (req) {
					req.onreadystatechange = processReqChange;
					req.open("GET", url, true);
					req.send();
				}
			}
		}
		
		function processReqChange() 
		{
			// если запрос показывает "готово"
			if (req.readyState == 4) {
				// если статус ответа сервера "OK"
				if (req.status == 200) {
					// выполняем парсинг пришедшего и выполняем с этими параметрами checkName() 
					response = req.responseXML.documentElement;
					method = response.getElementsByTagName('method')[0].firstChild.data;
					result = response.getElementsByTagName('result')[0].firstChild.data;
					eval(method + '(\'\', result)');
				} else {
					alert("There was a problem retrieving the XML data:\n" + req.statusText);
				}
			}
		}
	</script>

<?php

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

<h2>Підключення соціального профілю</h2>

<img src="<?= $src_photo_200 ?>" style="float:left; padding:15px">
<div style="padding:25px 15px">
<form method="post" action="<?=SITE_URL.$_SESSION['alias']->alias?>/confirm" id="register">
	<p>Доброго дня, <b><?= $name ?>.</b></p>
	<p>Для підключення даного соціального профілю натисність на кнопку підключити</p>
	
	<br>
	
	<input type=hidden name=method value="<?= $method ?>">
	<label><input class="button" type="submit" value="Підключити"></label>
	
</form>
</div>
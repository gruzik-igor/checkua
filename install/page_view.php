<!DOCTYPE html>
<html>
<head>
	<title>ІНСТАЛЯЦІЯ САЙТУ <?=$LOCAL_SITE_URL?></title>

	<meta charset="utf-8">

<!-- 	<link rel="stylesheet" href="<?=SITE_URL?>style/reset.css"> -->
	<link rel="stylesheet" href="<?=SITE_URL?>style/style-admin.css">

	<script type="text/javascript" src="<?=SITE_URL?>assets/jquery-1.11.0.min.js"></script>
</head>
<body>
<div class="container">
	<div class="header">
		<div class="title" class="f-l">White Lion 1.0 / <span class="site"><?=$LOCAL_SITE_URL?></span></div>
		<div class="time f-left f-l">Сьогодні: <?=date("d.m.Y H:i")?></div>
	</div>
	<div class="left-box">
		<ul class="left_nav">
			<li>
				<div class="f-l image"><img src="<?=SITE_URL?>style/images-admin/sync.png"></div>
				<div class="f-l nav">1. Налаштування БД</div>
				<div class="f-l image"><img src="<?=SITE_URL?>style/images-admin/globe.png"></div>
				<div class="f-l nav">2. Налаштування сайту</div>
				<div class="f-l image"><img src="<?=SITE_URL?>style/images-admin/user.png"></div>
				<div class="f-l nav">3. Реєстрація адміністратора</div>
				<div class="f-l image"><img src="<?=SITE_URL?>style/images-admin/home.png"></div>
				<div class="f-l nav">4. Завершення</div>
			</li>
		</ul>
	</div>
	<div class="right-content">
		<?php if ((isset($errors) && $errors != '') || (isset($success) && $success != '')) require APP_PATH.'views'.DIRSEP.'notify_view.php'; ?>
		
		<?php if(isset($view_file) && $view_file != '') require_once($view_file.'.php'); ?>
	</div>
</div>	
</body>
</html>
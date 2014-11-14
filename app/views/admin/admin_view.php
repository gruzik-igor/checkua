<!DOCTYPE html>
<html>
<head>
	<title>Панель керування <?=SITE_NAME?></title>

	<meta charset="utf-8">

<!-- 	<link rel="stylesheet" href="<?=SITE_URL?>style/reset.css"> -->
	<link rel="stylesheet" href="<?=SITE_URL?>style/style-admin.css">

	<script type="text/javascript" src="<?=SITE_URL?>assets/jquery-1.8.3.js"></script>
</head>
<body>
<div class="container">
	<div class="header">
		<div class="title" class="f-l">White Lion 1.2.1 / <span class="site"><?=SITE_NAME?></span></div>
		<div class="f-r top_menu">
			<a href="<?=SITE_URL?>">На головну</a>
			<a href="<?=SITE_URL?>logout">Вийти</a>
		</div>
		<div class="time f-left f-l">Сьогодні: <?=date("d.m.Y H:i")?></div>
	</div>
	<div class="left-box">
		<ul class="left_nav">
			<li>
				<div class="f-l image"><img src="<?=SITE_URL?>style/images-admin/home.png"></div>
				<div class="f-l nav"><a href="<?=SITE_URL?>admin">Головна</a></div>
				<div class="f-l image"><img src="<?=SITE_URL?>style/images-admin/mail-1.png"></div>
				<div class="f-l nav"><a href="<?=SITE_URL?>admin/mailhandler">Зворотній зв'язок</a></div>
			</li>
			<?php $wl_aliases = $this->db->getAllData('wl_aliases');
			if($wl_aliases){
				foreach ($wl_aliases as $wl_alias) if($wl_alias->active == 1 && $wl_alias->service > 0) { ?>
				<li>
					<div class="f-l image"><img src="<?=SITE_URL?>style/images-admin/document.png"></div>
					<div class="f-l nav"><a href="<?=SITE_URL?>admin/<?=$wl_alias->alias?>"><?=$wl_alias->alias?></a></div>
				</li>
			<?php	}
			}
			if($_SESSION['user']->admin == 1){ ?>
				<li>
					<div class="f-l image"><img src="<?=SITE_URL?>style/images-admin/user.png"></div>
					<div class="f-l nav"><a href="<?=SITE_URL?>admin/wl_users">Користувачі</a></div>
				</li>
				<li>
					<div class="f-l image"><img src="<?=SITE_URL?>style/images-admin/settings.png"></div>
					<div class="f-l nav"><a href="<?=SITE_URL?>admin/wl_aliases">Система</a></div>
				</li>
				<li>
					<div class="f-right nav"><a href="<?=SITE_URL?>admin/wl_ntkd">SEO</a></div>
				</li>
				<li>
					<div class="f-right nav"><a href="<?=SITE_URL?>admin/wl_aliases">Адреси</a></div>
				</li>
				<li>
					<div class="f-right nav"><a href="<?=SITE_URL?>admin/wl_services">Сервіси</a></div>
				</li>
			<?php } ?>
		</ul>
	</div>
	<div class="right-content">
		<?php if ((isset($errors) && $errors != '') || (isset($success) && $success != '')) require APP_PATH.'views/notify_view.php'; ?>
		
		<?php if(isset($view_file) && $view_file != '') require_once($view_file.'.php'); else require_once('index_view.php'); ?>
	</div>
</div>	
</body>
</html>
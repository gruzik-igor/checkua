<!DOCTYPE html>
<!--[if IE 8]> <html lang="uk" class="ie8"> <![endif]-->
<!--[if !IE]><!-->
<html lang="uk">
<!--<![endif]-->
<head>
    <meta charset="utf-8" />
    <title>Інсталяція White Lion CMS</title>
    <meta content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" name="viewport" />
    <meta content="" name="description" />
    <meta content="" name="author" />
    <link rel="shortcut icon" href="<?=SITE_URL?>style/admin/whitelion.png">
    
    <!-- ================== BEGIN BASE CSS STYLE ================== -->
    <link href="http://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700" rel="stylesheet">
    <link href="<?=SITE_URL?>assets/jquery-ui/themes/base/minified/jquery-ui.min.css" rel="stylesheet" />
    <link href="<?=SITE_URL?>assets/bootstrap/css/bootstrap.min.css" rel="stylesheet" />
    <link href="<?=SITE_URL?>assets/font-awesome/css/font-awesome.min.css" rel="stylesheet" />
    <link href="<?=SITE_URL?>style/admin/animate.min.css" rel="stylesheet" />
    <link href="<?=SITE_URL?>style/admin/style.min.css" rel="stylesheet" />
    <link href="<?=SITE_URL?>style/admin/style-responsive.min.css" rel="stylesheet" />
    <link href="<?=SITE_URL?>style/admin/theme/default.css" rel="stylesheet" id="theme" />
    <!-- ================== END BASE CSS STYLE ================== -->
    
    <!-- ================== BEGIN BASE JS ================== -->
    <script src="<?=SITE_URL?>assets/pace/pace.min.js"></script>
    <!-- ================== END BASE JS ================== -->
</head>
<body class="pace-top">
    <!-- begin #page-loader -->
    <div id="page-loader" class="fade in"><span class="spinner"></span></div>
    <!-- end #page-loader -->
    
    <div class="login-cover">
        <div class="login-cover-image"><img src="<?=SITE_URL?>style/admin/login-bg/bg-1.jpg" data-id="login-cover-image" alt="" /></div>
        <div class="login-cover-bg"></div>
    </div>
    <!-- begin #page-container -->
    <div id="page-container" class="fade">
        <!-- begin login -->
        <div class="login login-v2" data-pageload-addclass="animated fadeIn">
            <!-- begin brand -->
            <div class="login-header">
                <div class="brand">
                    <img src="<?=SITE_URL?>style/admin/images/WhiteLion-white.png" style="width: 50px;"> White Lion CMS
                    <small>Інсталяція <?=SITE_URL?></small>
                </div>
                <div class="icon">
                    <i class="fa fa-cogs"></i>
                </div>
            </div>
            <!-- end brand -->
            <div class="login-content">
                <?php if ((isset($errors) && $errors != '') || (isset($success) && $success != '')) require APP_PATH.'views'.DIRSEP.'admin'.DIRSEP.'notify_view.php'; ?>
        
                <?php if(isset($view_file) && $view_file != '') require_once($view_file.'.php'); ?>
            </div>
        </div>
        <!-- end login -->
        
        <ul class="login-bg-list">
            <li class="active"><a href="#" data-click="change-bg"><img src="<?=SITE_URL?>style/admin/login-bg/bg-1.jpg" alt="" /></a></li>
            <li><a href="#" data-click="change-bg"><img src="<?=SITE_URL?>style/admin/login-bg/bg-2.jpg" alt="" /></a></li>
            <li><a href="#" data-click="change-bg"><img src="<?=SITE_URL?>style/admin/login-bg/bg-3.jpg" alt="" /></a></li>
            <li><a href="#" data-click="change-bg"><img src="<?=SITE_URL?>style/admin/login-bg/bg-4.jpg" alt="" /></a></li>
            <li><a href="#" data-click="change-bg"><img src="<?=SITE_URL?>style/admin/login-bg/bg-6.jpg" alt="" /></a></li>
        </ul>
    </div>
    <!-- end page container -->
    
    <!-- ================== BEGIN BASE JS ================== -->
    <script src="<?=SITE_URL?>assets/jquery/jquery-1.9.1.min.js"></script>
    <script src="<?=SITE_URL?>assets/jquery/jquery-migrate-1.1.0.min.js"></script>
    <script src="<?=SITE_URL?>assets/jquery-ui/ui/minified/jquery-ui.min.js"></script>
    <script src="<?=SITE_URL?>assets/bootstrap/js/bootstrap.min.js"></script>
    <!--[if lt IE 9]>
        <script src="<?=SITE_URL?>assets/crossbrowserjs/html5shiv.js"></script>
        <script src="<?=SITE_URL?>assets/crossbrowserjs/respond.min.js"></script>
        <script src="<?=SITE_URL?>assets/crossbrowserjs/excanvas.min.js"></script>
    <![endif]-->
    <script src="<?=SITE_URL?>assets/slimscroll/jquery.slimscroll.min.js"></script>
    <script src="<?=SITE_URL?>assets/jquery-cookie/jquery.cookie.js"></script>
    <!-- ================== END BASE JS ================== -->
    
    <!-- ================== BEGIN PAGE LEVEL JS ================== -->
    <script src="<?=SITE_URL?>assets/admin/login-v2.min.js"></script>
    <script src="<?=SITE_URL?>assets/admin/apps.min.js"></script>
    <!-- ================== END PAGE LEVEL JS ================== -->

    <script>
        $(document).ready(function() {
            App.init();
            LoginV2.init();
        });
    </script>
</body>
</html>

<?php /*

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

*/

?>
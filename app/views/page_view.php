<!DOCTYPE html>
<html lang="uk">

<head>
    <title><?=$_SESSION['alias']->title?></title>

    <!-- Meta -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?=$_SESSION['alias']->description?>">
    <meta name="keywords" content="<?=$_SESSION['alias']->keywords?>">
    <meta name="author" content="webspirit.com.ua">

<!doctype html>
<html class="no-js" lang="en">
<head>
<meta charset="utf-8">

	<link rel="shortcut icon" href="<?=IMG_PATH?>ico.jpg">

	<link href="<?=SITE_URL?>assets/bootstrap/css/bootstrap.min.css" rel="stylesheet">

	<link href="<?=SITE_URL?>assets/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet" />
	<link href="<?=SITE_URL?>assets/css/animate.min.css" rel="stylesheet" />
	<link href="<?=SITE_URL?>assets/css/style.min.css" rel="stylesheet" />
	<link href="<?=SITE_URL?>assets/css/style-responsive.min.css" rel="stylesheet" />
	<link href="<?=SITE_URL?>assets/css/theme/default.css" id="theme" rel="stylesheet" />
	<!-- ================== END BASE CSS STYLE ================== -->
    
	<!-- ================== BEGIN BASE JS ================== -->
	<script src="<?=SITE_URL?>assets/plugins/pace/pace.min.js"></script>
	<!-- ================== END BASE JS ================== -->
</head>
<body>
	<script>
	  window.fbAsyncInit = function() {
	    FB.init({
	      appId      : '1280962155267129',
	      xfbml      : true,
	      version    : 'v2.6'
	    });
	  };

	  (function(d, s, id){
	     var js, fjs = d.getElementsByTagName(s)[0];
	     if (d.getElementById(id)) {return;}
	     js = d.createElement(s); js.id = id;
	     js.src = "//connect.facebook.net/en_US/sdk.js";
	     fjs.parentNode.insertBefore(js, fjs);
	   }(document, 'script', 'facebook-jssdk'));
	</script>
	
	<?php
		// include "@commons/mobile_menu.php";

		echo('<div id="wrapper">');

		include "@commons/header.php";

		if(isset($view_file)) require_once($view_file.'.php');


		include "@commons/footer.php";

		echo('</div>');
	?>
	<script src="<?=SITE_URL?>assets/plugins/jquery/jquery-1.9.1.min.js"></script>
	<script src="<?=SITE_URL?>assets/plugins/jquery/jquery-migrate-1.1.0.min.js"></script>
	<script type="text/javascript" src="<?=SITE_URL?>assets/bootstrap/js/bootstrap.min.js"></script>
	

	<script src="<?=SITE_URL?>assets/plugins/bootstrap/js/bootstrap.min.js"></script>
	<!--[if lt IE 9]>
		<script src="assets/crossbrowserjs/html5shiv.js"></script>
		<script src="assets/crossbrowserjs/respond.min.js"></script>
		<script src="assets/crossbrowserjs/excanvas.min.js"></script>
	<![endif]-->
	<script src="<?=SITE_URL?>assets/plugins/jquery-cookie/jquery.cookie.js"></script>
	<script src="<?=SITE_URL?>assets/js/apps.min.js"></script>
	<!-- ================== END BASE JS ================== -->
	
	<script>
	    $(document).ready(function() {
	        App.init();
	    });
	</script>



</body>
</html>
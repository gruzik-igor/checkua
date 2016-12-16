<html lang="uk" prefix="og: http://ogp.me/ns#">
<head>
	<title><?=html_entity_decode($_SESSION['alias']->title, ENT_QUOTES)?></title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="title" content="<?=html_entity_decode($_SESSION['alias']->title, ENT_QUOTES)?>">
    <meta name="description" content="<?=html_entity_decode($_SESSION['alias']->description, ENT_QUOTES)?>">
    <meta name="keywords" content="<?=html_entity_decode($_SESSION['alias']->keywords, ENT_QUOTES)?>">
    <meta name="author" content="webspirit.com.ua">

    <?=html_entity_decode($_SESSION['option']->global_MetaTags, ENT_QUOTES)?>
    <?=html_entity_decode($_SESSION['alias']->meta, ENT_QUOTES)?>

    <meta property="og:locale"             content="uk_UA" />
    <meta property="og:title"              content="<?=html_entity_decode($_SESSION['alias']->title, ENT_QUOTES)?>" />
    <meta property="og:description"        content="<?=html_entity_decode($_SESSION['alias']->description, ENT_QUOTES)?>" />
    <meta property="og:image"              content="<?=IMG_PATH.$_SESSION['alias']->image?>" />

	<link rel="shortcut icon" href="<?=SERVER_URL?>style/admin/images/whitelion-black.png">

	<link href="<?=SERVER_URL?>assets/bootstrap/css/bootstrap.min.css" rel="stylesheet">
	<link href="<?=SERVER_URL?>assets/font-awesome/css/font-awesome.min.css" rel="stylesheet" />
	<link href="<?=SERVER_URL?>style/animate.min.css" rel="stylesheet" />
	<link href="<?=SERVER_URL?>style/style.min.css" rel="stylesheet" />
	<link href="<?=SERVER_URL?>style/style-responsive.min.css" rel="stylesheet" />
	<!-- ================== END BASE CSS STYLE ================== -->
    
	<!-- ================== BEGIN BASE JS ================== -->
	<script src="<?=SERVER_URL?>assets/pace/pace.min.js"></script>
	<!-- ================== END BASE JS ================== -->
</head>
<body>
	<?php
		echo('<div id="wrapper">');

		include "@commons/header.php";

		if(isset($view_file)) require_once($view_file.'.php');

		include "@commons/footer.php";

		echo('</div>');
	?>
	<script type="text/javascript" src="<?=SERVER_URL?>assets/jquery/jquery-1.9.1.min.js"></script>
	<script type="text/javascript" src="<?=SERVER_URL?>assets/jquery/jquery-migrate-1.1.0.min.js"></script>
	<script type="text/javascript" src="<?=SERVER_URL?>assets/bootstrap/js/bootstrap.min.js"></script>
	<!--[if lt IE 9]>
		<script src="<?=SERVER_URL?>assets/crossbrowserjs/html5shiv.js"></script>
		<script src="<?=SERVER_URL?>assets/crossbrowserjs/respond.min.js"></script>
		<script src="<?=SERVER_URL?>assets/crossbrowserjs/excanvas.min.js"></script>
	<![endif]-->
	<script src="<?=SERVER_URL?>assets/jquery-cookie/jquery.cookie.js"></script>
	<script src="<?=SERVER_URL?>assets/color-admin/apps.min.js"></script>
	<!-- ================== END BASE JS ================== -->
	
	<script>
		var SITE_URL = '<?=SITE_URL?>';
	    $(document).ready(function() {
	        App.init();
	    });
	</script>
	<?php
		if(!empty($_SESSION['alias']->js_load)) {
			foreach ($_SESSION['alias']->js_load as $js) {
				echo '<script type="text/javascript" src="'.SITE_URL.$js.'"></script> ';
			}
		}
	?>
</body>
</html>
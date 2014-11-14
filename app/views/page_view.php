<!DOCTYPE html>
<html lang="uk">
<head>
  <title><?=$_SESSION['alias']->title?></title>
  <meta name="description" content="<?=$_SESSION['alias']->description?>" />
  <meta name="keywords" content="<?=$_SESSION['alias']->keywords?>" />
  <meta charset="utf-8">
  <meta name = "format-detection" content = "telephone=no" />
  <link rel="icon" href="<?=SITE_URL?>style/favicon.ico" type="image/x-icon">

  <link rel="stylesheet" href="<?=SITE_URL?>style/style.css">

  <!--[if lt IE 8]>
  <div style=' clear: both; text-align:center; position: relative;'>
   <a href="http://browsehappy.com/"><img src="images/old-browser.jpg" alt="Ви використовуєте застарілу версію Internet Explorer. Будь ласка, оновіть ваш Браузер, щоб поліпшити роботу в Інтернеті."/></a>
  </div>
  <![endif]-->
  <!--[if lt IE 9]>
  	<script src="js/html5shiv.js"></script>
  <link rel="stylesheet" type="text/css" media="screen" href="<?=SITE_URL?>style/ie.css">
  <![endif]-->
</head>

<body>

		<?php include "@commons/header.php";?>

		<!--========================================================
	                            CONTENT 
		=========================================================-->
		<section id="content">
      <?php if ((isset($errors) && $errors != '') || (isset($success) && $success != '')) require 'notify_view.php'; ?>
			<?php if(isset($view_file)) require_once($view_file.'.php');?> 
		</section>

    <?php include "@commons/footer.php";?>

</body>
</html>
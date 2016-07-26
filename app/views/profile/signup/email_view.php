<!DOCTYPE html>
<!--[if IE 8]> <html lang="en" class="ie8"> <![endif]-->
<!--[if !IE]><!-->
<html lang="en">
<!--<![endif]-->
<head>
	<meta charset="utf-8" />
	<title>Реєстрація | <?=SITE_NAME?></title>
	<meta content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" name="viewport" />
	<meta content="" name="description" />
	<meta content="" name="author" />
    <link rel="shortcut icon" href="<?=IMG_PATH?>ico.jpg">
	
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

    <script>
        // This is called with the results from from FB.getLoginStatus().
        function statusChangeCallback(response) {
            // The response object is returned with a status field that lets the
            // app know the current login status of the person.
            // Full docs on the response object can be found in the documentation
            // for FB.getLoginStatus().
            if (response.status === 'connected') {
              // Logged into your app and Facebook.
              window.location.replace('<?=SITE_URL?>signup/facebook');
            }
        }

        window.fbAsyncInit = function() {
            FB.init({
              appId      : '<?=$this->facebook->getAppId()?>',
              cookie     : true,
              xfbml      : true,
              version    : 'v2.6'
            });

            FB.Event.subscribe('auth.login', function(response) {
                statusChangeCallback(response);
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
</head>
<body class="pace-top bg-white">
	<!-- begin #page-loader -->
	<div id="page-loader" class="fade in"><span class="spinner"></span></div>
	<!-- end #page-loader -->
	
	<!-- begin #page-container -->
	<div id="page-container" class="fade">
	    <!-- begin register -->
        <div class="register register-with-news-feed">
            <!-- begin news-feed -->
            <div class="news-feed">
                <div class="news-image">
                    <img src="<?=SITE_URL?>style/admin/login-bg/bg-9.jpg" alt="" />
                </div>
                <div class="news-caption">
                    <h4 class="caption-title"><i class="fa fa-edit text-success"></i> Practical shooting - Практична стрільба</h4>
                    <p>
                        Ви не маєте ні найменшого уявлення що таке практична стрільба ? - в такому випадку, ви потрапили куди треба.
                    </p>
                </div>
            </div>
            <!-- end news-feed -->
            <!-- begin right-content -->
            <div class="right-content">
                <!-- begin register-header -->
                <h1 class="register-header">
                    Реєстрація
                    <small>Створіть Ваш особистий кабінет.</small>
                </h1>
                <!-- end register-header -->

                <!-- begin register-content -->
                <div class="register-content">

                    <?php if(!empty($_SESSION['notify']->errors)): ?>
                       <div class="alert alert-danger fade in">
                            <span class="close" data-dismiss="alert">×</span>
                            <h4><?=(isset($_SESSION['notify']->title)) ? $_SESSION['notify']->title : 'Помилка!'?></h4>
                            <p><?=$_SESSION['notify']->errors?></p>
                        </div>
                    <?php elseif(!empty($_SESSION['notify']->success)): ?>
                        <div class="alert alert-success fade in">
                            <span class="close" data-dismiss="alert">×</span>
                            <i class="fa fa-check fa-2x pull-left"></i>
                            <h4><?=(isset($_SESSION['notify']->title)) ? $_SESSION['notify']->title : 'Успіх!'?></h4>
                            <p><?=$_SESSION['notify']->success?></p>
                        </div>
                    <?php endif; unset($_SESSION['notify']); ?>
                
                    <form action="<?=SITE_URL?>signup/process" method="POST" class="margin-bottom-0">
                        <label class="control-label">Ім'я</label>
                        <div class="row row-space-10">
                            <div class="col-md-6 m-b-15">
                                <input name="first_name" type="text" value="<?=$this->data->re_post('first_name')?>" class="form-control" placeholder="Ім'я" required />
                            </div>
                            <div class="col-md-6 m-b-15">
                                <input name="last_name" type="text" value="<?=$this->data->re_post('last_name')?>" class="form-control" placeholder="Прізвище" required />
                            </div>
                        </div>
                        <label class="control-label">Email</label>
                        <div class="row m-b-15">
                            <div class="col-md-12">
                                <input name="email" type="email" value="<?=$this->data->re_post('email')?>" class="form-control" placeholder="Email address" required />
                            </div>
                        </div>
                        <label class="control-label">Контактний номер</label>
                        <div class="row m-b-15">
                            <div class="col-md-12">
                                <input name="phone" type="text" value="<?=$this->data->re_post('phone')?>" class="form-control" placeholder="+380*********" />
                            </div>
                        </div>
                        <label class="control-label">Пароль</label>
                        <div class="row m-b-15">
                            <div class="col-md-12">
                                <input name="password" type="password" value="<?=$this->data->re_post('password')?>" class="form-control" placeholder="Password" required />
                                <small>Ваш унікальний пароль, який використовується для входу на сайт. Може містити літери від а..я, a..z та числа. Довжина пороля від 5 до 20 символів.</small>
                            </div>
                        </div>
                        <label class="control-label">Повторити пароль</label>
                        <div class="row m-b-15">
                            <div class="col-md-12">
                                <input name="re-password" type="password" class="form-control" placeholder="Password" required />
                            </div>
                        </div>
                        <?php /*
                        <div class="checkbox m-b-30">
                            <label>
                                <input type="checkbox" /> By clicking Sign Up, you agree to our <a href="#">Terms</a> and that you have read our <a href="#">Data Policy</a>, including our <a href="#">Cookie Use</a>.
                            </label>
                        </div> */ ?>
                        <div class="register-buttons">
                            <button type="submit" class="btn btn-primary btn-block btn-lg">Зареєструватися</button>
                        </div>
                        <div class="m-t-20 text-center">
                            <big>АБО</big>
                            <div class="login-buttons m-t-10">
                                <button type="button" onClick="FB.login();" data-scope="public_profile,email" class="btn btn-success btn-block btn-lg"><i class="fa fa-facebook"></i> Швидка реєстрація facebook</button>
                            </div>
                        </div>
                        <div class="m-t-20 m-b-40 p-b-40">
                            Вже зареєстровані? <a href="<?=SITE_URL?>login">Увійти</a>. Перейти на <a href="<?=SITE_URL?>">головну сторінку</a>.
                        </div>
                        <hr />
                        <p class="text-center text-inverse">
                            &copy; White Lion CMS All Right Reserved 2015
                        </p>
                        <p class="text-center text-inverse">
                            &copy; Color Admin All Right Reserved 2015
                        </p>
                    </form>
                </div>
                <!-- end register-content -->
            </div>
            <!-- end right-content -->
        </div>
        <!-- end register -->
        
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
    <script src="<?=SITE_URL?>assets/color-admin/apps.min.js"></script>
	<!-- ================== END PAGE LEVEL JS ================== -->

	<script>
		$(document).ready(function() {
			App.init();
		});
	</script>
</body>
</html>
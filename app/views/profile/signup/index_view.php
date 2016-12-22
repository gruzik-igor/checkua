<!DOCTYPE html>
<!--[if IE 8]> <html lang="en" class="ie8"> <![endif]-->
<!--[if !IE]><!-->
<html lang="en">
<!--<![endif]-->
<head>
    <meta charset="utf-8" />
    <title>Реєстрація <?=SITE_NAME?></title>
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
</head>
<body class="pace-top">
    <!-- begin #page-loader -->
    <div id="page-loader" class="fade in"><span class="spinner"></span></div>
    <!-- end #page-loader -->
    
    <script>
        function signUp()
        {
            FB.getLoginStatus(function(response) {
                if (response.status === 'connected') {
                    window.location.replace('<?=SITE_URL?>signup/facebook');
                }
                else {
                    FB.login();
                }
            });
        };

        window.fbAsyncInit = function() {
            FB.init({
              appId      : '<?=$this->facebook->getAppId()?>',
              cookie     : true,
              xfbml      : true,
              version    : 'v2.6'
            });

            FB.Event.subscribe('auth.login', function(response) {
                if (response.status === 'connected') {
                  window.location.replace('<?=SITE_URL?>signup/facebook');
                }
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
                    <span class="logo"></span> <?=SITE_NAME?>
                    <small>Практична стрільба для кожного</small>
                </div>
                <div class="icon">
                    <i class="fa fa-sign-in"></i>
                </div>
            </div>
            <!-- end brand -->
            <div class="login-content">
                <h2 style="color:#fff">Профіль стрільця</h2>
                <div class="m-t-20 text-center">
                    <button type="button" onClick="signUp();" data-scope="public_profile,email" class="btn btn-success btn-block btn-lg m-b-20"><i class="fa fa-facebook"></i> Швидка реєстрація facebook</button>
                    <big>АБО</big>
                    <a href="<?=SITE_URL?>signup/email" class='btn btn-warning btn-block btn-lg m-t-20'><i class="fa fa-envelope"></i> Реєстрація через email</a>
                </div>
                <div class="m-t-20">
                    Вже зареєстровані? <a href="<?=SITE_URL?>reset">Увійти</a>. <br>
                    Повернутися на <a href="<?=SITE_URL?>">головну сторінку</a>.
                </div>
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
    <script src="<?=SITE_URL?>assets/color-admin/login-v2.min.js"></script>
    <script src="<?=SITE_URL?>assets/color-admin/apps.min.js"></script>
    <!-- ================== END PAGE LEVEL JS ================== -->

    <script>
        $(document).ready(function() {
            App.init();
            LoginV2.init();
        });
    </script>
</body>
</html>
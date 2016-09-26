<!DOCTYPE html>
<!--[if IE 8]> <html lang="en" class="ie8"> <![endif]-->
<!--[if !IE]><!-->
<html lang="en">
<!--<![endif]-->
<head>
    <meta charset="utf-8" />
    <title>Увійти в систему <?=SITE_NAME?></title>
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

    <?php if($_SESSION['option']->facebook_initialise) { ?>
        <script>
            // This is called with the results from from FB.getLoginStatus().
            function statusChangeCallback(response) {
                // The response object is returned with a status field that lets the
                // app know the current login status of the person.
                // Full docs on the response object can be found in the documentation
                // for FB.getLoginStatus().
                if (response.status === 'connected') {
                  // Logged into your app and Facebook.
                  window.location.replace('<?=SITE_URL?>login/facebook');
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

                <?php if(!isset($_SESSION['facebook'])) { ?>
                FB.getLoginStatus(function(response) {
                    statusChangeCallback(response);
                });
                <?php } ?>
            };

            (function(d, s, id){
                var js, fjs = d.getElementsByTagName(s)[0];
                if (d.getElementById(id)) {return;}
                js = d.createElement(s); js.id = id;
                js.src = "//connect.facebook.net/en_US/sdk.js";
                fjs.parentNode.insertBefore(js, fjs);
            }(document, 'script', 'facebook-jssdk'));
        </script>
    <?php } ?>
    
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
                    <small><?=SITE_URL?></small>
                </div>
                <div class="icon">
                    <i class="fa fa-sign-in"></i>
                </div>
            </div>
            <!-- end brand -->
            <div class="login-content">
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

                <form action="<?=SITE_URL?>login/process" method="POST" class="margin-bottom-0">
                    <div class="form-group m-b-20">
                        <input type="email" name="email" value="<?=$this->data->re_post('email')?>" class="form-control input-lg" placeholder="Ваша Email адреса" required />
                    </div>
                    <div class="form-group m-b-20">
                        <input type="password" name="password" class="form-control input-lg" placeholder="Пароль" required />
                    </div>
                    <div class="login-buttons">
                        <button type="submit" class="btn btn-success btn-block btn-lg">Увійти</button>
                    </div>
                    <?php if($_SESSION['option']->facebook_initialise) { ?>
                        <div class="m-t-20 text-center">
                            <big>АБО</big>
                            <div class="login-buttons m-t-10">
                                <?php if(!isset($_SESSION['facebook'])) { ?>
                                    <button type="button" onClick="FB.login();" data-scope="public_profile,email" class="btn btn-success btn-block btn-lg"><i class="fa fa-facebook"></i> Увійти через facebook</button>
                                <?php } elseif($_SESSION['facebook'] != false) { ?>
                                    <p>Користувач за email <b><?=$this->data->re_post('email')?></b> вже зареєстрований на сайті</p>
                                    <button type="submit" name="facebook" value="<?=$_SESSION['facebook']?>" class="btn btn-warning btn-block btn-lg"><i class="fa fa-facebook"></i> Синхронізувати профілі</button>
                                <?php } else { ?>
                                    <a href="<?=SITE_URL?>signup/facebook" class='btn btn-warning btn-block btn-lg'><i class="fa fa-facebook"></i> Швидка реєстрація facebook</a>
                                <?php } unset($_SESSION['facebook']); ?>
                            </div>
                        </div>
                    <?php } ?>
                    <div class="m-t-20">
                        <?php if($_SESSION['option']->userSignUp) { ?>
                            Ще не зареєстровані? <a href="<?=SITE_URL?>signup">Зареєструватися</a>. <br>
                        <?php } ?>
                        Не можете ввійти? <a href="<?=SITE_URL?>reset">Забув пароль</a>. <br>
                        Повернутися на <a href="<?=SITE_URL?>">головну сторінку</a>.
                    </div>
                </form>
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
<!DOCTYPE html>
<!--[if IE 8]> <html lang="uk" class="ie8"> <![endif]-->
<!--[if !IE]><!-->
<html lang="uk">
<!--<![endif]-->
<head>
    <meta charset="utf-8" />
    <meta name="title" content="<?=$_SESSION['alias']->title?>">
    <meta name="description" content="<?=$_SESSION['alias']->description?>">
    <meta name="keywords" content="<?=$_SESSION['alias']->keywords?>">
    <meta name="author" content="webspirit.com.ua">
    <link rel="shortcut icon" href="<?=SERVER_URL?>favicon.ico">

    <!-- ================== BEGIN BASE CSS STYLE ================== -->
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700" rel="stylesheet">
    <link href="<?=SERVER_URL?>assets/jquery-ui/themes/base/minified/jquery-ui.min.css" rel="stylesheet" />
    <link href="<?=SERVER_URL?>assets/bootstrap/css/bootstrap.min.css" rel="stylesheet" />
    <link href="<?=SERVER_URL?>assets/font-awesome/css/font-awesome.min.css" rel="stylesheet" />
    <link href="<?=SERVER_URL?>style/admin/animate.min.css" rel="stylesheet" />
    <link href="<?=SERVER_URL?>style/admin/style.min.css" rel="stylesheet" />
    <link href="<?=SERVER_URL?>style/admin/style-responsive.min.css" rel="stylesheet" />
    <link href="<?=SERVER_URL?>style/admin/theme/default.css" rel="stylesheet" id="theme" />
    <!-- ================== END BASE CSS STYLE ================== -->

    <?php if($this->googlesignin->clientId)
        echo '<meta name="google-signin-client_id" content="'.$this->googlesignin->clientId.'">';
    ?>

    <!-- ================== BEGIN BASE JS ================== -->
    <script src="<?=SERVER_URL?>assets/pace/pace.min.js"></script>
    <!-- ================== END BASE JS ================== -->
</head>
<body class="pace-top">
    <!-- begin #page-loader -->
    <div id="page-loader" class="fade in"><span class="spinner"></span></div>
    <!-- end #page-loader -->

    <?php if($_SESSION['option']->facebook_initialise) { ?>
        <script>
            window.fbAsyncInit = function() {
                <?php $this->load->library('facebook'); ?>
                FB.init({
                  appId      : '<?=$this->facebook->getAppId()?>',
                  cookie     : true,
                  xfbml      : true,
                  version    : 'v3.1'
                });
            };

            (function(d, s, id){
                var js, fjs = d.getElementsByTagName(s)[0];
                if (d.getElementById(id)) {return;}
                js = d.createElement(s); js.id = id;
                js.src = "https://connect.facebook.net/en_US/sdk.js";
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

                <div class="col-md-12 text-center" id="clientError"></div>
                <form action="<?=SITE_URL?>login/process" method="POST" class="margin-bottom-0">
                    <?php if(isset($_GET['redirect']) || $this->data->re_post('redirect')) { ?>
                        <input type="hidden" name="redirect" value="<?=$this->data->re_post('redirect', $this->data->get('redirect'))?>">
                    <?php } ?>
                    <div class="form-group m-b-20">
                        <input type="email" name="email" value="<?=$this->data->re_post('email')?>" class="form-control input-lg" placeholder="Email" required />
                    </div>
                    <div class="form-group m-b-20">
                        <input type="password" name="password" class="form-control input-lg" placeholder="Пароль" required />
                    </div>
                    <div class="login-buttons">
                        <button type="submit" class="btn btn-success btn-block btn-lg"><?=$this->text('Увійти')?></button>
                    </div>
                    <?php if($_SESSION['option']->userSignUp && ($_SESSION['option']->facebook_initialise || $this->googlesignin->clientId)) { ?>
                        <div class="m-t-20 text-center">
                            <big>АБО</big>
                            <div class="login-buttons m-t-10">
                                <?php if($_SESSION['option']->facebook_initialise) { ?>
                                    <button type="button" onclick="facebookSignUp()" class="btn btn-success btn-block btn-lg"><i class="fa fa-facebook"></i> <?=$this->text('Увійти через ')?>facebook</button>
                                <?php } if($this->googlesignin->clientId) { ?>
                                    <div class="g-signin2 m-t-20" data-width="match_parent" data-longtitle="true" data-onsuccess="onSignIn"></div>
                                <?php } ?>
                            </div>
                        </div>
                    <?php } ?>
                    <div class="m-t-20">
                        <?php if($_SESSION['option']->userSignUp) { ?>
                            <?=$this->text('Ще не зареєстровані')?>? <a href="<?=SITE_URL?>signup"><?=$this->text('Зареєструватися')?></a>. <br>
                        <?php } ?>
                        <?=$this->text('Не можете ввійти')?>? <a href="<?=SITE_URL?>reset"><?=$this->text('Забув пароль')?></a>. <br>
                        <?=$this->text('Повернутися на')?> <a href="<?=SITE_URL?>"><?=$this->text('головну сторінку')?></a>.
                    </div>
                </form>
            </div>
            <div id="divLoading"></div>
        </div>
        <!-- end login -->

        <ul class="login-bg-list">
            <li class="active"><a href="#" data-click="change-bg"><img src="<?=SITE_URL?>style/admin/login-bg/bg-1.jpg" alt="" /></a></li>
            <li><a href="#" data-click="change-bg"><img src="<?=SITE_URL?>style/admin/login-bg/bg-2.jpg" alt="" /></a></li>
            <li><a href="#" data-click="change-bg"><img src="<?=SITE_URL?>style/admin/login-bg/bg-3.jpg" alt="" /></a></li>
            <li><a href="#" data-click="change-bg"><img src="<?=SITE_URL?>style/admin/login-bg/bg-4.jpg" alt="" /></a></li>

            <?php require_once SYS_PATH.'libraries'.DIRSEP.'is_mobile.php'; 
            if(is_mobile() == false)
                echo '<li><a href="#" data-click="change-bg"><img src="'.SITE_URL.'style/admin/login-bg/bg-6.jpg" alt="" /></a></li>';
            ?>
        </ul>
    </div>
    <!-- end page container -->

    <!-- ================== BEGIN BASE JS ================== -->
    <script src="<?=SERVER_URL?>assets/jquery/jquery-1.9.1.min.js"></script>
    <script src="<?=SERVER_URL?>assets/jquery/jquery-migrate-1.1.0.min.js"></script>
    <script src="<?=SERVER_URL?>assets/jquery-ui/ui/minified/jquery-ui.min.js"></script>
    <script src="<?=SERVER_URL?>assets/bootstrap/js/bootstrap.min.js"></script>
    <!--[if lt IE 9]>
        <script src="<?=SERVER_URL?>assets/crossbrowserjs/html5shiv.js"></script>
        <script src="<?=SERVER_URL?>assets/crossbrowserjs/respond.min.js"></script>
        <script src="<?=SERVER_URL?>assets/crossbrowserjs/excanvas.min.js"></script>
    <![endif]-->
    <script src="<?=SERVER_URL?>assets/slimscroll/jquery.slimscroll.min.js"></script>
    <script src="<?=SERVER_URL?>assets/jquery-cookie/jquery.cookie.js"></script>
    <!-- ================== END BASE JS ================== -->

    <!-- ================== BEGIN PAGE LEVEL JS ================== -->
    <script src="<?=SERVER_URL?>assets/color-admin/login-v2.min.js"></script>
    <script src="<?=SERVER_URL?>assets/color-admin/apps.min.js"></script>
    <!-- ================== END PAGE LEVEL JS ================== -->

    <script>
        var SITE_URL = '<?=SITE_URL?>';
        var SERVER_URL = '<?=SERVER_URL?>';
        <?php if(!empty($_GET['redirect']) || $this->data->re_post('redirect')) {
            echo 'var redirect = "'.$this->data->re_post('redirect', $this->data->get('redirect')).'";';
        } else echo "var redirect = false;"; ?>
        
        $(document).ready(function() {
            App.init();
            LoginV2.init();
        });
    </script>
    <?php if($_SESSION['option']->userSignUp && ($_SESSION['option']->facebook_initialise || $this->googlesignin->clientId)) {
        if($this->googlesignin->clientId)
            echo '<script src="https://apis.google.com/js/platform.js" async defer></script>';
        echo '<script src="'.SERVER_URL.'assets/white-lion/login.js" async defer></script>';
    } ?>
</body>
</html>
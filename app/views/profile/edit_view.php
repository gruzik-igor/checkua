<link href="<?=SITE_URL?>style/css/profile.css" rel="stylesheet" />
<link href="<?=SITE_URL?>style/css/app.css"" rel="stylesheet" />
<link href="<?=SITE_URL?>style/css/sky-form.css" rel="stylesheet" />
<link href="<?=SITE_URL?>style/css/custom-sky-form.css" rel="stylesheet" />
<link href="<?=SITE_URL?>style/css/blocks.css" rel="stylesheet" />

<div class="wrapper">
    <div class="container">
        <div class="row">
            <div class="content profile">

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

                <!--Left Sidebar-->
                <div class="col-md-3 md-margin-bottom-40">
                    <div id="fileupload" class="fileupload-buttonbar">
                        <div style="text-align: center;">
                            <div>
                                <img class="img-responsive profile-img margin-bottom-20" id="photo" src="<?= ($user->photo > 0)? IMG_PATH.'profile/'.$user->id.'.jpg' : IMG_PATH.'empty-avatar.jpg'  ?>" alt="Фото" title="Фото" >
                            </div>
                            <div class="fileUpload ">
                                <span style="font-weight:bold"> Додати фото</span>
                                <input onchange="show_image(this)" type="file" name="photos" class="upload">
                            </div>
                            <img id="loading" style="" src="<?=IMG_PATH?>ajax-loader.gif" hidden>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-9">
                    <div class="profile-body margin-bottom-20">
                        <div class="tab-v1">
                            <ul class="nav nav-justified nav-tabs">
                                <li class="active"><a data-toggle="tab" href="#main">Загальні дані</a></li>
                                <li><a data-toggle="tab" href="#security">Зміна паролю</a></li>
                                <li><a data-toggle="tab" href="#register">Реєстр дій</a></li>
                            </ul>
                            <div class="tab-content">
                                <div id="main" class="profile-edit tab-pane fade in active">
                                     <?php require_once 'tabs/_tabs-main.php'; ?>
                                </div>

                                <div id="security" class="profile-edit tab-pane fade">
                                    <?php require_once 'tabs/_tabs-security.php'; ?>
                                </div>

                                <div id="register" class="profile-edit tab-pane fade">
                                    <h2 class="heading-md">Реєстр дій користувачем</h2>
                                    <br>
                                    <dl class="dl-horizontal">
                                    <?php if($registerDo) foreach ($registerDo as $register) { ?>
                                        <dt><strong><?= date("d.m.Y H:i", $register->date)?></strong></dt>
                                        <dd><?= $register->title_public?></dd>
                                        <hr>
                                    <?php } ?>
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
    $_SESSION['alias']->js_load[] = "assets/blueimp/js/vendor/jquery.ui.widget.js";
    $_SESSION['alias']->js_load[] = "assets/blueimp/js/load-image.all.min.js";
    $_SESSION['alias']->js_load[] = "assets/blueimp/js/jquery.fileupload.js";
    $_SESSION['alias']->js_load[] = "js/user.js";
 ?>
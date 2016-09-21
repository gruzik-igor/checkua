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
                                <li class="active"><a data-toggle="tab" href="#profile">Загальні дані</a></li>
                                <li><a data-toggle="tab" href="#security">Зміна паролю</a></li>
                                <li><a data-toggle="tab" href="#register">Реєстр дій</a></li>
                            </ul>
                            <div class="tab-content">
                                <div id="profile" class="profile-edit tab-pane fade in active">
                                    <h2 class="heading-md">Загальні дані мого аккаунту</h2>
                                    <br>
                                    <form action="<?= SITE_URL?>profile/saveUserInfo" method="POST" class="sky-form">
                                        <dl class="dl-horizontal">
                                            <dt>
                                                <strong>Моє ім'я</strong>
                                            </dt>
                                            <dd>
                                                <span id="name"><?=$_SESSION['user']->name?></span>
                                                <span>
                                                    <a class="pull-right" href="#" onclick="changeInfo('name')">
                                                        <i class="fa fa-pencil"></i>
                                                    </a>
                                                </span>
                                            </dd>
                                            <hr>

                                            <dt>
                                                <strong>Мій email </strong>
                                            </dt>
                                            <dd>
                                                <?=$_SESSION['user']->email?>
                                            </dd>
                                            <hr>

                                            <dt>
                                                <strong>Тип</strong>
                                            </dt>
                                            <dd>
                                                <?php if ($_SESSION['user']->admin == 1){ ?>
                                                    Адміністратор
                                                <?php }else{ ?>
                                                    Користувач
                                                <?php } ?>
                                            </dd>
                                            <hr>

                                            <dt>
                                                <strong>Останній вхід </strong>
                                            </dt>
                                            <dd>
                                                <?= date("d.m.Y H:i", $user->last_login); ?>
                                            </dd>
                                            <hr>

                                            <dt>
                                                <strong>Дата реєстрації</strong>
                                            </dt>
                                            <dd>
                                                <?= date("d.m.Y H:i", $user->registered); ?>
                                            </dd>
                                            <hr>
                                            <button class="btn-u hidden" type="submit" id="saveInfo" >Зберегти зміни</button>
                                        </dl>
                                    </form>
                                </div>

                                <div id="security" class="profile-edit tab-pane fade">
                                    <h2 class="heading-md">Управління налаштуваннями безпеки</h2>
                                    <br>
                                    <form class="sky-form" method="POST" action="<?=SITE_URL?>profile/save_security" novalidate="novalidate">
                                        <dl class="dl-horizontal">
                                            <dt>Введіть старий пароль</dt>
                                            <dd>
                                                <section>
                                                    <label class="input">
                                                        <i class="icon-append fa fa-lock"></i>
                                                        <input type="password" id="old_password" name="old_password" placeholder="Пароль">
                                                        <b class="tooltip tooltip-bottom-right">Введіть свій старий пароль</b>
                                                    </label>
                                                </section>
                                            </dd>
                                            <dt>Введіть новий пароль</dt>
                                            <dd>
                                                <section>
                                                    <label class="input">
                                                        <i class="icon-append fa fa-lock"></i>
                                                        <input type="password" id="new_password" name="new_password" placeholder="Новий пароль">
                                                        <b class="tooltip tooltip-bottom-right">Запам'ятайте свій пароль</b>
                                                    </label>
                                                </section>
                                            </dd>
                                            <dt>Повторіть пароль</dt>
                                            <dd>
                                                <section>
                                                    <label class="input">
                                                        <i class="icon-append fa fa-lock"></i>
                                                        <input type="password" id="new_password_re" name="new_password_re" placeholder="Введіть новий пароль ще раз">
                                                        <b class="tooltip tooltip-bottom-right">Запам'ятайте свій пароль</b>
                                                    </label>
                                                </section>
                                            </dd>
                                        </dl>
                                        <hr>
                                        <button class="btn-u" type="submit">Зберегти зміни</button>
                                    </form>
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
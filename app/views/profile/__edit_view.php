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
                
<div class="profile-body margin-bottom-20">
    <div class="tab-v1">
        <ul class="nav nav-justified nav-tabs">
            <li class="active"><a data-toggle="tab" href="#main"><?=$this->text('Загальні дані')?></a></li>
            <?php if(!empty($user->password)) {?>
            <li><a data-toggle="tab" href="#security"><?=$this->text('Зміна паролю')?></a></li>
            <?php } ?>
            <li><a data-toggle="tab" href="#register"><?=$this->text('Реєстр дій')?></a></li>
        </ul>
        <div class="tab-content">
            <div id="main" class="profile-edit tab-pane fade in active">
                 <?php require_once 'tabs/_tabs-main.php'; ?>
            </div>
            
            <?php if(!empty($user->password)) {?>
            <div id="security" class="profile-edit tab-pane fade">
                <?php require_once 'tabs/_tabs-security.php'; ?>
            </div>
            <?php } ?>

            <div id="register" class="profile-edit tab-pane fade">
                <h2 class="heading-md"><?=$this->text('Реєстр дій користувачем')?></h2>
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

<?php
    $_SESSION['alias']->js_load[] = "assets/blueimp/js/vendor/jquery.ui.widget.js";
    $_SESSION['alias']->js_load[] = "assets/blueimp/js/load-image.all.min.js";
    $_SESSION['alias']->js_load[] = "assets/blueimp/js/jquery.fileupload.js";
    $_SESSION['alias']->js_load[] = "js/user.js";
 ?>
<h2 class="heading-md"><?=$this->text('Загальні дані мого аккаунту')?></h2>
<br>
<div class="row">
    <form action="<?= SITE_URL?>profile/saveUserInfo" method="POST" class="sky-form col-md-8">
        <dl class="dl-horizontal">
            <dt>
                <strong><?=$this->text('Моє ім\'я')?></strong>
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
                <strong><?=$this->text('Мій')?> email </strong>
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
                    <?=$this->text('Користувач')?>
                <?php } ?>
            </dd>
            <hr>

            <dt>
                <strong><?=$this->text('Останній вхід')?> </strong>
            </dt>
            <dd>
                <?= date("d.m.Y H:i", $user->last_login); ?>
            </dd>
            <hr>

            <dt>
                <strong><?=$this->text('Дата реєстрації')?></strong>
            </dt>
            <dd>
                <?= date("d.m.Y H:i", $user->registered); ?>
            </dd>
            <hr>
            <?php $info = array('phone' => 'Номер телефону', 'address' => 'Адреса');
                foreach($info as $key => $title) { if(isset($user->info[$key])) {?>
                <dt>
                    <?= $title ?>
                </dt>
                <dd>
                    <span id="<?= $key ?>"><?= $user->info[$key] ?></span>
                    <span>
                        <a class="pull-right" href="#" onclick="changeInfo('<?= $key ?>')">
                            <i class="fa fa-pencil"></i>
                        </a>
                    </span>
                </dd>
                <hr>
            <?php } }?>
            <button class="btn-u hidden" type="submit" id="saveInfo"><?=$this->text('Зберегти зміни')?></button>
        </dl>
    </form>
    <div class="col-md-4 md-margin-bottom-40">
        <div id="fileupload" class="fileupload-buttonbar">
            <div style="text-align: center;">
                <div>
                    <img class="img-responsive profile-img margin-bottom-20" id="photo" src="<?= ($user->photo > 0)? IMG_PATH.'profile/'.$user->id.'.jpg' : IMG_PATH.'empty-avatar.jpg'  ?>" alt="Фото" title="Фото" >
                </div>
                <div class="fileUpload ">
                    <span style="font-weight:bold"> <?=$this->text('Додати фото')?></span>
                    <input onchange="show_image(this)" type="file" name="photos" class="upload">
                </div>
                <img id="loading" class="hidden" src="<?=IMG_PATH?>ajax-loader.gif" >
            </div>
        </div>
    </div>
</div>
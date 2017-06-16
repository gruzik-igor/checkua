<h2 class="heading-md"><?=$this->text('Управління налаштуваннями безпеки')?></h2>
<br>
<form class="sky-form" method="POST" action="<?=SITE_URL?>profile/save_security" novalidate="novalidate">
    <dl class="dl-horizontal">
        <dt><?=$this->text('Введіть старий пароль')?></dt>
        <dd>
            <section>
                <label class="input">
                    <i class="icon-append fa fa-lock"></i>
                    <input type="password" id="old_password" name="old_password" placeholder="Пароль">
                    <b class="tooltip tooltip-bottom-right"><?=$this->text('Введіть свій старий пароль')?></b>
                </label>
            </section>
        </dd>
        <dt><?=$this->text('Введіть новий пароль')?></dt>
        <dd>
            <section>
                <label class="input">
                    <i class="icon-append fa fa-lock"></i>
                    <input type="password" id="new_password" name="new_password" placeholder="<?=$this->text('Новий пароль')?>">
                    <b class="tooltip tooltip-bottom-right"><?=$this->text('Запам\'ятайте свій пароль')?></b>
                </label>
            </section>
        </dd>
        <dt><?=$this->text('Повторіть пароль')?></dt>
        <dd>
            <section>
                <label class="input">
                    <i class="icon-append fa fa-lock"></i>
                    <input type="password" id="new_password_re" name="new_password_re" placeholder="<?=$this->text('Введіть новий пароль ще раз')?>">
                    <b class="tooltip tooltip-bottom-right"><?=$this->text('Запам\'ятайте свій пароль')?></b>
                </label>
            </section>
        </dd>
    </dl>
    <hr>
    <button class="btn-u" type="submit"><?=$this->text('Зберегти зміни')?></button>
</form>
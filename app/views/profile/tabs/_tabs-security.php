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
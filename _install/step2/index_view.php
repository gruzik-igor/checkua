<h4 style="color:#fff">2. Налаштування безпеки reCAPTCHA</h4>
<form action="<?=SITE_URL?>step2" method="POST" class="margin-bottom-0">
	<div class="form-group">
        <input type="text" name="public" value="" class="form-control input-lg" placeholder="Відкритий ключ (видимий у html)" required />
    </div>
	<div class="form-group">
        <input type="text" name="secret" value="" class="form-control input-lg" placeholder="Закритий ключ (таємний, для перевірки на сервері)" required />
    </div>
    Для реєстрації використовуйте <a href="https://www.google.com/recaptcha" target="_blank">reCAPTCHA від google</a>
	<button type="submit" class="btn btn-success btn-block btn-lg m-t-20 m-b-20">Далі</button>
	<a href="<?=SITE_URL?>step3" class="btn btn-warning btn-block btn-lg">Пропустити</a>
</form>
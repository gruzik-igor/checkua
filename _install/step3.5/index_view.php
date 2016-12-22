<h4 style="color:#fff">3.5 Налаштування безпеки facebook</h4>
<form action="<?=SITE_URL?>step3.5" method="POST" class="margin-bottom-0">
	<div class="form-group">
        <input type="text" name="appId" value="" class="form-control input-lg" placeholder="appId (видимий у html)" required />
    </div>
	<div class="form-group">
        <input type="text" name="secret" value="" class="form-control input-lg" placeholder="Закритий ключ (таємний, для перевірки на сервері)" required />
    </div>
    Для реєстрації використовуйте <a href="https://developers.facebook.com/apps/" target="_blank">facebook applications</a>
	<button type="submit" class="btn btn-success btn-block btn-lg m-t-20 m-b-20">Далі</button>
	<a href="<?=SITE_URL?>step4" class="btn btn-warning btn-block btn-lg">Пропустити</a>
</form>
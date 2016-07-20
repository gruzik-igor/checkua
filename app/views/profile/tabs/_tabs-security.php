<?php if(@$_SESSION['notify']->show == true) { if(!empty($_SESSION['notify']->type == 'error')) : ?>
    <div class="col-lg-12 alert alert-danger fade in m-b-15">
        <strong>Помилка!</strong>
        <span class="close" data-dismiss="alert">&times;</span>
        <?= $_SESSION['notify']->text?>
    </div>
<?php endif; ?>
<?php if(!empty($_SESSION['notify']->type == 'success')) :?>
    <div class="col-lg-12 alert alert-success fade in m-b-15">
        <strong>Інформація!</strong>
        <span class="close" data-dismiss="alert">&times;</span>
        <?= $_SESSION['notify']->text?>
    </div>
<?php endif; } ?>
<form id="securityForm" action="<?=SITE_URL?>profile/save_security" method="POST">
    <div  class="col-lg-6">
        <div class="form-group">
            <label class="portfolioLabel">Поточний пароль</label>
            <input type="password" class="form-control" id="old_password" name="old_password" placeholder="Поточний пароль" required >
            <small>Ваш поточний пароль. Для підтвердження та безпеки профілю.</small>
        </div>
        <div class="form-group">
            <label class="portfolioLabel">Новий пароль</label>
            <input type="password" class="form-control" id="new_password" name="new_password" placeholder="Новий пароль" required >
            <small>Ваш унікальний пароль, який використовується для входу на сайт. Може містити літери від а..я, a..z та числа. Довжина пороля від 5 до 20 символів.</small>
        </div>
        <div class="form-group">
            <label class="portfolioLabel">Підтвердіть пароль</label>
            <input type="password" class="form-control" id="new_password_re" name="new_password_re" placeholder="Повторіть новий пароль" required >
        </div>
        <div class="form-group text-center">
            <input type="submit" class="btn btn-success" value="Зберегти">
        </div>
    </div>
</form>


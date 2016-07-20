<link href="<?=SITE_URL?>style/kabinet.css" rel="stylesheet" />

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
                    <img class="img-responsive profile-img margin-bottom-20" src="<?=IMG_PATH?>Fake.png" alt="">
                </div>
                
                <div class="col-md-9">
                    <div class="profile-body margin-bottom-20">
                        <div class="tab-v1">
                            <ul class="nav nav-justified nav-tabs">
                                <li class="active"><a data-toggle="tab" href="#profile">Загальні дані</a></li>
                                <li><a data-toggle="tab" href="#passwordTab">Зміна паролю</a></li>
                                <li><a data-toggle="tab" href="#payment">Способи оплати</a></li>
                                <li><a data-toggle="tab" href="#settings">Notification Settings</a></li>
                            </ul>
                            <div class="tab-content">
                                <div id="profile" class="profile-edit tab-pane fade in active">
                                    <h2 class="heading-md">Загальні дані мого аккаунту</h2>
                                    <!-- <p>Below are the name and email addresses on file for your account.</p> -->
                                    <br>
                                    <dl class="dl-horizontal">
                                        <dt><strong>Моє ім'я</strong></dt>
                                        <dd>
                                            <?=$_SESSION['user']->name?>
                                        <hr>
                                        <dt><strong>Мій email </strong></dt>
                                        <dd>
                                            <?=$_SESSION['user']->email?>
                                            <span>
                                                <a class="pull-right " data-toggle="tab" href="#passwordTab">
                                                    <i class="fa fa-pencil"></i>
                                                </a>
                                            </span>
                                        </dd>
                                        <hr>
                                        <dt><strong>Тип</strong></dt>
                                        <dd>
                                            <?php if ($_SESSION['user']->admin == 1){ ?>
                                                Адміністратор
                                            <?php }else{ ?>
                                                Користувач
                                            <?php } ?>
                                        </dd>
                                        <hr>
                                        <dt><strong>Статус </strong></dt>
                                        <dd>
                                        </dd>
                                        <hr>

                                    </dl>

                                </div>

                                <div id="passwordTab" class="profile-edit tab-pane fade">
                                    <h2 class="heading-md">Управління налаштуваннями безпеки</h2>
                                    <p>Змінити пароль</p>
                                    <br>
                                    <form class="sky-form" id="sky-form4" action="<?=SITE_URL?>kabinet/password" novalidate="novalidate">
                                        <dl class="dl-horizontal">
                                            <dt>Введіть старий пароль</dt>
                                            <dd>
                                                <section>
                                                    <label class="input">
                                                        <i class="icon-append fa fa-lock"></i>
                                                        <input type="password" id="password" name="password" placeholder="Пароль">
                                                        <b class="tooltip tooltip-bottom-right">Введіть свій старий пароль</b>
                                                    </label>
                                                </section>
                                            </dd>
                                            <dt>Введіть новий пароль</dt>
                                            <dd>
                                                <section>
                                                    <label class="input">
                                                        <i class="icon-append fa fa-lock"></i>
                                                        <input type="password" id="newpassword" name="newpassword" placeholder="Новий пароль">
                                                        <b class="tooltip tooltip-bottom-right">Запам'ятайте свій пароль</b>
                                                    </label>
                                                </section>
                                            </dd>
                                            <dt>Повторіть пароль</dt>
                                            <dd>
                                                <section>
                                                    <label class="input">
                                                        <i class="icon-append fa fa-lock"></i>
                                                        <input type="password" id="newpassword1" name="newpassword1" placeholder="Введіть новий пароль ще раз">
                                                        <b class="tooltip tooltip-bottom-right">Запам'ятайте свій пароль</b>
                                                    </label>
                                                </section>
                                            </dd>
                                        </dl>
                                        <hr>




                                        <label class="toggle toggle-change"><input type="checkbox" checked="" name="checkbox-toggle-1" style="margin-right: 100px"><i class="no-rounded"></i><span>Запам'ятати пароль</span></label>
                                        <hr>
                                        <br>
                                        <button type="button" class="btn-u btn-u-default">Відмінити</button>
                                        <button class="btn-u" type="submit">Зберегти зміни</button>
                                    </form>
                                </div>

                                <div id="payment" class="profile-edit tab-pane fade">
                                    <h2 class="heading-md">Оберіть спосіб оплати</h2>
                                    <!-- <p>Below are the payment options for your account.</p> -->
                                    <br>
                                    <form class="sky-form" id="sky-form" action="#" novalidate="novalidate">
                                        <!--Checkout-Form-->
                                        <section>
                                            <div class="inline-group">
                                                <label class="radio" style="margin-top: 0px"><input type="radio" checked="" name="radio-inline"><i class="rounded-x"></i>Visa</label>
                                                <label class="radio"><input type="radio" name="radio-inline"><i class="rounded-x"></i>MasterCard</label>
                                                <label class="radio"><input type="radio" name="radio-inline"><i class="rounded-x"></i>PayPal</label>
                                            </div>
                                        </section>

                                        <section>
                                            <label class="input">
                                                <input type="text" name="name" placeholder="Назва карти">
                                            </label>
                                        </section>

                                        <div class="row">
                                            <section class="col col-10">
                                                <label class="input">
                                                    <input type="text" name="card" id="card" placeholder="Номер карти">
                                                </label>
                                            </section>
                                            <section class="col col-2">
                                                <label class="input">
                                                    <input type="text" name="cvv" id="cvv" placeholder="CVV2">
                                                </label>
                                            </section>
                                        </div>

                                        <div class="row">
                                            <label class="label col col-4">Термін придатності</label>
                                            <section class="col col-5">
                                                <label class="select">
                                                    <select name="month">
                                                        <option disabled="" selected="" value="0">Місяць</option>
                                                        <option value="1">Січень</option>
                                                        <option value="2">Лютий</option>
                                                        <option value="3">Березень</option>
                                                        <option value="4">Квітень</option>
                                                        <option value="5">Травень</option>
                                                        <option value="6">Червень</option>
                                                        <option value="7">Липень</option>
                                                        <option value="8">Серпень</option>
                                                        <option value="9">Вересень</option>
                                                        <option value="10">Жовтень</option>
                                                        <option value="11">Листопад</option>
                                                        <option value="12">Грудень</option>
                                                    </select>
                                                    <i></i>
                                                </label>
                                            </section>
                                            <section class="col col-3">
                                                <label class="input">
                                                    <input type="text" placeholder="Рік" id="year" name="year">
                                                </label>
                                            </section>
                                        </div>
                                        <button type="button" class="btn-u btn-u-default">Cancel</button>
                                        <button class="btn-u" type="submit">Save Changes</button>
                                        <!--End Checkout-Form-->
                                    </form>
                                </div>

                                <div id="settings" class="profile-edit tab-pane fade">
                                    <h2 class="heading-md">Manage your Notifications.</h2>
                                    <p>Below are the notifications you may manage.</p>
                                    <br>
                                    <form class="sky-form" id="sky-form3" action="#">
                                        <label class="toggle"><input type="checkbox" checked="" name="checkbox-toggle-1"><i class="no-rounded"></i>Email notification</label>
                                        <hr>
                                        <label class="toggle"><input type="checkbox" checked="" name="checkbox-toggle-1"><i class="no-rounded"></i>Send me email notification when a user comments on my blog</label>
                                        <hr>
                                        <label class="toggle"><input type="checkbox" checked="" name="checkbox-toggle-1"><i class="no-rounded"></i>Send me email notification for the latest update</label>
                                        <hr>
                                        <label class="toggle"><input type="checkbox" checked="" name="checkbox-toggle-1"><i class="no-rounded"></i>Send me email notification when a user sends me message</label>
                                        <hr>
                                        <label class="toggle"><input type="checkbox" checked="" name="checkbox-toggle-1"><i class="no-rounded"></i>Receive our monthly newsletter</label>
                                        <hr>
                                        <button type="button" class="btn-u btn-u-default">Reset</button>
                                        <button class="btn-u" type="submit">Save Changes</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
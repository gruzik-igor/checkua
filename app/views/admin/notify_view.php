<div style="padding:15px">
    <?php if(!empty($guest) && $guest == true) : ?>
        <div class="notify-error">Ви не авторизовані. Будь ласка <a href="<?=SITE_URL?>login">увійдіть</a> або <a href="<?=SITE_URL?>signup">зареєструйтесь</a></div>
    <?php elseif(!empty($errors)): ?>
        <div class="notify-error"><?=$errors?></div>
    <?php elseif(!empty($success)): ?>
        <div class="notify-success"><?=$success?></div>
    <?php endif; ?>
</div>


<STYLE type="text/css">
	.notify-success {
    margin: 5px 0px;
    padding: 5px;
    width: 625px;;
    background-color: #F1FFEF;
    border: 1px solid #8CBF83;
	color:black;
}

	.notify-error {
    margin: 5px 0px;
    padding: 5px;
    width: 625px;;
    background-color: #FFF2E8;
    border: 1px solid #FF0000;
	color:black;
}

	.notify-error li {
    margin: 5px 20px;
}
 </STYLE>

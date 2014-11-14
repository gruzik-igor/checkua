<div class="site1">
	<div class="title site">МАГАЗИН НЕМО - АКВАРІУМНІ РИБКИ І ВСЕ ДЛЯ НИХ!</div>
</div>
<br>
<br>
<div class="site1">
	<a href="<?=SITE_URL?>admin/mailhandler" class="f-right">До всіх листів</a>
	<div class="title site">Зворотній зв'язок</div>
</div>
<table cellspacing="0">
	<tr class="top">
		<th></th><th>Дата</th><th>Ім'я</th><th>Телефон</th><th>email</th>
	</tr>
		<?php
		$messages = $this->db->getAllData('mail_handler', 'date DESC LIMIT 20');
		if($messages){
			foreach ($messages as $message) {
				?>
				<tr>
    				<td><a href="<?=SITE_URL?>admin/mailhandler/<?=$message->id?>"><?=$message->id?></a></td>
    				<td><?=date("d.m.Y H:i", $message->date)?></td>
    				<td><a href="<?=SITE_URL?>admin/mailhandler/<?=$message->id?>"><?=$message->name?></a></td>
    				<td><?=$message->phone?></td>
    				<td><?=$message->email?></td>
   				</tr>
				<?php
			}
		} else {
	?>
		<tr>
			<td colspan="5">Листи відсутні!</td>
		</tr>
	<?php } ?>
</table>

<?php if($_SESSION['user']->admin == 1){ ?>
	<div class="site1">
		<div class="title site">Налаштування сайту</div>
	</div>
	<div class="f-left">
		<a href="<?=SITE_URL?>admin/wl_users">
			<div class="f-l s-but">
				<div class="f-l image"><img src="<?=SITE_URL?>style/images-admin/user.png"></div>
				<div class="f-l nav1">Користувачі</div>
			</div>
		</a>
		<a href="<?=SITE_URL?>admin/wl_ntkd">
			<div class="f-l s-but">
				<div class="f-l image"><img src="<?=SITE_URL?>style/images-admin/stats-2.png"></div>
				<div class="f-l nav1">SEO</div>
			</div>
		</a>
		<a href="<?=SITE_URL?>admin/wl_aliases">
			<div class="f-l s-but">
				<div class="f-l image"><img src="<?=SITE_URL?>style/images-admin/location-pin.png"></div>
				<div class="f-l nav1">Адреси</div>
			</div>
		</a>
		<a href="<?=SITE_URL?>admin/wl_services">
			<div class="f-l s-but">
				<div class="f-l image"><img src="<?=SITE_URL?>style/images-admin/list.png"></div>
				<div class="f-l nav1">Сервіси</div>
			</div>
		</a>
	</div>
<?php } ?>
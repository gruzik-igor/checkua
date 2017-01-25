<div class="row">
	<div class="panel panel-inverse">
	    <div class="panel-heading">
	        <div class="panel-heading-btn">
	            <a href="<?=SITE_URL?>admin/wl_mail_template/<?=$history->template?>#tabs-history" class="btn btn-info btn-xs"><i class="fa fa-envelope"></i> До всіх листів</a>
	        </div>
	        <h4 class="panel-title">Лист архіву <strong>#<?=$history->id?></strong></h4>
	    </div>
	    <div class="panel-body">
			<p>Дата відправки: <strong><?=date('d.m.Y H:i', $history->date)?></strong></p>
			<p>Шаблон: <a href="<?=SITE_URL?>admin/wl_mail_template/<?=$history->template?>#tabs-history" class="btn btn-info btn-xs"><i class="fa fa-envelope"></i> <strong><?=$history->template .' '.$history->template_name?></strong></a></p>
			<p>Від: <strong><?=$history->from?></strong></p>
			<p>До: <strong><?=$history->to?></strong></p>
			<p>Тема: <strong><?=$history->title?></strong></p>
			<p>Вміст листа:</p>
			<pre><?=$history->text?></pre>
		</div>
	</div>
</div>
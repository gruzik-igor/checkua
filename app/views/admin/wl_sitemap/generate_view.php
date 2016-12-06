<div class="row">
	<div class="col-md-6">
        <div class="panel panel-inverse">
	        <div class="panel-heading">
	        	<div class="panel-heading-btn">
	        		<a href="<?=SITE_URL?>admin/wl_sitemap" class="btn btn-success btn-xs"><i class="fa fa-refresh"></i> До всіх записів</a>
	        	</div>
	            <h4 class="panel-title">Загальні налаштування SiteMap</h4>
	        </div>
	        <div class="panel-body panel-form">
	            <form class="form-horizontal form-bordered" action="<?=SITE_URL?>admin/wl_sitemap/save_generate" method="POST">
	                <div class="form-group">
	                	<label class="col-md-9 control-label">Генератор карти сайту активний<br>
							<small>Автоматично оновлювати SiteMap при зміні контенту на сайті</small>
	                	</label>
	                    <div class="col-md-3">
                    		<input type="checkbox" data-render="switchery" checked value="1" name="active" />
						</div>
	                </div>
	                <div class="form-group">
	                	<label class="col-md-9 control-label">Автоматично відправляти SiteMap пошуковим роботам<br>
							<small>google.com, yahoo.com, ask.com, bing.com не частіше 1 раза за добу <br>та не менше 2 год від останньої зміни інформації на сайті</small>
	                	</label>
	                    <div class="col-md-3">
                    		<input type="checkbox" data-render="switchery" checked value="1" name="sent" />
						</div>
	                </div>
	                <div class="form-group">
	                	<label class="col-md-6 control-label">Остання генерація</label>
	                    <div class="col-md-6">
                    		<?= date('d.m.Y H:i') ?>
						</div>
	                </div>
	                <div class="form-group">
	                	<label class="col-md-6 control-label">Відправлено пошуковим роботам</label>
	                    <div class="col-md-6">
                    		<?= date('d.m.Y H:i') ?>
						</div>
	                </div>
	                <div class="form-group">
                        <label class="col-md-3 control-label"></label>
                        <div class="col-md-9">
                            <button type="submit" class="btn btn-sm btn-success">Зберегти</button>
                        </div>
                    </div>
	            </form>
	        </div>
	    </div>
	</div>
	<div class="col-md-6">
        <div class="panel panel-inverse">
	        <div class="panel-heading">
	            <h4 class="panel-title">Ручне генерування SiteMap</h4>
	        </div>
	        <div class="panel-body panel-form">
	            <form class="form-horizontal form-bordered" action="<?=SITE_URL?>admin/wl_sitemap/start_generate" method="POST">
	                <div class="form-group">
	                	<label class="col-md-9 control-label">Автоматично відправляти SiteMap пошуковим роботам</label>
	                    <div class="col-md-3">
                    		<input type="checkbox" data-render="switchery" value="1" name="sent" />
						</div>
	                </div>
	                <?php $Cache = rand(0, 999); ?>
	                <input type="hidden" name="code_hidden" value="<?=$Cache?>">
	                <div class="form-group">
	                	<label class="col-md-3 control-label">Код перевірки <strong><?=$Cache?></strong></label>
                        <div class="col-md-9">
		                	<input type="number" name="code_open" placeholder="<?=$Cache?>" min="0" class="form-control" required>
						</div>
	                </div>
	                <div class="form-group">
                        <label class="col-md-3 control-label"></label>
                        <div class="col-md-9">
                            <button type="submit" class="btn btn-sm btn-success">Генерувати</button>
                        </div>
                    </div>
	            </form>
	        </div>
	    </div>
	</div>
</div>

<?php
$_SESSION['alias']->js_load[] = 'assets/switchery/switchery.min.js';
?>
<link rel="stylesheet" href="<?=SITE_URL?>assets/switchery/switchery.min.css" />
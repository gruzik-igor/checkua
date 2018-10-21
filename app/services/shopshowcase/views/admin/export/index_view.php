<div class="row">
    <div class="col-md-12">
        <div class="panel panel-inverse">
            <div class="panel-heading">
            	<div class="panel-heading-btn">
            		<a href="<?=SITE_URL?>admin/wl_aliases/<?=$_SESSION['alias']->alias?>" class="btn btn-xs btn-info"><i class="fa fa-cogs"></i> Налаштування</a>
                </div>
                <h4 class="panel-title">Автоматизований експорт товарів у форматі yml (для prom.ua)</h4>
            </div>
			<div class="panel-body">
				<?php if(empty($_SESSION['option']->exportKey)) { ?>
			        <div class="note note-info">
			        	<h4>Увага! Експорт товарів в автоматизованому режимі відключено</h4>
			        	<p> <a href="<?=SITE_URL.'admin/'.$_SESSION['alias']->alias?>/export?active" class="btn btn-warning"><i class="fa fa-check"></i> активувати ключ експорту</a> </p>
			        	<small>В процесі активації автоматично створиться індивідуальний ключ безпеки</small>
			        </div>
				<?php } else { ?>
					<div class="note note-info">
			        	<h4>Посилання для всіх товарів</h4>
			        	<?php if($_SESSION['all_languages'])
			        	foreach ($_SESSION['all_languages'] as $i => $language) {
			        		$link = SERVER_URL;
			        		if($i > 0)
			        			$link .= $language.'/'; ?>
			        		<p> <strong><?=$language?>:</strong> <a href="<?=$link.$_SESSION['alias']->alias?>/exportyml?key=<?=$_SESSION['option']->exportKey?>"><strong><?=$link.$_SESSION['alias']->alias?>/exportyml?key=<?=$_SESSION['option']->exportKey?></strong></a> </p>
			        	<?php } else { ?>
			        		<p> <a href="<?=SERVER_URL.$_SESSION['alias']->alias?>/exportyml?key=<?=$_SESSION['option']->exportKey?>"><strong><?=SERVER_URL.$_SESSION['alias']->alias?>/exportyml?key=<?=$_SESSION['option']->exportKey?></strong></a> </p>
			        	<?php } ?>
			        	<p>Також можна виконувати частковий експорт з  <a href="<?=SITE_URL.'admin/'.$_SESSION['alias']->alias?>/export?groups" class="btn btn-xs btn-info">груп товарів</a></p>
			        </div>
				<?php } ?>
			</div>
		</div>
	</div>
</div>
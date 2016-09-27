<div class="row">
	<div class="col-md-4 ui-sortable">
        <div class="panel panel-inverse" data-sortable-id="form-plugins-1">
	        <div class="panel-heading">
	            <h4 class="panel-title">Фільтр</h4>
	        </div>
	        <div class="panel-body panel-form">
	            <form class="form-horizontal form-bordered" _lpchecked="1">
	                <div class="form-group">
	                    <label class="col-md-3 control-label">Оберіть період</label>
	                    <div class="col-md-9">
	                        <div class="input-group input-daterange">
	                            <input type="text" class="form-control" name="start" placeholder="Date Start">
	                            <span class="input-group-addon">-</span>
	                            <input type="text" class="form-control" name="end" placeholder="Date End">
	                        </div>
	                    </div>
	                </div>
	                <div class="form-group">
	                	<label class="col-md-3 control-label">Статистика по </label>
                        <div class="col-md-9">
		                	<select name="alias" class="form-control">
		                		<option value="*">всіх адресах</option>
		                		<?php $aliases = $this->db->getAllData('wl_aliases');
		                		foreach ($aliases as $alias) {
		                			echo("<option value='{$alias->id}'>{$alias->alias}</option>");
		                		}
		                		?>
		                	</select>
						</div>
	                </div>
	                <div class="form-group">
                        <label class="col-md-3 control-label"></label>
                        <div class="col-md-9">
                            <button type="submit" class="btn btn-sm btn-success">Пошук</button>
                        </div>
                    </div>
	            </form>
	        </div>
	    </div>
    </div>
    <div class="col-md-8 ui-sortable">
    	<?php require_once APP_PATH.'views'.DIRSEP.'admin'.DIRSEP.'@commons'.DIRSEP.'_wl_statistic.php'; ?>
    </div>
</div>
<div class="row">
	<div class="col-md-12 ui-sortable">
		<div class="panel panel-inverse" data-sortable-id="form-plugins-2">
			<div class="panel-body">
				<table class="table table-striped table-responsive table-hover">
		            <thead>
		                <tr>
		                    <th>#</th>
		                    <th>Адреса</th>
		                    <?php if($_SESSION['language']) { ?>
		                    	<th>Мова</th>
		                    <?php } ?>
		                    <th>День</th>
		                    <th>Унікальні відвідувачі</th>
		                    <th>Загальні перегляди</th>
		                </tr>
		            </thead>
		            <tbody>
		            	<?php if($wl_statistic) foreach ($wl_statistic as $statistic) { ?>
			                <tr>
			                    <td><?=$statistic->id?></td>
			                    <td><a href="<?=SITE_URL?><?=($statistic->link == 'main')?'':$statistic->link?>" target="_blank">/<?=$statistic->link?></a></td>
			                    <?php if($_SESSION['language']) { ?>
			                    	<td><?=$statistic->language?></td>
			                    <?php } ?>
			                    <td><?=date('d.m.Y', $statistic->day)?></td>
			                    <td><?=$statistic->unique?></td>
			                    <td><?=$statistic->views?></td>
			                </tr>
		                <?php } ?>
		            </tbody>
		        </table>
		        <?php
	                $this->load->library('paginator');
	                echo $this->paginator->get();
	            ?>
	        </div>
	    </div>
    </div>
</div>
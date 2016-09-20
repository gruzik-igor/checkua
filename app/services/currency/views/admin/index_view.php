<div class="row">
    <div class="col-md-12">
        <div class="panel panel-inverse">
            <div class="panel-heading">
            	<div class="panel-heading-btn">
					<a href="<?=SITE_URL.'admin/'.$_SESSION['alias']->alias?>/add" class="btn btn-warning btn-xs"><i class="fa fa-plus"></i> Додати валюту</a>
					<?php if($_SESSION['option']->saveToHistory) { ?>
						<a href="<?=SITE_URL.'admin/'.$_SESSION['alias']->alias?>/history" class="btn btn-success btn-xs"><i class="fa fa-list"></i> Історія змін</a>
					<?php } ?>
					<a href="<?=SITE_URL.'admin/'.$_SESSION['alias']->alias?>/updatePrivat24" class="btn btn-info btn-xs"><i class="fa fa-refresh"></i> Оновити через Privat24</a>
            	</div>
                <h4 class="panel-title"><?=$_SESSION['alias']->name?></h4>
            </div>
            <div class="panel-body">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered nowrap" width="100%">
                        <thead>
                            <tr>
                                <th>Внутрішній код</th>
                                <th>Код валюти</th>
								<th>Курс</th>
								<th>Інформація актуальна на</th>
                            </tr>
                        </thead>
                        <tbody>
                        	<?php
                        	if(!empty($currents)) { 
                        		foreach($currents as $currency) { ?>
									<tr>
										<td><?=$currency->id?></td>
										<td><?=$currency->code?></td>
										<td>
											<button class='btn btn-sm  btn-warning' data-toggle='modal' data-target='#ModalEditCurrency' data-currencyid='<?=$currency->id?>' data-currencyvalue='<?=$currency->currency?>' title="Редагувати курс <?=$currency->code?>"><i class='fa fa-pencil'></i></button> 
											<b><?=$currency->currency?></b> 
										</td>
										<td><?=date("d.m.Y", $currency->day)?></td>
									</tr>
							<?php } } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="ModalEditCurrency" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" style="margin: 15% auto;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title"></h4>
            </div>
            <div class="modal-body">
	            <form action="<?=SITE_URL.'admin/'.$_SESSION['alias']->alias?>/save" method="POST" id="FormEditCurrency">
		            <input type="hidden" name="id" value="0" id="currencyId">
	                <div class="row">
	                    <div class="form-group">
	                        <label class="control-label col-md-3">Курс:</label>
	                        <input type="number" name="currency" class="form-control col-md-3" min="0" step="0.01" style="height: 39px;" id="currencyValue" required>
	                    </div>
	                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Закрити</button>
                <button type="button" class="btn btn-primary" onClick="document.getElementById('FormEditCurrency').submit();">Оновити</button>
            </div>
        </div>
    </div>
</div>

<?php $_SESSION['alias']->js_load[] = 'app/services/'.$_SESSION['service']->name.'/views/admin/currency.js';
 if($_SESSION['option']->saveToHistory) { ?>

 	<div class="row">
		<div class="col-md-12">
			<div class="widget-chart with-sidebar bg-black">
			    <div class="widget-chart-content">
			        <h4 class="chart-title">
			            Курс UAH / USD
			            <small>Статистика подобово за останній місяць. Дані ПриватБанку</small>
			        </h4>
			        <div id="visitors-line-chart" class="morris-inverse" style="height: 260px;"></div>
			    </div>
			</div>
		</div>
	</div>

	<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/morris.js/0.5.1/morris.css">
	<script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.0/jquery.min.js"></script>
	<script src="//cdnjs.cloudflare.com/ajax/libs/raphael/2.1.0/raphael-min.js"></script>
	<script src="//cdnjs.cloudflare.com/ajax/libs/morris.js/0.5.1/morris.min.js"></script>

	<script>
	var getMonthName = function(e) {
	    var t = [];
	    t[0] = "Січень";
	    t[1] = "Лютий";
	    t[2] = "Березень";
	    t[3] = "Квітень";
	    t[4] = "Травень";
	    t[5] = "Червень";
	    t[6] = "Липень";
	    t[7] = "Серпень";
	    t[8] = "Вересень";
	    t[9] = "Жовтень";
	    t[10] = "Листопад";
	    t[11] = "Грудень";
	    return t[e];
	};

	var e = "#0D888B",
		t = "#00ACAC",
		n = "#3273B1",
		r = "#348FE2",
		i = "rgba(0,0,0,0.6)",
		z = "#eee",
		s = "rgba(255,255,255,0.4)";

	Morris.Line({
	    element: "visitors-line-chart",
	    data: [
	    <?php
	    $day = strtotime('today') - 30*3600*24;
	    $where = array();
	    $where['day'] = '>='.$day;
	    $where['currency'] = 2;
	    $where['from'] = 'Privat24';
    	$this->db->select($_SESSION['service']->table.'_history', '*', $where);
    	$history = $this->db->get('array');
    	if(!empty($history)) { 
    		foreach($history as $currency) { ?>
	    	{
		        x: "<?= date('d.m.Y', $currency->day)?>",
		        y: <?= $currency->value?>
		    },
	    <?php } } ?>
	    ],
	    xkey: "x",
	    ykeys: "y",
	    labels: "UAH / 1 USD",
	    lineColors: [z],
	    pointFillColors: [z],
	    xLabels:'week',
	    // xLabelFormat:function(e){
	    // 	e=getMonthName(e.getMonth());
	    // 	return e.toString()
	    // },
	    lineWidth: "2px",
	    pointStrokeColors: [i, i],
	    resize: true,
	    gridTextFamily: "Open Sans",
	    gridTextWeight: "normal",
	    gridTextSize: "11px",
	    gridLineColor: "rgba(0,0,0,0.5)",
	    hideHover: "auto"
	});
	</script>

	<style>
	.morris-inverse .morris-hover {
	    background: rgba(0,0,0,.4)!important;
	    border: none!important;
	    padding: 8px!important;
	    color: #ccc!important;
	}
	</style>
<?php } ?>
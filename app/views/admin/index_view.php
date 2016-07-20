<?php if($views) { ?>
<div class="row">
	<div class="col-md-8">
		<div class="widget-chart with-sidebar bg-black">
		    <div class="widget-chart-content">
		        <h4 class="chart-title">
		            Аналітика відвідувань
		            <small>тут може бути текст</small>
		        </h4>
		        <div id="visitors-line-chart" class="morris-inverse" style="height: 260px;"></div>
		    </div>
		    <div class="widget-chart-sidebar bg-black-darker">
	            <div class="chart-number">
	                <?= $views->totalUsers?>
	                <small>Відвідувачів</small>
	            </div>
	            <div id="visitors-donut-chart" style="height: 160px">
	            </div>
	            <ul class="chart-legend">
	                <li><i class="fa fa-circle-o fa-fw text-success m-r-5"></i> <?= $views->newPercentage?>% <span>Нових відвідувачів</span></li>
	                <li><i class="fa fa-circle-o fa-fw text-primary m-r-5"></i> <?= $views->returnedPercentage?>% <span>Повторних відвідувачів</span></li>
	            </ul>
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
    <?php foreach($views->tableData as $data) {?>
    	{
	        x: "<?= date('Y-m-d' ,$data->day)?>",
	        w: <?= $data->views?>,
	        y: <?= $data->cookie?>,
	        z: <?= $data->unique - $data->cookie?>
	    },
    <?php } ?>
    ],
    xkey: "x",
    ykeys: ["w", "y", "z"],
    labels: ["Загальна к-сть переглядів", "Нових відвідувачів", "Повторних відвідувачів"],
    lineColors: [z, e, n],
    pointFillColors: [z, t, r],
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

var handleVisitorsDonutChart = function() {
    var e = "#00acac";
    var t = "#348fe2";
    Morris.Donut({
        element: "visitors-donut-chart",
        data: [{
            label: "Нових",
            value: <?= $views->newUsers?>
        }, {
            label: "Повторні",
            value: <?= $views->returnedUser?>
        }],
        colors: [e, t],
        labelFamily: "Open Sans",
        labelColor: "rgba(255,255,255,0.4)",
        labelTextSize: "12px",
        backgroundColor: "#242a30"
    })
};

handleVisitorsDonutChart();
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


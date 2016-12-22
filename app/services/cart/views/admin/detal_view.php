<div class="row">
    <div class="col-md-12">
		<ul class="nav nav-tabs" id="myTab">
			<li class="active"><a data-target="#tabs-products" href="#tabs-products" data-toggle="tab">Товари</a></li>
			<li><a data-target="#tabs-history" href="#tabs-history" data-toggle="tab">Дія</a></li>
			<li><a data-target="#tabs-main" href="#tabs-main" data-toggle="tab">Загальні дані</a></li>
		</ul>

		<div class="tab-content">
			<div class="tab-pane active" id="tabs-products">
				<?php require_once 'tabs/_tabs-products.php'; ?>
			</div>
			<div class="tab-pane" id="tabs-history">
				<?php require_once 'tabs/_tabs-history.php'; ?>
			</div>
			<div class="tab-pane" id="tabs-main">
				<?php require_once 'tabs/_tabs-main.php'; ?>
			</div>
		</div>
    </div>
</div>
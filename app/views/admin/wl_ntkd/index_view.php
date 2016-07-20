<?php

	$wl_aliases = $this->db->getAllData('wl_aliases');
	$wl_services = $this->db->getAllData('wl_services');
	$services_title = array(0 => '');
	if($wl_services){
		foreach ($wl_services as $s) if($s->active == 1) {
			$services_title[$s->id] = $s->title;
		}
	}
	
?>

<div class="row">
    <div class="col-md-12">
        <div class="panel panel-inverse">
            <div class="panel-heading">
                <h4 class="panel-title">Наявні адреси:</h4>
            </div>
            <div class="panel-body">
                <div class="table-responsive">
                    <table id="data-table" class="table table-striped table-bordered nowrap" width="100%">
                        <thead>
                            <tr>
								<th>id</th>
								<th>Головна адреса</th>
								<th>Сервіс</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php if($wl_aliases) foreach ($wl_aliases as $alias) { ?>
							<tr>
								<td><?=$alias->id?></td>
								<td><a href="<?=SITE_URL.'admin/wl_ntkd/'.$alias->alias?>"><?=$alias->alias?></a></td>
								<td><?=$services_title[$alias->service]?></td>
							</tr>
						<?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
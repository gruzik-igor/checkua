<?php

	$wl_aliases = $this->db->getAllData('wl_aliases');
	$wl_services = $this->db->getAllData('wl_services');
	$services_name = array(0 => '');
	$services_title = array(0 => '');
	if($wl_services){
		foreach ($wl_services as $s) if($s->active == 1) {
			$services_name[$s->id] = $s->name;
			$services_title[$s->id] = $s->title;
		}
	}
	
?>

<!-- begin row -->
<div class="row">
    <!-- begin col-12 -->
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
								<th>alias</th>
								<th>service</th>
								<th>alias table</th>
								<th>options</th>
								<th>active</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php if($wl_aliases) foreach ($wl_aliases as $alias) { ?>
							<tr>
								<td><?=$alias->id?></td>
								<td><a href="<?=SITE_URL?>admin/wl_images/<?=$alias->alias?>"><?=$alias->alias?></a></td>
								<td><?=$services_title[$alias->service]?></td>
								<td><?=$alias->table?></td>
								<td><?=$alias->options?></td>
								<td><?=$alias->active?></td>
							</tr>
						<?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <!-- end col-12 -->
</div>
<!-- end row -->



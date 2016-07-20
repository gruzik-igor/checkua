<div class="row">
    <div class="col-md-12">
        <div class="panel panel-inverse">
            <div class="panel-heading">
            	<div class="panel-heading-btn">
            		<a href="<?=SITE_URL.'admin/wl_ntkd/'.$alias->alias?>/edit" class="btn btn-xs btn-warning"><i class="fa fa-at"></i> Головна сторінка</a>
            	</div>
                <h4 class="panel-title">Наявні адреси:</h4>
            </div>
            <div class="panel-body">
                <div class="table-responsive">
                    <table id="data-table" class="table table-striped table-bordered nowrap" width="100%">
                        <thead>
                            <tr>
								<th>alias</th>
								<th>id content</th>
                                <th>name</th>
								<th>title</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php if($articles) foreach ($articles as $a) { ?>
							<tr>
								<td><?=$alias->alias?></td>
								<td><a href="<?=SITE_URL.'admin/wl_ntkd/'.$alias->alias.'/'.$a->content?>"><?=$a->content?></a></td>
                                <td><a href="<?=SITE_URL.'admin/wl_ntkd/'.$alias->alias.'/'.$a->content?>"><?=$a->name?></a></td>
								<td><?=$a->title?></td>
							</tr>
						<?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

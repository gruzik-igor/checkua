<div class="row">
    <div class="col-md-12">
        <div class="panel panel-inverse">
            <div class="panel-heading">
                <div class="panel-heading-btn">
                </div>
                <h4 class="panel-title">Інформація:</h4>
            </div>
            <div class="panel-body">
                <div class="table-responsive">
                    <table id="data-table" class="table table-striped table-bordered nowrap" width="100%">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <?php foreach ($formInfo as $info) { ?>
                                <th><?= $info->title?></th>
                                <?php } ?>
                                <th>Дата додачі</th>
                            </tr>
                        </thead>
                        <tbody>
                        	<?php if($tableInfo) foreach ($tableInfo as $info) {  ?>
                            <tr>
                                <?php foreach ($info as $key => $value) 
                                { 
                                    if($key == 'language') continue;
                                    echo "<td>" . (preg_match('/date/', $key) ? date('d.m.Y H:i', $value) : $value) . '</td>';
                                }
                                ?>
                            </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
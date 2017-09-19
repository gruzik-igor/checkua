<div class="row">
    <div class="col-md-12">
        <div class="panel panel-inverse">
            <div class="panel-heading">
                <?php if($_SESSION['user']->admin) { ?>
                    <div class="panel-heading-btn">
                        <a href="<?=SITE_URL?>admin/wl_forms/<?=$this->data->uri(3)?>" class="btn btn-warning btn-xs"><i class="fa fa-cogs"></i> До налаштування форми</a>
                    </div>
                <?php } ?>
                <h4 class="panel-title">Інформація:</h4>
            </div>
            <div class="panel-body">
                <div class="table-responsive">
                    <table id="data-table" class="table table-striped table-bordered nowrap" width="100%">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <?php if($formInfo)
                                {
                                    if($tableInfo)
                                    {
                                        foreach ($tableInfo[0] as $key => $value) {
                                            foreach ($formInfo as $info) {
                                                if($info->name == $key)
                                                    echo '<th>'.$info->title.'</th>';
                                            }
                                        }
                                    }
                                    else
                                        foreach ($formInfo as $info) {
                                            echo '<th>'.$info->title.'</th>';
                                        }
                                } ?>
                                <th>Дата додачі</th>
                                <th>Мова сайту</th>
                            </tr>
                        </thead>
                        <tbody>
                        	<?php if($tableInfo) foreach ($tableInfo as $info) {
                                echo ('<tr>');
                                foreach ($info as $key => $value)
                                    echo "<td>" . (preg_match('/date/', $key) ? date('d.m.Y H:i', $value) : $value) . '</td>';
                                echo ('</tr>');
                            } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
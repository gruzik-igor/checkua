<div class="row">
    <div class="col-md-6">
        <div class="panel panel-inverse">
            <div class="panel-heading">
                <h4 class="panel-title">Детальна інформація</h4>
            </div>
            <div class="panel-body">
                <div class="table-responsive">
                    <table id="data-table" class="table table-striped table-bordered nowrap" width="100%">
                        <tr>
                            <th width="100px" nowrap>ID</th>
                            <td><?php print_r($application->id) ?></td>
                        </tr>
                        <tr>
                            <th width="100px" nowrap>Ім'я</th>
                            <td><?php print_r($application->name) ?></td>
                        </tr>
                        <tr>
                            <th width="100px" nowrap>Телефон</th>
                            <td><?php print_r($application->phone) ?></td>
                        </tr>
                        <tr>
                            <th width="100px" nowrap>E-mail</th>
                            <td><?php print_r($application->email) ?></td>
                        </tr>
                        <tr>
                            <th width="100px" nowrap>Сайт</th>
                            <td><?php print_r($application->site) ?></td>
                        </tr>
                        <tr>
                            <th width="100px" nowrap>Що цікавить</th>
                            <td><?=$application->interesting ?></td>
                        </tr>
                        <tr>
                            <th>Інформація до заявки</th>
                            <td><?=$application->additional?></td>
                        </tr>
                        <tr>
                            <th width="100px" nowrap>З якої форми</th>
                            <td><?php print_r($application->from) ?></td>
                        </tr>
                        <tr>
                            <th width="100px" nowrap>Дата заявки</th>
                            <td><?php print_r(date('d.m.Y H:i', $application->date_add)) ?></td>
                        </tr>
                        <?php if ($application->manager > 0){ ?>
                            <tr>
                                <th width="100px" nowrap>Менеджер</th>
                                <td><?php
                                    echo $application->manager.'. ';
                                    $manager = $this->db->getAllDataById('wl_users', $application->manager);
                                    echo $manager->name;
                                ?></td>
                            </tr>
                            <tr>
                                <th width="100px" nowrap>Дата оброблення</th>
                                <td><?=date('d.m.Y H:i', $application->date_manage)?></td>
                            </tr>
                        <?php } ?>
                    </table>
                </div>
            </div>
        </div>
    </div> 
    <div class="col-md-6">
    <?php if(!empty($_SESSION['notify']->error) || !empty($_SESSION['notify']->success)) { ?>
        <div class="row">
            <?php if(!empty($_SESSION['notify']->success)) { ?>
                <div class="alert alert-success fade in m-b-15">
                    <?=$_SESSION['notify']->success?>
                    <span class="close" data-dismiss="alert">&times;</span>
                </div>
            <?php } if(!empty($_SESSION['notify']->error)) { ?>
                <div class="alert alert-danger fade in m-b-15">
                    <strong>Помилка!</strong>
                    <?=$_SESSION['notify']->error?>
                    <span class="close" data-dismiss="alert">&times;</span>
                </div>
            <?php } ?>
        </div>
        <?php unset($_SESSION['notify']->success, $_SESSION['notify']->error); } ?>

        <div class="row">
            <div class="panel panel-inverse">
                <div class="panel-heading">
                    <h4 class="panel-title">Менеджер</h4>
                </div>
                <div class="panel-body">
                     <form action="<?=SITE_URL?>admin/application/save" method="POST">
                        <h4>Статус заявки</h4>
                        <input type="hidden" name="id" value="<?=$application->id?>">
                        <p><select name="status" class="form-control">
                            <?php $statuses = $this->db->getAllData('call_order_status');
                            foreach ($statuses as $status) {
                                $selected = '';
                                if ($status->id == $application->status){
                                    $selected = 'selected';
                                }
                                echo "<option {$selected} value='{$status->id}'>{$status->name}</option>";
                            } ?>
                        </select> </p>
                        <h4>Коментар менеджера (для себе):</h4>
                        <textarea name="answer" style="height:300px;" class="form-control"><?=$application->answer?></textarea>
                        <h4>Після заповнення форми перейти</h4>
                        <p>
                            <label><input type="radio" name="after" value="this" checked> на цю заявку</label>
                            <label><input type="radio" name="after" value="all"> до всіх заявок</label>
                        </p>
                        <p><button type="submit" class="btn btn-sm btn-success ">Зберегти</button></p>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<link href="<?=SITE_URL?>assets/DataTables/css/data-table.css" rel="stylesheet" />
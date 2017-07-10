<div class="row">
    <div class="col-md-12">
        <div class="panel panel-inverse">
            <div class="panel-heading">
                <div class="panel-heading-btn">
                    <a href="<?=SITE_URL?>admin/wl_mail_template/add" class="btn btn-warning btn-xs"><i class="fa fa-plus"></i> Додати нову розсилку</a>
                </div>
                <h4 class="panel-title">Усі розсилки</h4>
            </div>
            <div class="panel-body">
                <div class="table-responsive">
                    <table id="data-table" class="table table-striped table-bordered nowrap" width="100%">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th></th>
                                <th>Від</th>
                                <th>До</th>
                                <th>Мульмимовність</th>
                                <th>Зберегти в історію</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if($mailTemplates) foreach ($mailTemplates as $template) { ?>
                            <tr>
                                <td><a href="<?= SITE_URL.'admin/wl_mail_template/'. $template->id ?>"> <?= $template->id ?></a></td>
                                <td><a href="<?= SITE_URL.'admin/wl_mail_template/'. $template->id ?>" class="btn btn-xs btn-info"> Дивитися</a></td>
                                <td><?= $template->from ?></td>
                                <td><?= $template->to ?></td>
                                <td><?= $template->multilanguage ?></td>
                                <td><?= $template->savetohistory ?></td>
                                <td><button onclick="deleteTemplate(<?= $template->id ?>)" class="btn btn-xs btn-danger"> Видалити</button></td>
                            </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function deleteTemplate(id) {
        if(confirm('Видалити розсилку?')){
            $.ajax({
                url : '<?= SITE_URL?>admin/wl_mail_template/deleteTemplate',
                method : 'POST',
                data : {
                    id: id
                },
                success : function (res) {
                    window.location.href = '<?= SITE_URL?>admin/wl_mail_template';
                }
            })
        }
    }
</script>
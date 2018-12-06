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
                                            if($key != 'new')
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
                                echo ('<tr id="row-'.$info->id.'">');
                                foreach ($info as $key => $value)
                                    if($key == 'id' && $_SESSION['user']->type == 1)
                                        echo '<td><button class="btn btn-xs btn-danger" data-id="'.$value.'"><i class="fa fa-trash" aria-hidden="true"></i></button> '.$value.'</td>';
                                    elseif($key != 'new')
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

<?php if($_SESSION['user']->type == 1) { ?>
<script>
    var btn = document.querySelectorAll('table#data-table td > button.btn-danger');
    for (var i = 0; i < btn.length; i++) {
        btn[i].onclick = deleteRow;
    }
    function deleteRow() {
        var rowId = this.dataset.id;
        if(confirm('Видалити запис #'+rowId+'? Увага! Дані відновленню не підлягають!'))
        {
            $('#saveing').css("display", "block");
            $.ajax({
                url: SITE_URL + "admin/wl_forms/deleteRow",
                type: 'POST',
                data: {
                    id: this.dataset.id,
                    table: '<?=$form->table?>',
                    json: true
                },
                success: function(res){
                    if(res['result'] == false) {
                        $.gritter.add({title:"Помилка!", text:res['error']});
                    }
                    else
                    {
                        $('tr#row-'+rowId).slideUp();
                        $.gritter.add({title:'Запис #'+rowId, text:"Дані успішно видалено!"});
                    }
                    $('#saveing').css("display", "none");
                },
                error: function(){
                    $.gritter.add({title:"Помилка!",text:"Помилка! Спробуйте ще раз!"});
                    $('#saveing').css("display", "none");
                },
                timeout: function(){
                    $.gritter.add({title:"Помилка!",text:"Помилка: Вийшов час очікування! Спробуйте ще раз!"});
                    $('#saveing').css("display", "none");
                }
            });
        }
    }
</script>
<?php } ?>
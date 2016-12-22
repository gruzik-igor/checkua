<div class="row">
    <div class="col-md-12">
        <?php if(isset($_SESSION['notify'])){ 
            require APP_PATH.'views/admin/notify_view.php';
        } ?>

        <div class="panel panel-inverse">
            <div class="panel-heading">
            	<div class="panel-heading-btn">
                	<a href="<?=SITE_URL.'admin/'.$_SESSION['alias']->alias?>/add_group" class="btn btn-warning btn-xs"><i class="fa fa-plus"></i> Додати групу</a>
                </div>
                <h4 class="panel-title">Керування групами</h4>
            </div>
            <div class="panel-body">
                <div class="table-responsive">
                    <table id="data-table" class="table table-striped table-bordered nowrap" width="100%">
                        <thead>
                            <tr>
								<th>Id</th>
								<th>Група</th>
								<th>Адреса</th>
								<th>Автор</th>
								<th>Редаговано</th>
								<th>Стан</th>
								<th>Змінити порядок</th>
                            </tr>
                        </thead>
                        <tbody>
							<?php 
							if(!empty($groups)){
                                $max = count($groups); 
								foreach ($groups as $group) {?>

									<tr>
										<td><?=$group->id?></td>
										<td><a href="<?=SITE_URL.'admin/'.$_SESSION['alias']->alias.'/groups/'.$group->alias?>"><?=$group->name?></a></td>
										<td><?=$group->alias?></td>
										<td><?=$group->author_edit?></td>
										<td><?=date('d.m.Y h:i', $group->date_edit)?></td>
										<td style="background-color:<?=($group->active == 1)?'green':'red'?>;color:white"><?=($group->active == 1)?'активний':'відключено'?></td>
                                        <td style="padding: 1px 5px;">
                                            <form method="POST" action="<?=SITE_URL.'admin/'.$_SESSION['alias']->alias?>/change_group_position">
                                                <input type="hidden" name="id" value="<?=$group->id?>">
                                                <input type="number" name="position" min="1" max="<?=$max?>" value="<?=$group->position?>" onchange="this.form.submit();" autocomplete="off" class="form-control">
                                            </form>
                                        </td>
									</tr>

								<?php }
							}
							?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <!-- end col-12 -->
</div>
<!-- end row -->
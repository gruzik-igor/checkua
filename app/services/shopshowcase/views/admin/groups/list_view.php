<div class="row">
    <div class="col-md-12">
        <div class="panel panel-inverse">
            <div class="panel-heading">
            	<div class="panel-heading-btn">
                	<a href="<?=SITE_URL.'admin/'.$_SESSION['alias']->alias?>/add_group<?=($parent)?'?parent='.$parent:''?>" class="btn btn-warning btn-xs"><i class="fa fa-plus"></i> Додати групу</a>
                	<a href="<?=SITE_URL.'admin/'.$_SESSION['alias']->alias.'/groups?all'?>" class="btn btn-success btn-xs">Всі групи деревом</a>
                	<a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-default" data-click="panel-expand"><i class="fa fa-expand"></i></a>
                </div>
                <h4 class="panel-title">Керування групами <?=$_SESSION['admin_options']['word:products_to_all']?></h4>
            </div>

            <?php
            if(isset($_SESSION['notify'])) { 
	        	require APP_PATH.'views/admin/notify_view.php';
	        }

	        if(!empty($groups)) { ?>

            <div class="panel-body">
                <div class="table-responsive">
                    <table id="data-table" class="table table-striped table-bordered nowrap" width="100%">
                        <thead>
                            <tr>
								<th>Id</th>
								<th>Група</th>
								<th>Адреса</th>
								<th>Востаннє редаговано</th>
								<th>Автор</th>
								<th>Стан</th>
								<th>Змінити порядок</th>
                            </tr>
                        </thead>
                        <tbody>
							<?php foreach ($groups as $g) { ?>
							<tr>
								<td><?=$g->id?></td>
								<td>
									<a href="<?=SITE_URL.'admin/'.$_SESSION['alias']->alias?>/groups/<?=$g->id?>-<?=$g->alias?>"><?=($g->parent == 0) ? '<strong>'.$g->name.'</strong>' : $g->name?></a>
									<a href="<?=SITE_URL.'admin/'.$_SESSION['alias']->alias?>/groups/edit-<?=$g->id?>-<?=$g->alias?>" class="btn btn-info btn-xs"><i class="fa fa-edit"></i> Редагувати</a>
								</td>
								<td><a href="<?=SITE_URL.$_SESSION['alias']->alias.'/'.$g->link?>">/<?=$_SESSION['alias']->alias.'/'.$g->link?></a></td>
								<td><?=date("d.m.Y H:i", $g->date_edit)?></td>
								<td><a href="<?=SITE_URL.'admin/wl_users/'.$g->author_edit?>"><?=$g->user_name?></a></td>
								<td style="background-color:<?=($g->active == 1)?'green':'red'?>;color:white"><?=($g->active == 1)?'активний':'відключено'?></td>
								<td style="padding: 1px 5px;">
									<form method="POST" action="<?=SITE_URL.'admin/'.$_SESSION['alias']->alias?>/change_group_position">
										<input type="hidden" name="id" value="<?=$g->id?>">
										<input type="number" name="position" min="1" max="<?=count($groups)?>" value="<?=$g->position?>" onchange="this.form.submit();" autocomplete="off" style="height:35px; padding-left:5px; min-width:80px;">
									</form>
								</td>
							</tr>
						<?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <?php } else { ?>
            	<div class="note note-info">
					<h4>Увага! В налаштуваннях адреси не створено жодної групи!</h4>
					<p>
					    <a href="<?=SITE_URL.'admin/'.$_SESSION['alias']->alias?>/add_group" class="btn btn-warning"><i class="fa fa-plus"></i> Додати групу</a>
	                </p>
				</div>
			<?php } ?>
        </div>
    </div>
</div>
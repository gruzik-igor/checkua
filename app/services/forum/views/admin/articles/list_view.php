<div class="row">
    <div class="col-md-12">
        <div class="panel panel-inverse">
            <div class="panel-heading">
                <div class="panel-heading-btn">
                	<a href="<?=SITE_URL.'admin/'.$_SESSION['alias']->alias?>/add<?=(isset($group))?'?group='.$group->id:''?>" class="btn btn-warning btn-xs"><i class="fa fa-plus"></i> <?=$_SESSION['admin_options']['word:article_add']?></a>
					
                    <?php if($_SESSION['option']->useGroups == 1){ ?>
						<a href="<?=SITE_URL.'admin/'.$_SESSION['alias']->alias?>/all" class="btn btn-info btn-xs">До всіх <?=$_SESSION['admin_options']['word:articles_to_all']?></a>
						<a href="<?=SITE_URL.'admin/'.$_SESSION['alias']->alias?>/groups" class="btn btn-info btn-xs">До всіх <?=$_SESSION['admin_options']['word:groups_to_all']?></a>
					<?php } ?>
                </div>
                <h4 class="panel-title"><?=(isset($group))?$_SESSION['alias']->name .'. Список '.$_SESSION['admin_options']['word:articles_to_all']:'Список всіх '.$_SESSION['admin_options']['word:articles_to_all']?></h4>
            </div>
            <?php if(isset($group)){ ?>
                <div class="panel-heading">
	            	<h4 class="panel-title">
	            		<a href="<?=SITE_URL.'admin/'.$_SESSION['alias']->alias?>"><?=$group->alias_name?></a> ->
						<?php if(!empty($group->parents)){
							$link = SITE_URL.'admin/'.$_SESSION['alias']->alias;
							foreach ($group->parents as $parent) { 
								$link .= '/'.$parent->link;
								echo '<a href="'.$link.'">'.$parent->name.'</a> -> ';
							}
							echo($_SESSION['alias']->name);
						} ?>
	            	</h4>
	            </div>
	        <?php } ?>
            <div class="panel-body">
                <div class="table-responsive">
                    <table id="data-table" class="table table-striped table-bordered nowrap" width="100%">
                        <thead>
                            <tr>
                                <th>Id</th>
								<th>Назва</th>
								<th>Адреса</th>
								<?php if($_SESSION['option']->useGroups == 1){ 
									if($_SESSION['option']->ArticleMultiGroup == 0) $categories = $this->forum_model->getGroups(-1, false);
									?>
									<th>Група</th>
								<?php } ?>
								<th>Автор</th>
								<th>Редаговано</th>
								<th>Стан</th>
								<th>Змінити порядок</th>
                            </tr>
                        </thead>
                        <tbody>
                        	<?php
                        	if(!empty($articles)){ 
                        		$max = count($articles); 
                        		foreach($articles as $a){ ?>
									<tr>
										<td><?=$a->id?></td>
										<td><a href="<?=SITE_URL.'admin/'.$a->link?>"><?=$a->name?></a></td>
										<td><a href="<?=SITE_URL.$a->link?>"><?=$a->alias?></a></td>
										<?php 
										if($_SESSION['option']->useGroups == 1) {
											if($_SESSION['option']->ArticleMultiGroup) {
												echo("<td>");
												if(!empty($a->group) && is_array($a->group)) {
                                                    foreach ($a->group as $group) {
                                                        echo('<a href="'.SITE_URL.$_SESSION['alias']->alias.'/'.$group->alias.'">'.$group->name.'</a> ');
                                                    }
                                                } else {
                                                    echo("Не визначено");
                                                }
                                                echo("</td>");
                                        	} else {
                                        ?>
											<td>
												<select onchange="changeCategory(this, <?=$a->id?>)" class="form-control">
													<option value="0">Немає</option>
													<?php if(isset($categories)) foreach ($categories as $c) {
														echo('<option value="'.$c->id.'"');
														if($c->id == $a->group) echo(' selected');
														echo('>'.$c->name.'</option>');
													} ?>
												</select>
											</td>
										<?php } } ?>
										<td><a href="<?=SITE_URL.'admin/wl_users/'.$a->author_edit?>"><?=$a->user_name?></a></td>
										<td><?=date("d.m.Y H:i", $a->date_edit)?></td>
										<td style="background-color:<?=($a->active == 1)?'green':'red'?>;color:white"><?=($a->active == 1)?'активний':'відключено'?></td>
										<td>
											<form method="POST" action="<?=SITE_URL.'admin/'.$_SESSION['alias']->alias?>/changeposition">
												<input type="hidden" name="id" value="<?=$a->id?>">
												<input type="number" name="position" min="1" max="<?=$max?>" value="<?=$a->position?>" onchange="this.form.submit();" autocomplete="off" class="form-control">
											</form>
										</td>
									</tr>
							<?php } } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
	function changeCategory(e, id){
		$.ajax({
			url: "<?=SITE_URL.$_SESSION['alias']->alias?>/changeGroup",
			type: 'POST',
			data: {
				group :  e.value,
				id :  id,
				json : true
			},
			success: function(res){
				if(res['result'] == false){
					alert('Помилка! Спробуйте щераз');
				}
			}
		});
	}
</script>

<style type="text/css">
	input[type="number"]{
		min-width: 50px;
	}
	select {
		max-width: 200px;
	}
</style>
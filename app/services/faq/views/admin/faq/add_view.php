<div class="row">
    <div class="col-md-6">
        <div class="panel panel-inverse">
            <div class="panel-heading">
            	<?php if($_SESSION['option']->useGroups){ ?>
                	<div class="panel-heading-btn">
						<a href="<?=SITE_URL.'admin/'.$_SESSION['alias']->alias?>/all" class="btn btn-info btn-xs">До всіх питань</a>
						<a href="<?=SITE_URL.'admin/'.$_SESSION['alias']->alias?>/groups" class="btn btn-info btn-xs">До всіх груп</a>
                	</div>
                <?php } ?>
                <h4 class="panel-title">Додати питання</h4>
            </div>
            <div class="panel-body">
            	<form action="<?=SITE_URL.'admin/'.$_SESSION['alias']->alias?>/save_question" method="POST">
					<input type="hidden" name="id" value="0">
	                <div class="table-responsive">
	                    <table class="table table-striped table-bordered nowrap" width="100%">
							<?php 
							if($_SESSION['option']->useGroups){
								$this->load->smodel('faq_model');
								$groups = $this->faq_model->getGroups();
								if($groups){
									echo "<tr><th>Оберіть групу</th><td>";
									echo('<select name="group" class="form-control">');
										echo ('<option value="0">Немає</option>');
										foreach ($groups as $group) {
											$selected = '';
											if($this->data->get('group') == $group->id) $selected = 'selected';
											echo ("<option value=\"{$group->id}\" {$selected}>{$group->name}</option>");
										}
									echo('</select>');
									echo "</td></tr>";
								} else { ?>
									<div class="note note-info">
										<h4>Увага! В налаштуваннях адреси не створено жодної групи!</h4>
										<p>
										    <a href="<?=SITE_URL.'admin/'.$_SESSION['alias']->alias?>/add_group">Додати групу</a>
		                                </p>
									</div>
							<?php }
							}
							if($_SESSION['language']) foreach ($_SESSION['all_languages'] as $lang) { ?>
								<tr>
									<th>Питання <?=$lang?></th>
									<td><input type="text" name="name_<?=$lang?>" value="" class="form-control" required></td>
								</tr>
							<?php } else { ?>
								<tr>
									<th>Питання</th>
									<td><input type="text" name="name" value="" class="form-control" required></td>
								</tr>
							<?php } ?>
							<tr>
								<td></td>
								<td><input type="submit" class="btn btn-sm btn-success" value="Додати"></td>
							</tr>
	                    </table>
	                </div>
	            </form>
            </div>
        </div>
    </div>
</div>
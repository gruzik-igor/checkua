<?php
    $this->db->select('wl_aliases as a', 'alias, admin_ico');
    $where = array('alias' => '#a.id', 'content' => 0);
    if($_SESSION['language']) $where['language'] = $_SESSION['language'];
    $this->db->join('wl_ntkd', 'name', $where);
    $this->db->join('wl_sitemap', 'time, changefreq, priority', $where);
	$wl_aliases = $this->db->get('array');

?>

<div class="row">
    <div class="col-md-12">
        <div class="panel panel-inverse">
            <div class="panel-heading">
                <div class="panel-heading-btn">
                    <a href="<?=SITE_URL?>admin/wl_ntkd/global_metatags" class="btn btn-success btn-xs"><i class="fa fa-globe"></i> Загальні Meta-теги</a>
                </div>
                <h4 class="panel-title">Наявні адреси:</h4>
            </div>
            <div class="panel-body">
                <div class="table-responsive">
                    <table id="data-table" class="table table-striped table-bordered nowrap" width="100%">
                        <thead>
                            <tr>
								<th>Адреса</th>
                                <th>Назва</th>
                                <th>Остання зміна<?=($_SESSION['language']) ? ' ('.$_SESSION['language'].')' : ''?></th>
                                <th>Частота оновлення<?=($_SESSION['language']) ? ' ('.$_SESSION['language'].')' : ''?></th>
								<th>Пріорітетність<?=($_SESSION['language']) ? ' ('.$_SESSION['language'].')' : ''?> [0..1]</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php if($wl_aliases) foreach ($wl_aliases as $alias) { ?>
							<tr>
								<td><a href="<?=SITE_URL.'admin/wl_ntkd/'.$alias->alias?>"><?=($alias->admin_ico) ? '<i class="fa '.$alias->admin_ico.'"></i>' : ''?> <?=$alias->alias?></a></td>
                                <td><?=$alias->name?></td>
                                <td><?=($alias->time)?date('d.m.Y H:i', $alias->time):'Не індексовано'?></td>
                                <?php if($alias->priority < 0) echo('<td colspan="2">Сторінка не індексується</td>'); else { ?>
                                    <td><?=$alias->changefreq?></td>
    								<td><?=$alias->priority/10?></td>
                                <?php } ?>
							</tr>
						<?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
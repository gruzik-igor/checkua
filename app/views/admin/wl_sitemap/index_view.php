<!-- begin row -->
<div class="row">
    <!-- begin col-12 -->
    <div class="col-md-12">
        <div class="panel panel-inverse">
            <div class="panel-heading">
                <div class="panel-heading-btn">
                	<a href="<?=SITE_URL?>admin/wl_sitemap/generate" class="btn btn-warning btn-xs"><i class="fa fa-refresh"></i> Генерувати карту сайту</a>
                    <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-default" data-click="panel-expand"><i class="fa fa-expand"></i></a>
                </div>
                <h4 class="panel-title">Список всіх адрес, за якими відбувалися заходи на сайт</h4>
            </div>
            <div class="panel-body">
                <div class="table-responsive">
                    <table id="data-table" class="table table-striped table-bordered" width="100%">
                        <thead>
                            <tr>
                                <th><input type="checkbox" value="all"></th>
                                <th width="100px"></th>
                                <th>Адреса</th>
                                <th>Код відповіді</th>
                                <?php if($_SESSION['language']) { ?>
                                    <th>Мова</th>
                                <?php } ?>
                                <th>Частота</th>
                                <th>Пріорітет [0..1]</th>
                                <th>Оновлено</th>
                                <th>Кеш</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php if($sitemap)
                            foreach ($sitemap as $map) { ?>
                                <tr>
                                    <td><input type="checkbox" value="<?=$map->id?>" class="sitemap-multiedit"></td>
                                    <td><a href="<?=SITE_URL?>admin/wl_sitemap/<?=$map->id?>" class="btn btn-xs btn-default"><i class="fa fa-pencil"></i> Редагувіти <i class="fa fa-signal"></i> Статистика</a></td>
                                    <td>
                                        <i class="fa fa-<?=($map->alias > 0)?'check':'times'?>"></i> 
                                        <?=$map->link?>
                                    </td>
                                    <td><?=$map->code?></td>
                                    <?php if($_SESSION['language']) { ?>
                                        <td><?=$map->language?></td>
                                    <?php } if($map->alias == 0 || $map->priority < 0) echo('<td colspan="2">Сторінка не індексується</td>'); else { ?>
                                        <td><?=$map->changefreq?></td>
                                        <td><?=$map->priority/10?></td>
                                    <?php } ?>
                                    <td><?=($map->time)?date('d.m.Y H:i', $map->time):'інформація відсутня'?></td>
                                    <td><?=$map->id?></td>
                                </tr>
                        <?php } ?>
                        </tbody>
                    </table>
                    <?php
                    $this->load->library('paginator');
                    echo $this->paginator->get();
                    ?>
                </div>
            </div>
        </div>
    </div>
    <!-- end col-12 -->
</div>
<!-- end row -->


<link href="<?=SITE_URL?>assets/DataTables/css/data-table.css" rel="stylesheet" />
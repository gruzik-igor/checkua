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
                                <th><input type="checkbox" id="all" onchange="selectAll(this)"></th>
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
                                    <td><input type="checkbox" id="<?=$map->id?>" class="sitemap-multiedit" onChange="setEditPoint('<?=$map->id?>')"></td>
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
                    $_SESSION['alias']->js_load[] = 'assets/switchery/switchery.min.js';
                    $_SESSION['alias']->js_load[] = 'assets/white-lion/sitemap.js';
                    ?>
                </div>
                <form action="<?=SITE_URL?>admin/wl_sitemap/multi_edit" class="form-bordered" method="POST" onSubmit="return multi_edit();">
                    <input type="hidden" id="sitemap-ids" name="sitemap-ids" required="required">
                    <div class="col-md-6">
                        <div class="form-group">
                            <div class="col-md-6">
                                <label class="control-label">Оновити <strong>Код відповіді</strong></label>
                                <input type="checkbox" data-render="switchery" value="1" id="active-code" name="active-code" onChange="setActive(this, 'code')" />
                            </div>
                            <div class="col-md-6">
                                <select name="code" id="field-code" class="form-control" onChange="setCode()" disabled="disabled">
                                    <option value="200">200 Cache активний</option>
                                    <option value="201">200 Cache НЕ активний</option>
                                    <option value="404">404 Адреса недоступна</option>
                                </select>
                            </div>
                            <div class="clear"></div>
                        </div>
                        <div class="form-group">
                            <div class="col-md-8">
                                <label class="control-label">Оновити <strong>Сторінка включена до індексації</strong></label>
                                <input type="checkbox" data-render="switchery" value="1" id="active-index" name="active-index" onChange="setActive(this, 'index')"/>
                            </div>
                            <div class="col-md-4">
                                <input type="checkbox" data-render="switchery" value="1" id="field-index" name="index" onChange="setIndex()" checked disabled="disabled"/>
                            </div>
                            <div class="clear"></div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <div class="col-md-7">
                                <label class="control-label">Оновити <strong>Частота оновлення</strong></label>
                                <input type="checkbox" data-render="switchery" value="1" id="active-changefreq" name="active-changefreq" onChange="setActive(this, 'changefreq')"/>
                            </div>
                            <div class="col-md-5">
                                <select name="changefreq" id="field-changefreq" class="form-control index" disabled="disabled">
                                    <?php $changefreq = array('always', 'hourly', 'daily', 'weekly', 'monthly', 'yearly', 'never');
                                        foreach ($changefreq as $freq) {
                                            echo('<option value="'.$freq.'">'.$freq.'</option>');
                                        }
                                        ?>
                                </select>
                            </div>
                            <div class="clear"></div>
                        </div>
                        <div class="form-group">
                            <div class="col-md-7">
                                <label class="control-label">Оновити <strong>Пріорітетність</strong></label>
                                <input type="checkbox" data-render="switchery" value="1" name="active-priority" id="active-priority" onChange="setActive(this, 'priority')"/>
                            </div>
                            <div class="col-md-5">
                                <input type="number" name="priority" id="field-priority" value="0.5" placeholder="0.5" min="0" max="1" step="0.1" class="form-control index" disabled="disabled">
                            </div>
                            <div class="clear"></div>
                        </div>
                    </div>
                    <?php if($_SESSION['language']) { ?>
                        <div class="form-group col-md-6">
                            <label class="col-md-6 control-label">Застосувати до всіх мов</label>
                            <div class="col-md-6">
                                <input type="checkbox" data-render="switchery" checked value="1" name="all_languages" />
                            </div>
                        </div>
                    <?php } ?>
                    <div class="form-group col-md-6">
                        <label class="col-md-3 control-label"></label>
                        <div class="col-md-9">
                            <button type="submit" class="btn btn-sm btn-success">Зберегти</button>
                            <button type="button" class="btn btn-sm btn-danger">Видалити</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- end col-12 -->
</div>
<!-- end row -->
<div class="modal fade" id="modal-notset">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title">Керування картою сайту SiteMap</h4>
            </div>
            <div class="modal-body">
                Увага! Оберіть адреси зі списку, до яких необхідно застосувати параметри.
            </div>
            <div class="modal-footer">
                <a href="javascript:;" class="btn btn-sm btn-white" data-dismiss="modal">Закрити</a>
            </div>
        </div>
    </div>
</div>


<link href="<?=SITE_URL?>assets/DataTables/css/data-table.css" rel="stylesheet" />
<link rel="stylesheet" href="<?=SITE_URL?>assets/switchery/switchery.min.css" />
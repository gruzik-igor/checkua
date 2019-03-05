<div class="row">
    <div class="col-md-12">
        <div class="panel panel-inverse">
            <div class="panel-heading">
                <div class="panel-heading-btn">
                    <a href="<?=SITE_URL.'admin/'.$_SESSION['alias']->alias?>/settings/shipping/add" class="btn btn-xs btn-warning"><i class="fa fa-plus"></i> Додати перевізника</a>
                </div>
                <h4 class="panel-title"><i class="fa fa-car"></i> Доставка (керування перевізниками)</h4>
            </div>
			<div class="panel-body">
                <div class="table-responsive">
                    <table id="data-table" class="table table-striped table-bordered nowrap" width="100%">
                        <thead>
                            <tr>
                                <th style="width: 50px"></th>
                            	<th>Перевізник</th>
                                <th>Тип доставки</th>
                            	<th>Вартість</th>
                            	<th>Інформація</th>
								<th>Стан</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if($shippings) foreach ($shippings as $shipping) { ?>
                                <tr id="shipping-<?=$shipping->id?>" <?=$shipping->active ? '' : 'class="danger"' ?>>
                                    <td class="move sortablehandle"><i class="fa fa-sort"></i></td>
                                    <td><a href="<?=SITE_URL.'admin/'.$_SESSION['alias']->alias.'/settings/shipping/'.$shipping->id?>"><?=$shipping->name?></a></td>
                                    <td><?php if($shipping->wl_alias)
                                                echo "Спеціальна (власні налаштування)";
                                              else if($shipping->type == 1)
                                                echo('за адресою');
                                              else if($shipping->type == 2)
                                                echo('у відділення');
                                              else if($shipping->type == 3)
                                                echo('без адреси');
                                     ?></td>
                                    <td><?php if($shipping->pay == -2)
                                                echo "Не виводити";
                                              else if($shipping->pay == -1)
                                                echo('безкоштовно');
                                              else
                                              {
                                                if($shipping->pay > 0)
                                                    echo('Ціна до ');
                                                echo($shipping->price.' y.o.');
                                              }
                                     ?></td>
                                    <td><?=$this->data->getShortText($shipping->info)?></td>
                                    <td>
                                        <input type="checkbox" data-render="switchery" <?=($shipping->active == 1) ? 'checked' : ''?> value="1" onchange="changeActive(this, 'shipping-<?=$shipping->id?>')" />
                                    </td>
                                </tr>
                            <?php } else { ?>
                                <tr><td colspan="6" class="text-center"><a href="<?=SITE_URL.'admin/'.$_SESSION['alias']->alias?>/settings/shipping/add" class="btn btn-xs btn-warning"><i class="fa fa-plus"></i> Додати перевізника</a></td></tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="panel panel-inverse">
            <div class="panel-heading">
                <div class="panel-heading-btn">
                    <a href="<?=SITE_URL.'admin/'.$_SESSION['alias']->alias?>/settings/payment/add" class="btn btn-xs btn-warning"><i class="fa fa-plus"></i> Додати просту оплату</a>
                </div>
                <h4 class="panel-title"><i class="fa fa-dollar"></i> Оплата</h4>
            </div>
            <div class="panel-body">
                <div class="table-responsive">
                    <table id="data-table" class="table table-striped table-bordered nowrap" width="100%">
                        <thead>
                            <tr>
                                <th style="width: 50px"></th>
                                <th>Оплата</th>
                                <th>Інформація</th>
                                <th>Стан</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if($payments) foreach ($payments as $pay) { ?>
                                <tr id="payment-<?=$pay->id?>" <?=$pay->active ? '' : 'class="danger"' ?>>
                                    <td class="move sortablehandle"><i class="fa fa-sort"></i></td>
                                    <td><a href="<?=SITE_URL.'admin/'.$_SESSION['alias']->alias.'/settings/payment/'.$pay->id?>"><?=$pay->name?></a></td>
                                    <td><?=$this->data->getShortText($pay->info)?></td>
                                    <td>
                                        <input type="checkbox" data-render="switchery" <?=($pay->active == 1) ? 'checked' : ''?> value="1" onchange="changeActive(this, 'payment-<?=$pay->id?>')" />
                                    </td>
                                </tr>
                            <?php } else { ?>
                                <tr><td colspan="4" class="text-center"><a href="<?=SITE_URL.'admin/'.$_SESSION['alias']->alias?>/settings/payment/add" class="btn btn-xs btn-warning"><i class="fa fa-plus"></i> Додати просту оплату</a></td></tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="panel panel-inverse panel-info">
            <div class="panel-heading">
                <div class="panel-heading-btn">
                    <a href="<?= SITE_URL.'admin/wl_aliases/'.$_SESSION['alias']->alias?>" class="btn btn-warning btn-xs"><i class="fa fa-cogs"></i> Додаткові налаштування</a>
                </div>
                <h4 class="panel-title">Формат виводу ціни (<strong>Перед ціною</strong> <i>ціна (число)</i> <strong>Після ціни</strong>)</h4>
            </div>
            <div class="panel-body">
                <?php $before = $after = '';
                $round = 2;
                if(!empty($_SESSION['option']->price_format))
                {
                    $price_format = unserialize($_SESSION['option']->price_format);
                    if(isset($price_format['before']))
                        $before = $price_format['before'];
                    if(isset($price_format['after']))
                        $after = $price_format['after'];
                    if(isset($price_format['round']))
                        $round = $price_format['round'];
                } ?>
                <form action="<?=SITE_URL?>admin/<?=$_SESSION['alias']->alias?>/save_price_format" method="POST" class="form-horizontal">
                    <input type="hidden" name="service" value="<?=$_SESSION['service']->id?>">
                    <div class="form-group">
                        <label class="col-md-4 control-label">Перед ціною</label>
                        <div class="col-md-8">
                            <input type="text" name="before" value="<?=$before?>" class="form-control">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-4 control-label">Точність ціни <br><small>Кількість знаків після коми, копійки</small></label>
                        <div class="col-md-8">
                            <input type="number" name="round" value="<?=$round?>" class="form-control" min="0" max="2">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-4 control-label">Після ціни</label>
                        <div class="col-md-8">
                            <input type="text" name="after" value="<?=$after?>" class="form-control">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-4 control-label"></label>
                        <div class="col-md-8">
                            <input type="submit" class="btn btn-success" value="Зберегти">
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php $_SESSION['alias']->js_load[] = 'js/'.$_SESSION['alias']->alias.'/admin_settings.js';
      $_SESSION['alias']->js_load[] = 'assets/switchery/switchery.min.js'; ?>
<link rel="stylesheet" href="<?=SITE_URL?>assets/switchery/switchery.min.css" />
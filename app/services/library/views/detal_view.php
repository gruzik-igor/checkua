<div class="intro">
    <div class="dtable hw100">
        <div class="dtable-cell hw100">
            <div class="container text-center">
                <h1 class="intro-title animated fadeInDown"> Пошук автозапчастин </h1>
                <p class="sub animateme fittext3 animated fadeIn">KIA & HYUNDAI Оригінали та Замінники</p>
                <div class="row search-row animated fadeInUp">
                    <form action="<?=SITE_URL?>parts/search">
                        <div class="col-lg-8 col-sm-8 search-col relative locationicon">
                            <i class="fa fa-pencil icon-append"></i>
                            <input type="text" name="article" class="form-control locinput input-rel searchtag-input has-icon" placeholder="Артикул" value="<?=$product->article?>" required="required">
                        </div>
                        <div class="col-lg-4 col-sm-4 search-col">
                            <button class="btn btn-primary btn-search btn-block"><i class="fa fa-search"></i><strong> Знайти</strong></button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="container">
    <div class="row">
        <div class="col-sm-9 page-content col-thin-right">
            <div class="inner-box ads-details-wrapper">
                <h2>
                    <small class="label label-default adlistingtype"><?=$product->article?></small>
                    <?= html_entity_decode($product->name)?> 
                    <?php if(isset($product->options['1-vyrobnyk']) && $product->options['1-vyrobnyk']->value != '') { ?>
                        <small class="label label-default adlistingtype"><?=nl2br($product->options['1-vyrobnyk']->value)?></small>
                    <?php } ?>
                </h2>
                <span class="info-row"> <span class="date"><i class=" icon-clock"> </i> <?=date('d.m.Y H:i', $product->date_edit)?> by <?=$product->user_name?></span> - <span class="category"><?=$product->group_name?> </span> </span>
                
                <?php if(trim($product->text) != '') { ?>
                    <div class="ads-details-info col-md-8">
                        <?= html_entity_decode($product->text)?>
                    </div>
                <?php }
                if($this->userIs())
                {
                    $cooperation = $this->db->getAllDataByFieldInArray('wl_aliases_cooperation', $_SESSION['alias']->id, 'alias1');
                    if($cooperation)
                    {
                        $showStorages = true;
                        foreach ($cooperation as $storage) {
                            if($storage->type == 'storage')
                            {
                                $invoices = $this->load->function_in_alias($storage->alias2, '__get_Invoices_to_Product', $product->id);
                                if($invoices)
                                {
                                    foreach ($invoices as $invoice) {
                                        if($showStorages)
                                        {
                                            echo('<div><table class="table">');
                                            echo("<tr>");
                                            echo("<td style='width:25%'>Артикул</td>");
                                            echo("<td style='width:25%'>Ціна</td>");
                                            echo("<td style='width:25%'>Термін</td>");
                                            echo("<td style='width:25%'>Додати до корзини</td>");
                                            echo("</tr>");
                                            $showStorages = false;
                                        }
                                        echo("<tr>");
                                        echo("<td>{$invoice->storage_name}</td>");
                                        echo("<td>{$invoice->price_out}</td>");
                                        echo("<td>{$invoice->storage_time}</td>");
                                        echo("<td><button onclick=\"cart.add({$invoice->product}, {$_SESSION['alias']->id}, {$invoice->id}, {$invoice->storage})\">Додати до корзини</button></td>");
                                        echo("</tr>");
                                    }
                                }
                            }
                        }
                        if(!$showStorages)
                        {
                            echo("</table></div>");
                        }
                    }
                }
                if(isset($product->options['33-analohy']) && $product->options['33-analohy']->value != '')
                {
                    echo("<h2> Аналоги для ".html_entity_decode($product->name).'</h2>');
                    echo('<div><table class="table">');
                    echo("<tr>");
                    echo("<td>Артикул</td>");
                    echo("<td>Назва</td>");
                    echo("<td>Виробник</td>");
                    echo("<td>Ціна</td>");
                    echo("</tr>");
                    $analogs = explode(';', $product->options['33-analohy']->value);
                    foreach ($analogs as $analog) {
                        $analog = $this->shop_model->getProduct($analog, 'article');
                        if($analog && $analog->price > 0)
                        {
                            echo("<tr>");
                            $analog->link = SITE_URL.$analog->link;
                            echo("<td><a href=\"{$analog->link}\">{$analog->article}</a></td>");
                            echo("<td><a href=\"{$analog->link}\">{$analog->name}</a></td>");
                            if(isset($product->options['1-vyrobnyk']))
                            {
                                echo("<td>{$product->options['1-vyrobnyk']->value}</td>");
                            }
                            echo("<td>{$analog->price}</td>");
                            echo("</tr>");
                        }
                        
                    }
                    echo("</table></div>");
                }
                ?>
            </div>
        </div>
        <div class="col-sm-3 page-sidebar-right">
            <div class="headline"><h2><?=$this->text('Вартість: ')?>$<?= $product->price?></h2></div>
            <button onclick="cart.add(<?= $product->id.', '.$product->wl_alias?>)">Додати до корзини</button>
        </div>
    </div>
</div>
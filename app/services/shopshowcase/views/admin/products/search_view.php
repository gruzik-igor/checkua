<?php require_once APP_PATH.'services'.DIRSEP.$_SESSION['service']->name.DIRSEP.'views'.DIRSEP.'admin'.DIRSEP.'__search_subview.php'; ?>

<div class="row">
    <div class="col-md-12">
        <div class="panel panel-inverse">
            <div class="panel-heading">
                <div class="panel-heading-btn">
                	<a href="<?=SITE_URL.'admin/'.$_SESSION['alias']->alias?>/add<?=(isset($group))?'?group='.$group->id:''?>" class="btn btn-warning btn-xs"><i class="fa fa-plus"></i> <?=$_SESSION['admin_options']['word:product_add']?></a>
					
                    <?php if($_SESSION['option']->useGroups == 1) { ?>
						<a href="<?=SITE_URL.'admin/'.$_SESSION['alias']->alias?>/all" class="btn btn-info btn-xs">До всіх <?=$_SESSION['admin_options']['word:products_to_all']?></a>
						<a href="<?=SITE_URL.'admin/'.$_SESSION['alias']->alias?>/groups" class="btn btn-info btn-xs">До всіх <?=$_SESSION['admin_options']['word:groups_to_all']?></a>
					<?php } ?>

					<a href="<?=SITE_URL.'admin/'.$_SESSION['alias']->alias?>/options" class="btn btn-info btn-xs">До всіх <?=$_SESSION['admin_options']['word:options_to_all']?></a>

					<a href="<?=SITE_URL.'admin/wl_ntkd/'.$_SESSION['alias']->alias?><?=(isset($group))?'/-'.$group->id:''?>" class="btn btn-info btn-xs">SEO</a>
                </div>
                <h4 class="panel-title"><?=$_SESSION['alias']->name .'. Пошук '.$_SESSION['admin_options']['word:products_to_all']?></h4>
            </div>
            <div class="panel-body">
                <div class="table-responsive">
                    <table id="data-table" class="table table-striped table-bordered nowrap" width="100%">
                        <thead>
                            <tr>
                                <th><?=($_SESSION['option']->ProductUseArticle) ? 'Артикул' : 'Id'?></th>
								<th>Бренд</th>
								<th>Назва</th>
								<th>Постачальник</th>
								<th>Термін</th>
								<?php
								$groups = $this->db->getAllDataByFieldInArray('wl_user_types', 1, 'active');
								foreach($groups as $group) 
				                    if($group->id > 2)
				                        echo("<th>Ціна для {$group->title}</th>");
								 ?>
								<th>Наявна кількість</th>
								<th>Доступна кількість</th>
                            </tr>
                        </thead>
                        <tbody>
                        	<?php
                        	$currency_USD = $this->load->function_in_alias('currency', '__get_Currency', 'USD');

                        	if($products && $cooperation)
                        	{ 
				                foreach ($products as $product) {
				                    $count = 0;
				                    echo("<tr>");
				                    echo('<td><a href="'.SITE_URL.'admin/'.$product->link.'">'.$product->article.'</a></td>');
                                    echo "<td>". ((isset($product->options['1-vyrobnyk']) && $product->options['1-vyrobnyk']->value != '') ? nl2br($product->options['1-vyrobnyk']->value) : '')."</td>";
                                    echo '<td><a href="'.SITE_URL.'admin/'.$product->link.'">'.html_entity_decode($product->name).'</a></td>';

				                    foreach ($cooperation as $storage) {
				                        if($storage->type == 'storage')
				                        {
				                            $invoice_where = array('id' => $product->id, 'user_type' => -1);
				                            $invoices = $this->load->function_in_alias($storage->alias2, '__get_Invoices_to_Product', $invoice_where);
				                            if($invoices)
				                            {
				                                foreach ($invoices as $invoice) {
			                                        if($count > 0)
			                                            echo("</tr><tr><td></td><td></td><td></td>");

			                                        echo("<td>{$invoice->storage_name}</td>");
			                                        echo("<td>{$invoice->storage_time}</td>");
			                                        
			                                        $price_out = unserialize($invoice->price_out);
										            foreach($groups as $group) 
										                if($group->id > 2)
										                {
										                    $price_out_uah = round($price_out[$group->id] * $currency_USD, 2);
										                    echo("<td>\${$price_out[$group->id]}<br>");
										                    echo("<strong>{$price_out_uah} грн</strong></td>");
										                }
										            echo("<td>{$invoice->amount}</td>");
										            echo("<td><strong>{$invoice->amount_free}</strong></td>");

										            $count++;
				                                }
				                            }
				                        }
				                    }

				                    if($count == 0)
			                    	{
			                    		$count = 4;
										foreach($groups as $group) 
						                    if($group->id > 2) $count++;
			                    		echo "<td colspan={$count}><strong>Товар на складі відсутній</strong></td>";
			                    	}
				                    echo("</tr>");
				                }
							} 
							else
							{
								$count = 7;
								foreach($groups as $group) 
				                    if($group->id > 2) $count++;
								echo "<tr><td colspan={$count}>Товар за артикулом <strong>".$this->data->get('article')."</strong> не знайдено</td></tr>";
							}
							?>
                        </tbody>
                    </table>
                </div>
                <a href="https://privatbank.ua/" target="_blank" class="pull-right">1 USD = <?=$currency_USD?> UAH</a>
                <?php
                $this->load->library('paginator');
                echo $this->paginator->get();
                ?>
            </div>
        </div>
    </div>
</div>
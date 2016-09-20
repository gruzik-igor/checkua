<?php
$showStorages = true;
$count_products = 0;
$groups = $this->db->getAllDataByFieldInArray('wl_user_types', 1, 'active');
$currency_USD = $this->load->function_in_alias('currency', '__get_Currency', 'USD');

foreach ($storages as $storage) {
    $invoice_where = array('id' => $product->id, 'user_type' => -1);
    $invoices = $this->load->function_in_alias($storage, '__get_Invoices_to_Product', $invoice_where);
    if($invoices)
    {
        foreach ($invoices as $invoice) {
            if($showStorages)
            {
                echo('<div class="table-responsive"><table class="table table-condensed table-bordered">');
                echo("<tr>");
                echo("<td>Постачальник</td>");
                echo("<td>Термін</td>");
                foreach($groups as $group) 
                    if($group->id > 2)
                        echo("<td>Ціна для {$group->title}</td>");
                echo("<td>Наявна кількість</td>");
                echo("<td>Доступна кількість</td>");
                echo("<td></td>");
                echo("</tr>");
                $showStorages = false;
            }
            echo("<tr>");
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
            echo("<td></td>");
            echo("</tr>");
            $count_products++;
        }
    }
}
if(!$showStorages)
{
    echo("</table></div>");
}
if($count_products == 0)
{
    ?>
    <div class="alert alert-danger">
        <h4><?=$product->article?> <?=$product->name?></h4>
        <p>Увага! Товар відсутній на складі.</p>
    </div>
<?php
}
?>
<a href="https://privatbank.ua/" target="_blank" class="pull-right">1 USD = <?=$currency_USD?> UAH</a>
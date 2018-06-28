<?php

// --- cart manager change status. Info mail to client --- //

/* Вхідні дані
   data[
	id - номер замовлення
	user_name - ім'я користувача
	user_email - email користувача
	user_phone - код підтвердження
   ]
*/


$subject = $data['status_name'].': Замовлення # '.$data['id'].' від '.$data['date'] . ' '.SITE_NAME;
$message = '<html><head><title>Замовлення #'.$data['id']. ' '.SITE_NAME.'</title></head><body><p>Доброго дня <b>'.$data['user_name'].'</b>!</p><h1>Замовлення #'.$data['id'].' від '.$data['date'].'</h1><p>Оновлену інформацію по Вашому замовлення представлено нижче:</p>';

$message .= '<h3><b>Поточний статус: </b>'.$data['status_name'].'</h3>';
$message .= '<p><b>Оновлено: </b>'.$data['date'];
if($data["comment"] != '')
	$message .= "<br><b>Супроводжуюча інформація:</b> {$data["comment"]}";
$message .= '</p>';

$message .= '<h3><b>Покупець</b></h3>';
$message .= '<p><b>'.$data['user_name'].'</b><br> '.$data['user_email'].', '.$data['user_phone'].'</p>';

if(!empty($data['delivery']))
{
	$message .= '<h3><b>Доставка</b></h3>';
	$message .= '<p>'.$data['delivery'].'</p>';
}

if(!empty($data['info']))
{
	$message .= '<h3><b>Коментар до замовлення</b></h3>';
	$message .= '<p>'.$data['info'].'</p>';
}

$message .= '<h3><b>Замовлення</b></h3>';
$message .= '<table align="center" border="2" cellpadding="5" cellspacing="3" width="100%" style="border-collapse: collapse;">
                    <thead><tr><th></th><th width="65%">Продукт</th><th width="10%">Ціна</th><th width="10%">К-сть</th><th width="10%">Разом</th></tr></thead><tbody>';

$i = 1;
foreach($data['products'] as $product){
    $message .=  '<tr>
                    <td>'. $i .'</td>
                    <td>'. $product->info->name;
					if(!empty($product->product_options))
					{
						$product->product_options = unserialize($product->product_options);
						foreach ($product->product_options as $key => $value) {
							$message .= "<br>{$key}: <strong>{$value}</strong>";
						}
					}
    $message .= '</td>
                    <td>'. $product->price .'</td>
                    <td>'. $product->quantity .'</td>
                    <td>'. $product->sum .'</td>
                </tr>';
    $i++;
}

$message .= '<tr><td colspan="5" align="right">Сума: '.$data['total_formatted'].'</td></tr></tbody></table>';
$message .= '<p><a href="'.$data['link'].'">Щоб подивитися замовлення детальніше, перейдіть по посиланню.</a></p>';

if($data['action'] == 'new')
	$message .= "<p>Для онлайн оплати через Приват24 або Liqpay перейдіть <a href=\"{$data['pay_link']}\">{$data['pay_link']}</a></p>";

$message .= '<p>З найкращими побажаннями, адміністрація '.SITE_NAME.'</p></body></html>';

?>


                
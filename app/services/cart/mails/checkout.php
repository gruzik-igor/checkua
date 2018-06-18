<?php

// --- cart checkout mail --- //

/* Вхідні дані
   data[
	id - номер замовлення
	user_name - ім'я користувача
	user_email - email користувача
	user_phone - код підтвердження
   ]
*/

$subject = 'Замовлення #'.$data['id']. ' '.SITE_NAME;
$message = '<html><head><title>Замовлення #'.$data['id']. ' '.SITE_NAME.'</title></head><body><p>Доброго дня <b>'.$data['user_name'].'</b>!</p><p>Дякуємо за покупку в нашому магазині. Інформація про ваше замовлення представлена нижче.</p>';

$message .= '<h3><b>Покупець</b></h3>';
$message .= '<p><b>'.$data['user_name'].'</b><br> '.$data['user_email'].', '.$data['user_phone'].'</p>';
if($data['new_user'] && !empty($data['password']))
	$message .= '<p>Ваш пароль до персонального кабінету: <b>'.$data['password'].'</b></p>';

if(!empty($data['delivery']))
{
	$message .= '<h3><b>Доставка</b></h3>';
	$message .= '<p><b>'.$data['delivery']->method_name.'</b> '.$data['delivery']->method_site;
	if(!empty($data['delivery']->address) && $data['delivery']->department < 2)
		$message .= '<br><b>'.$data['delivery']->address.'</b>';
	$message .= '</p>';
}

if(!empty($data['payment']))
	$message .= '<p>Платіжний механізм: <b>'.$data['payment'].'</b></p>';

if(!empty($data['comment']))
{
	$message .= '<h3><b>Коментар</b></h3>';
	$message .= '<p>'.$data['comment'].'</p>';
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
						if(!is_array($product->product_options))
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
$message .= '<p><a href="'.$data['link'].'">Щоб подивитися замовлення, перейдіть по посиланні нижче.</a></p>';

$message .= '<p>З найкращими побажаннями, адміністрація '.SITE_NAME.'</p></body></html>';
?>


                
<?php
/**
 * Список бібліотек які будуть завантаження за замовчуванням
 */
$config['autoload'] = array('db', 'data');
$config['recaptcha'] = array('public' => 'RECAPTCHA_PUBLIC_KEY', 'secret' => 'RECAPTCHA_SECRET_KEY');

/**
 * Параметри для з'єднання до БД
 */
$config['db'] = array(
	'host' 		=> '$HOST',
	'user' 		=> '$USER',
	'password'	=> '$PASSWORD',
	'database'	=> '$DATABASE'
);

$config['Paginator'] = array(
	'ul'		=> 'pagination nomargin'
);
$config['video'] = array(
	'width'		=> 737
);

?>

<?php
/**
 * Список бібліотек які будуть завантаження за замовчуванням
 */
$config['autoload'] = array('db', 'data');
$config['recaptcha'] = array('public' => '6LdVVgwTAAAAAJhk9NTB3lGZZVzwB0FJfT4iJ0q-', 'secret' => '6LdVVgwTAAAAAHlNTNW12X5_tqrqWbvD3bEQ_Ixo');

/**
 * Параметри для з'єднання до БД
 */
$config['db'] = array(
	'host' 		=> 'localhost',
	'user' 		=> 'root',
	'password'	=> '',
	'database'	=> 'whitelion.cms'
);

$config['Paginator'] = array(
	'ul'		=> 'pagination nomargin'
);
$config['video'] = array(
	'width'		=> 737
);

?>

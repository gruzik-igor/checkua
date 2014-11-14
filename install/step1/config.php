<?php
/**
 * Список бібліотек які будуть завантаження за замовчуванням
 */
$config['autoload'] = array('db', 'data');

/**
 * Параметри для з'єднання до БД
 */
$config['db'] = array(
	'host' 		=> '$HOST',
	'user' 		=> '$USER',
	'password'	=> '$PASSWORD',
	'database'	=> '$DATABASE'
);

?>

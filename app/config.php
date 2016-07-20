<?php
 
/**
 * Список бібліотек які будуть завантаження а замовчуванням
 */
$config['autoload'] = array('db', 'data');
$config['recaptcha'] = array('public' => '6LeOgR0TAAAAAPQ3izhIWy5wmrmvCtMU94cnQ0fd', 'secret' => '6LeOgR0TAAAAAIrQJ5YYuH_Aful886Qkavojiq0Z');
$config['facebook'] = array(
  'appId'  => '1280962155267129',
  'secret' => 'f0da32f9b4fa41ed5cfdb0d4c7352486',
);

/**
 * Параметри для з'єднання до БД
 */
$config['db'] = array(
	'host' 		=> 'localhost',
	'user' 		=> 'root',
	'password'	=> '',
	'database'	=> 'ipsc-shooting.com'
);

$config['Paginator'] = array(
	'ul'		=> 'pagination nomargin'
);
$config['video'] = array(
	'width'		=> 737
);
?>
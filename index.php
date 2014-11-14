<?php

//--- CMS White Lion 1.2.1 ---//

session_start();

error_reporting(E_ALL);

//показники на початку виконання скрипта
$time_start = microtime(true);
$mem_start = memory_get_usage();

$LOCAL_SITE_URL = '';
if(file_exists('LOCAL_SITE_URL.php')) {
	require_once 'LOCAL_SITE_URL.php';
} 
//задаєм системні константи
$sys_folder = 'system';
$app_folder = 'app';
$cache_folder = 'cache';

$_SESSION['cache'] = false; // використання кешованих даних
//Після інсталяції НЕ ЗМІНЮВАТИ!
$_SESSION['language'] = false; // Якщо false то сайт НЕ мультимовний! Якщо local - режим мультимовності на локальному сервері (береться перша мова від списку всіх мов). В іншому випадку мова за замовчуванням
$_SESSION['all_languages'] = array(); // список всіх  мов, перша мова - мова по замовчуванню
$_SESSION['option'] = null;

define('DIRSEP', DIRECTORY_SEPARATOR);
define('SYS_PATH', $sys_folder.DIRSEP);
define('APP_PATH', $app_folder.DIRSEP);
define('CACHE_PATH', getcwd() . DIRSEP .$cache_folder.DIRSEP);
define('SITE_URL', 'http://'.$_SERVER["SERVER_NAME"].'/'.$LOCAL_SITE_URL);
define('SITE_NAME', $_SERVER["SERVER_NAME"]);
define('SITE_EMAIL', 'ostap.matskiv@gmail.com');
define('SYS_EMAIL', 'ostap.matskiv@gmail.com');
define('IMG_PATH', SITE_URL.'images/');

define('SYS_PASSWORD', '');


unset($sys_folder, $app_folder, $cache_folder);

if (file_exists('install/index.php')){
	// Load the installation check
	return include 'install/index.php';
	exit;
}
require SYS_PATH.'base'.DIRSEP.'framework.php';

if(empty($_POST) && empty($_GET['ajax'])){
	$mem_end = memory_get_usage();
	$time_end = microtime(true);
	$time = $time_end - $time_start;
	$mem = $mem_end - $mem_start;
	$mem = round($mem/1024, 5);
	echo '<div class="clear"></div><div style="color:black; text-align:center">Час виконання(сек): '.$time.' Використанок памяті(кб): '.$mem.'</div>';
}
?>
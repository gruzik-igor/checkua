<?php

//--- CMS White Lion 1.0 ---//

$https = false;
if($https && (empty($_SERVER['HTTPS']) || $_SERVER['HTTPS'] == "off"))
{
	$request = '/';
	if(isset($_SERVER['REQUEST_URI'])) $request = $_SERVER['REQUEST_URI'];
    $redirect = 'https://' . $_SERVER['HTTP_HOST'] . $request;
    header('HTTP/1.1 301 Moved Permanently');
    header('Location: ' . $redirect);
    exit();
}

$time_start = microtime(true);
$mem_start = memory_get_usage();

session_start();

error_reporting(E_ALL);

//Після інсталяції НЕ ЗМІНЮВАТИ!
define('SITE_EMAIL', 'info@whitelion.cms'); // Від даної пошти сайт відправляє листи
define('SYS_PASSWORD', 'e1ac1a5a4ed3'); // Сіль для кешування критичних даних (паролі)
$useWWW = false; // Автовиправлення ОСНОВНОЇ адреси (не мультомовної якщо використовується піддомен)
$multilanguage_type = false; // Якщо false то сайт НЕ мультимовний! може бути: false, "*.domain.com.ua" (адреса по головному домену, існування піддоменів мов на роботу не впливає), 'main domain' (мультимовність site.com/en/link..)
$_SESSION['all_languages'] = array(); // Список всіх  мов в масиві, перша мова - мова по замовчуванню
$_SESSION['cache'] = false; // використання кешованих даних
$images_folder = 'images';

//задаєм системні константи
define('DIRSEP', DIRECTORY_SEPARATOR);
define('SYS_PATH', getcwd() . DIRSEP.'system'.DIRSEP);
define('APP_PATH', getcwd() . DIRSEP.'app'.DIRSEP);
define('CACHE_PATH', getcwd() . DIRSEP.'cache'.DIRSEP);

require SYS_PATH.'base'.DIRSEP.'framework.php';

?>
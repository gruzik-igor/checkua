<?php

//--- CMS White Lion 1.0 ---//

$time_start = microtime(true);
$mem_start = memory_get_usage();

session_start();

error_reporting(E_ALL);

//Після інсталяції НЕ ЗМІНЮВАТИ!
define('SITE_EMAIL', '#SITE_EMAIL'); // Від даної пошти сайт відправляє листи
define('SYS_PASSWORD', '#SYS_PASSWORD'); // Сіль для кешування критичних даних (паролі)
$useWWW = #useWWW; // Автовиправлення ОСНОВНОЇ адреси (не мультомовної якщо використовується піддомен)
$multilanguage_type = #multilanguage_type; // Якщо false то сайт НЕ мультимовний! може бути: false, "*.domain.com.ua" (адреса по головному домену, існування піддоменів мов на роботу не впливає), 'main domain' (мультимовність site.com/en/link..)
$_SESSION['all_languages'] = array(#all_languages); // Список всіх  мов в масиві, перша мова - мова по замовчуванню
$_SESSION['cache'] = #cache; // використання кешованих даних
$images_folder = 'images';

//задаєм системні константи
define('DIRSEP', DIRECTORY_SEPARATOR);
define('SYS_PATH', getcwd() . DIRSEP.'system'.DIRSEP);
define('APP_PATH', getcwd() . DIRSEP.'app'.DIRSEP);
define('CACHE_PATH', getcwd() . DIRSEP.'cache'.DIRSEP);

require SYS_PATH.'base'.DIRSEP.'framework.php';

?>
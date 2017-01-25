<?php

//--- CMS White Lion 1.0 ---//
//-- Installation package --//

session_start();

error_reporting(E_ALL);

define('DIRSEP', DIRECTORY_SEPARATOR);
define('APP_PATH', getcwd() . DIRSEP.'app'.DIRSEP);

if($_SERVER["SERVER_NAME"] == 'localhost')
{
	$REQUEST_URI = explode('/', $_SERVER["REQUEST_URI"]);
	if(isset($REQUEST_URI[1]))
	{
		define('SITE_URL', 'http://'.$_SERVER["SERVER_NAME"].'/'.$REQUEST_URI[1].'/');
		define('SERVER_URL', 'http://'.$_SERVER["SERVER_NAME"].'/'.$REQUEST_URI[1].'/');
		define('SITE_NAME', $REQUEST_URI[1]);
	}
	else
	{
		define('SITE_URL', 'http://'.$_SERVER["SERVER_NAME"].'/');
		define('SERVER_URL', 'http://'.$_SERVER["SERVER_NAME"].'/');
		define('SITE_NAME', $_SERVER["SERVER_NAME"]);
	}
}
else
{
	define('SITE_URL', 'http://'.$_SERVER["SERVER_NAME"].'/');
	define('SERVER_URL', 'http://'.$_SERVER["SERVER_NAME"].'/');
	define('SITE_NAME', $_SERVER["SERVER_NAME"]);
}

if (file_exists('install/index.php')){
	// Load the Installation package
	return include 'install/index.php';
	exit;
}

require 'app/views/404_view.php';

?>
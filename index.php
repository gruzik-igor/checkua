<?php

//--- CMS White Lion 1.0 ---//

session_start();

error_reporting(E_ALL);

if (file_exists('install/index.php')){
	// Load the Installation package
	return include 'install/index.php';
	exit;
}

require 'app/views/404_view.php';

?>
<?php

$uri = explode("/", $_SERVER['REQUEST_URI']);
require_once("step0".DIRSEP."step0.php");

switch ($uri[2]) {

	case 'step1':
		require_once("checkstep.php");
		require_once("step1".DIRSEP."step1.php");
		break;

	case 'step2':
		require_once("step2".DIRSEP."checkstep.php");
		$view_file = "step2".DIRSEP."index_view";
		break;

	case 'step3':
	    require_once("step3".DIRSEP."checkstep.php");
		$view_file = "step3".DIRSEP."index_view";
		break;
	
	default:
		require_once("checkstep.php");
		$view_file = "step1".DIRSEP."index_view";
		break;
}

require_once("page_view.php");
?>
<?php

//--- CMS White Lion 1.0 ---//
//-- Installation package --//

$request = (empty($_GET['request'])) ? '' : $_GET['request'];

switch ($request) {

	case 'step1':
		require_once("checkstep.php");
		require_once("step1".DIRSEP."step1.php");
		break;

	case 'step2':
		$view_file = "step2".DIRSEP."index_view";
		if(!empty($_POST)) require_once("step2".DIRSEP."step2.php");
		break;

	case 'step3':
		require_once("step3".DIRSEP."checkstep.php");
		$view_file = "step3".DIRSEP."index_view";
		if(!empty($_POST)) require_once("step3".DIRSEP."step3.php");
		break;

	case 'step3.5':
		$view_file = "step3.5".DIRSEP."index_view";
		if(!empty($_POST)) require_once("step3.5".DIRSEP."step35.php");
		break;

	case 'step4':
		require_once("step3".DIRSEP."checkstep.php");
		$view_file = "step4".DIRSEP."index_view";
		break;
	
	default:
		require_once("checkstep.php");
		$view_file = "step1".DIRSEP."index_view";
		break;
}

require_once("page_view.php");

?>
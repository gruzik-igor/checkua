<?php
require_once("checkConnection.php");

if ($res['result']) 
{	

	$nakedDir = getcwd();
	$dirName = $nakedDir.DIRSEP."install".DIRSEP."step1".DIRSEP."config.php";
	$content = file_get_contents ($dirName);
	$placeholders = array('$HOST', '$USER', '$PASSWORD', '$DATABASE');
	$stringReplace = array($_POST['host'],$_POST['user'], $_POST['password'], $_POST['db']);
	$newConfig = str_replace($placeholders, $stringReplace, $content);
	$configOpen = fopen($nakedDir.DIRSEP."app".DIRSEP."config.php", "w+");
	fwrite($configOpen, $newConfig);
	fclose($configOpen);

	header("Location: ".SITE_URL."step2");
	exit();
}
else
{
	$view_file = "step1".DIRSEP."index_view";
	$errors = $res['content'];
}

?>
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

	$dirName = $nakedDir.DIRSEP."install".DIRSEP."step1".DIRSEP."white_lion.sql";
	$sql = file_get_contents($dirName);
	if($sql)
	{
		$mysqli->set_charset("utf8");
		if($mysqli->multi_query($sql) === true)
		{
			$mysqli->close();
			header("Location: ".SITE_URL."step2");
			exit();
		}
		else
		{
			$_SESSION['notify']->errors = "Помилка MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
		}
		$mysqli->close();
	}
	else
	{
		$_SESSION['notify']->errors = "Error: File white_lion.sql not found!";
	}
}
else
{
	$_SESSION['notify']->errors = $res['content'];
}

$view_file = "step1".DIRSEP."index_view";

?>
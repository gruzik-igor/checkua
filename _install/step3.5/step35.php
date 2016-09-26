<?php

$dirName = getcwd().DIRSEP."app".DIRSEP."config.php";

if(file_exists($dirName))
{
	$content = file_get_contents ($dirName);
	$placeholders = array('FACEBOOK_APP_ID', 'FACEBOOK_SECRET_KEY');
	$stringReplace = array($_POST['appId'], $_POST['secret']);
	$newConfig = str_replace($placeholders, $stringReplace, $content);
	$configOpen = fopen($dirName, "w+");
	fwrite($configOpen, $newConfig);
	fclose($configOpen);
}

header("Location: ".SITE_URL."step4");
exit();

?>
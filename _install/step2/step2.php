<?php

$dirName = getcwd().DIRSEP."app".DIRSEP."config.php";

if(file_exists($dirName))
{
	$content = file_get_contents ($dirName);
	$placeholders = array('RECAPTCHA_PUBLIC_KEY', 'RECAPTCHA_SECRET_KEY');
	$stringReplace = array($_POST['public'], $_POST['secret']);
	$newConfig = str_replace($placeholders, $stringReplace, $content);
	$configOpen = fopen($dirName, "w+");
	fwrite($configOpen, $newConfig);
	fclose($configOpen);
}

header("Location: ".SITE_URL."step3");
exit();

?>
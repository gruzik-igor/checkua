<?php


if(!file_exists(getcwd().DIRSEP."app".DIRSEP."config.php"))
{
 	header("Location: ".SITE_URL);
 	exit();
}

chdir("install".DIRSEP."step2");
if(file_exists(getcwd().DIRSEP."_index.php"))
{
	header("Location: ".SITE_URL."step3");
 	exit();
}

?>
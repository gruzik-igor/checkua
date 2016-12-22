<?php

if(!file_exists(getcwd().DIRSEP."app".DIRSEP."config.php"))
{
 	header("Location: ".SITE_URL);
 	exit();
}

?>
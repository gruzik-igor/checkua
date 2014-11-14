<?php 
require_once('../../LOCAL_SITE_URL.php');

define('DIRSEP', DIRECTORY_SEPARATOR);
define('APP_PATH', "../../app".DIRSEP);
define('SITE_URL', 'http://'.$_SERVER["SERVER_NAME"].'/'.$LOCAL_SITE_URL);
define('SITE_NAME', 'nazva');

$success = "Інсталяція пройшла успішно. <br> Для переходу на сайт виберіть необхідну дію.";
if(file_exists("../../install")) $success .= "<br> <br> Перейменуйте папку install в _install"; 

$view_file = "index_view";
require_once("../page_view.php");

?>
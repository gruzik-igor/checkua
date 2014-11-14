<pre>
<?php
require_once('../../LOCAL_SITE_URL.php');
require_once('../../app/config.php');
require_once('../step2/data.php');

$name = $_POST['name'];
$email = $_POST['email'];
$passwordFirst = $_POST['admin_password'];
$passwordRepeat = $_POST['admin_password_repeat'];
if($passwordFirst === $passwordRepeat) $password = sha1(md5($_POST['admin_password']) . $sys_password);
$time = time();

$connect = new mysqli($config['db']['host'], $config['db']['user'], $config['db']['password']);
$connect->set_charset("utf8");
$connect->select_db($config['db']['database']);
$query = "INSERT INTO wl_users (`name`,`email`,`type`,`status`, `registered`, `password`) VALUES ('{$name}','{$email}', 1, 1, '{$time}','{$password}');";
$connect->query($query);


chdir("../");
$installDir = getcwd();
$dirsep = DIRECTORY_SEPARATOR;
chdir("../");
$nakedDir = getcwd().$dirsep;
rename($nakedDir."index.php", $nakedDir."_index.php");
$step2Dir = $nakedDir."install".$dirsep."step2".$dirsep;
rename($step2Dir."_index.php", $nakedDir."index.php");


@rename($installDir, $nakedDir."_install");
if(file_exists($installDir))
{
	header("Location: ".'http://'.$_SERVER["SERVER_NAME"].'/'.$LOCAL_SITE_URL."install/step4/step4.php");
	exit();
} else{
	header("Location: ".'http://'.$_SERVER["SERVER_NAME"].'/'.$LOCAL_SITE_URL."_install/step4/step4.php");
	exit();
}



?>
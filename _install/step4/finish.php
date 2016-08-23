<?php
session_start();

define('DIRSEP', DIRECTORY_SEPARATOR);
$step4Dir = getcwd().DIRSEP;
chdir("../");
$installDir = getcwd();
chdir("../");
$nakeDir = getcwd().DIRSEP;
$file_config = $nakeDir."app".DIRSEP."config.php";

if(file_exists($file_config))
{
	require_once($file_config);
	$connect = new mysqli($config['db']['host'], $config['db']['user'], $config['db']['password'], $config['db']['database']);
	$connect->set_charset("utf8");

	$site_name = $_POST['site_name'];
	$useWWW = (isset($_POST['useWWW']) && $_POST['useWWW'] == 1) ? 'true' : 'false';
	$site_email = $_POST['site_email'];
	$cache = (isset($_POST['cache']) && $_POST['cache'] == 1) ? 'true' : 'false';
	$language = "false";
	$languages = '';
	if($_POST['language'] == "multi" && !empty($_POST['languages']))
	{
		$language = ($_POST['language_type'] == 'maindomain') ? "'main domain'" : "'*.{$site_name}'";
		$deleteEmptyLang = array_diff($_POST['languages'], array(''));
		$languages = implode("', '", $deleteEmptyLang);
		$languages = "'{$languages}'";
		foreach ($deleteEmptyLang as $value) {
			$query = "INSERT INTO `wl_ntkd` (`alias`, `content`, `language`, `name`) VALUES 
						(1, 0, '{$value}', '{$site_name}'),
						(2, 0, '{$value}', 'Пошук {$site_name}'),
						(3, 0, '{$value}', 'Мій кабінет {$site_name}'),
						(4, 0, '{$value}', 'Увійти у {$site_name}'),
						(5, 0, '{$value}', 'Реєстрація {$site_name}'),
						(6, 0, '{$value}', 'Відновлення паролю {$site_name}'),
						(7, 0, '{$value}', 'Підписка');";
			$connect->query($query);
		}
	}
	else
	{
		$query = "INSERT INTO `wl_ntkd` (`alias`, `content`, `name`) VALUES 
						(1, 0, '{$site_name}'),
						(2, 0, 'Пошук {$site_name}'),
						(3, 0, 'Мій кабінет {$site_name}'),
						(4, 0, 'Увійти у {$site_name}'),
						(5, 0, 'Реєстрація {$site_name}'),
						(6, 0, 'Відновлення паролю {$site_name}'),
						(7, 0, 'Підписка');";
		$connect->query($query);
	}
	$connect->close();

	$indexContent = file_get_contents($step4Dir."index.php");
	$placeholders = array("#SITE_EMAIL", "#SYS_PASSWORD", "#useWWW", "#multilanguage_type", "#all_languages", "#cache");
	$stringReplace = array($site_email, $_SESSION['SYS_PASSWORD'], $useWWW, $language, $languages, $cache);
	$newIndex = str_replace($placeholders, $stringReplace, $indexContent);
	$indexOpen = fopen($nakeDir."index.php", "w+");
	fwrite($indexOpen, $newIndex);
	fclose($indexOpen);

	@rename($installDir, $nakeDir."_install");

	if($_SERVER["SERVER_NAME"] == 'localhost')
	{
		$REQUEST_URI = explode('/', $_SERVER["REQUEST_URI"]);
		if(isset($REQUEST_URI[1]))
			define('SITE_URL', 'http://'.$_SERVER["SERVER_NAME"].'/'.$REQUEST_URI[1].'/');
		else
			define('SITE_URL', 'http://'.$_SERVER["SERVER_NAME"].'/');
	}
	else
		define('SITE_URL', 'http://'.$_SERVER["SERVER_NAME"].'/');

	header("Location: ".SITE_URL."admin");
	exit();
}
?>
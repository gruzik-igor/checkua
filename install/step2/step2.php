<?php

require_once("../../app/config.php");
require_once('../../LOCAL_SITE_URL.php');

$site_name = $_POST['site_name'];
$sys_password = $_POST['sys_password'];
$site_email = $_POST['site_email'];
$sys_email = $_POST['sys_email'];
$language = ($_POST['language'] == "one")? "false" : "'local'";

$deleteEmptyLang = array_diff($_POST['languages'], array(''));
$languages = implode('","', $deleteEmptyLang);

$data['language'] = array();

$dirName = getcwd().DIRECTORY_SEPARATOR."index.php";
$indexContent = file_get_contents($dirName);
$placeholders = array("#LANGUAGE", "#ALL_LANGUAGES", "#LOCAL_SITE_URL", "#SITE_NAME", "#SITE_EMAIL", "#SYS_EMAIL", "#SYS_PASSWORD");
$stringReplace = array($language ,$languages, $LOCAL_SITE_URL, $site_name, $site_email, $sys_email, $sys_password);
$newIndex = str_replace($placeholders, $stringReplace, $indexContent);
$indexOpen = fopen(getcwd().DIRECTORY_SEPARATOR."_index.php", "w+");
$data = fopen(getcwd().DIRECTORY_SEPARATOR."data.php", "w+");
$data_pass = "<?php \$sys_password = '{$sys_password}'; ?>";
fwrite($indexOpen, $newIndex);
fwrite($data, $data_pass);
fclose($indexOpen);
fclose($data);


$sql = file_get_contents('white_lion.sql');
if($sql) {
	$connect = new mysqli($config['db']['host'], $config['db']['user'], $config['db']['password']);
	$connect->set_charset("utf8");
	$connect->select_db($config['db']['database']);
	if($connect->multi_query($sql) === true){
		do {
			if($_POST['language'] == "one"){
			$query = "INSERT INTO wl_ntkd (`alias`, `content`, `name`, `title`) VALUES (1, 0, '{$site_name}', '{$site_name}');";
			$connect->query($query);
		}
		elseif (!empty($deleteEmptyLang)) {
			foreach ($deleteEmptyLang as $value) {
				$query = "INSERT INTO wl_ntkd (`alias`, `content`, `language`, `name`, `title`) VALUES (1, 0, '{$value}','{$site_name}', '{$site_name}');";
				$connect->query($query);
			}
		}
		} while (@$connect->next_result());
		
		header("Location: ".'http://'.$_SERVER["SERVER_NAME"].'/'.$LOCAL_SITE_URL."step3");
		exit();

	} else {
		printf("Error: %s\n", $connect->error);
	}
	$connect->close();

} else {
	exit("Error: File white_lion.sql not found!");
}

?>
<?php
$res = array('result' => false, 'content' => 'Підключитися до SQL серверу не вдалося! Перевірте логін та пароль.');

$host = $_POST['host'];
$user = $_POST['user'];
$password = $_POST['password'];
$db = $_POST['db'];
$json = false;
if(isset($_POST['json'])) $json = $_POST['json'];

if(!empty($host) && !empty($user))
{

	@$mysqli = new mysqli($host, $user, $password, $db);
	if ($mysqli->connect_errno)
	{
		if($mysqli->connect_errno == 1049)
		{
			if(isset($_POST['create']) && $_POST['create'])
			{
				@$mysqli2 = new mysqli($host, $user, $password);
				if ($mysqli2->connect_errno)
					$res['content'] = "Не вдалося підключитися до MySQL 2: (" . $mysqli2->connect_errno . ") " . $mysqli2->connect_error;
				else
				{
					if($mysqli2->query("CREATE DATABASE IF NOT EXISTS `{$db}` DEFAULT CHARACTER SET utf8 DEFAULT COLLATE utf8_general_ci;"))
					{
						$res['result'] = true;
						$res['content'] = 'Базу даних створено. Для продовження натисніть "Далі"';
					}
					else
						$res['content'] = "Помилка створення бази даних: (" . $mysqli2->errno . ") " . $mysqli2->error;
				}
			}
			else
				$res['content'] = "Не вдалося підключитися до бази даних {$db}: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
		}
		else
	    	$res['content'] = "Не вдалося підключитися до MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
	}
	else
	{
		$res['result'] = true;
		$res['content'] = 'База даних існує. Для продовження натисніть "Далі"';
	}
			
}
if($json)
{
	header('Content-type: application/json');
	echo json_encode($res);
	exit;
}
?>
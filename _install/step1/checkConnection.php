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
	    $res['content'] = "Не вдалося підключитися до MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
	}
	else
	{
		$res['result'] = true;
		$res['content'] = 'База даних існує. Для продовження натисніть "Далі"';
	}
			
}
if($json){
	header('Content-type: application/json');
	echo json_encode($res);
	exit;
}
?>
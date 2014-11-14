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
		$connect = mysql_connect($host, $user, $password);
		if(!$connect)
		{
			$res['content'] = "Не вдалося підключитися до бази даниx";
		}
		else
		{
			if($_POST['check'] == 1)
			{
				$res['result'] = true;
				$res['content'] = "Вдалося підключитися до SQL серверу.";
			}
			elseif(!@mysql_select_db($db, $connect))
			{
				$res['content'] ="База даних не доступна";
			}
			else
			{
				$res['result'] = true;
				$res['content'] = 'База даних існує. Для продовження натисніть "Далі"';
			}
		}
				
	}
	if($json){
		header('Content-type: application/json');
		echo json_encode($res);
		exit;
	}
?>
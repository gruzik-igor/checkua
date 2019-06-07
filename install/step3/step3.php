<?php
$file_config = getcwd().DIRSEP."app".DIRSEP."config.php";

if(file_exists($file_config))
{
	require_once($file_config);

	$SYS_PASSWORD = md5($_POST['email'].'|white-lion-cms|SYSTEM-PASSWORD|'.time());
	$SYS_PASSWORD = substr(str_shuffle($SYS_PASSWORD), 0, 12);

	$name = $_POST['name'];
	$email = $_POST['email'];
	$passwordFirst = $_POST['admin_password'];
	$passwordRepeat = $_POST['admin_password_repeat'];
	if($passwordFirst === $passwordRepeat) 
	{

	    $text = mb_strtolower($name, "utf-8");      
	    $ua = array('а', 'б', 'в', 'г', 'ґ', 'д', 'е', 'є', 'ж', 'з', 'и', 'і', 'ї', 'й', 'к', 'л', 'м', 'н', 'о', 'п', 'р', 'с', 'т', 'у', 'ф', 'х', 'ц', 'ч', 'ш', 'щ', 'ь', 'ю', 'я' , '1', '2', '3', '4', '5', '6', '7', '8', '9', '0', '-', '_', ' ', '`', '~', '!', '@', '#', '$', '%', '^', '&', '"', ',', '\.', '\?', '/', ';', ':', '\'', 'ы', 'ё');
	    $en = array('a', 'b', 'v', 'h', 'g', 'd', 'e', 'e', 'zh', 'z', 'y', 'i', 'i', 'y', 'k', 'l', 'm', 'n', 'o', 'p', 'r', 's', 't', 'u', 'f', 'kh', 'c', 'ch', 'sh', 'sch', '', 'u', 'ja' , '1', '2', '3', '4', '5', '6', '7', '8', '9', '0', '.', '.', '.', '*', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '*', 'y', 'e');
	    for($i = 0; $i < count($ua); $i++)
	    {
	        $text = mb_eregi_replace($ua[$i], $en[$i], $text);
	    }
	    $alias = mb_eregi_replace("[.]{2,}", '.', $text);
		$password = sha1($email . md5($_POST['admin_password']) . $SYS_PASSWORD . 1);
		$auth_id = md5($name.'|'.$_POST['admin_password'].'|auth_id|'.$email);
		$time = time();

		$connect = new mysqli($config['db']['host'], $config['db']['user'], $config['db']['password'], $config['db']['database']);
		$connect->set_charset("utf8");

		$query = "INSERT INTO `wl_users` (`id`, `alias`, `email`, `name`, `type`, `status`, `last_login`, `registered`, `auth_id`, `password`) VALUES (1, '{$alias}', '{$email}', '{$name}', 1, 1, {$time}, {$time}, '{$auth_id}', '{$password}');";
		$connect->query($query);

		$query = "INSERT INTO `wl_user_register` (`id`, `date`, `do`, `user`) VALUES (1, {$time}, 1, 1);";
		$connect->query($query);
		

		$_SESSION['SYS_PASSWORD'] = $SYS_PASSWORD;
		setcookie('auth_id', $auth_id, $time + 3600*24*31, '/');

		$_SESSION['user']->id = 1;
		$_SESSION['user']->alias = $alias;
        $_SESSION['user']->name = $name;
        $_SESSION['user']->email = $email;
        $_SESSION['user']->status = 1;
        $_SESSION['user']->type = 1;
        $_SESSION['user']->admin = 1;
        $_SESSION['user']->manager = 0;
        $_SESSION['user']->permissions = array('wl_users', 'wl_ntkd', 'wl_images', 'wl_video');

        $nextstep = 3.5;
        if($_POST['userSignUp'] == 1)
        {
        	$query = "INSERT INTO `wl_options` (`service`, `alias`, `name`, `value`) VALUES (0, 0, 'userSignUp', 1);";
			$connect->query($query);
        }
        else
        {
        	$query = "INSERT INTO `wl_options` (`service`, `alias`, `name`, `value`) VALUES (0, 0, 'userSignUp', 0);";
			$connect->query($query);

        	$query = "UPDATE `wl_sitemap` SET `code`= 404 WHERE `id` = 5";
			$connect->query($query);

			$query = "UPDATE `wl_user_status` SET `load`= 'admin' WHERE `id` = 1";
			$connect->query($query);

        	$nextstep = 4;
        }

        $connect->close();
        header("Location: ".SITE_URL."step".$nextstep);
		exit();
	}
	else
	{
		$_SESSION['notify']->errors = "Паролі не співпадають";
	}
}
?>
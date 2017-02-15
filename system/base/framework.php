<?php  if (!defined('SYS_PATH')) exit('Access denied');

/*
 * Шлях: SYS_PATH/base/framework.php
 *
 * Підключаємо всі необхідні файли і створюєм обєкт route
 */

if(empty($_SESSION['user'])) 
{
	$_SESSION['user'] = new stdClass();
}
$_SESSION['option'] = null;

$protocol = ($https) ? 'https://' : 'http://';
$request = (empty($_GET['request'])) ? '' : $_GET['request'];
$request = trim($request, '/\\');

if($_SERVER["SERVER_NAME"] == 'localhost')
{
	$REQUEST_URI = explode('/', $_SERVER["REQUEST_URI"]);
	if(isset($REQUEST_URI[1]))
	{
		define('SERVER_URL', 'http://'.$_SERVER["SERVER_NAME"].'/'.$REQUEST_URI[1].'/');
		define('SITE_NAME', $REQUEST_URI[1]);

		if($multilanguage_type)
		{
			if(isset($REQUEST_URI[2]) && in_array($REQUEST_URI[2], $_SESSION['all_languages']) && $REQUEST_URI[2] != $_SESSION['all_languages'][0])
			{
				$_SESSION['language'] = $REQUEST_URI[2];
				define('SITE_URL', 'http://'.$_SERVER["SERVER_NAME"].'/'.$REQUEST_URI[1].'/'.$REQUEST_URI[2].'/');
				$request = explode('/', $request);
				if($request[0] == $REQUEST_URI[2])
				{
					array_shift($request);
				}
				$request = implode('/', $request);
			}
			else
			{
				$_SESSION['language'] = $_SESSION['all_languages'][0];
				define('SITE_URL', 'http://'.$_SERVER["SERVER_NAME"].'/'.$REQUEST_URI[1].'/');
			}
			
			for ($i = 1; $i < count($_SESSION['all_languages']); $i++) {
				define('SITE_URL_'.strtoupper($_SESSION['all_languages'][$i]), 'http://'.$_SERVER["SERVER_NAME"].'/'.$REQUEST_URI[1].'/'.$_SESSION['all_languages'][$i].'/'.$request);
			}
			define('SITE_URL_'.strtoupper($_SESSION['all_languages'][0]), 'http://'.$_SERVER["SERVER_NAME"].'/'.$REQUEST_URI[1].'/'.$request);
		}
		else
		{
			define('SITE_URL', 'http://'.$_SERVER["SERVER_NAME"].'/'.$REQUEST_URI[1].'/');
			$_SESSION['language'] = false;
		}
	}
}
else
{
	$uri = explode('.', $_SERVER["SERVER_NAME"]);

	if($multilanguage_type)
	{
		if($multilanguage_type == 'main domain')
		{
			$REQUEST_URI = explode('/', $_SERVER["REQUEST_URI"]);
			if(isset($REQUEST_URI[1]) && in_array($REQUEST_URI[1], $_SESSION['all_languages']) && $REQUEST_URI[1] != $_SESSION['all_languages'][0])
			{
				$_SESSION['language'] = $REQUEST_URI[1];
				define('SITE_URL', $protocol.$_SERVER["SERVER_NAME"].'/'.$REQUEST_URI[1].'/');
				$request = explode('/', $request);
				if($request[0] == $REQUEST_URI[1])
				{
					array_shift($request);
				}
				$request = implode('/', $request);
			}
			elseif(!$useWWW && $uri[0] == 'www')
			{
				array_shift($uri);
				$uri = implode(".", $uri);
				$request = '/';
				if(isset($_GET['request'])) $request .= $_GET['request'];
				header ('HTTP/1.1 301 Moved Permanently');
				header ('Location: '. $protocol . $uri . $request);
				exit();
			}
			elseif($useWWW && $uri[0] != 'www')
			{
				$uri = implode(".", $uri);
				$request = '/';
				if(isset($_GET['request'])) $request .= $_GET['request'];
				header ('HTTP/1.1 301 Moved Permanently');
				header ('Location: '. $protocol . $uri . $request);
				exit();
			}
			else
			{
				$_SESSION['language'] = $_SESSION['all_languages'][0];
				define('SITE_URL', $protocol.$_SERVER["SERVER_NAME"].'/');
			}

			for ($i = 1; $i < count($_SESSION['all_languages']); $i++) {
				if($https)
					define('SITE_URL_'.strtoupper($_SESSION['all_languages'][$i]), 'https://'.$_SERVER["SERVER_NAME"].'/'.$_SESSION['all_languages'][$i].'/'.$request);
				else
					define('SITE_URL_'.strtoupper($_SESSION['all_languages'][$i]), 'http://'.$_SERVER["SERVER_NAME"].'/'.$_SESSION['all_languages'][$i].'/'.$request);
			}

			define('SITE_NAME', $_SERVER["SERVER_NAME"]);
			define('SERVER_URL', $protocol.$_SERVER["SERVER_NAME"].'/');
		}
		elseif($multilanguage_type != '')
		{
			$multilanguage_type = explode('.', $multilanguage_type);
			if($multilanguage_type[0] == '*')
			{
				if(in_array($uri[0], $_SESSION['all_languages']) && $uri[0] != $_SESSION['all_languages'][0])
				{
					$_SESSION['language'] = $uri[0];
					define('SITE_URL', $protocol.$_SERVER["SERVER_NAME"].'/');
					array_shift($uri);
					$uri = implode(".", $uri);
					define('SERVER_URL', $protocol.$uri.'/');
					define('SITE_NAME', $uri);

					for ($i = 1; $i < count($_SESSION['all_languages']); $i++) {
						define('SITE_URL_'.strtoupper($_SESSION['all_languages'][$i]), $protocol.$_SESSION['all_languages'][$i].'.'.SITE_NAME.'/'.$request);
					}
				}
				elseif($uri[0] == $_SESSION['all_languages'][0] || (!$useWWW && $uri[0] == 'www'))
				{
					array_shift($uri);
					$uri = implode(".", $uri);
					$request = '/';
					if(isset($_GET['request'])) $request .= $_GET['request'];
					header ('HTTP/1.1 301 Moved Permanently');
					header ('Location: '. $protocol . $uri . $request);
					exit();
				}
				elseif($useWWW && $uri[0] != 'www')
				{
					array_unshift($uri, 'www');
					$uri = implode(".", $uri);
					$request = '/';
					if(isset($_GET['request'])) $request .= $_GET['request'];
					header ('HTTP/1.1 301 Moved Permanently');
					header ('Location: '. $protocol . $uri . $request);
					exit();
				}
				elseif($uri[0] == $multilanguage_type[1])
				{
					$_SESSION['language'] = $_SESSION['all_languages'][0];
					define('SITE_URL', $protocol.$_SERVER["SERVER_NAME"].'/');
					define('SERVER_URL', $protocol.$_SERVER["SERVER_NAME"].'/');
					define('SITE_NAME', $_SERVER["SERVER_NAME"]);
				}
				else
				{
					header('HTTP/1.0 404 Not Found');
					exit(file_get_contents('404.html'));
				}
			}
			else
			{
				exit("Невірне налаштування мультимовності 'multilanguage_type' у index.php");
			}
		}

		define('SITE_URL_'.strtoupper($_SESSION['all_languages'][0]), $protocol.SITE_NAME.'/'.$request);
	}
	else
	{
		define('SITE_URL', $protocol.$_SERVER["SERVER_NAME"].'/');
		define('SERVER_URL', $protocol.$_SERVER["SERVER_NAME"].'/');
		define('SITE_NAME', $_SERVER["SERVER_NAME"]);
		$_SESSION['language'] = false;
	}
}

if(isset($_GET['request']))
{
	$last = substr($_GET['request'], -1, 1);
	if($last == '/')
	{
		header ('HTTP/1.1 301 Moved Permanently');
		header ('Location: '. SITE_URL . $request);
		exit();
	}
}

define('IMG_PATH', SERVER_URL.$images_folder.'/');
$request = ($request == '') ? 'main' : $request;
start_route($request);

function start_route($request)
{
	require 'registry.php';
	require 'loader.php';
	require 'controller.php';
	require 'router.php';
	
	$request = ($request == '') ? 'main' : $request;
	$route = new Router($request);
}

?>
<?php  if (!defined('SYS_PATH')) exit('Access denied');

/*
 * Шлях: SYS_PATH/base/framework.php
 *
 * Підключаємо всі необхідні файли і створюєм обєкт route
 */

$go = true;
if(empty($_SESSION['user'])) @$_SESSION['user']->check = false;

if($_SESSION['language']){
	if($_SESSION['language'] == 'local'){
		$_SESSION['language'] = $_SESSION['all_languages'][0];
	} else {
		$uri = explode('.',$_SERVER["SERVER_NAME"]);
		$_SESSION['language'] = $_SESSION['all_languages'][0];
		if(in_array($uri[0], $_SESSION['all_languages'])) $_SESSION['language'] = $uri[0];
		else $go = false;
		if($uri[0] == $_SESSION['all_languages'][0] || $uri[0] == 'www'){
			if($uri[0] == 'www'){
				array_shift($uri);
			}
			$uri = implode(".", $uri);
			$request = '/';
			if(isset($_GET['request'])) $request .= $_GET['request'];
			header ('HTTP/1.1 301 Moved Permanently');
			header ('Location: http://'. $uri . $request);
			exit();
		}
		if($go == false){
			$name = explode('.', SITE_NAME);
			if($uri[0] == $name[0]) $go = true;
		}
	}
}

if($_SESSION['cache'] && empty($_POST) && count($_GET) < 2){
	require 'cache.php';
	$cache = new cache();
	if($cache->load()){
		$mem_end = memory_get_usage();
		$time_end = microtime(true);
		$time = $time_end - $time_start;
		$mem = $mem_end - $mem_start;
		$mem = round($mem/1024, 5);
		echo '<div class="clear"></div><div style="color:black; text-align:center">Час виконання(сек): '.$time.' Використанок памяті(кб): '.$mem.'</div>';
		exit();
	} 
	else {
		$cache->create_start();
		if($cache->layout){
			foreach($cache->blocks as $block){
				if($cache->check_block($block) == false){
					start_route();
					$cache->create_finish($block);
				}
			}
			$cache->load();
		} else {
			start_route();
			$cache->create_finish();
			$mem_end = memory_get_usage();
			$time_end = microtime(true);
			$time = $time_end - $time_start;
			$mem = $mem_end - $mem_start;
			$mem = round($mem/1024, 5);
			echo '<div class="clear"></div><div style="color:black; text-align:center">Час виконання(сек): '.$time.' Використанок памяті(кб): '.$mem.'</div>';
			exit;
		}
	}
}

if($go){

	start_route();

} else {
	header('HTTP/1.0 404 Not Found');
	exit(file_get_contents('404.html'));
}

function start_route(){
	require 'registry.php';
	require 'loader.php';
	require 'controller.php';
	require 'router.php';
	
	$request = (empty($_GET['request'])) ? 'main' : $_GET['request'];
	$route = new Router($request);
}

?>

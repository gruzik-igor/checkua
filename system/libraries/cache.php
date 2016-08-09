<?php  if (!defined('SYS_PATH')) exit('Access denied');

/*
 * Шлях: SYS_PATH/libraries/cache.php
 *
 * Отримуємо частини URI
 *
 * Версія 1.0.0 (10.08.2016) create library
 */

class Cache {

	private $uri_data;

	function __construct()
	{
		$arr = (empty($_GET['request'])) ? '' : $_GET['request'];
		$arr = trim($arr, '/\\');
		$arr = explode('/', $arr);
		$this->uri_data = $arr;

		if($_SESSION['language'] && ($GLOBALS['multilanguage_type'] == 'main domain' || $_SERVER["SERVER_NAME"] == 'localhost'))
		{
			if(isset($_SESSION['language']) && $_SESSION['language'] != $_SESSION['all_languages'][0] && in_array($_SESSION['language'], $_SESSION['all_languages']))
			{
				array_shift ($this->uri_data);
			}
		}

		if(!empty($_POST))
		{
			foreach ($_POST as $key => $value) {
				$_SESSION['_POST'][$key] = $this->post($key);
			}
		}
		if(!empty($_GET))
		{
			foreach ($_GET as $key => $value) {
				$_SESSION['_GET'][$key] = $this->get($key);
			}
		}
	}

	/**
	 * Отримуємо частину URI
	 *
	 * @param int частина
	 * @param bool очистити від xss
	 */
	public function uri($idx = null, $xss = false)
	{
		if($idx && array_key_exists($idx, $this->uri_data)){
			if($xss){
				return $this->xss_clean($this->uri_data[$idx]);
			} else {
				return $this->uri_data[$idx];
			}
		}

		return null;
	}

}

?>

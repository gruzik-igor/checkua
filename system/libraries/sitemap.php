<?php  if (!defined('SYS_PATH')) exit('Access denied');

/*
 * Шлях: SYS_PATH/libraries/sitemap.php
 *
 * Отримуємо частини URI
 *
 * Версія 1.0.0 (11.08.2016) create library
 */

class SiteMap {

	private $request;

	function __construct()
	{
		$request = (empty($_GET['request'])) ? '' : $_GET['request'];
		$this->request = trim($request, '/\\');
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

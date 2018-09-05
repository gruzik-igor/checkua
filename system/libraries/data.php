<?php  if (!defined('SYS_PATH')) exit('Access denied');

/*
 * Шлях: SYS_PATH/libraries/data.php
 *
 * Отримуємо POST дані і частини URI
 *
 * Версія 1.0.1 (06.11.2014) Додано insertFromForm()
 * Версія 1.0.2 (29.09.2015) Додано getShortText()
 * Версія 1.0.3 (19.10.2015) Бібліотеку розділено на 2 (data + form), модифіковано функцію make()
 * Версія 1.0.4 (04.12.2015) Виправлено getShortText()
 * Версія 1.0.5 (09.03.2016) Виправлено констуктор Data() згідно режиму мультимовності 'main domain', додано заміну "багато дефів" на один у latterUAtoEN(), додано перевірку існування папки у removeDirectory()
 * Версія 1.1 (07.04.2016) Додано re_get(), re_post()
 * Версія 1.1.1 (22.07.2016) До re_get(), re_post() додано значення за замовчуванням; приведено дос тандарту php7
 * Версія 1.2 (27.09.2016) Виправлено make()
 * Версія 1.2.1 (17.10.2016) Виправлено latterUAtoEN(): додано символ +
 * Версія 1.2.2 (24.10.2016) Виправлено make(): правильно розпізнає поля як з типом так і без; непередані поля
 * Версія 1.3 (06.12.2016) Додано get_link() - формування лінку за GET зі змінами
 * Версія 1.3.1 (20.01.2017) Функцію make() перейменовано на prepare()
 * Версія 1.3.2 (14.09.2017) Fix get_link(): коректно працює із масивами в get
 * Версія 1.3.3 (05.09.2018) Add userIP()
 */

class Data {

	private $uri_data;
	public $errors = array();

	function __construct()
	{
		$arr = (empty($_GET['request'])) ? '' : $_GET['request'];
		$arr = trim($arr, '/\\');
		$arr = explode('/', $arr);

		if(end($arr) == 'amp')
		{
			$_SESSION['amp'] = true;
			array_pop($arr);
		}
		$this->uri_data = $arr;

		if($_SESSION['language'] && ($GLOBALS['multilanguage_type'] == 'main domain' || $_SERVER["SERVER_NAME"] == 'localhost'))
		{
			if(isset($_SESSION['language']) && $_SESSION['language'] != $_SESSION['all_languages'][0] && in_array($_SESSION['language'], $_SESSION['all_languages']))
				array_shift ($this->uri_data);
		}

		if(!empty($_POST))
		{
			foreach ($_POST as $key => $value) {
				if(is_array($value))
					foreach ($value as $key2 => $value2) {
						if(is_array($value2)){
							foreach ($value2 as $key3 => $value3) {
								$_SESSION['_POST'][$key][$key2][$key3] = $this->xss_clean($value3);
							}
						}
						else
							$_SESSION['_POST'][$key][$key2] = $this->xss_clean($value2);
					}
				else
					$_SESSION['_POST'][$key] = $this->post($key);
			}
		}
		if(!empty($_GET))
		{
			foreach ($_GET as $key => $value) {
				if(is_array($value))
					foreach ($value as $key2 => $value2) {
						$_SESSION['_GET'][$key][$key2] = $this->xss_clean($value2);
					}
				else
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
		if($idx && array_key_exists($idx, $this->uri_data))
		{
			if($xss)
				return $this->xss_clean($this->uri_data[$idx]);
			else
				return $this->uri_data[$idx];
		}
		return null;
	}

	public function url()
	{
		return $this->uri_data;
	}

	/**
	 * Отримуємо POST дані
	 *
	 * @param string ключ
	 * @param bool очистити від xss
	 */
	public function post($key, $xss = true)
	{
		if($key && array_key_exists($key, $_POST))
		{
			if($xss)
				return $this->xss_clean($_POST[$key]);
			else
				return $_POST[$key];
		}
		return null;
	}

	public function get($key, $xss = true)
	{
		if($key && array_key_exists($key, $_GET))
		{
			if($xss)
				return $this->xss_clean($_GET[$key]);
			else
				return $_GET[$key];
		}
		return null;
	}

	public function re_post($key = '', $default = false)
	{
		if(isset($_SESSION['_POST'][$key]))
			return $_SESSION['_POST'][$key];
		return $default;
	}

	public function re_get($key = '', $default = false)
	{
		if(isset($_SESSION['_GET'][$key]))
			return $_SESSION['_GET'][$key];
		return $default;
	}

	public function get_link($new_key = '', $new_value = '')
	{
		$link = SITE_URL . $_GET['request'];
		if(($new_key != '' && $new_value != '') || count($_GET) > 1)
		{
        	$link .= '?';
        	$updated = false;
        	$nk2 = $new_key;
        	if(substr($nk2, -2) == '[]')
        		$nk2 = substr($nk2, 0, -2);
        	foreach ($_GET as $key => $value) {
	            if($key != 'request' && $key != $nk2)
	            {
	                if(!is_array($value)) {
	                    $link .= $key .'='.$value . '&';
	                } else {
	                    foreach ($value as $key2 => $value2) {
	                        $link .= $key .'%5B%5D='.$value2 . '&';
	                    }
	                }
	            }
	            elseif($key == $new_key && $new_value != '')
	            {
	            	$link .= $key .'='.$new_value . '&';
	            	$updated = true;
	            }
	        }
	        if(!$updated && $new_value != '')
	        	$link .= $new_key .'='.$new_value . '&';
	        $link = substr($link, 0, -1);
		}
        return $link;
	}

    public function getShortText($text, $len = 155)
    {
        $text = strip_tags(html_entity_decode($text));
        if(mb_strlen($text, 'UTF-8') > $len)
        {
            $pos = mb_strpos($text, ' ', $len, 'UTF-8');
			if($pos)
				return mb_substr($text, 0, $pos, 'UTF-8');
			else
			{
				$pos = mb_strpos($text, ' ', $len - 10, 'UTF-8');
				if($pos)
					return mb_substr($text, 0, $pos, 'UTF-8');
			}
        }
        return $text;
    }

    /**
	 * Отримуємо та готуємо POST дані до вставки в бд
	 *
	 * @param string ключ
	 * @param bool очистити від xss
	 */
	public function prepare($fields, $var = '_POST')
	{
		if(!empty($fields) && is_array($fields))
		{
			$data = array();
			foreach ($fields as $f => $type) {
                $value = null;
                if($var == '_POST')
                    $value = $this->post($f, true);
                else
                    $value = $this->get($f, true);
                if($value !== null)
                {
    				if($type == 'number' && is_numeric($value))
                        $data[$f] = $value;
    				elseif($type == 'date')
    				{
    					$date = explode('.', $value);
    					if(count($date) == 3)
    					{
	    					$date = mktime(0,0,0, $date[1], $date[0], $date[2]);
	    					if(is_numeric($date))
	                            $data[$f] = $date;
	                    }
    				}
    				else
                        $data[$f] = $value;
    			}
    			elseif(is_numeric($f))
    			{
                    if($var == '_POST')
                        $data[$type] = $this->post($type, true);
                    else
                        $data[$type] = $this->get($type, true);
                }
            }
			return $data;
		}
		return null;
	}

	/**
	 * Очищуємо від xss
	 */
	private function xss_clean($value)
    {
		return htmlspecialchars(trim($value), ENT_QUOTES);
	}

	public function latterUAtoEN($text)
	{
        $text = mb_strtolower($text, "utf-8");
        $ua = array('а', 'б', 'в', 'г', 'ґ', 'д', 'е', 'є', 'ж', 'з', 'и', 'і', 'ї', 'й', 'к', 'л', 'м', 'н', 'о', 'п', 'р', 'с', 'т', 'у', 'ф', 'х', 'ц', 'ч', 'ш', 'щ', 'ь', 'ю', 'я' , '1', '2', '3', '4', '5', '6', '7', '8', '9', '0', '-', '_', ' ', '`', '~', '!', '@', '#', '$', '%', '^', '&', '"', ',', '\.', '\?', '/', ';', ':', '\'', 'ы', 'ё', '[+]', '“', '”');
        $en = array('a', 'b', 'v', 'h', 'g', 'd', 'e', 'e', 'zh', 'z', 'y', 'i', 'i', 'y', 'k', 'l', 'm', 'n', 'o', 'p', 'r', 's', 't', 'u', 'f', 'kh', 'c', 'ch', 'sh', 'sch', '', 'u', 'ja' , '1', '2', '3', '4', '5', '6', '7', '8', '9', '0', '-', '-', '-', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 'y', 'e', '', '', '');
        for ($i = 0; $i < count($ua); $i++) {
            $text = mb_eregi_replace($ua[$i], $en[$i], $text);
        }
        $text = mb_eregi_replace("[-]{2,}", '-', $text);
        return $text;
    }

    public function removeDirectory($dir)
    {
    	if(is_dir($dir))
    	{
    		if ($objs = glob($dir."/*"))
    		{
	           	foreach($objs as $obj) {
	             	is_dir($obj) ? $this->removeDirectory($obj) : unlink($obj);
	           	}
	        }
	        rmdir($dir);
    	}
    }

    public function userIP()
    {
    	if (!empty($_SERVER['HTTP_CLIENT_IP']))
		    return $_SERVER['HTTP_CLIENT_IP'];
		elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))
		    return $_SERVER['HTTP_X_FORWARDED_FOR'];
		else
		    return $_SERVER['REMOTE_ADDR'];
		return false;
    }

}

?>

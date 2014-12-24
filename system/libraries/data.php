<?php  if (!defined('SYS_PATH')) exit('Access denied');

/*
 * Шлях: SYS_PATH/libraries/data.php
 *
 * Отримуємо POST дані і частини URI
 *
 * Версія 1.0.1 (06.11.2014) Додано insertFromForm
 */

class Data extends Controller {

	private $uri_data;
	public $errors = array();
	
	function Data(){
		$arr = (empty($_GET['request'])) ? '' : $_GET['request'];
		$arr = trim($arr, '/\\');
		$arr = explode('/', $arr);
		$this->uri_data = $arr;
	}
	
	/**
	 * Отримуємо частину URI
	 *
	 * @param int частина
	 * @param bool очистити від xss
	 */
	function uri($idx = null, $xss = false){
		if($idx && array_key_exists($idx, $this->uri_data)){
			if($xss){
				return $this->xss_clean($this->uri_data[$idx]);
			} else {
				return $this->uri_data[$idx];
			}
		}
		
		return null;
	}

	function url()
	{
		return $this->uri_data;
	}

	/**
	 * Отримуємо POST дані
	 *
	 * @param string ключ
	 * @param bool очистити від xss
	 */		
	function post($key, $xss = true){
		if($key && array_key_exists($key, $_POST)){
			if($xss){
				return $this->xss_clean($_POST[$key]);
			} else {
				return $_POST[$key];
			}
		}	
		
		return null;
	}
	
	function get($key, $xss = true){
		if($key && array_key_exists($key, $_GET)){
			if($xss){
				return $this->xss_clean($_GET[$key]);
			} else {
				return $_GET[$key];
			}
		}	
		
		return null;
	}

	function makeFromPOST($fields)
	{
		if(!empty($fields)){
			$data = array();
			foreach ($fields as $f => $type) if(isset($_POST[$f])){
				if($type == 'number'){
					if(is_numeric($_POST[$f])) $data[$f] = $_POST[$f];
				}
				elseif($type == 'date'){
					$date = explode('.', $_POST[$f]);
					$date = mktime(0,0,0, $date[1], $date[0], $date[2]);
					if(is_numeric($date)) $data[$f] = $date;
				}
				else $data[$f] = $this->post($f, true);
			}
			return $data;
		}
		return null;
	}

	public function insertFromForm($form='', $additionally = array(), $user = -1)
    {
        if($form != ''){
        	$this->library('db');
            $form = $this->db->getAllDataById('wl_forms', $form, 'name');
            if($form && $form->table != '' && $form->type > 0 && $form->type_data > 0){
                $fields = $this->db->getQuery("SELECT f.*, t.name as type_name FROM wl_fields as f LEFT JOIN wl_input_types as t ON t.id = f.input_type WHERE f.form = {$form->id}", 'array');
                if($fields){
                	$data = array();
                	$this->errors = array();
                	foreach ($fields as $field) {
                		$input_data = null;
                		if($form->type == 1) $input_data = $this->get($field->name);
                		elseif($form->type == 2) $input_data = $this->post($field->name);
                		if($field->required && $input_data == null) {
                			$this->errors[] = "Field '{$field->title}' is required!";
                		}
                		if($input_data){
                			$data[$field->name] = $input_data;
                		}
                	}
                	if(!empty($data) && empty($this->errors)){
                		if(!empty($additionally)) $data = array_merge ($data, $additionally);
                		if($form->type_data == 1){
                			foreach ($data as $field => $value) {
                				if($user == 0 && isset($_SESSION['user']->id) && $_SESSION['user']->id > 0){
                					$row['user'] = $_SESSION['user']->id;
                				} elseif($user > 0){
                					$row['user'] = $user;
                				}
                				$row['field'] = $field;
                				$row['value'] = $value;
                				$this->db->insertRow($form->table, $row);
                			}
                		} elseif($form->type_data == 2){
                			$this->db->insertRow($form->table, $data);
                			$data['id'] = $this->db->getLastInsertedId();
                		}
                		return $data;
                	}
                }
            }
        }
        return false;
    }

	/**
	 * Очищуємо від xss
	 */	
	private function xss_clean($value){
		return htmlspecialchars($value);
	}
	
	function latterUAtoEN($text){
		$text = mb_strtolower($text, "utf-8");		
		$text = stripcslashes($text);	
		$ua = array('а', 'б', 'в', 'г', 'ґ', 'д', 'е', 'є', 'ж', 'з', 'и', 'і', 'ї', 'й', 'к', 'л', 'м', 'н', 'о', 'п', 'р', 'с', 'т', 'у', 'ф', 'х', 'ц', 'ч', 'ш', 'щ', 'ь', 'ю', 'я' , '1', '2', '3', '4', '5', '6', '7', '8', '9', '0', '-', '_', ' ', '`', '~', '!', '@', '#', '$', '%', '^', '&', '"', ',', '\.', '\?', '/', ';', ':', '\'');
		$en = array('a', 'b', 'v', 'h', 'g', 'd', 'e', 'e', 'zh', 'z', 'y', 'i', 'i', 'y', 'k', 'l', 'm', 'n', 'o', 'p', 'r', 's', 't', 'u', 'f', 'kh', 'c', 'ch', 'sh', 'sch', '', 'u', 'ja' , '1', '2', '3', '4', '5', '6', '7', '8', '9', '0', '-', '_', '_', '*', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '*');
		for($i = 0; $i < count($ua); $i++){
			$text = mb_eregi_replace($ua[$i], $en[$i], $text);
		}
		return $text;
	}
	
	function NoXssPHP($text){
		$text = preg_replace("|<script[\w\s]*>|si", "<span style=\"color:red\">!!! ALERT START XSS SCRIPT !!! ", $text);
		$text = preg_replace("|</script>|si", " !!! FINISH !!!</span>", $text);
		$text = preg_replace("|<\?[\w\s]*\?>|si", "", $text);
		return $text;
	}

}

?>

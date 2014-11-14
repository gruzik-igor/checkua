<?php if (!defined('SYS_PATH')) exit('Access denied');

/*
 * Шлях: SYS_PATH/base/cache.php
 *
 * Клас для роботи з КЕШом
 */
 
class cache {
	
	public $layout = false; //false - використовувати шаблон (складне кешування)
	public $blocks = array();
	private $name_file_in_block = '';
	public $path = '';
	public $alias = array('head' => 0, 'header' => 0, 'lcolumn' => 0, 'content' => 0, 'rcolumn' => 0, 'footer' => 0);
	public $rules = array(0 => 'no', 1 => 'up', 2 => 'down', 3 => 'write', 4 => 'clear');
	public $help = array(0 => 'без змін', 1 => 'доповнити зверху', 2 => 'доповнити знизу', 3 => 'перезаписати', 4 => 'очистити');
	
	/**
	 * Якшо в конфігу задана секція "autoload" то завантажуємо ці бібліотеки.
	 */
	function __construct($path = ''){
		$this->path = $path;
	}
	
	/**
	 * Завантажуємо cache
	 */
	function load(){
		if(isset($_COOKIE['auth_id']) && $_SESSION['user']->check == false) return $false;
		else {
			$path = CACHE_PATH . $this->path;
			if(!empty($_GET['request'])) $path .= $_GET['request']; else $path .= 'index';
			if($_SESSION['language']) $path .= DIRSEP . $_SESSION['language'];
			$path .= '.php';
			if(file_exists($path)){
				require_once($path);
				return true;
			}
		}
		return false;
	}
	
	function check_block($block){
		$path = CACHE_PATH . 'block_' . $block . DIRSEP;
		$path .= $this->get_name_file_in_block();
		if($_SESSION['language']) $path .= DIRSEP . $_SESSION['language'];
		$path .= '.php';
		if(file_exists($path)) return true;
		return false;
	}
	
	function get_name_file_in_block($block){
		if($this->name_file_in_block == ''){
			require_once($path . 'cache_rules.php');
			$block = new $block();
			$this->name_file_in_block = $block->name();
		}
		return $this->name_file_in_block;
	}
	
	function create_start(){
		if($this->layout){
			require_once(CACHE_PATH . 'cache_blocks.php');
			if(in_array('page')) $this->blocks = $cache_blocks;
			else $this->layout = false;
		}
		ob_start();
	}
	
	function create_finish($block = false){
		if($block){
			$path = CACHE_PATH . 'block_' . $block . DIRSEP;
			$name = $this->get_name_file_in_block();
			if($_SESSION['language']) $name .= DIRSEP . $_SESSION['language'];
			$this->create_cache($path . $name);
			$this->name_file_in_block = '';
		} else {
			$path = CACHE_PATH;
			if(!empty($_GET['request'])) $path .= $_GET['request']; else $path .= 'index';
			if($_SESSION['language']) $path .= DIRSEP . $_SESSION['language'];
			$this->create_cache($path);
			ob_end_flush();
		}
	}
	
	private function create_cache($path){
		$path = str_replace('/', DIRSEP, $path);
		$folder = substr($path, 0, strrpos($path, DIRSEP));
		echo $path;
		if(!is_dir($folder)){
			$folder = substr($folder, strlen(CACHE_PATH));
			$folder = explode(DIRSEP, $folder);
			$pos = CACHE_PATH;
			foreach($folder as $f){
				$pos .= $f;
				if(!is_dir($pos)) mkdir($pos, 0777);
				$pos .= DIRSEP;
			}
		}
		$content = ob_get_contents();
		$fp = fopen($path . '.php', 'w'); 
		fwrite($fp, $content); 
		fclose($fp);
	}
	
	function delete($alias = ''){
		if($alias == '') $alias = $this->path;
		if($alias == '') return false;
		if($_SESSION['language']){
			foreach($_SESSION['all_languages'] as $lng){
				$path = CACHE_PATH . $alias . DIRSEP. $lng .'.php';
				if(file_exists($path)){
					if(unlink($path) == false) return false;
				}
			}
		} else {
			$path = CACHE_PATH . $alias . '.php';
			if(file_exists($path)){
				if(unlink($path) == false) return false;
			}
		}
		return true;
	}
	
	function test(){
		echo 'test cache';
	}
	
}

?>

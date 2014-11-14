<?php if (!defined('SYS_PATH')) exit('Access denied');

/*
 * Шлях: SYS_PATH/base/router.php
 *
 * Шукає шлях до контроллеру і створює об'єкт
 */
 
class Router  extends Loader {
	
	private $request;
	private $class;
	private $method;
	
	function __construct($req = null){
		if($req != null){
			$this->request = $req;
			$this->findRoute();
		}
	}
	
	/**
	 * Шукаємо шлях
	 */
	function findRoute(){
		$route = trim($this->request, '/\\');
		$parts = explode('/', $route);
				
		$path = APP_PATH.'controllers'.DIRSEP;
		$admin = false;

		if($parts[0] == 'admin'){
			if(isset($_SESSION['user']->id) && $_SESSION['user']->id > 0 && ($_SESSION['user']->admin || $_SESSION['user']->manager)){
				if(count($parts) == 1) $parts[] = 'admin';
				else {
					parent::library('db', $this);
					parent::model('wl_alias_model');
					$this->wl_alias_model->alias($parts[1]);
				}
				$admin = true;
			} else {
				header("Location: ".SITE_URL."login");
				exit();
			}
		} else {
			parent::library('db', $this);
			parent::model('wl_alias_model');
			$this->wl_alias_model->alias($parts[0]);
		}

		if($admin == false && $this->isservice()){
			
			$path = APP_PATH.'services'.DIRSEP.$_SESSION['alias']->service.DIRSEP;
			array_shift($parts);
			
		} else {
		
			foreach($parts as $part){
			
				if(is_dir($path.$part.DIRSEP)){
					$path .= $part.DIRSEP;
					array_shift($parts);
					continue;
				}
				
				if(is_file($path.$part.'.php')){
					$this->class = $part;
					array_shift($parts);
					break;
				}
			}
			
		}
		
		$this->method = (empty($parts)) ? 'index' : $parts[0];
		if(is_readable($path.$this->class.'.php')){
			require $path.$this->class.'.php';
			$this->callController();
		} elseif($admin) {
			$this->class = 'admin';
			require $path.$this->class.'.php';
			$this->callController();
		} else {
			header('HTTP/1.0 404 Not Found');
			exit(file_get_contents('404.html'));
		}
	}
	
	/**
	 * Створюємо об'єкт і викликаємо метод
	 */	
	function callController(){
		$controller = new $this->class();
		$method = $this->method;
		if(is_callable(array($controller, '_remap'))){
			$controller->_remap($method);
		} else if(is_callable(array($controller, $method)) && $method != 'library' && $method != 'db') {
			$controller->$method();
		} else {
			header('HTTP/1.0 404 Not Found');
			exit(file_get_contents('404.html'));
		}
	}
	
	private function isservice(){
		if(isset($_SESSION['alias']->service) && $_SESSION['alias']->service){
			
			$path = APP_PATH.'services'.DIRSEP.$_SESSION['alias']->service.DIRSEP;
			if(is_file($path.$_SESSION['alias']->service.'.php')){
				$this->class = $_SESSION['alias']->service;
				return true;
			}
			
		}
		return false;
	}
	
}

?>

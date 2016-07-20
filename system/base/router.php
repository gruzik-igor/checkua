<?php if (!defined('SYS_PATH')) exit('Access denied');

/*
 * Шлях: SYS_PATH/base/router.php
 *
 * Шукає шлях до контроллеру і створює об'єкт
 */
 
class Router extends Loader {
	
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
		parent::library('db', $this);

		$route = trim($this->request, '/\\');
		$parts = explode('/', $route);
				
		$path = APP_PATH.'controllers'.DIRSEP;
		$admin = false;

		if($parts[0] == 'admin'){
			if(isset($_SESSION['user']->id) && $_SESSION['user']->id > 0) {
				if($_SESSION['user']->admin) $admin = true;
    			if($_SESSION['user']->manager == 1 && (!isset($parts[1]) || isset($parts[1]) && in_array($parts[1], $_SESSION['user']->permissions))) $admin = true;
    			if($admin) {
					if(count($parts) == 1) {
						$parts[] = 'admin';
						$_SESSION['alias'] = new stdClass();
						$_SESSION['alias']->service = false;
						$_SESSION['service'] = new stdClass();
					} else {
						parent::model('wl_alias_model');
						$this->wl_alias_model->alias($parts[1]);
						$this->wl_alias_model->admin_options();
					}
				} else {
					parent::page_404();
				}
			} else {
				header("Location: ".SITE_URL."login");
				exit();
			}
		} else {
			parent::model('wl_alias_model');
			$this->wl_alias_model->alias($parts[0]);

			if(@!$_SESSION['user']->admin && !in_array($parts[0], array('images', 'assets', 'style'))){
				parent::model('wl_statistic_model');
				$this->wl_statistic_model->set($route);
			}
		}

		if($this->isservice())
		{
			if($admin){
				$path = APP_PATH.'services'.DIRSEP.$_SESSION['alias']->service.DIRSEP.'admin';
				$this->method = (!isset($parts[2])) ? 'index' : $parts[2];
			} else {
				$path = APP_PATH.'services'.DIRSEP.$_SESSION['alias']->service.DIRSEP.$_SESSION['alias']->service;
				$this->method = (!isset($parts[1])) ? 'index' : $parts[1];
			}
		}
		else
		{
			foreach($parts as $part)
			{
				if(is_dir($path.$part.DIRSEP))
				{
					$path .= $part.DIRSEP;
					array_shift($parts);
					continue;
				}
				
				if(is_file($path.$part.'.php'))
				{
					$this->class = $part;
					array_shift($parts);
					break;
				}
			}

			$path .= $this->class;
			$this->method = (empty($parts)) ? 'index' : $parts[0];
		}
		
		if(is_readable($path.'.php')){
			require $path.'.php';
			$this->callController();
		} else {
			parent::page_404();
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
			parent::page_404();
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

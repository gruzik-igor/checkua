<?php if (!defined('SYS_PATH')) exit('Access denied');

/*
 * Шлях: SYS_PATH/base/loader.php
 *
 * Завантажуємо сторонні класи, бібліотеки тощо...
 */
 
class Loader {
	
	/**
	 * Якшо в конфігу задана секція "autoload" то завантажуємо ці бібліотеки.
	 * Якщо виклик із сервісу, то передається назва сервісу
	 */
	function __construct(){
		if($this->config('autoload')){
			$this->autoload($this->config('autoload'));
		}
	}
	
	/**
	 * Завантажуємо конфіг
	 *
	 * @params $key назва індексу масива
	 *
	 * @return значення
	 */
	function config($key){
		require APP_PATH.'config.php';
		if(array_key_exists($key, $config)){
			return $config[$key];
		} else {
			return null;
		}
	}
	
	/**
	 * Завантажуємо бібліотеки за замовчуванням
	 *
	 * @params масив назв бібліотек
	 */
	function autoload($arr){
		foreach($arr as $class){
			$class = strtolower($class);
			if($this->config($class)) {
				$this->$class = $this->register($class, $this->config($class));
			} else {
				$this->$class = $this->register($class);
			}
		}
	}
	
	/**
	 * Завантажуємо подання
	 *
	 * @params $view назва подання
	 * @params $data параметри
	 */	
	function view($view, $data = null){
		if($data){
			foreach($data as $key => $value){
				$$key = $value;
			}
		}
		$view_path = APP_PATH.'views'.DIRSEP.$view.'.php';
		if($_SESSION['alias']->service) $view_path = APP_PATH.'services'.DIRSEP.$_SESSION['alias']->service.DIRSEP.'views'.DIRSEP.$view.'.php';
		if(file_exists($view_path)){
			require $view_path;
		}
	}
	
	/**
	 * Завантажуємо подання сервісу
	 *
	 * @params $service назва сервісу
	 * @params $view назва подання
	 * @params $data параметри
	 */	
	function service_view($service = null, $view, $data = null){
		if(empty($service)) return null;
		if($data){
			foreach($data as $key => $value){
				$$key = $value;
			}
		}
		$view_path = APP_PATH.'services'.DIRSEP.$service.DIRSEP.'views'.DIRSEP.$view.'.php';
		if(file_exists($view_path)){
			require $view_path;
		}
	}
	
	/**
	 * Завантажуємо подання головної розмітки (layout)
		*
	 * @params $view_file назва подання
	 * @params $data параметри
	 */	
	function page_view($view_file = false, $data = null){
		if($data){
			foreach($data as $key => $value){
				$$key = $value;
			}
		}
		$view_path = APP_PATH.'views'.DIRSEP.'page_view.php';
		if($_SESSION['alias']->service && $view_file) {
			if(isset($_SESSION['option']->uniqueDesign) && $_SESSION['option']->uniqueDesign > 0 && $view_file) $view_file = APP_PATH.'views'.DIRSEP.$_SESSION['alias']->alias.DIRSEP.$view_file;
			else $view_file = APP_PATH.'services'.DIRSEP.$_SESSION['alias']->service.DIRSEP.'views'.DIRSEP.$view_file;
		}		
		if(file_exists($view_path)){
			require $view_path;
		}
	}
	
	/**
	 * Завантажуємо подання повідомлення з головною розміткою (layout)
	 *
	 * @params $data параметри
	 */	
	function notify_view($data = null){
		if($data){
			foreach($data as $key => $value){
				$$key = $value;
			}
		}
		$view_path = APP_PATH.'views'.DIRSEP.'page_view.php';
		$view_file = 'notify_view';
		if(file_exists($view_path)){
			require $view_path;
		}
	}

	function page_404(){
		header('HTTP/1.0 404 Not Found');
		exit(file_get_contents('404.html'));
	}

	/**
	 * Завантажуємо подання розмітки панелі керування сайтом
		*
	 * @params $view_file назва подання
	 * @params $data параметри
	 */	
	function admin_view($view_file = false, $data = null){
		if($data){
			foreach($data as $key => $value){
				$$key = $value;
			}
		}
		$view_path = APP_PATH.'views'.DIRSEP.'admin/admin_view.php';
		if($_SESSION['alias']->service && $view_file) $view_file = APP_PATH.'services'.DIRSEP.$_SESSION['alias']->service.DIRSEP.'views'.DIRSEP.$view_file;
		if(file_exists($view_path)){
			require $view_path;
		}
	}
	
	/**
	 * Завантажуємо моделі
	 *
	 * @params $model назва моделі
	 */	
	function model($model){
		$model_path = APP_PATH.'models'.DIRSEP.$model.'.php';
		if(file_exists($model_path)){
			require_once $model_path;
			$this->$model = new $model();
			if(is_object($this->db)){
				$this->$model->db = $this->db;
			}
		}
	}
	
	
	/**
	 * Завантажуємо моделі
	 *
	 * @params $model назва моделі
	 */	
	function smodel($model){
		if($_SESSION['service']->name){
			$model_path = APP_PATH.'services'.DIRSEP.$_SESSION['service']->name.DIRSEP.'models'.DIRSEP.$model.'.php';
			if(file_exists($model_path)){
				require_once $model_path;
				$this->$model = new $model();
				if(is_object($this->db)){
					$this->$model->db = $this->db;
				}
			}
		}
	}

	/**
	 * Завантажуємо моделі
	 *
	 * @params $model назва моделі
	 */	
	function service($service, $method = '', $data = array()){
		$model_path = APP_PATH.'services'.DIRSEP.$service.DIRSEP.$service.'.php';
		if(file_exists($model_path)){

			$old = $_SESSION['alias']->service;
			if(isset($_SESSION['service'])) $old_service = clone $_SESSION['service'];

			$this->model('wl_services_model');
			$this->wl_services_model->loadService($service);
			require_once $model_path;
			if($method != ''){
				$service = new $service();
				if(is_callable(array($service, '_remap'))){
					$service->_remap($method, $data);
				} else if(is_callable(array($service, $method))) {
					$service->$method($data);
				}
			} else {
				$this->$service = new $service();
			}

			$_SESSION['alias']->service = $old;
			if(isset($old_service)) $_SESSION['service'] = $old_service;
		}
	}
	
	/**
	 * Завантажуємо бібліотеки
	 *
	 * @params $class назва класу/файла
	 * @params $ref посилання на обєкт
	 */
	function library($class, $ref){
		if(empty($class)) return false;
		$class = strtolower($class);
		if($this->config($class)) {
			$this->$class = $this->register($class, $this->config($class));
		} else {
			$this->$class = $this->register($class);
		}
	}

	/**
	 * Створюємо об'єкти і зберігаємо в реєстрі
	 *
	 * @param $class назва класу
	 *
	 * @return створений об'єкт
	 */
	function register($class, $params = null){
		$registry = Registry::singleton();
		if($registry->get($class) !== null)
			return $registry->get($class);
		
		$class_path = SYS_PATH.'libraries'.DIRSEP.$class.'.php';
		if(file_exists($class_path)){
			require $class_path;
			$obj = new $class($params);
			if(is_object($obj)){
				$registry->set($class, $obj);
				return $obj;
			}
		}

		return null;
	}
	
}

?>

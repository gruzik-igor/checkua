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
	function __construct()
	{
		if($this->config('autoload'))
			$this->autoload( $this->config('autoload') );
		$this->model('wl_alias_model');
	}
	
	/**
	 * Завантажуємо конфіг
	 *
	 * @params $key назва індексу масива
	 *
	 * @return значення
	 */
	function config($key)
	{
		require APP_PATH.'config.php';
		if(array_key_exists($key, $config))
			return $config[$key];
		else
			return null;
	}
	
	/**
	 * Завантажуємо бібліотеки за замовчуванням
	 *
	 * @params масив назв бібліотек
	 */
	function autoload($arr)
	{
		foreach($arr as $class) {
			$class = strtolower($class);
			if($this->config($class))
				$this->$class = $this->register($class, $this->config($class));
			else
				$this->$class = $this->register($class);
		}
	}

	function authorize()
    {
        if(isset($_COOKIE['auth_id']) && empty($_SESSION['user']->id))
        {
            $this->model('wl_auth_model');
            $this->wl_auth_model->authByCookies($_COOKIE['auth_id']);
        }
    }
	
	/**
	 * Завантажуємо подання
	 *
	 * @params $view назва подання
	 * @params $data параметри
	 */	
	function view($view, $data = null)
	{
		unset($_SESSION['alias-cache'][$_SESSION['alias']->id]);
		if($data)
			foreach($data as $key => $value) {
				$$key = $value;
			}
		$view_path = APP_PATH.'views'.DIRSEP.$view.'.php';
		if($_SESSION['alias']->service)
		{
			if(isset($_SESSION['option']->uniqueDesign) && $_SESSION['option']->uniqueDesign == 2)
				$view_path = APP_PATH.'views'.DIRSEP.$_SESSION['alias']->alias.DIRSEP.$view.'.php';
			else
				$view_path = APP_PATH.'services'.DIRSEP.$_SESSION['alias']->service.DIRSEP.'views'.DIRSEP.$view.'.php';
		}
		if(file_exists($view_path))
			require $view_path;
	}
	
	/**
	 * Завантажуємо подання головної розмітки (layout)
		*
	 * @params $view_file назва подання
	 * @params $data параметри
	 */	
	function page_view($view_file = false, $data = null)
	{
		unset($_SESSION['alias-cache'][$_SESSION['alias']->id]);
		if($data)
		{
			$this->wl_alias_model->setContentRobot($data);
			foreach($data as $key => $value) {
				$$key = $value;
			}
		}
		$view_path = APP_PATH.'views'.DIRSEP.'page_view.php';
		if($_SESSION['alias']->service && $view_file)
		{
			if(isset($_SESSION['option']->uniqueDesign) && $_SESSION['option']->uniqueDesign > 0 && $view_file)
				$view_file = APP_PATH.'views'.DIRSEP.$_SESSION['alias']->alias.DIRSEP.$view_file;
			else
				$view_file = APP_PATH.'services'.DIRSEP.$_SESSION['alias']->service.DIRSEP.'views'.DIRSEP.$view_file;
		}
		if(file_exists($view_path))
			require $view_path;
	}
	
	/**
	 * Завантажуємо подання повідомлення з головною розміткою (layout)
	 *
	 * @params $data параметри
	 */	
	function notify_view($data = null)
	{
		if($data)
			foreach($data as $key => $value) {
				$$key = $value;
			}
		$view_path = APP_PATH.'views'.DIRSEP.'page_view.php';
		$view_file = 'notify_view';
		if(file_exists($view_path))
		{
			require $view_path;
			exit();
		}
	}

	function page_404($update_SiteMap = true)
	{
		if($update_SiteMap)
		{
			$this->library('db');
			if($_SESSION['alias']->content === NULL)
			{
				$page = $this->db->sitemap_add($_SESSION['alias']->content, $_SESSION['alias']->link, 404);
				$referer = array();
				$referer['sitemap'] = $page->id;
				$referer['from'] = (!empty($_SERVER['HTTP_REFERER'])) ? $_SERVER['HTTP_REFERER'] : 'direct link';
				$referer['date'] = time();
				$this->db->insertRow('wl_sitemap_from', $referer);
			}
			else
				$this->db->sitemap_update($_SESSION['alias']->content, 'code', 404);
		}
		header('HTTP/1.0 404 Not Found');
		$view_path = APP_PATH.'views'.DIRSEP.'page_view.php';
		$view_file = '404_view';
		if(file_exists($view_path))
		{
			require $view_path;
			exit();
		}
	}

	/**
	 * Завантажуємо подання розмітки панелі керування сайтом
		*
	 * @params $view_file назва подання
	 * @params $data параметри
	 */	
	function admin_view($view_file = false, $data = null)
	{
		unset($_SESSION['alias-cache'][$_SESSION['alias']->id]);
		if($data)
			foreach($data as $key => $value) {
				$$key = $value;
			}
		$view_path = APP_PATH.'views'.DIRSEP.'admin/admin_view.php';
		if($_SESSION['alias']->service && $view_file)
			$view_file = APP_PATH.'services'.DIRSEP.$_SESSION['alias']->service.DIRSEP.'views'.DIRSEP.'admin'.DIRSEP.$view_file;
		if(file_exists($view_path))
		{
			require $view_path;
			$_SESSION['_POST'] = $_SESSION['_GET'] = NULL;
			exit();
		}
	}
	
	/**
	 * Завантажуємо моделі
	 *
	 * @params $model назва моделі
	 */	
	function model($model)
	{
		if(isset($this->$model) && is_object($this->$model))
			return true;
		$model_path = APP_PATH.'models'.DIRSEP.$model.'.php';
		if(file_exists($model_path))
		{
			require_once $model_path;
			$this->$model = new $model();
			if(is_object($this->db))
				$this->$model->db = $this->db;
			if(isset($this->data) && is_object($this->data))
				$this->$model->data = $this->data;
		}
	}
	
	
	/**
	 * Завантажуємо моделі
	 *
	 * @params $model назва моделі
	 */	
	function smodel($model)
	{
		if($_SESSION['service']->name)
		{
			$model_path = APP_PATH.'services'.DIRSEP.$_SESSION['service']->name.DIRSEP.'models'.DIRSEP.$model.'.php';
			if(file_exists($model_path))
			{
				require_once $model_path;
				$this->$model = new $model();
				if(is_object($this->db))
				{
					$this->$model->db = $this->db;
					$this->$model->data = $this->data;
				}
			}
		}
	}

	/**
	 * Завантажуємо функцію у контролері сайту або контролері сервісу згідно назви сторінки
	 *
	 * @params $alias адреса
	 * @params $method назва функції, яку викликаємо у контролері
	 * @params $data дані, що передаємо функції
	 * @params $admin позначка що відповідає за режим доступу та контролер панелі керування
	 */	
	function function_in_alias($alias, $method = '', $data = array(), $admin = false)
	{
		$rezult = NULL;
		$old_alias = $_SESSION['alias']->id;
		$this->library('db');

		if(is_numeric($alias))
			$alias = $this->db->getAllDataById('wl_aliases', $alias);
		else
			$alias = $this->db->getAllDataById('wl_aliases', $alias, 'alias');

		if(is_object($alias))
		{
			if($admin && !$this->userCan($alias->alias))
				return false;

			if(!isset($_SESSION['alias-cache'][$_SESSION['alias']->id]))
			{
				$_SESSION['alias-cache'][$_SESSION['alias']->id] = new stdClass();
				$_SESSION['alias-cache'][$_SESSION['alias']->id]->alias = clone $_SESSION['alias'];
				if(isset($_SESSION['option']))
					$_SESSION['alias-cache'][$_SESSION['alias']->id]->options = clone $_SESSION['option'];
				else
					$_SESSION['alias-cache'][$_SESSION['alias']->id]->options = null;
				if(isset($_SESSION['service']))
					$_SESSION['alias-cache'][$_SESSION['alias']->id]->service = clone $_SESSION['service'];
				else
					$_SESSION['alias-cache'][$_SESSION['alias']->id]->service = null;
			}

			if(isset($_SESSION['alias-cache'][$alias->id]))
			{
				if($alias->id != $_SESSION['alias']->id)
				{
					$_SESSION['alias'] = $_SESSION['alias-cache'][$alias->id]->alias;
					$_SESSION['option'] = $_SESSION['alias-cache'][$alias->id]->options;
					$_SESSION['service'] = $_SESSION['alias-cache'][$alias->id]->service;
				}
				
				if($admin == false)
				{
					$service = $alias->alias;
					if(isset($this->$service) && is_object($this->$service))
					{
						if(is_callable(array($this->$service, '_remap')))
							$rezult = $this->$service->_remap($method, $data);
						else if(is_callable(array($this->$service, $method)))
							$rezult = $this->$service->$method($data);
					}
				}
			}
			else
			{
				$_SESSION['alias'] = $alias;
				$_SESSION['alias-cache'][$alias->id] = new stdClass();
				$_SESSION['alias-cache'][$alias->id]->alias = clone $alias;
				$_SESSION['alias-cache'][$alias->id]->options = null;
				$_SESSION['alias-cache'][$alias->id]->service = null;
			}

			if($rezult === NULL)
			{
				if($alias->service > 0)
				{
					$this->model('wl_services_model');
					if($this->wl_services_model->loadService($alias->service))
					{
						$service = $_SESSION['alias']->service;
						$model_path = APP_PATH.'services'.DIRSEP.$service.DIRSEP.$service.'.php';
						if($admin)
							$model_path = APP_PATH.'services'.DIRSEP.$service.DIRSEP.'admin.php';
					}
				}
				else
				{
					$this->model('wl_alias_model');
					$this->wl_alias_model->init($alias->alias);
					$service = $alias->alias;
					$model_path = APP_PATH.'controllers'.DIRSEP.$service.'.php';
					if($admin)
						$model_path = APP_PATH.'controllers'.DIRSEP.'admin'.DIRSEP.$service.'.php';
				}

				if(isset($_SESSION['option']))
					$_SESSION['alias-cache'][$alias->id]->options = clone $_SESSION['option'];
				if(isset($_SESSION['service']))
					$_SESSION['alias-cache'][$alias->id]->service = clone $_SESSION['service'];

				if(file_exists($model_path))
				{
					require_once $model_path;
					if($method != '')
					{
						$controller = $service;
						$service = new $service();
						if(is_callable(array($service, '_remap')))
							$rezult = $service->_remap($method, $data);
						else if(is_callable(array($service, $method)))
							$rezult = $service->$method($data);
						if($admin == false)
							$this->$controller = clone $service;
					}
				}
			}
			
			if($old_alias != $alias->id)
			{
				$_SESSION['alias'] = clone $_SESSION['alias-cache'][$old_alias]->alias;
				$_SESSION['option'] = clone $_SESSION['alias-cache'][$old_alias]->options;
				$_SESSION['service'] = clone $_SESSION['alias-cache'][$old_alias]->service;
			}
		}
		return $rezult;
	}
	
	/**
	 * Завантажуємо бібліотеки
	 *
	 * @params $class назва класу/файла
	 * @params $ref посилання на обєкт
	 */
	function library($class, $ref)
	{
		if(empty($class))
			return false;
		$class = strtolower($class);
		if($this->config($class))
			$this->$class = $this->register($class, $this->config($class));
		else
			$this->$class = $this->register($class);
	}

	/**
	 * Здійснюємо перенаправлення на вказану адресу
	 *
	 * @params $link адреса перенаправлення. Якщо відсутня, то на сторінку звідки прийшов користувач
	 * @params $use_SITE_URL чи використовувати префікс адреси сайту до адреси перенаправлення
	 */
	function redirect($link = '', $use_SITE_URL = true)
	{
		if($link == '' || $link[0] == '#')
		{
			if($_SERVER['HTTP_REFERER'])
				$link = $_SERVER['HTTP_REFERER'] . $link;
			else
				$link = SITE_URL;
		}
		elseif($use_SITE_URL)
			$link = SITE_URL . $link;
		header ('HTTP/1.1 303 See Other');
		header("Location: {$link}");
		exit();
	}

	function json($value = '')
	{
		header('Content-type: application/json');
		echo json_encode($value);
		$_SESSION['_POST'] = $_SESSION['_GET'] = NULL;
		exit();
	}

	function text($word = '', $alias = -1)
	{
		if($word != '')
		{
			$this->model('wl_language_model');
			return $this->wl_language_model->get($word, $alias);
		}
		return $word;
	}

	/**
	 * Створюємо об'єкти і зберігаємо в реєстрі
	 *
	 * @param $class назва класу
	 *
	 * @return створений об'єкт
	 */
	function register($class, $params = null)
	{
		$registry = Registry::singleton();
		if($registry->get($class) !== null)
			return $registry->get($class);
		
		$class_path = SYS_PATH.'libraries'.DIRSEP.$class.'.php';
		if(file_exists($class_path))
		{
			require $class_path;
			$obj = new $class($params);
			if(is_object($obj))
			{
				$registry->set($class, $obj);
				return $obj;
			}
		}
		return null;
	}
	
}

?>
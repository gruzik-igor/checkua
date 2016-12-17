<?php  if (!defined('SYS_PATH')) exit('Access denied');

/*
 * Шлях: SYS_PATH/base/controller.php
 *
 * Всі контроллери успадковують цей клас
 */

class Controller extends Loader {
	
	public $load;
	
	/**
	 * Визиваємо батьківський конструктор та копіюємо ідентифікатор на обєкт
         * це потрібно для надання логіки. Відтак для завантаження скажімо бібліотеки
         * ми не пишемо $this->library(library_name), а пишемо $this->load->library(library_name)
	 */
	function __construct()
    {
        parent::__construct();
        $this->load = $this;
	}
	
	/**
	 * Викликаємо батьківський метод з ідентифікатором на обєкт
	 *
	 * @params $class ім'я класу
	 * @params $var завжди не задана(null)
	 */
	public function library($classname, $var = null)
    {
		parent::library($classname, $this);
	}

    public function userIs()
    {
    	if(isset($_SESSION['user']->id) && $_SESSION['user']->id > 0)
    		return true;
    	return false;
    }

    public function userCan($permissions = '')
    {
    	if(isset($_SESSION['user']->id) && $_SESSION['user']->id > 0)
        {
    		if($_SESSION['user']->admin)
                return true;
    		else
            {
    			if($permissions == '')
                    $permissions = $_SESSION['alias']->alias;
    			if($_SESSION['user']->manager == 1 && in_array($permissions, $_SESSION['user']->permissions))
                    return true;
    		}
    	}
    	return false;
    }
	
}

?>
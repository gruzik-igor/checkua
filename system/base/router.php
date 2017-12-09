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
	
	function __construct($req = null)
	{
		if($req != null)
		{
			$this->request = $req;
			$this->findRoute();
		}
	}
	
	/**
	 * Шукаємо шлях
	 */
	function findRoute()
	{
		parent::library('db', $this);
		$this->authorize();

		if(empty($_POST) && count($_GET) == 1 && preg_match('/[A-Z]/', $this->request))
		{
			header('HTTP/1.1 301 Moved Permanently');
		    header('Location: ' . SITE_URL.mb_strtolower($this->request));
		    exit();
		}

		$parts = explode('/', $this->request);
		$path = APP_PATH.'controllers'.DIRSEP;
		$admin = false;

		$_SESSION['amp'] = false;
		if(end($parts) == 'amp')
		{
			$_SESSION['amp'] = true;
			array_pop($parts);
		}
		
		if(empty($parts[0]))
			array_shift($parts);
		if(empty($parts))
			$parts[] = 'main';

		if($this->request == 'wlLoadPage404')
			new Page404();
		elseif($parts[0] == 'admin')
		{
			if(isset($_SESSION['user']->id) && $_SESSION['user']->id > 0)
			{
				if($_SESSION['user']->admin)
					$admin = true;
    			if($_SESSION['user']->manager == 1 && (!isset($parts[1]) || isset($parts[1]) && in_array($parts[1], $_SESSION['user']->permissions)))
    				$admin = true;
    			if($admin)
    			{
    				if($_SESSION['language'] && $_SESSION['language'] != $_SESSION['all_languages'][0])
    					parent::redirect(SERVER_URL.$this->request, false);
					if(count($parts) == 1)
					{
						$parts[] = 'admin';
						$_SESSION['alias'] = new stdClass();
						$_SESSION['alias']->service = false;
						$_SESSION['service'] = new stdClass();
					}
					else
					{
						parent::model('wl_alias_model');
						$this->wl_alias_model->init($parts[1], $this->request);
						$this->wl_alias_model->admin_options();
					}
				}
				else
					new Page404();
			}
			else
				parent::redirect('login');
		}
		else
		{
			parent::model('wl_alias_model');

			if(empty($_POST))
			{
				if(@!$_SESSION['user']->admin && @!$_SESSION['user']->manager)
				{
					parent::model('wl_statistic_model');
					$this->wl_statistic_model->set_views();
				}

				parent::model('wl_cache_model');
				if($this->wl_cache_model->init($this->request))
				{
					if(@!$_SESSION['user']->admin && @!$_SESSION['user']->manager)
						$this->wl_statistic_model->set_page($this->wl_cache_model->page);
					
					$this->wl_alias_model->initFromCache($this->wl_cache_model->page);
					$this->wl_cache_model->get();
				}
				else
					$this->wl_alias_model->init($parts[0], $this->request);
			}
			else
				$this->wl_alias_model->init($parts[0], $this->request);
		}

		if($this->isService())
		{
			if($admin)
			{
				$path = APP_PATH.'services'.DIRSEP.$_SESSION['alias']->service.DIRSEP.'admin';
				$this->method = (!isset($parts[2])) ? 'index' : $parts[2];
			}
			else
			{
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
		
		if(is_readable($path.'.php'))
		{
			require $path.'.php';
			$this->callController();
		}
		else
			new Page404();

		$_SESSION['_POST'] = $_SESSION['_GET'] = NULL;
		if(!$admin && empty($_POST) && (isset($parts[0]) && !in_array($parts[0], array('admin', 'app', 'assets', 'style', 'js', 'css', 'images', 'upload')) || $this->method == 'index'))
		{
			if($this->wl_cache_model->page == false)
			{
				$this->wl_cache_model->page = $this->db->sitemap_add($_SESSION['alias']->content, $this->request);
				
				if(@!$_SESSION['user']->admin && @!$_SESSION['user']->manager)
				{
					$this->wl_statistic_model->set_page($this->wl_cache_model->page);
					$this->wl_statistic_model->updatePageIndex();
				}
				$this->wl_cache_model->set();
			}
			else
			{
				if(@!$_SESSION['user']->admin && @!$_SESSION['user']->manager)
				{
					$this->wl_statistic_model->set_page($this->wl_cache_model->page);
					$this->wl_statistic_model->updatePageIndex();
				}
				$this->wl_cache_model->set();

				if($sitemap = $this->wl_cache_model->SiteMap())
	            {
	                parent::library('SitemapGenerator');
	                foreach ($sitemap as $url) {
	                    if($url->link == 'main') $url->link = '';
	                    $this->sitemapgenerator->addUrl(SITE_URL.$url->link, date('c', $url->time), $url->changefreq, $url->priority/10);
	                }
	                try {
	                    // create sitemap
	                    $this->sitemapgenerator->createSitemap();
	                    // write sitemap as file
	                    $this->sitemapgenerator->writeSitemap();
	                    // update robots.txt file
	                    $this->sitemapgenerator->updateRobots();

	                    $this->db->updateRow('wl_options', array('value' => time()), array('service' => 0, 'alias' => 0, 'name' => 'sitemap_lastgenerate'));

	                    if($_SESSION['option']->sitemap_autosent == 1)
	                    {
	                        // submit sitemaps to search engines
	                        // $result = $this->sitemapgenerator->submitSitemap("yahooAppId");
	                        $result = $this->sitemapgenerator->submitSitemap();
	                        $this->db->updateRow('wl_options', array('value' => time()), array('service' => 0, 'alias' => 0, 'name' => 'sitemap_lastsent'));
	                    }
	                }
	                catch (Exception $exc) {
	                    $_SESSION['notify']->errors = $exc->getTraceAsString();
	                }
	            }
	        }
		}
	}
	
	/**
	 * Створюємо об'єкт і викликаємо метод
	 */	
	function callController()
	{
		$controller = new $this->class();
		$method = $this->method;
		if(is_callable(array($controller, '_remap'))) {
			$controller->_remap($method);
		} else if(is_callable(array($controller, $method)) && $method != 'library' && $method != 'db') {
			$controller->$method();
		} else {
			$controller->load->page_404();
		}
	}
	
	private function isService()
	{
		if(isset($_SESSION['alias']->service) && $_SESSION['alias']->service)
		{
			$path = APP_PATH.'services'.DIRSEP.$_SESSION['alias']->service.DIRSEP;
			if(is_file($path.$_SESSION['alias']->service.'.php'))
			{
				$this->class = $_SESSION['alias']->service;
				return true;
			}
		}
		return false;
	}
	
}

class Page404 extends Controller {

	function __construct($update_SiteMap = true)
	{
		parent::__construct();
		$this->load->page_404($update_SiteMap);
	}

}

?>

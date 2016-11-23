<?php 

class wl_cache_model extends Loader
{

	public $page = false;
	public $updateSiteMap = false;
	
	public function init($link)
	{
		$where['link'] = $link;
		if($_SESSION['language']) $where['language'] = $_SESSION['language'];
		
		$this->page = $this->db->getAllDataById('wl_sitemap', $where);
		if($this->page == false)
		{
			$this->page = new stdClass();
			$page = array();
			$page['link'] = $this->page->link = $link;
			$page['alias'] = $this->page->alias = 0;
			$page['content'] = $this->page->content = 0;
			$page['language'] = $this->page->language = $_SESSION['language'];
			$page['code'] = $this->page->code = 200;
			$page['data'] = $this->page->data = NULL;
			$page['time'] = $this->page->time = time();
			$this->db->insertRow('wl_sitemap', $page);
			$this->page->id = $this->db->getLastInsertedId();
			$this->updateSiteMap = true;
		}

		$_SESSION['alias']->siteMap = $this->page->id;
		$this->page->uniq_link = $link;
		if($_SESSION['language']) $this->page->uniq_link .= '/'.$_SESSION['language'];
	}

	public function get()
	{
		switch ($this->page->code) {
			case 200:
				if($this->page->data != '' && $this->page->data != NULL)
				{
					if(extension_loaded('zlib'))
						echo ( gzdecode ($this->page->data) );
					else
						echo ( $this->page->data );

					$this->showTime('load from cache');
					exit();
				}
				break;
			
			case 301:
				header ('HTTP/1.1 301 Moved Permanently');
				header("Location: ".SITE_URL.$this->page->data);
				exit();
				break;

			case 404:
				new Page404(false);
				break;
		}
		if($_SESSION['cache'])
			ob_start();
	}

	public function set()
	{
		$cache = array();

		if($_SESSION['alias']->content !== NULL && $this->page->alias != $_SESSION['alias']->id)
		{
			$cache['alias'] = $_SESSION['alias']->id;
			$cache['content'] = $_SESSION['alias']->content;
		}

		if($_SESSION['alias']->code != $this->page->code)
			$cache['code'] = $_SESSION['alias']->code;

		if($_SESSION['cache'] && $this->page->data == '')
		{
			$content = ob_get_contents();
			if(extension_loaded('zlib') && $data = gzencode ($content, 2))
				$cache['data'] = (string) $data;
			else
				$cache['data'] = (string) $content;
			$cache['time'] = time();

			ob_end_flush();
		}

		if(!empty($cache))
			$this->db->updateRow('wl_sitemap', $cache, $this->page->id);

		// $this->showTime();
		exit;
	}

	private function showTime()
	{
		$mem_end = memory_get_usage();
		$time_end = microtime(true);
		$time = $time_end - $GLOBALS['time_start'];
		$mem = $mem_end - $GLOBALS['mem_start'];
		$mem = round($mem/1024, 5);
		if($mem > 1024)
		{
			$mem = round($mem/1024, 5);
			$mem = (string) $mem . ' Мб';
		}
		else
			$mem = (string) $mem . ' Кб';

		$after = ($_SESSION['cache']) ? 'Cache активний' : 'Cache відключено';
		echo '<hr><center>Час виконання: '.round($time, 5).' сек. Використанок памяті: '.$mem.'. '.$after.'</center>';
	}

	public function SiteMap($force = false)
	{
		$update = true;
		if(!$force && $this->updateSiteMap)
		{
			$where = array('service' => 0, 'alias' => 0, 'name' => 'siteMapLastUpdate');
			if($siteMapLastUpdate = $this->db->getAllDataById('wl_options', $where))
			{
				if($siteMapLastUpdate->value + 3600 < time())
					$update = false;
			}
		}
		if($update || $force)
		{
			$where = array();
			$where['code'] = '!301';
			$where['+code'] = '!404';
			$where['priority'] = '>=0';
			$this->db->select('wl_sitemap', 'link, time, changefreq, priority', $where);
			return $this->db->get();
		}
		return false;
	}

}

?>
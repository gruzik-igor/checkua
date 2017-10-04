<?php 

class wl_cache_model extends Loader
{

	public $page = false;
	
	public function init($link)
	{
		$where['link'] = $link;
		if($_SESSION['language']) $where['language'] = $_SESSION['language'];
		
		$pages = $this->db->getAllDataByFieldInArray('wl_sitemap', $where);
		if(count($pages) > 1)
		{
			for ($i=1; $i < count($pages); $i++) {
				$this->db->deleteRow('wl_sitemap', $pages[$i]->id);
				$this->db->deleteRow('wl_sitemap_from', $pages[$i]->id, 'sitemap');
				$this->db->deleteRow('wl_statistic_pages', array('alias' => 0, 'content' => $pages[$i]->id));
			}
		}
		if(isset($pages[0]))
		{
			$this->page = $pages[0];
			$this->page->uniq_link = $link;
			if($_SESSION['language']) $this->page->uniq_link .= '/'.$_SESSION['language'];
			return true;
		}
		return false;
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

					if($_SESSION['option']->showTimeSiteGenerate)
						$this->showTime('load from cache');
					exit();
				}
				break;
			
			case 301:
				$referer = array();
				$referer['sitemap'] = $this->page->id;
				$referer['from'] = (!empty($_SERVER['HTTP_REFERER'])) ? $_SERVER['HTTP_REFERER'] : 'direct link';
				$referer['date'] = time();
				$this->db->insertRow('wl_sitemap_from', $referer);

				header ('HTTP/1.1 301 Moved Permanently');
				header("Location: ".SITE_URL.$this->page->data);
				exit();
				break;

			case 404:
				$referer = array();
				$referer['sitemap'] = $this->page->id;
				$referer['from'] = (!empty($_SERVER['HTTP_REFERER'])) ? $_SERVER['HTTP_REFERER'] : 'direct link';
				$referer['date'] = time();
				$this->db->insertRow('wl_sitemap_from', $referer);
				
				new Page404(false);
				break;
		}
		if($_SESSION['cache'])
			ob_start();
		$_SESSION['alias']->content = $this->page->content;
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

			ob_end_flush();
		}

		if(!empty($cache))
		{
			$cache['time'] = time();
			$this->db->updateRow('wl_sitemap', $cache, $this->page->id);
		}

		if($_SESSION['option']->showTimeSiteGenerate)
			$this->showTime();
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
		$update = $_SESSION['option']->sitemap_active;
		if(!$force && $update)
		{
			if($_SESSION['option']->sitemap_lastedit + 7200 < time())
				$update = false;
			if($update && $_SESSION['option']->sitemap_lastgenerate > $_SESSION['option']->sitemap_lastedit)
				$update = false;
			$lastedit_day = mktime (1, 0, 0, date("n", $_SESSION['option']->sitemap_lastedit), date("j", $_SESSION['option']->sitemap_lastedit), date("Y", $_SESSION['option']->sitemap_lastedit));
			if($lastedit_day + 3600 * 24 < time())
				$update = false;
		}
		if($update || $force)
		{
			$where = array();
			$where['alias'] = '>0';
			$where['code'] = '!301';
			$where['+code'] = '!404';
			$where['priority'] = '>=0';
			$this->db->select('wl_sitemap', 'language, link, time, changefreq, priority', $where);
			return $this->db->get();
		}
		return false;
	}

}

?>
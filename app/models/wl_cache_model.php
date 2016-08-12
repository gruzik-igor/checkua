<?php 

class wl_cache_model extends Loader
{

	public $page = false;
	
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
			$page['data'] = $this->page->data = '';
			$page['time'] = $this->page->time = time();
			$this->db->insertRow('wl_sitemap', $page);
			$this->page->id = $this->db->getLastInsertedId();
		}

		$this->page->uniq_link = $link;
		if($_SESSION['language']) $this->page->uniq_link .= '/'.$_SESSION['language'];
	}

	public function get()
	{
		if($_SESSION['cache'])
		{
			switch ($this->page->code) {
				case 200:
					if($this->page->data != '')
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
					if($this->page->data != '')
					{
						header ('HTTP/1.1 301 Moved Permanently');
						header("Location: ".SITE_URL.$this->page->data);
						exit();
					}
					break;

				case 404:
					$this->page_404();
					break;
			}

			ob_start();
		}
	}

	public function set()
	{
		$cache = array();

		if($_SESSION['cache'] && $this->page->data == '')
		{
			$content = ob_get_contents();
			if(extension_loaded('zlib') && $data = gzencode ($content, 2))
				$cache['data'] = (string) $data;
			else
				$cache['data'] = (string) $content;


			$this->db->updateRow('wl_sitemap', $cache, $this->page->id);

			ob_end_flush();
		}
		$this->showTime('before');
		exit;
	}

	private function showTime($after = '')
	{
		$mem_end = memory_get_usage();
		$time_end = microtime(true);
		$time = $time_end - $GLOBALS['time_start'];
		$mem = $mem_end - $GLOBALS['mem_start'];
		$mem = round($mem/1024, 5);
		echo 'Час виконання(сек): '.$time.' Використанок памяті(кб): '.$mem.' '.$after;
	}

}

?>
<?php

class wl_statistic_model{

	public function set($link)
	{	
		if($this->searchBot()) return true;

		$lastRow = $this->db->getQuery("SELECT id, day FROM `wl_statistic_views` WHERE ID = (SELECT MAX(ID) FROM `wl_statistic_views`)");
		$today = strtotime('today');

		if($today == $lastRow->day){
			if(!isset($_COOKIE['statisticViews'])) $update = ' `cookie` = `cookie` + 1, `unique` = `unique` + 1, `views` = `views` + 1 ';
			elseif(!isset($_SESSION['statistic'])) $update = ' `unique` = `unique` + 1, `views` = `views` + 1 ';
			else $update = ' `views` = `views` + 1 ';
			
			$this->db->executeQuery("UPDATE `wl_statistic_views` SET {$update} WHERE `id` = {$lastRow->id}");
		} else {
			$data['day'] = $today;
			$data['cookie'] = isset($_COOKIE['statisticViews']) ? 0 : 1;
			$data['unique'] = 1;
			$data['views'] = 1;

			$this->db->insertRow('wl_statistic_views', $data);
		}

		if(!isset($_SESSION['statistic'])){
			$_SESSION['statistic'] = new stdClass();
			$_SESSION['statistic']->pages = array();
		}

		if(!in_array($link, $_SESSION['statistic']->pages)){
			$_SESSION['statistic']->pages[] = $link;
			$this->updatePageViews($link, $today, true);
		} else {
			$this->updatePageViews($link, $today);
		}

		setcookie('statisticViews', 'views', time() + 3600*24*31, '/');
	}

	private function updatePageViews($link, $today, $unique = false)
	{
		$page['link'] = $link;
		$page['day'] = $today;

		$result = $this->db->getAllDataById('wl_statistic_pages', $page);
		if(!is_object($result)){
			$page['unique'] = 1;
			$page['views'] = 1;
			$this->db->insertRow('wl_statistic_pages', $page);
		} else {
			$update = $unique == true ? ' `unique` = `unique` + 1, `views` = `views` + 1 ' : ' `views` = `views` + 1 ';
			$this->db->executeQuery("UPDATE `wl_statistic_pages` SET {$update} WHERE `id` = {$result->id}");
		}
	}

	public function searchBot()
	{
		$bots = array('Googlebot', 'Yahoo', 'Slurp', 'MSNBot', 'Teoma', 'Scooter', 'ia_archiver', 'Lycos', 'Yandex', 'StackRambler', 'Mail.Ru', 'Aport', 'WebAlta', 'bot', 'Google');
		foreach ($bots as $bot) {
			if ( stristr($_SERVER['HTTP_USER_AGENT'], $bot) ) return true;
		}
		return false;
	}
	
}

?>
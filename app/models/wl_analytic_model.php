<?php

class wl_analytic_model {

	public function getViewers()
	{
		$day = strtotime('today') - 30*3600*24;
		$views = $this->db->getQuery("SELECT SUM(`unique`) as totalUsers, SUM(`cookie`) as newUsers, SUM(`views`) as viewsCount FROM `wl_statistic_views` WHERE `day` >= {$day}");
		if($views)
		{
			if($views->totalUsers == 0) $views->totalUsers = 1;

			$views->returnedUser = $views->totalUsers - $views->newUsers;
			$views->returnedPercentage = round($views->returnedUser * 100 / $views->totalUsers, 1) ;
			$views->newPercentage = round($views->newUsers * 100 / $views->totalUsers, 1)  ;

			$views->tableData = $this->db->getQuery("SELECT * FROM `wl_statistic_views` WHERE `day` >= {$day}", 'array');
		}
		return $views; 
	}

	public function getStatistic()
	{
		$where = array();
		if($alias = $this->data->get('alias'))
		{
			$where['alias'] = $alias;
			if($content = $this->data->get('content')) $where['content'] = $content;
		}
		if($language = $this->data->post('language')) $where['language'] = $language;
		if($min = $this->data->get('min'))
		{
			$where['day'] = '>='.$min;
		}
		if($max = $this->data->get('max'))
		{
			$where['day'] = '<='.$max;
		}
		if(empty($where))
			$this->db->select('wl_statistic_pages as s');
		else
			$this->db->select('wl_statistic_pages as s', '*', $where);
		$this->db->join('wl_sitemap', 'link', array('alias' => '#s.alias', 'content' => '#s.content'));
		$this->db->order('id DESC');
		if(isset($_SESSION['option']->paginator_per_page) && $_SESSION['option']->paginator_per_page > 0)
		{
			$start = 0;
			if(isset($_GET['per_page']) && is_numeric($_GET['per_page']) && $_GET['per_page'] > 0)
				$_SESSION['option']->paginator_per_page = $_GET['per_page'];
			if(isset($_GET['page']) && is_numeric($_GET['page']) && $_GET['page'] > 1)
				$start = ($_GET['page'] - 1) * $_SESSION['option']->paginator_per_page;
			$this->db->limit($start, $_SESSION['option']->paginator_per_page);
		}
		return $this->db->get('array');
	}

} 

?>
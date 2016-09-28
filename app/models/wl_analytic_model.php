<?php

class wl_analytic_model {

	public function getViewers()
	{
		$where = '';
		$day = strtotime('-1 month');

		$get = array('start' => 'date', 'end' => 'date');
		$get = $this->data->make($get, '_GET');
		if(isset($get['start']))
			$where = '`day` >= '.$get['start'];
		else
			$where = '`day` >= '.$day;
		if(isset($get['end']))
			$where .= ' AND `day` <= '.$get['end'];

		
		$views = $this->db->getQuery("SELECT SUM(`unique`) as totalUsers, SUM(`cookie`) as newUsers, SUM(`views`) as viewsCount FROM `wl_statistic_views` WHERE {$where}");
		if($views)
		{
			if($views->totalUsers == 0) $views->totalUsers = 1;

			$views->returnedUser = $views->totalUsers - $views->newUsers;
			$views->returnedPercentage = round($views->returnedUser * 100 / $views->totalUsers, 1) ;
			$views->newPercentage = round($views->newUsers * 100 / $views->totalUsers, 1)  ;

			$views->tableData = $this->db->getQuery("SELECT * FROM `wl_statistic_views` WHERE {$where}", 'array');
		}
		return $views; 
	}

	public function getStatistic()
	{
		$where = array();
		$alias = $this->data->get('alias');
		if(is_numeric($alias))
		{
			$where['alias'] = $alias;
			if($content = $this->data->get('content'))
				$where['content'] = $content;
		}
		if($language = $this->data->post('language'))
			$where['language'] = $language;
		$get = array('start' => 'date', 'end' => 'date');
		$get = $this->data->make($get, '_GET');
		if(isset($get['start']))
			$where['day'] = '>='.$get['start'];
		if(isset($get['end']))
			$where['+day'] = '<='.$get['end'];

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
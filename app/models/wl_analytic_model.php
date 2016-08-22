<?php

class wl_analytic_model {

	public function getViewers()
	{
		$day = strtotime('today') - 30*3600*24;
		$views = $this->db->getQuery("SELECT SUM(`unique`) as totalUsers, SUM(`cookie`) as newUsers, SUM(`views`) as viewsCount FROM `wl_statistic_views` WHERE `day` >= {$day}");
		
		if($views->totalUsers == 0) $views->totalUsers = 1;

		$views->returnedUser = $views->totalUsers - $views->newUsers;
		$views->returnedPercentage = round($views->returnedUser * 100 / $views->totalUsers, 1) ;
		$views->newPercentage = round($views->newUsers * 100 / $views->totalUsers, 1)  ;

		$views->tableData = $this->db->getQuery("SELECT * FROM `wl_statistic_views` WHERE `day` >= {$day}", 'array');

		return $views; 
	}

} 

?>
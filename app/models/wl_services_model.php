<?php

class wl_services_model{

	public function getServicesList()
	{
		return $this->db->getAllData('wl_services');
	}

	public function loadService($service)
	{
		$this->db->executeQuery("SELECT `id`, `name`, `table` FROM `wl_services` WHERE `name` = '{$service}' AND `active` = 1");
		if($this->db->numRows() == 1){
			$service = $this->db->getRows();
			$_SESSION['alias']->service = $service->name;
			@$_SESSION['service']->name = $service->name;
			$_SESSION['service']->table = $service->table;

			$this->db->executeQuery("SELECT `name`, `value` FROM `wl_options` WHERE `service` = {$service->id} AND `alias` = 0");
			if($this->db->numRows() > 0){
				$options = $this->db->getRows('array');
				foreach($options as $opt){
					$key = $opt->name;
					@$_SESSION['option']->$key = $opt->value;
				}
			}
			$this->db->executeQuery("SELECT `name`, `value` FROM `wl_options` WHERE `service` = {$service->id} AND `alias` = {$_SESSION['alias']->id}");
			if($this->db->numRows() > 0){
				$options = $this->db->getRows('array');
				foreach($options as $opt){
					$key = $opt->name;
					$_SESSION['option']->$key = $opt->value;
				}
			}

			return true;
		}
		return false;
	}
	
}

?>
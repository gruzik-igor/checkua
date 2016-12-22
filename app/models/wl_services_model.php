<?php

class wl_services_model {

	public function getServicesList()
	{
		return $this->db->getAllData('wl_services');
	}

	public function loadService($service)
	{
		if($service = $this->db->getQuery("SELECT `id`, `name`, `table` FROM `wl_services` WHERE `id` = '{$service}'"))
		{
			$_SESSION['alias']->service = $service->name;
			$_SESSION['service'] = new stdClass();
			$_SESSION['service']->name = $service->name;
			$_SESSION['service']->table = $service->table;

			if($options = $this->db->getAllDataByFieldInArray('wl_options', array('service' => $service->id, 'alias' => 0)))
				foreach($options as $opt){
					$key = $opt->name;
					@$_SESSION['option']->$key = $opt->value;
				}

			if($options = $this->db->getAllDataByFieldInArray('wl_options', array('service' => $service->id, 'alias' => $_SESSION['alias']->id)))
				foreach($options as $opt){
					$key = $opt->name;
					$_SESSION['option']->$key = $opt->value;
				}

			return true;
		}
		return false;
	}
	
}

?>
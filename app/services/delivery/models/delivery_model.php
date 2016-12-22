<?php 

class delivery_model
{

	public function table($sufix = '')
	{
		return $_SESSION['service']->table.$sufix;
	}

	public function method_add()
	{
		$delivery['name'] = $this->data->post('name');
		$delivery['site'] = $this->data->post('site');
		$delivery['info'] = $this->data->post('info');
		$delivery['active'] = 1;
		$delivery['date_add'] = time();
		$this->db->insertRow($this->table('_methods'), $delivery);
		return true;
	}

	public function method_update($id)
	{
		$delivery['name'] = $this->data->post('name');
		$delivery['site'] = $this->data->post('site');
		$delivery['info'] = $this->data->post('info');
		$delivery['placeholder'] = $this->data->post('placeholder');
		$delivery['active'] = $this->data->post('active');
		$this->db->updateRow($this->table('_methods'), $delivery, $id);
		return true;
	}

    public function get($code)
    {
    	$delivery = $this->db->getAllDataById($this->table(), $code, 'code');
    	if($delivery)
    	{
    		$today = strtotime('today');
    		if($delivery->day != $today) return $this->updatePrivat24($code);
    		else return $delivery->delivery;
    	}
    	return false;
	}

}

?>
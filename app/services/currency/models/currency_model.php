<?php 

class currency_model
{

	public function table($sufix = '')
	{
		return $_SESSION['service']->table.$sufix;
	}

	public function create()
	{
		$currency['code'] = $this->data->post('code');
		$currency['currency'] = $this->data->post('currency');
		$currency['day'] = strtotime('today');
		$id = $this->db->insertRow($this->table(), $currency);
		if($id == 1)
			$this->db->updateRow($this->table(), array('default' => 1), $id);
		if($_SESSION['option']->saveToHistory)
		{
			$history['currency'] = $id;
			$history['value'] = $currency['currency'];
			$history['day'] = $currency['day'];
			$history['from'] = 'Користувач: '.$_SESSION['user']->id.'. '.$_SESSION['user']->name;
			$history['update'] = time();
			$this->db->insertRow($this->table('_history'), $history);
		}
		return true;
	}

	public function update($id, $newCurrency = -1)
	{
		if($newCurrency < 0)
			$currency['currency'] = $this->data->post('currency');
		else
			$currency['currency'] = $newCurrency;
		$currency['day'] = strtotime('today');
		$this->db->updateRow($this->table(), $currency, $id);
		if($_SESSION['option']->saveToHistory)
		{
			$history['currency'] = $id;
			$history['value'] = $currency['currency'];
			$history['day'] = $currency['day'];
			if($newCurrency < 0)
				$history['from'] = 'Користувач: '.$_SESSION['user']->id.'. '.$_SESSION['user']->name;
			else
				$history['from'] = 'Privat24';
			$history['update'] = time();
			$this->db->insertRow($this->table('_history'), $history);
		}
		return true;
	}

	public function setDefault($id)
	{
		$this->db->executeQuery('UPDATE `'.$this->table().'` SET `default` = 0');
		$currency['default'] = $this->data->post('default');
		return $this->db->updateRow($this->table(), $currency, $id);
	}

	public function updatePrivat24($code = false)
	{
		$sale = 0;
		$currency_all = $this->db->getAllData($this->table());
		if($currency_all)
		{
			$currency = array();
			foreach ($currency_all as $row) {
				$currency[$row->code] = clone $row;
			}

			$json = file_get_contents('https://api.privatbank.ua/p24api/pubinfo?json&exchange&coursid=5');
			$privat24 = json_decode($json);
			if($privat24)
			{
				foreach ($privat24 as $row) {
					if(isset($currency[$row->ccy]) && $currency[$row->ccy]->currency != $row->sale) $this->update($currency[$row->ccy]->id, $row->sale);
					if($code == $row->ccy) $sale = $row->sale;
				}
			}
		}
		if($code) return $sale;
		return true;
	}

    public function get($code = false)
    {
    	$currency = false;
    	if($code)
    		$currency = $this->db->getAllDataById($this->table(), $code, 'code');
    	else
    		$currency = $this->db->getAllDataById($this->table(), 1, 'default');
    	if($currency)
    	{
    		if($_SESSION['option']->autoUpdate)
    		{
	    		$today = strtotime('today');
	    		if($currency->day != $today)
	    			return $this->updatePrivat24($code);
	    		else
	    			return $currency->currency;
	    	}
    		return $currency->currency;
    	}
    	return false;
	}

}

?>
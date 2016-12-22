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
		$this->db->insertRow($this->table(), $currency);
		if($_SESSION['option']->saveToHistory)
		{
			$history['currency'] = $this->db->getLastInsertedId();
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

    public function get($code)
    {
    	$currency = $this->db->getAllDataById($this->table(), $code, 'code');
    	if($currency)
    	{
    		$today = strtotime('today');
    		if($currency->day != $today) return $this->updatePrivat24($code);
    		else return $currency->currency;
    	}
    	return false;
	}

}

?>
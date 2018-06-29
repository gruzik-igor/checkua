<?php

/*

 	Service "NovaPoshta.ua 1.0"
	for WhiteLion 1.0

*/

class novaposhta extends Controller {

    function _remap($method, $data = array())
    {
        if (method_exists($this, $method)) {
        	if(empty($data)) $data = null;
            return $this->$method($data);
        } else {
        	$this->index($method);
        }
    }

    public function index($uri)
    {
        $this->load->page_404(false);
    }

    public function __get_Search($content)
    {
    	return false;
    }

    public function __get_info($info)
    {
        $text = '';
        if(!empty($info['city']))
            $text .= $this->text('Місто').': <b>'.$info['city'].'</b>';
        if(!empty($info['department']))
            $text .= ', '.$this->text('Відділення').': <b>'.$info['department'].'</b>';
        return $text;
    }

    public function __get_Shipping_to_cart($userShipping)
    {
        $warehouse_by_city = array();
        if($warehouselist = file_get_contents (APP_PATH.'services'.DIRSEP.$_SESSION['alias']->service.DIRSEP.'js'.DIRSEP.'np.json'))
        {
            $warehouselist = json_decode ($warehouselist, true);
            foreach($warehouselist['response'] as $warehouse) {
                $warehouse_by_city[$warehouse['city']][] = array(
                    'city' => $warehouse['city'],  //назва міста
                    'address' => preg_replace('/\([^)]+\)/', '', $warehouse['address']), //адрес відділення
                    'number' => $warehouse['number'] //номер відділення
                );
            }
            ksort($warehouse_by_city);
        }
        $this->load->view('__cart_view', array('userShipping' => $userShipping, 'warehouse_by_city' => json_encode($warehouse_by_city)));
    }

    public function __set_Shipping_from_cart()
    {
        $info = array('text' => '');
        $info['info'] = array('city' => '', 'department' => '');
        if($city = $this->data->post('shipping-city'))
        {
            $info['info']['city'] = $city;
            $info['text'] .= $this->text('Місто').': '.$city;
        }
        if($department = $this->data->post('shipping-novaposhta'))
        {
            $info['info']['department'] = $department;
            $info['text'] .= ' '.$this->text('Відділення').': '.$department;
        }
        return $info;
    }

}

?>
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

    public function __get_delivery_info($id)
    {
        return  $this->db->select($_SESSION['service']->table.'_carts as d', '*', $id)
                            ->join($_SESSION['service']->table.'_methods', 'name as method_name, site as method_site, department', '#d.method')
                            ->get('single');
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
        $data = array();
        $data['user'] = $_SESSION['user']->id;
        $data['method'] = $this->data->post('shipping-method');
        $data['address'] = $this->data->post('shipping-city');
        if($department = $this->data->post('shipping-department'))
            $data['address'] .= ': '.$department;
        if($department = $this->data->post('shipping-department-other'))
            $data['address'] .= ': '.$department;
        if($address = $this->data->post('shipping-address'))
            $data['address'] .= '. '.$address;
        $data['receiver'] = $this->data->post('name');
        $data['phone'] = $this->data->post('phone');

        if($this->data->post('shipping-default') == 1)
        {
            if($default = $this->db->getAllDataById($_SESSION['service']->table.'_users', $_SESSION['user']->id, 'user'))
                $this->db->updateRow($_SESSION['service']->table.'_users', $data, $default->id);
            else
                $this->db->insertRow($_SESSION['service']->table.'_users', $data);
        }

        $data['comment'] = NULL;

        $delivery = array('shipping_alias' => $_SESSION['alias']->id);
        $delivery['shipping_id'] = $this->db->insertRow($_SESSION['service']->table.'_carts', $data);
        $delivery['info'] = $this->__get_delivery_info($delivery['shipping_id']);
        return $delivery;
    }

}

?>
<?php

/*

 	Service "Delivery 1.0"
	for WhiteLion 1.0

*/

class delivery extends Controller {
				
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
        if($this->userIs())
        {
            $methods = $this->db->getAllDataByFieldInArray($_SESSION['service']->table.'_methods', 1, 'active');
            $delivery = $this->db->getAllDataById($_SESSION['service']->table.'_users', $_SESSION['user']->id, 'user');
            if(!$delivery)
            {
                $delivery = new stdClass();
                $delivery->id = 0;
                $delivery->method = 0;
                $delivery->address = '';
            }
            $this->load->page_view('user_view', array('delivery' => $delivery, 'methods' => $methods));
        }
    	$this->load->page_404();
    }

    public function __get_Search($content)
    {
    	return false;
    }

    public function __show_user_form()
    {
        $methods = $this->db->getAllDataByFieldInArray($_SESSION['service']->table.'_methods', 1, 'active');
        $delivery = $this->db->getAllDataById($_SESSION['service']->table.'_users', $_SESSION['user']->id, 'user');
        if(!$delivery)
        {
            $delivery = new stdClass();
            $delivery->id = 0;
            $delivery->method = 0;
            $delivery->address = '';
        }
        $this->load->view('user_view', array('delivery' => $delivery, 'methods' => $methods));
    }

    public function __get_delivery_info($id)
    {
        $this->db->select($_SESSION['service']->table.'_carts as d', '*', $id);
        $this->db->join($_SESSION['service']->table.'_methods', 'name as method_name, site as method_site', '#d.method');

        return $this->db->get('single');
    }

    public function __get_Shipping_to_cart()
    {
        if($this->userIs() && isset($_SESSION['cart'])) {
            $methods = $this->db->getAllDataByFieldInArray($_SESSION['service']->table.'_methods', 1, 'active');
            $delivery = $this->db->getAllDataById($_SESSION['service']->table.'_users', $_SESSION['user']->id, 'user');
            if(!$delivery)
            {
                $delivery = new stdClass();
                $delivery->id = 0;
                $delivery->method = 0;
                $delivery->address = '';
            }
            $this->load->view('cart_view', array('delivery' => $delivery, 'methods' => $methods));
        }
    }

    public function __set_Default_from_cart()
    {
        $delivery['method'] = $this->data->post('shippingMethod');
        $delivery['address'] = $this->data->post('shippingAddress');
        $shipping = $this->db->getAllDataById($_SESSION['service']->table.'_users', $_SESSION['user']->id, 'user');
        if($shipping)
        {
            $this->db->updateRow($_SESSION['service']->table.'_users', $delivery, $shipping->id);
        }
        else
        {
            $delivery['user'] = $_SESSION['user']->id;
            $this->db->insertRow($_SESSION['service']->table.'_users', $delivery);
        }
        return true;
    }

    public function __get_Method_info($id = 0)
    {
        return $this->db->getAllDataById($_SESSION['service']->table.'_methods', $id);
    }

    public function __set_Delivery_from_cart($data = array())
    {
        $this->db->insertRow($_SESSION['service']->table.'_carts', $data);

        return $this->db->getLastInsertedId();
    }

    public function save()
    {
        if($this->userIs())
        {
            $delivery['method'] = $this->data->post('method');
            $delivery['address'] = $this->data->post('address');
            if($this->data->post('id') == 0)
            {
                $delivery['user'] = $_SESSION['user']->id;
                $this->db->insertRow($_SESSION['service']->table.'_users', $delivery);
            }
            else
            {
                $check = $this->db->getAllDataById($_SESSION['service']->table.'_users', $this->data->post('id'));
                if($check && $check->user == $_SESSION['user']->id) $this->db->updateRow($_SESSION['service']->table.'_users', $delivery, $this->data->post('id'));
            }
            $this->redirect('#tabs-delivery');
        }
        $this->load->page_404();
    }

}

?>
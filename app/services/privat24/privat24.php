<?php

/*

 	Service "Privat24 1.0"
	for WhiteLion 1.0

*/

class privat24 extends Controller {
				
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
    	$this->load->page_404();
    }

    public function validate()
    {
        $id = $this->data->uri(2);
        if(is_numeric($id) && $id > 0)
        {
            $this->load->smodel('privat24_model');
            $pay = $this->privat24_model->validate($id);
            if($pay)
            {
                $this->load->function_in_alias($pay->cart_alias, '__set_Payment', $pay, true);
                $this->_addtionall($pay);
            }
        }
        else $this->load->page_404();
    }

    public function __get_Search($content)
    {
    	return false;
    }

    public function __get_Payment($cart)
    {
        $this->load->smodel('privat24_model');
        $pay = $this->privat24_model->create($cart);
        $pay->return_url = $cart->return_url;
        $this->load->view('privat24_form_view', array('pay' => $pay));
    	return true;
    }

    private function _addtionall($pay)
    {
        $this->db->select('s_cart as c', 'id, user, total, currency', $pay->cart_id);
        $this->db->join('wl_users', 'ballance', '#c.user');
        $cart = $this->db->get();

        $ballance = round($pay->amount * 0.99 / $cart->currency, 2);
        $this->db->executeQuery("UPDATE `notification` SET `show` = 0 WHERE `user` = {$cart->user} AND `cart` = {$cart->id}");

        if(($cart->total - $cart->ballance) <= $ballance && $cart->status < 3)
        {
            $this->db->executeQuery("UPDATE `wl_users` SET `ballance` = 0 WHERE `id` = {$cart->user}");

            $payments['cart'] = $pay->cart_id;
            $payments['user'] = $cart->user;
            $payments['amount_wont'] = $payments['amount_do'] = $pay->amount;
            $payments['currency'] = $cart->currency;
            $payments['manager'] = 0;
            $payments['credit'] = $cart->total;
            $payments['ballance'] = 0;
            $payments['status'] = 2;
            $payments['info'] = 'Оплата '.$pay->comment;
            $payments['date_add'] = $payments['date_edit'] = $pay->date_edit;
            $this->db->insertRow('payments', $payments);
        }
    }

}

?>
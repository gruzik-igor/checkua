<?php

class prices extends Controller {

	function _remap($method)
    {
        if (method_exists($this, $method) && $method != 'library' && $method != 'db') {
            $this->$method();
        } else {
            $this->index($method);
        }
    }

	function index()
	{
		$prices = $this->db->getQuery("SELECT p.*, pg.name as group_name FROM `prices` as p LEFT JOIN `prices_group` as pg ON p.group = pg.id ORDER BY p.group ASC", 'array');
		$groups = $this->db->getAllData('prices_group', 'position');
		$this->load->admin_view('prices/index_view', array('prices' => $prices, 'groups' => $groups));
	}

	function save()
	{
		$prices = $res = array();
		$id= $this->data->post('id');
		$prices['name'] = $this->data->post('name');
		$prices['unit'] = $this->data->post('unit');
		$prices['amount'] = $this->data->post('amount');
		$prices['group'] = $this->data->post('group');

		if($this->db->updateRow('prices', $prices, $id))
			$res['result'] = true;


		header('Content-type: application/json');
		echo json_encode($res);
		exit;
	}

	function add()
	{
		$prices = array();
		$prices['name'] = $this->data->post('name');
		$prices['unit'] = $this->data->post('unit');
		$prices['amount'] = $this->data->post('amount');
		$prices['group'] = $this->data->post('group');

		$this->db->insertRow('prices', $prices);

		header('Location:'.$_SERVER['HTTP_REFERER']);
	}

}

?>
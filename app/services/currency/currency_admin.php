<?php

/*

 	Service "Currency 2.0"
	for WhiteLion 1.0

*/

class currency_admin extends Controller {
				
    function _remap($method, $data = array())
    {
        $this->wl_alias_model->setContent();
    	$_SESSION['alias']->breadcrumb = array($_SESSION['alias']->name => '');
        if (method_exists($this, $method)) {
        	if(empty($data)) $data = null;
            return $this->$method($data);
        } else {
        	$this->index($method);
        }
    }

    public function index($uri)
    {
    	$currents = $this->db->getAllData($_SESSION['service']->table);
	    $this->load->admin_view('index_view', array('currents' => $currents));
    }

    public function add()
    {
        $this->load->admin_view('add_view');
    }

    public function history()
    {
        $this->load->admin_view('history_view');
    }

    public function save()
    {
        $this->load->smodel('currency_model');
        if($this->data->post('id') == 0)
            $this->currency_model->create();
        elseif($this->data->post('id') > 0)
        {
            if(isset($_POST['default']))
                $this->currency_model->setDefault($this->data->post('id'));
            else
                $this->currency_model->update($this->data->post('id'));
        }
        
        if(!empty($_POST['json']))
            $this->load->json(['success' => true]);
        else
            $this->redirect('admin/'.$_SESSION['alias']->alias);
    }

    public function updatePrivat24()
    {
        $this->load->smodel('currency_model');
        $this->currency_model->updatePrivat24();
        $this->redirect('admin/'.$_SESSION['alias']->alias);
    }
	
}

?>
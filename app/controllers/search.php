<?php 

class Search extends Controller {

	function index(){
		$errors = (!isset($_GET['search']) || strlen($_GET['search']) >= 3) ? null : "Введіть мінімум 3 символи";
		$data = null;
		if(isset($_GET['search']) && $_GET['search'] != '' && strlen($_GET['search']) >= 3)
		{
			$this->load->model('wl_search_model');
			$data = $this->wl_search_model->search($this->data->get('search'));
		}
		$this->load->page_view('search/search_view', array('data' => $data, 'errors' => $errors));
	}
}

?>
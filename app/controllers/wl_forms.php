<?php

/**
*
*/
class wl_forms extends Controller
{

	function save()
	{
		$type = 'get';
		if(!empty($_POST)) $type = 'post';

		if(!empty($_POST['form_name']) || !empty($_GET['form_name'])){
			$this->load->library('form');
			$this->form->saveFromForm($this->data->$type('form_name'));
			header("Location: ".SITE_URL."profile");
			exit();
		}
	}
}

?>
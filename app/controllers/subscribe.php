<?php

class subscribe extends Controller {
				
    function _remap($method)
    {
        if (method_exists($this, $method)) {
            $this->$method();
        } else {
            $this->index($method);
        }
    }

    function index(){
		if(isset($_POST['email'])){
			$this->load->library('data');
			$this->load->model('subscribe_model');
			if($this->subscribe_model->add_mail($this->data->post('email', true))){
				$success = 'Дякуємо! Ваш email успішно доданий до бази!';
				$this->load->view('page_view', array('view_file' => 'notify_view', 'success' => $success));
			} else {
				$errors = 'Увага! Ваш email вже є у базі!';
				$this->load->view('page_view', array('view_file' => 'notify_view', 'errors' => $errors));
			}
		} elseif($this->userCan('subscribe')){
    		header("Location: ".SITE_URL.'admin/subscribe');
    		exit();
    	} else {
    		header("Location: ".SITE_URL);
    		exit();
    	}
    }
	
}

?>
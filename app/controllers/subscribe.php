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

    public function index(){
		$this->load->library('validator');
        $this->validator->setRules('email', $this->data->post('email'), 'required|email|3..40');

        if($this->validator->run())
        {
			$this->load->model('subscribe_model');
			if($this->subscribe_model->add_mail($this->data->post('email', true))) {
				$this->load->notify_view(array('success' => 'Дякуємо! Ваш email успішно доданий до бази!'));
			} else {
				$this->load->notify_view(array('success' => 'Увага! Ваш email вже є у базі!'));
			}
    	} else {
    		$this->load->notify_view(array('errors' => 'Невірний формат email'));
    	}
    }

    public function add()
    {
        $result = array('add' => false, 'message' => 'Невірний формат email');

        $this->load->library('validator');
        $this->validator->setRules('email', $this->data->post('email'), 'required|email|3..40');

        if($this->validator->run())
        {
            $this->load->model('subscribe_model');
            if($this->subscribe_model->add_mail($this->data->post('email', true))) {
                $result['message'] = 'Дякуємо! Ваш email успішно доданий до бази!';
                $result['add'] = true;
            } else {
                $result['message'] = 'Увага! Ваш email вже є у базі!';
            }
        }

        $this->load->json($result);
    }
	
}

?>
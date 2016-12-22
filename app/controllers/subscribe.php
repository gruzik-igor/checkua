<?php

class subscribe extends Controller {
				
    private $additionall = array('phone'); // false додаткові поля при реєстрації. Згодом можна використовувати у ідентифікації, тощо
    private $new_user_type = 5; // Ід типу новозареєстрованого користувача
    private $special_types = array('order-call' => 6, 'price' => 7); // ШД спец типу (за полем type). Інакше false
    private $after_good_view = 'temp_subscribe_success';

    public function index()
    {
		$this->load->library('validator');
        $this->validator->setRules('email', $this->data->post('email'), 'required|email|3..40');

        if($this->validator->run())
        {
			$this->load->model('wl_user_model');
            $info['email'] = $this->data->post('email');
            $info['name'] = $this->data->post('name');
            $info['password'] = '';
            $info['photo'] = '';
            $additionall = array();
            if(!empty($this->additionall))
            {
                foreach ($this->additionall as $key)
                {
                    $value = $this->data->post($key);
                    if($value)
                        $additionall[$key] = $value;
                }
            }
            if($this->special_types && array_key_exists($this->data->post('type'), $this->special_types))
                $this->new_user_type = $this->special_types[$this->data->post('type')];

            $_SESSION['notify']->title = 'Підписка';
			if($user = $this->wl_user_model->add($info, $additionall, $this->new_user_type, false))
				$_SESSION['notify']->success = 'Дякуємо! Ваш email успішно доданий до бази!';
			else
				$_SESSION['notify']->success = 'Увага! Ваш email вже є у базі!';
			$this->load->view($this->after_good_view, array('user' => $user));
    	}
        else
        {
            $_SESSION['notify']->errors = 'Невірний формат email';
    		$this->load->notify_view();
    	}
    }
	
}

?>
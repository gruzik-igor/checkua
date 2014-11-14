<?php

/*
 * Контроллер використовується для POST авторизації.
 */

class Login extends Controller {

    /*
     * Метод за замовчуванням. Якщо сесії не існує то виводим форум для входу.
     */
    public function index(){
        if(isset($_SESSION['user']->id) && $_SESSION['user']->id > 0){
            header("Location: ". SITE_URL);
            exit();
        } else {
            $this->load->page_view('users/login_view');
        }
    }

    /*
     * Оброблюємо вхідні POST параметри.
     */
    public function process(){
        $this->load->library('validator');
        $this->validator->setRules('email', $this->data->post('email'), 'required|email|3..40');		
        $this->validator->setRules('Поле пароль', $this->data->post('password'), 'required|5..40');
        
        if($this->validator->run()){
            $this->load->model('wl_user_model');
			$sequred = false;
			if($this->data->post('sequred') == true) $sequred = true;
            if($this->wl_user_model->checkUser($this->data->post('email'), $this->data->post('password'), $sequred) ){
				if($_SESSION['user']->verify == 1){

					$auth_id = md5($_SESSION['user']->email.'|'.$this->data->post('password'));
					setcookie('auth_id', $auth_id, time() + 3600*24*31, '/');
					$this->wl_user_model->set_auth_id($auth_id);
					
					if($this->data->post('json') == true){					
						$res['result'] = true;
						$res['additional-control-panel'] = '';
							
						header('Content-type: application/json');
						echo json_encode($res);
						exit;
					} else header('Location: '.SITE_URL.'admin');					
				} else {
					if($this->data->post('json') == true){
						$res['href'] = true;
						$res['link'] = SITE_URL . 'login/confirmed';
							header('Content-type: application/json');
							echo json_encode($res);
							exit;
					} else {
						header("Location: ".SITE_URL.'login/confirmed');
						exit;					
					}	
				}
            } else {
				if($this->data->post('json') == true){
					$res['result'] = false;
					$res['login_error'] = $this->wl_user_model->user_errors;
						header('Content-type: application/json');
						echo json_encode($res);
						exit;
				} else {
           			$this->load->page_view('users/login_view', array('login_error' => $this->wl_user_model->user_errors));
				}
            }
        } else {
			if($this->data->post('json') == true){
					$res['result'] = false;
					$res['login_error'] = $this->validator->getErrors('');
						header('Content-type: application/json');
						echo json_encode($res);
						exit;
			} else {
       			$this->load->page_view('users/login_view', array('login_error' => $this->validator->getErrors()));
			}
        }
    }
	
	public function confirmed(){
		if(isset($_SESSION['user']->name)){
			$this->load->model('wl_ntkd_model');
			$this->wl_ntkd_model->setContent(1);

			$this->load->page_view('users/confirmed_view');
		} else header('Location: '.SITE_URL . 'login');
	}
	
	// public function password()
 //    {
 //        $email = $this->db->sanitizeString('123321@gmail.com');
 //        $password = '123321';
 //        $password = md5($password);
 //        $password = sha1($password . SYS_PASSWORD);
 //        echo($password);
 //    }
    
}
?>

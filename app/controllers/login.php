<?php

/*
 * Контроллер використовується для POST авторизації.
 */

class Login extends Controller {

	private $after = 'profile';

    /*
     * Метод за замовчуванням. Якщо сесії не існує то виводим форум для входу.
     */
    public function index()
    {
    	$_SESSION['alias']->content = 0;
    	$_SESSION['alias']->code = 201;
    	
        if($this->userIs())
        	$this->redirect($this->after);
        else
        {
        	$this->load->library('facebook');
            $this->load->view('profile/login_view');
        }
    }

    /*
     * Оброблюємо вхідні POST параметри.
     */
    public function process()
    {
        $this->load->library('validator');
        $this->validator->setRules('email', $this->data->post('email'), 'required|email|3..40');
        $this->validator->setRules('Поле пароль', $this->data->post('password'), 'required|5..40');

        if($this->validator->run())
        {
            $this->load->model('wl_user_model');
            if($status = $this->wl_user_model->login('email', $_POST['password'], $this->data->post('sequred')))
            {
            	if($actions = $this->db->getAllDataByFieldInArray('wl_aliases_cooperation', array('alias1' => 0, 'type' => 'login')))
					foreach ($actions as $action) {
						$this->load->function_in_alias($action->alias2, '__user_login');
					}

				if($this->data->post('json')){
					$res['result'] = true;
					$this->load->json($res);
				} else 
					header('Location: '.SITE_URL.$status->load);
				exit;
            }
            else
            {
				if($this->data->post('json'))
				{
					$res['result'] = false;
					$res['login_error'] = $this->wl_user_model->user_errors;
					$this->load->json($res);
				}
				else
				{
					$_SESSION['notify'] = new stdClass();
           			$_SESSION['notify']->errors = $this->wl_user_model->user_errors;
				}
            }
        }
        else
        {
			if($this->data->post('json'))
			{
				$res['result'] = false;
				$res['login_error'] = $this->validator->getErrors('');
				$this->load->json($res);
			} else {
				$_SESSION['notify'] = new stdClass();
       			$_SESSION['notify']->errors = $this->validator->getErrors();
			}
        }
        $this->redirect();
    }

	public function confirmed()
	{
		$_SESSION['alias']->code = 201;
		if($this->userIs())
			$this->load->view('profile/signup/confirmed_view');
		$this->load->redirect('login');
	}

	public function emailSend()
	{
		$_SESSION['alias']->code = 201;
		$_SESSION['notify'] = new stdClass();
		if ($this->userIs() && $_SESSION['user']->status != 1)
		{
			$user = $this->db->getAllDataById('wl_users', $_SESSION['user']->id);

			$this->load->library('mail');
			$info['name'] = $user->name;
			$info['email'] = $user->email;
			$info['auth_id'] = $user->auth_id;
			if($this->mail->sendTemplate('signup/user_signup', $user->email, $info))
				$_SESSION['notify']->success = 'Лист з кодом підтвердження відправлено.<br>Увага! Повідомлення може знаходитися у папці СПАМ.';
			else
				$_SESSION['notify']->errors = 'Виникла помилка при відправленні листа';
		}
		$this->redirect();
	}

	public function facebook()
	{
		$_SESSION['alias']->code = 201;
		$this->load->library('facebook');
		if($_SESSION['option']->facebook_initialise)
		{
			// Get User ID
			$user = $this->facebook->getUser();
			
			// We may or may not have this data based on whether the user is logged in.
			//
			// If we have a $user id here, it means we know the user is logged into
			// Facebook, but we don't know if the access token is valid. An access
			// token is invalid if the user logged out of Facebook.
			
			if ($user)
			{
				try {
					// Proceed knowing you have a logged in user who's authenticated.
					$user_profile = $this->facebook->api('/me?fields=email,id,name,link');
				} catch (FacebookApiException $e) {
					error_log($e);
					$user = null;
				}
			}

			// Login or logout url will be needed depending on current user state.
			if ($user)
			{
				// $logoutUrl = $facebook->getLogoutUrl();
				$this->load->model('wl_user_model');
				if($status = $this->wl_user_model->login('facebook', $user_profile['id']))
				{
					header('Location: '.SITE_URL.$status->load);
					exit;
				}
				elseif($this->wl_user_model->userExists($user_profile['email']))
				{
					$_SESSION['facebook'] = $_SESSION['facebook_id'] = $user_profile['id'];
					$_SESSION['facebook_link'] = $user_profile['link'];
					$_SESSION['_POST']['email'] = $user_profile['email'];
				}
				else
				{
					$_SESSION['facebook'] = false;
				}
				$this->redirect();
			}
			else
			{
				// $statusUrl = $facebook->getLoginStatusUrl();
				$loginUrl = $this->facebook->getLoginUrl();
				header('Location: '.$loginUrl);
				exit;
			}
		}
		else
			$this->redirect();
	}

}

?>
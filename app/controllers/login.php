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
        	$this->load->library('googlesignin');
            $this->load->view('profile/login_view');
        }
    }

    /*
     * Оброблюємо вхідні POST параметри.
     */
    public function process()
    {
        $this->load->library('validator');
        $email = '';
    	if($email = $this->data->post('email'))
    		$email = strtolower($email);
		$this->validator->setRules('E-mail', $email, 'required|email');
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
				{
					if($redirect = $this->data->post('redirect'))
						$this->redirect($redirect);
					else
						$this->redirect();
						// header('Location: '.SITE_URL.$status->load);
				}
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
        $this->redirect('login');
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
		$res = array('result' => false, 'message' => 'Error validate facebook access Token');
		$this->load->library('facebook');
		if($_SESSION['option']->userSignUp && $_SESSION['option']->facebook_initialise)
		{
			$accessToken = $this->data->post('accessToken');
			$user_profile = null;

			if ($accessToken)
			{
				$this->facebook->setAccessToken($accessToken);

				try {
					$user_profile = $this->facebook->api('/me?fields=email,id,name,link');
				} catch (FacebookApiException $e) {
					error_log($e);
					$user_profile = null;
				}
			}

			if ($user_profile)
			{
				$this->load->model('wl_user_model');
				if($status = $this->wl_user_model->login('facebook', $user_profile['id']))
				{
					if($status->id != 1)
					{
						$auth_id = md5($_SESSION['user']->email.'|facebook auto login|auth_id|'.time());
						setcookie('auth_id', $auth_id, time() + 3600*24*31, '/');

						$this->db->updateRow('wl_users', array('status' => 1, 'auth_id' => $auth_id), $_SESSION['user']->id);
					}
					if(empty($_SESSION['user']->photo))
					{
						$facebookPhotoLink = 'https://graph.facebook.com/'.$user_profile['id'].'/picture?width=9999';
						if(file_exists($facebookPhotoLink))
							$this->wl_user_model->setPhotoByLink($facebookPhotoLink);
					}
					$res['result'] = true;
				}
				else
				{
					$info = array();
					$info['email'] = $user_profile['email'];
				    $info['name'] = $user_profile['name'];
				    $info['status'] = 1;
				    $info['photo'] = NULL;
				    $additionall['facebook'] = $user_profile['id'];
				    if(!empty($user_profile['link']))
				    	$additionall['facebook_link'] = $user_profile['link'];
					if($__user = $this->wl_user_model->add($info, $additionall, 0, false, 'by facebook'))
					{
						$this->wl_user_model->setSession($__user);
						$auth_id = md5($_SESSION['user']->email.'|facebook auto login|auth_id|'.time());
						setcookie('auth_id', $auth_id, time() + 3600*24*31, '/');
						$this->db->updateRow('wl_users', array('status' => 1, 'auth_id' => $auth_id), $_SESSION['user']->id);

						if(empty($__user->photo))
						{
							$facebookPhotoLink = 'https://graph.facebook.com/'.$user_profile['id'].'/picture?width=9999';
							if(file_exists($facebookPhotoLink))
								$this->wl_user_model->setPhotoByLink($facebookPhotoLink);
						}

						if(!isset($_POST['ajax']))
							$this->redirect($__user->load);
						else
							$res['result'] = true;
					}
					else
						$res['message'] = $this->wl_user_model->user_errors;
				}
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
			$res['message'] = 'Login by facebook is closed';
		$this->load->json($res);
	}

	public function google()
	{
		$_SESSION['alias']->code = 201;
		$res = array('result' => false, 'message' => 'Error validate google access Token');
		if($_SESSION['option']->userSignUp)
		{
			$this->load->library('googlesignin');
			if($user = $this->googlesignin->validate())
			{
				$this->load->model('wl_user_model');
				if($status = $this->wl_user_model->login('google', $user['id']))
				{
					if($status->id != 1 && $user['verified_email'])
					{
						$auth_id = md5($_SESSION['user']->email.'|google auto login|auth_id|'.time());
						setcookie('auth_id', $auth_id, time() + 3600*24*31, '/');

						$this->db->updateRow('wl_users', array('status' => 1, 'auth_id' => $auth_id), $_SESSION['user']->id);
					}
					if(!empty($user['picture']) && empty($_SESSION['user']->photo))
						$this->wl_user_model->setPhotoByLink($user['picture']);
					$res['result'] = true;
				}
				else
				{
					$info = array();
					$info['email'] = $user['email'];
				    $info['name'] = $user['name'];
				    $info['status'] = 1;
				    $info['photo'] = NULL;
				    $additionall['google'] = $user['id'];
				    $additionall['google_link'] = $user['link'];
				    $additionall['gender'] = $user['gender'];
					if($__user = $this->wl_user_model->add($info, $additionall, 0, false, 'by google'))
					{
						$this->wl_user_model->setSession($__user);
						$auth_id = md5($_SESSION['user']->email.'|google auto login|auth_id|'.time());
						setcookie('auth_id', $auth_id, time() + 3600*24*31, '/');
						$this->db->updateRow('wl_users', array('status' => 1, 'auth_id' => $auth_id), $_SESSION['user']->id);

						if(!empty($user['picture']) && empty($__user->photo))
							$this->wl_user_model->setPhotoByLink($user['picture']);

						if(!isset($_POST['ajax']))
							$this->redirect($__user->load);
						else
							$res['result'] = true;
					}
					else
						$res['message'] = $this->wl_user_model->user_errors;
				}
			}
		}
		else
			$res['message'] = 'Login by google is closed';
		$this->load->json($res);
	}

}

?>
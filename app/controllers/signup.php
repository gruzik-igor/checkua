<?php

class Signup extends Controller {

    private $errors = array();
    private $name = 'first_name, last_name'; // 'name'||'first_name, last_name' ім'я в одній змінній чи 2-х
    public $additionall = array('phone'); // false додаткові поля при реєстрації. Згодом можна використовувати у ідентифікації, тощо
    private $new_user_type = 4; // Ід типу новозареєстрованого користувача

    function _remap($method, $data = array())
    {
    	if(isset($_SESSION['option']->new_user_type))
    		$this->new_user_type = $_SESSION['option']->new_user_type;
        if (method_exists($this, $method)) {
        	if(empty($data)) $data = null;
            return $this->$method($data);
        } else {
        	$this->index($method);
        }
    }

    public function index()
    {
    	$where = array('service' => 0, 'alias' => 4, 'name' => 'userSignUp');
    	$userSignUp = $this->db->getAllDataById('wl_options', $where);
    	if(!$this->userIs())
    	{
    		if($userSignUp && $userSignUp->value == 1)
    		{
    			$this->wl_alias_model->setContent();
    			$this->load->library('facebook');
	    		if($_SESSION['option']->facebook_initialise)
	        		$this->load->view('profile/signup/index_view');
	        	else
	        		$this->load->view('profile/signup/email_view');
    		}
    		else
    			$this->load->page_404();
    	}
        else
        	$this->redirect('profile');
    }

    public function email()
    {
    	if(!$this->userIs())
    	{
    		$this->wl_alias_model->setContent();
    		$this->load->library('facebook');
        	$this->load->view('profile/signup/email_view');
    	}
        else
        	$this->redirect('profile');
    }

    public function process()
    {
		if(!$this->userIs())
		{
			$_SESSION['notify'] = new stdClass();

	        $this->load->library('validator');
	        if($this->name == 'name')
				$this->validator->setRules("Ім'я", $this->data->post('name'), 'required');
			else
			{
				$this->validator->setRules("Ім'я", $this->data->post('first_name'), 'required');
				$this->validator->setRules("Прізвище", $this->data->post('last_name'), 'required');
			}
			$email = '';
	    	if($email = $this->data->post('email'))
	    		$email = strtolower($email);
			$this->validator->setRules('E-mail', $email, 'required|email');
			$this->validator->setRules('Пароль', $this->data->post('password'), 'required|5..20');
			$this->validator->password($this->data->post('password'), $this->data->post('re-password'));
	        if($this->validator->run())
	        {
	            $this->load->model('wl_user_model');
	            $info['email'] = $email;
		    	$info['name'] = $this->data->post('name');
		    	$info['password'] = $_POST['password'];
		    	$info['photo'] = '';
		    	if(isset($_POST['first_name']) && isset($_POST['last_name']))
		    		$info['name'] = $this->data->post('first_name') .' '. $this->data->post('last_name');
		    	$additionall = array();
		    	if(!empty($this->additionall))
				{
					foreach ($this->additionall as $key) {
						$value = $this->data->post($key);
						if($value)
							$additionall[$key] = $value;
					}
				}
                if($user = $this->wl_user_model->add($info, $additionall, $this->new_user_type))
                {
                	$this->load->library('mail');
					$info['auth_id'] = $user->auth_id;
					if($this->mail->sendTemplate('signup/user_signup', $user->email, $info))
					{
						$_SESSION['notify']->title = 'Реєстрація пройшла успішно!';
						$_SESSION['notify']->success = 'На поштову скриньку відправлено лист з <b>кодом підтвердження</b> та подальшими інструкціями. <b>УВАГА!</b> Лист може знаходитися у папці <b>СПАМ!</b>';
					}
					else 
						$_SESSION['notify']->errors = 'Виникла помилка при додаванні нового користувача';
                }
                else
                	$_SESSION['notify']->errors = $this->wl_user_model->user_errors;
	        }
	        else
	            $_SESSION['notify']->errors = '<ul>'.$this->validator->getErrors('<li>', '</li>').'</ul>';
	        $this->redirect();
		}
		$this->redirect('profile');
    }

	public function confirmed()
	{
		if($this->userIs() && isset($_POST['code']))
		{
			$_SESSION['notify'] = new stdClass();
			$this->load->model('wl_user_model');
			if($status = $this->wl_user_model->checkConfirmed($_SESSION['user']->email, $this->data->post('code')))
			{
				$_SESSION['notify']->success = 'Підтвердження пройшло успішно!';
				$this->redirect($status->load);
			}
			else 
			{
				$_SESSION['notify']->errors = 'Код підтвердження не співпав!';
				$this->redirect();
			}
		}
		$this->load->page_404();
	}

	public function get_confirmed()
	{
		$_SESSION['notify'] = new stdClass();
		if (isset($_GET['code']) and isset($_GET['email']))
		{
			$this->load->model('wl_user_model');
			if ($status = $this->wl_user_model->checkConfirmed($this->data->get('email'), $this->data->get('code')))
			{
				$_SESSION['notify']->success = 'Підтвердження пройшло успішно!';
				$this->redirect($status->load);
			}
			else
			{
				$_SESSION['notify']->errors = 'Код підтвердження не співпав!';
				$this->redirect('login');
			}
		}
		$this->load->page_404();
	}

	public function check_email()
	{
		$this->load->model('wl_user_model');
		$res['result'] = $this->wl_user_model->userExists($this->data->post('email'));
		$res['message'] = $this->wl_user_model->user_errors;
		$this->load->json($res);
	}

	public function facebook()
	{
		$this->load->library('facebook');

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
			if(isset($user_profile['email'])){
				$this->load->model('wl_user_model');

				$res = array('result' => false);

				$info['email'] = $user_profile['email'];
			    $info['name'] = $_SESSION['facebook_name'] = $user_profile['name'];
			    $info['password'] = 'facebook';
			    $info['photo'] = '';
			    $additionall['facebook'] = $user_profile['id'];
			    $additionall['facebook_link'] = $user_profile['link'];
				if($user = $this->wl_user_model->add($info, $additionall, $this->new_user_type, false, 'by facebook'))
				{
					$this->wl_user_model->setSession($user);

					if(!isset($_POST['ajax']))
					{	
						$this->redirect($user->load);
						exit;
					} 
					else 
					{
						$res['result'] = true;
						$this->load->json($res);
					}
				}
			} 

			$this->redirect('login/facebook');
		}
		else
		{
			$loginUrl = $this->facebook->getLoginUrl();
			header('Location: '.$loginUrl);
			exit;
		}
	}

	// function checkPhone()
	// {
	// 	$res = array('result' => false, 'message' => '');
	// 	$phone = $this->data->post('phone');
	// 	if(isset($phone)){
	// 		$this->load->model('wl_user_model');
	// 		$res['result'] = $this->wl_user_model->phoneExists($phone);
	// 		if(count($this->wl_user_model->user_errors) == 1) $res['message'] = $this->wl_user_model->user_errors[0];
	// 	}
	// 	header('Content-type: application/json');
	// 	echo json_encode($res);
	// 	exit;
	// }

	// public function phone_confirmed()
	// {
	// 	$res = array('result' => false, 'message' => '');
	// 	$phone = str_replace(' ', '', $this->data->post('phone'));
	// 	$id = $_SESSION['user']->id;
	// 	$this->db->executeQuery("SELECT * FROM wl_users WHERE phone = '{$phone}' AND `id` != $id");
	// 	if($this->db->numRows() == 0){
	// 		if(is_numeric($phone) && (strlen($phone) == 13) && isset($id)){
	//             $this->db->executeQuery("UPDATE wl_users SET phone = '{$phone}' WHERE id = $id");
	//             $_SESSION['code'] = rand(10000 , 99999);
	//             $this->load->library('turbosms');
	//             $this->turbosms->send($phone, $_SESSION['code']);
	//             $res['result'] = true;
	//             $res['message'] = 'Зараз на телефон вам прийде код підтвердження';
	//         }
	//         else $res['message'] = 'Не вірний формат телефону (Приклад: +380 12 345 6789).';
	//     } else $res['message'] = 'Користувач з таким телефоном вже існує';

	// 	header('Content-type: application/json');
	// 	echo json_encode($res);
	// 	exit;
	// }

	// public function code_confirmed()
	// {
	// 	$res = array('result' => false, 'message' => '');
	// 	$code = $this->data->post('code');
	// 	$id = $_SESSION['user']->id;
	// 	if($code == $_SESSION['code']){
	// 		$this->db->executeQuery("UPDATE wl_users SET confirmed = confirmed+2 WHERE id = $id ");

	// 		$res['result'] = true;
 //            $res['message'] = 'Успішно!';
 //            if(!isset($_SESSION['alias']->referTo))
 //            	$res['referTo'] = 'profile';
 //            else {
 //            	$res['referTo'] = $_SESSION['alias']->referTo;
 //            }
	// 	}
	// 	else $res['message'] = 'Код підтвердження не співпав';

	// 	header('Content-type: application/json');
	// 	echo json_encode($res);
	// 	exit;
	// }
}

?>
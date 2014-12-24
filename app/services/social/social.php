<?php

class social extends Controller {
	
	private $errors = array();

    function _remap($method)
    {
        if (method_exists($this, $method)) {
            $this->$method();
        } else {
            $this->index($method);
        }
    }

    function index(){
		$this->load->page_view('index_view');
	}
	
	function vk(){
		header('Location: https://oauth.vk.com/authorize?client_id='.$_SESSION['option']->vk_id
				.'&scope='.$_SESSION['option']->vk_scope
				.'&redirect_uri='.SITE_URL .$_SESSION['alias']->alias .'/go_vk'
				.'&display='.$_SESSION['option']->vk_display
				.'&response_type=code' 
				.'&v='.$_SESSION['option']->vk_API_VERSION
				);
		exit();
	}
	
	function go_vk(){
		if(isset($_GET['code'])){
			$user = file_get_contents('https://oauth.vk.com/access_token?client_id='.$_SESSION['option']->vk_id
				.'&client_secret='.$_SESSION['option']->vk_secret
				.'&code='.$_GET['code']
				.'&redirect_uri='.SITE_URL .$_SESSION['alias']->alias .'/go_vk'
				);
			$user = json_decode($user);
			$user = file_get_contents('https://api.vk.com/method/users.get?fields=nickname,screen_name,sex,bdate,city,country,timezone,photo_50,photo_100,photo_200_orig,has_mobile,contacts,education,online,counters,relation,last_seen,verified&access_token='.$user->access_token, false);
			$user = json_decode($user);
			if(isset($user->error) && $user->error->error_code > 0) $this->load->notify_view(array('errors' => 'vk.com: '.$user->error->error_code .' '.$user->error->error_msg));
			else{
				$this->load->smodel('user_model');
				if($this->user_model->checkUser('vk', $user->response[0]->uid)){
					if(isset($_SERVER['HTTP_REFERER'])){
						$HTTP_REFERER = $_SERVER['HTTP_REFERER'];
						$HTTP_REFERER = substr($HTTP_REFERER, strlen(SITE_URL));
					}
					if($this->data->post('referer')) $HTTP_REFERER = $this->data->post('referer');
					if(isset($_SERVER['HTTP_REFERER']) && $HTTP_REFERER != 'login/process' && $HTTP_REFERER != 'login' && $HTTP_REFERER != 'reset' && $HTTP_REFERER != '')
						header('Location: '.SITE_URL.$HTTP_REFERER);
					else
						header('Location: '.SITE_URL.'profile');
					exit;
				} else {
					$_SESSION['vk_user'] = $user->response[0];
					if(empty($_SESSION['id'])) $this->load->page_view('signup_view', array('method' => 'vk'));
					else $this->load->page_view('confirm_view', array('method' => 'vk'));
				}
			}
		}
	}
	
	function signup(){
		if(isset($_POST['method']) && ($_POST['method'] == 'vk' || $_POST['method'] == 'fb')){
			$this->load->library('validator');
			$this->validator->setRules('email', $this->data->post('email'), 'required|email');
			$this->validator->setRules('Поле пароль', $this->data->post('password'), 'required|5..20');
			// if(($this->data->post('type') == 'm' || $this->data->post('type') == 's') == false) array_push($this->errors, 'Поле тип профілю заповнено невірно');
			
			if($this->validator->run() && empty($this->errors)){
				$this->load->smodel('user_model');
				if($this->user_model->addUser($_POST['method'])){
					header ('Location: '.SITE_URL.'profile/edit');
				} else
					$this->load->page_view('signup_view', array('errors' => 'Невірний пароль!', 'recovery' => $_POST));
			} else
			$this->load->page_view('signup_view', array('errors' => $this->validator->getErrors('<li>', '</li>'), 'recovery' => $_POST));
			exit;
		}
	}
	
	function confirm(){
		if(isset($_POST['method']) && ($_POST['method'] == 'vk' || $_POST['method'] == 'fb')){
			$this->load->smodel('user_model');
			if($this->user_model->confirm($_POST['method']))
				header ('Location: '.SITE_URL.'profile');
			else $this->load->notify_view(array('errors' => 'Помилка авторизації!'));
			exit();
		}
	}
	
	function fb(){
		/**
		 * Copyright 2011 Facebook, Inc.
		 * http://www.apache.org/licenses/LICENSE-2.0
		 */
		
		require __DIR__ .'/src/facebook.php';
		
		// Create our Application instance (replace this with your appId and secret).
		$facebook = new Facebook(array(
			'appId'  => $_SESSION['option']->fb_appId,
			'secret' => $_SESSION['option']->fb_secret,
		));
		
		// Get User ID
		$user = $facebook->getUser();
		
		// We may or may not have this data based on whether the user is logged in.
		//
		// If we have a $user id here, it means we know the user is logged into
		// Facebook, but we don't know if the access token is valid. An access
		// token is invalid if the user logged out of Facebook.
		
		if ($user) {
			try {
				// Proceed knowing you have a logged in user who's authenticated.
				$user_profile = $facebook->api('/me');
				} catch (FacebookApiException $e) {
				error_log($e);
				$user = null;
			}
		}
		
		// Login or logout url will be needed depending on current user state.
		if ($user) {
			// $logoutUrl = $facebook->getLogoutUrl();
			$this->load->smodel('user_model');
			if($this->user_model->checkUser('fb', $user_profile['id'])){
				if(isset($_SERVER['HTTP_REFERER'])){
					$HTTP_REFERER = $_SERVER['HTTP_REFERER'];
					$HTTP_REFERER = substr($HTTP_REFERER, strlen(SITE_URL));
				}
				if($this->data->post('referer')) $HTTP_REFERER = $this->data->post('referer');
				if(isset($_SERVER['HTTP_REFERER']) && $HTTP_REFERER != 'login/process' && $HTTP_REFERER != 'login' && $HTTP_REFERER != 'reset' && $HTTP_REFERER != '')
					header('Location: '.SITE_URL.$HTTP_REFERER);
				else
					header('Location: '.SITE_URL.'profile');
				exit;
			} else {
				$_SESSION['fb_user'] = $user_profile;
				if(empty($_SESSION['id'])) $this->load->page_view('signup_view', array('method' => 'fb'));
				else $this->load->page_view('confirm_view', array('method' => 'fb'));
			}
		} else {
			// $statusUrl = $facebook->getLoginStatusUrl();
			$loginUrl = $facebook->getLoginUrl();
			header('Location: '.$loginUrl);
			exit;
		}
	}
	
}

?>
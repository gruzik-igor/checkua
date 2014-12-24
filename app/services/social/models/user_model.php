<?php

/*
 * Модель для роботи з базою даних користувачів.
 */

class User_model {

    /*
     * В цю властивість записуються всі помилки.
     */
    public $user_errors = array();
	private $networks = array('vk', 'fb');

    function checkUser($method = '', $id = 0){
		if(in_array($method, $this->networks) && $id != 0){
			$id = $this->db->mysql_real_escape_string($id);
			
			$this->db->executeQuery("SELECT id, email, name, type, confirmed, permissions, region, city FROM users WHERE s_{$method} = '{$id}'");
			if($this->db->numRows() == 1){
				$data = $this->db->getRows();
				$_SESSION['id'] = $data->id;
				$_SESSION['type'] = $data->type;
				$_SESSION['region'] = $data->region;
				setcookie('region', $data->region, time() + 604800);
				$_SESSION['city'] = $data->city;
				if($data->type == 1) $_SESSION['admin'] = true;
				if($data->type == 2) $_SESSION['manager'] = true;
				if($data->permissions != ''){
					$data->permissions = explode(',', $data->permissions);
					foreach($data->permissions as $permission) @$_SESSION['permissions']->$permission = true;
				} else $_SESSION['permissions'] = null;
				$_SESSION['name'] = $data->name;

				// -- for white lion cms -- //

	            $_SESSION['user']->id = $data->id;
	            $_SESSION['user']->name = $data->name;
	            $_SESSION['user']->email = $data->email;
	            $_SESSION['user']->verify = $data->confirmed;
	            if($data->type == 1) $_SESSION['user']->admin = 1; else $_SESSION['user']->admin = 0;
				if($data->type == 2) $_SESSION['user']->moderator = 1; else $_SESSION['user']->moderator = 0;
				$_SESSION['user']->permissions = array();
				if($data->permissions != ''){
					$data->permissions = explode(',', $data->permissions);
					foreach($data->permissions as $permission) $_SESSION['user']->permissions[$permission] = 1;
				}
				return true;
			} else {
				$this->user_errors = 'Невірна пошта чи пароль.';
				return false;
			}
		}
		return false;
		
    }
	
	function addUser($method){
		$email      = $this->db->sanitizeString($_POST['email']);
        $password   = md5($_POST['password']);
		$uid = 0;
		if($method == 'vk'){
			$uid = $_SESSION['vk_user']->uid;
			$name = $_SESSION['vk_user']->first_name .' '.$_SESSION['vk_user']->last_name;
			$screen_name = 'vk_screen_name:::'.$_SESSION['vk_user']->screen_name;
		}
		if($method == 'fb'){
			$uid = $_SESSION['fb_user']['id'];
			$name = $_SESSION['fb_user']['name'];
			$screen_name = 'fb_screen_name:::'.$_SESSION['fb_user']['name'];
		}
		
		$this->db->executeQuery("SELECT `id`, `password` FROM `users` WHERE `email` = '{$email}'");
		if($this->db->numRows() == 1){
			$user = $this->db->getRows();
			if($user->password == $password){
				$changes = array('s_'.$method => $uid);
				$this->db->updateRow('users', $changes, $user->id);
				$this->checkUser($method, $uid);
				return true;
			} else return false;
		}
			
        $auth_id = md5($_POST['password'].'|'.$_POST['email']);
		$date = time();
		
		if($_POST['type'] == 'm') $type = 3; 
		elseif($_POST['type'] == 's' && isset($_POST['typeS']) && is_numeric($_POST['typeS']) && $_POST['typeS'] > 3) $type = $_POST['typeS']; 
		else $type = 4;
		
        $this->db->executeQuery("INSERT INTO users (email, password, name, type, confirmed, auth_id, registered, s_{$method}) VALUES('{$email}', '{$password}', '{$name}', '{$type}', 1, '{$auth_id}', '{$date}', '{$uid}')");
        if($this->db->affectedRows() == 0) return false;
		
		$this->db->executeQuery("SELECT id, name, email, confirmed, type, permissions, region, city FROM users WHERE auth_id = '{$auth_id}'");
		if($this->db->numRows() == 1){
			$data = $this->db->getRows();
			
			$this->db->executeQuery("INSERT INTO register (date, do, user) VALUES('{$date}', 1, '{$data->id}' )");
			
			$p['info'] = $screen_name;
			$p['photo'] = $data->id . '-' . md5($uid);
			$this->db->updateRow('users', $p, $data->id);
			
			$_SESSION['id'] = $data->id;
			$_SESSION['type'] = $data->type;
			$_SESSION['region'] = $data->region;
			setcookie('region', $data->region, time() + 604800);
			$_SESSION['city'] = $data->city;
			if($data->type == 1) $_SESSION['admin'] = true;
			if($data->type == 2) $_SESSION['manager'] = true;
			if($data->permissions != ''){
				$data->permissions = explode(',', $data->permissions);
				foreach($data->permissions as $permission) @$_SESSION['permissions']->$permission = true;
			} else $_SESSION['permissions'] = null;
			$_SESSION['name'] = $data->name;

			// -- for white lion cms -- //

	            $_SESSION['user']->id = $data->id;
	            $_SESSION['user']->name = $data->name;
	            $_SESSION['user']->email = $data->email;
	            $_SESSION['user']->verify = $data->confirmed;
	            if($data->type == 1) $_SESSION['user']->admin = 1; else $_SESSION['user']->admin = 0;
				if($data->type == 2) $_SESSION['user']->moderator = 1; else $_SESSION['user']->moderator = 0;
				$_SESSION['user']->permissions = array();
				if($data->permissions != ''){
					$data->permissions = explode(',', $data->permissions);
					foreach($data->permissions as $permission) $_SESSION['user']->permissions[$permission] = 1;
				}
			
			
			$url50 = '';
			$url100 = '';
			$url200 = '';
			if($_POST['method'] == 'vk'){
				$url50 = $_SESSION['vk_user']->photo_50;
				$url100 = $_SESSION['vk_user']->photo_100;
				$url200 = $_SESSION['vk_user']->photo_200_orig;
			}
			if($_POST['method'] == 'fb'){
				$url50 = 'https://graph.facebook.com/'.$_SESSION['fb_user']['id'].'/picture?type=square';
				$url100 = 'https://graph.facebook.com/'.$_SESSION['fb_user']['id'].'/picture?type=normal';
				$url200 = 'https://graph.facebook.com/'.$_SESSION['fb_user']['id'].'/picture?type=large';
			}
			
			$path = IMG_PATH.'users/';
			if(strlen($path) > strlen(SITE_URL)) $path = substr($path, strlen(SITE_URL));
			$name = $_SESSION['id'] . '-' . md5($uid);
			file_put_contents($path.$name.'.jpg', file_get_contents($url200));
			file_put_contents($path.'b_'.$name.'.jpg', file_get_contents($url200));
			file_put_contents($path.'s_'.$name.'.jpg', file_get_contents($url100));
			file_put_contents($path.'p_'.$name.'.jpg', file_get_contents($url50));
			
			return true;
		}
    }
	
	function confirm($method){
		if($method == 'vk' && isset($_SESSION['vk_user']->uid)){
			$changes = array('s_vk' => $_SESSION['vk_user']->uid);
			$this->db->updateRow('users', $changes, $_SESSION['id']);
			return true;
		}
		if($method == 'fb' && isset($_SESSION['fb_user']['id'])){
			$changes = array('s_fb' => $_SESSION['fb_user']['id']);
			$this->db->updateRow('users', $changes, $_SESSION['id']);
			return true;
		}
		return false;
	}
	
}
?>
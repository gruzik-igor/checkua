<?php

/*
 * Модель для роботи з базою даних користувачів.
 */

class wl_user_model {

    /*
     * В цю властивість записуються всі помилки.
     */
    public $user_errors = array();

    /*
     * Отримуємо дані користувача з бази даних
     */
    function getUserInfoById($id){
        $id = $this->db->sanitizeString($id);
        $this->db->executeQuery("SELECT u.* FROM wl_users AS u
                                WHERE u.id = '{$id}'");
        if($this->db->numRows() > 0){
            return $this->db->getRows();
        }
        return null;
    }

    /*
     * Метод додає користувача до бази.
     */
    public function addUser($name, $email, $password){
    	$email = $this->db->sanitizeString($email);
        $this->db->executeQuery("SELECT id, email, type, status FROM wl_users WHERE email = '{$email}'");
        if($this->db->numRows() > 0){
        	$user = $this->db->getRows();
        	if(is_array($user)) return false;
        	if($user->type == 5){
        		$data['name'] = $name;
        		$data['photo'] = 0;
		    	$data['type'] = 4;
		    	$data['status'] = 2;
		    	$data['registered'] = time();
		    	$data['auth_id'] = md5($name.'|'.$password.'|'.$email);
		    	$data['password'] = sha1(md5($password) . SYS_PASSWORD);
		    	if($this->db->updateRow('wl_users', $data, $user->id)){
		    		$register['date'] = $data['registered'];
		    		$register['do'] = 1;
		    		$register['user'] = $user->id;
		    		if($this->db->insertRow('wl_user_register', $register)){
		    			$this->user_errors['id'] = $user->id;
		    			$this->user_errors['auth_id'] = $data['auth_id'];
		    			return true;
		    		}
		    	}
        	} return false;
        } else {
        	$user['email'] = $email;
	    	$user['name'] = $name;
	    	$user['photo'] = 0;
	    	$user['type'] = 4;
	    	$user['status'] = 2;
	    	$user['registered'] = time();
	    	$user['auth_id'] = md5($name.'|'.$password.'|'.$email);
	    	$user['password'] = sha1(md5($password) . SYS_PASSWORD);
	    	if($this->db->insertRow('wl_users', $user)){
	    		$id = $this->db->getLastInsertedId();
	    		$register['date'] = $user['registered'];
	    		$register['do'] = 1;
	    		$register['user'] = $id;
	    		if($this->db->insertRow('wl_user_register', $register)){
	    			$this->user_errors['id'] = $id;
	    			$this->user_errors['auth_id'] = $user['auth_id'];
	    			return true;
	    		}
	    	}
        }
    	$this->user_errors = 'Виникли проблеми при реєстрації. Будь-ласка спробуйте пізніше.';
        return false;
    }

    /*
     * Оновлюємо інформацію користувача
     */
    function saveUser($id, $data = array()){
        $this->db->updateRow('wl_users', $data, $id);
        if($this->db->affectedRows() > 0){
            return true;
        } else {
            return false;
        }
    }


    /*
     * Метод перевіряє чи емейл існує в базі.
     * Використовується при реєстрації.
     */
    public function userExists($email = ''){
        $email = $this->db->sanitizeString($email);
        $this->db->executeQuery("SELECT email, type, status FROM wl_users WHERE email = '{$email}'");
        if($this->db->numRows() == 1){
            $data = $this->db->getRows();
            if($data->status == 3){
                array_push($this->user_errors, 'Користувач із даною адресою заблокований!');
            } elseif($data->type == 5){
                return false;
            } else array_push($this->user_errors, 'Користувач з таким е-мейлом вже є!');
            return true;
        } else if($this->db->numRows() > 1) {
            array_push($this->user_errors, 'Така емейл адреса користувача вже існує.');
            return true;
        }

        return false;
    }
	
	public function chekConfirmed($code){
		$this->db->executeQuery("SELECT id, email, name, type, auth_id FROM wl_users WHERE id = '{$_SESSION['user']->id}'");
		if($this->db->numRows() == 1){
            $user = $this->db->getRows();
			if($code == $user->auth_id){
				$date = time();
				$this->db->executeQuery("UPDATE wl_users SET status = 1 WHERE id = '{$_SESSION['user']->id}'");
				$this->db->executeQuery("INSERT INTO wl_user_register (date, do, user) VALUES('{$date}', 2, '{$_SESSION['user']->id}' )");
				
				$_SESSION['user']->id = $user->id;
	            $_SESSION['user']->name = $user->name;
	            $_SESSION['user']->email = $user->email;
	            $_SESSION['user']->type = $user->type;
	            $_SESSION['user']->verify = 1;
	            $_SESSION['user']->permissions = array();
	            if($user->type == 1) $_SESSION['user']->admin = 1; else $_SESSION['user']->admin = 0;
	            if($user->type == 2){
	                $_SESSION['user']->manager = 1;
	                $permissions = $this->db->getAllDataByFieldInArray('wl_user_permissions', $user->id, 'user');
	                $aliases = $this->db->getAllData('wl_aliases');
	                $alias_list = array();
	                foreach ($aliases as $a) {
	                	$alias_list[$a->id] = $a->alias;
	                }
	                if($permissions){
	                    foreach($permissions as $permission) $_SESSION['user']->permissions[$alias_list[$permission]] = 1;
	                }
	            } else $_SESSION['user']->manager = 0;

				return true;
			}
			else return false;
        }
        return null;
	}
	
	// wl+
	public function checkConfirmedByEmailCode($email, $code){
		$email = $this->db->sanitizeString($email);
		$this->db->executeQuery("SELECT id, name, type, auth_id FROM wl_users WHERE email = '{$email}'");
		if($this->db->numRows() == 1){
            $user = $this->db->getRows();
			if($code == $user->auth_id){
				$date = time();
				$this->db->executeQuery("UPDATE wl_users SET status = 1, active = 1 WHERE id = '{$user->id}'");
				$this->db->executeQuery("INSERT INTO wl_user_register (date, do, user) VALUES('{$date}', 2, '{$user->id}' )");
				
				$_SESSION['user']->id = $user->id;
	            $_SESSION['user']->name = $user->name;
	            $_SESSION['user']->email = $email;
	            $_SESSION['user']->type = $user->type;
	            $_SESSION['user']->verify = 1;
	            $_SESSION['user']->permissions = array();
	            if($user->type == 1) $_SESSION['user']->admin = 1; else $_SESSION['user']->admin = 0;
	            if($user->type == 2){
	                $_SESSION['user']->manager = 1;
	                $permissions = $this->db->getAllDataByFieldInArray('wl_user_permissions', $user->id, 'user');
	                $aliases = $this->db->getAllData('wl_aliases');
	                $alias_list = array();
	                foreach ($aliases as $a) {
	                	$alias_list[$a->id] = $a->alias;
	                }
	                if($permissions){
	                    foreach($permissions as $permission) $_SESSION['user']->permissions[$alias_list[$permission]] = 1;
	                }
	            } else $_SESSION['user']->manager = 0;

				return true;
			}
			else return false;
        }
        return null;
	}

    /* wl+
     * Перевірка логіну та пароля. Використовується для POST авторизація.
     * Якщо логін та пароль вірні в сессії робляться відповідні записи.
     */
    public function checkUser($email = '', $password = '', $sequred = false){		
		if($email != '' && $password != ''){
			$email = $this->db->sanitizeString($email);
			if($sequred == false) $password = md5($password);
			$password = sha1($password . SYS_PASSWORD);
			
			$this->db->executeQuery("SELECT id, name, email, type, status FROM wl_users WHERE email = '{$email}' AND password = '{$password}'");
			if($this->db->numRows() == 1){
				$data = $this->db->getRows();

				if($data->status != 3){
					$_SESSION['user']->id = $data->id;
		            $_SESSION['user']->name = $data->name;
		            $_SESSION['user']->email = $data->email;
		            $_SESSION['user']->verify = $data->status;
		            $_SESSION['user']->permissions = array();
		            if($data->type == 1) $_SESSION['user']->admin = 1; else $_SESSION['user']->admin = 0;
		            if($data->type == 2){
		                $_SESSION['user']->manager = 1;
		                $permissions = $this->db->getAllDataByFieldInArray('wl_user_permissions', $data->id, 'user');
		                if($permissions){
		                	$aliases = $this->db->getAllData('wl_aliases');
			                $alias_list = array();
			                foreach ($aliases as $a) {
			                	$alias_list[$a->id] = $a->alias;
			                }
		                    foreach($permissions as $permission) $_SESSION['user']->permissions[] = $alias_list[$permission->permission];
		                }
		            } else $_SESSION['user']->manager = 0;

					$this->db->executeQuery("UPDATE wl_users SET active = 1 WHERE id = {$_SESSION['user']->id}");

					return true;
				} else $this->user_errors = 'Користувач заблокований! Зверніться до адміністрації за формою праворуч або на email '.SYS_EMAIL;

	            
			} else {
				$this->user_errors = 'Невірна пошта чи пароль.';
			}
		}
		return false;
    }
    
    function getPmInfo($user_id){
    	if(is_numeric($user_id)) {
    		$this->db->executeQuery("SELECT id, name, photo FROM users WHERE id = {$user_id}");
    		return $this->db->getRows();
    	}
    	return null;
    }
	
	
	function change_pass($confirm=false,$pass=null){
		if ($confirm){
			if($pass!=0){
				$this->db->updateRow('users',array('password'=>$pass),$_SESSION['user']->id);
				if($this->db->affectedRows()==0) return false; else return true;
			}
		}else{
			$this->db->executeQuery("SELECT password FROM users WHERE id = '{$_SESSION['user']->id}'");
			if($this->db->numRows() > 0)
				return $this->db->getRows();
				
		}
	}
	
	function change_email($confirm=false,$email=null){
		if ($confirm){
			if($email!=0){
				$this->db->updateRow('users',array('email'=>$email),$_SESSION['user']->id);
				if($this->db->affectedRows()==0) return false; else return true;
			}
		}else{
			$this->db->executeQuery("SELECT email FROM users WHERE email = '{$email}'");
			if($this->db->numRows() > 0) return false; 
			else{
				$this->db->executeQuery("SELECT password FROM users WHERE id = '{$_SESSION['user']->id}'");
				if($this->db->numRows() > 0)
					return $this->db->getRows();
			}	
		}
	}
	
	// wl+
	public function set_auth_id($auth_id){
		$this->db->executeQuery("UPDATE `wl_users` SET `auth_id` = '{$auth_id}' WHERE `id` = '{$_SESSION['user']->id}'");
		if($this->db->affectedRows()>0)
            return true;
		else 
            return false;        
	}

	// wl+
    public function checkUserAPI($email = '', $password = '', $sequred = false){		
		if($email != '' && $password != ''){
			$email = $this->db->sanitizeString($email);
			if($sequred == false) $password = md5($password);
			$password = sha1($password . SYS_PASSWORD);
			
			$this->db->executeQuery("SELECT id, name, status FROM wl_users WHERE email = '{$email}' AND password = '{$password}' AND status = 1");
			if($this->db->numRows() == 1){
				return $this->db->getRows();
			} else return false;
		}
		return false;		
    }
	
}

?>

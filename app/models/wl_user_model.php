<?php

/*
 * Модель для роботи з базою даних користувачів.
 * For White Lion CMS 1.0
 */

class wl_user_model {

    /*
     * В цю властивість записуються всі помилки.
     */
    public $user_errors = '';

    /*
     * Отримуємо дані користувача з бази даних
     */
    function getInfo($id = 0, $additionall = '*')
    {
    	if($id == 0 && isset($_SESSION['user']->id)) $id = $_SESSION['user']->id;
        $this->db->select('wl_users as u', '*', $id);
        $this->db->join('wl_user_types', 'name as type_name', '#u.type');
    	$user = $this->db->get('single');
        if($user && $additionall)
        {
        	$info = false;
        	if($additionall == '*')
        	{
        		$info = $this->db->getAllDataByFieldInArray('wl_user_info', $user->id, 'user');
        	}
        	else
        	{
        		$where['user'] = $user->id;
        		$where['key'] = explode(',', $additionall);
        		$info = $this->db->getAllDataByFieldInArray('wl_user_info', $where);
        	}
        	if($info)
        	{
        		foreach ($info as $i) {
        			$key = $i->key;
        			if(isset($user->$key))
        			{
        				if(is_array($user->$key)) array_push($user->$key, $i->value);
        				else
        				{
        					$value = clone $user->$key;
        					$user->$key = array();
        					array_push($user->$key, $value);
        					array_push($user->$key, $i->value);
        					unset($value);
        				}
        			}
        			else
        			{
        				$user->$key = $i->value;
        			}
        		}
        	}
        }
        return $user;
    }

    /*
     * Метод додає користувача до бази.
     * info (array) масив з основними даними користувача (email, name, photo, password)
     * additionall (array) додаткові дані користувача (phone, facebook, vk, city..)
     * new_user_type (int) ід типу користувача за замовчуванням (4 - простий користувач)
     * set_password (bool) чи встановлювати пароль до профілю (класична реєстрація - так, швидка зі соц. мереж - ні). також впливає на статус новозареєстрованого користувача (класична - 2, швидка наступний після 2)
     * comment (text) службовий коментар у реєстр
     */
    public function add($info = array(), $additionall = array(), $new_user_type = 4, $set_password = true, $comment = '')
    {
    	$status = $this->db->getAllDataById('wl_user_status', 2);
        if(!$set_password)
            $status = $this->db->getAllDataById('wl_user_status', $status->next);

    	$user = $this->db->getAllDataById('wl_users', $info['email'], 'email');
        if($user)
        {
        	if($user->type == 5)
        	{
        		$data = array();
                $data['alias'] = $user->alias = $this->makeAlias($info['name']);
                $data['name'] = $user->name = $info['name'];
        		$data['photo'] = $user->photo = $info['photo'];
		    	$data['type'] = $user->type = $new_user_type;
                $data['status'] = $user->status = $status->id;
		    	$data['last_login'] = 0;
		    	$data['auth_id'] = $user->auth_id = md5($info['name'].'|'.$info['password'].'|'.$user->email);
                if($set_password)
		    	    $data['password'] = $user->password = $this->getPassword($user->id, $user->email, $info['password']);
		    	if($this->db->updateRow('wl_users', $data, $user->id))
		    		$this->db->register('signup', $comment, $user->id);
        	}
        	else
    		{
    			$this->user_errors = 'Користувач з таким е-мейлом вже є!';
    			return false;
    		}
        }
        else
        {
        	$user = new stdClass();
        	$data = array();
            $data['alias'] = $user->alias = $this->makeAlias($info['name']);
        	$data['email'] = $user->email = $info['email'];
	    	$data['name'] = $user->name = $info['name'];
            $data['photo'] = $user->photo = $info['photo'];
	    	$data['type'] = $user->type = $new_user_type;
	    	$data['status'] = $user->status = $status->id;
	    	$data['auth_id'] = $user->auth_id = md5($info['name'].'|'.$info['password'].'|'.$user->email);
    		$data['registered'] = $user->registered = time();
            $data['last_login'] = $user->last_login = 0;
	    	if($this->db->insertRow('wl_users', $data))
	    	{
	    		$user->id = $this->db->getLastInsertedId();

                if($set_password)
                {
                    $password = $this->getPassword($user->id, $user->email, $info['password']);
                    $this->db->updateRow('wl_users', array('password' => $password), $user->id);
                }

	    		$this->db->register('signup', $comment, $user->id);
	    	}
        }
        if($user)
        {
            $user->load = $status->load;
        	if(!empty($additionall))
			{
				foreach ($additionall as $key => $value) {
					$info = array();
					$info['user'] = $user->id;
					$info['key'] = $key;
					$info['value'] = $value;
					$info['date'] = time();
					$this->db->insertRow('wl_user_info', $info);

					if(isset($user->$key))
        			{
        				if(is_array($user->$key)) array_push($user->$key, $value);
        				else
        				{
        					$exist_value = clone $user->$key;
        					$user->$key = array();
        					array_push($user->$key, $exist_value);
        					array_push($user->$key, $value);
        					unset($exist_value);
        				}
        			}
        			else
        			{
        				$user->$key = $value;
        			}
				}
			}
			return $user;
		}

    	$this->user_errors = 'Виникли проблеми при реєстрації. Будь ласка, спробуйте пізніше.';
        return false;
    }

    private function makeAlias($name)
    {
        $name = $this->data->latterUAtoEN($name);
        $name = $alias = mb_eregi_replace('-', '.', $name);
        $i = 2;
        while($check = $this->db->getAllDataById('wl_users', $alias, 'alias'))
        {
            $alias = $name .'.'. $i;
            $i++;
        }
        return $alias;
    }


    /*
     * Метод перевіряє чи емейл існує в базі.
     * Використовується при реєстрації.
     */
    public function userExists($email = '')
    {
        $email = $this->db->sanitizeString($email);
        $this->db->executeQuery("SELECT `email`, `type`, `status` FROM `wl_users` WHERE `email` = '{$email}'");
        if($this->db->numRows() == 1){
            $user = $this->db->getRows();
            if($user->status == 3){
                $this->user_errors = 'Користувач із даною адресою заблокований!';
            } elseif($user->type == 5){
                return false;
            } else $this->user_errors = 'Користувач з таким е-мейлом вже є!';
            return true;
        } else if($this->db->numRows() > 1) {
            $this->user_errors = 'Така емейл адреса користувача вже існує.';
            return true;
        }

        return false;
    }
	
	public function checkConfirmed($email, $code)
	{
		$this->db->select('wl_users as u', '*', $email, 'email');
        $this->db->join('wl_user_status', 'next', '#u.status');
        $user = $this->db->get('single');
		if($user && $code == $user->auth_id)
		{
            $status = $this->db->getAllDataById('wl_user_status', $user->next);
			$this->db->updateRow('wl_users', array('status' => $user->next), $user->id);
			$user->status = $user->next;
			$this->setSession($user);
			$this->db->register('confirmed');
			return $status;
        }
        return false;
	}

    /* 
     * Перевірка логіну та пароля. Використовується для POST авторизація.
     * Якщо логін та пароль вірні в сессії робляться відповідні записи.
     */
    public function login($key = 'email', $password = '', $sequred = false)
    {	
    	$user = false;
		if($key == 'email')
		{
			$user = $this->db->getAllDataById('wl_users', $this->data->post('email'), 'email');
			if($user)
				$password = $this->getPassword($user->id, $user->email, $password, $sequred);
		}
		else
		{
			$this->db->select('wl_user_info as ui', 'value as password', $key, 'key');
			$this->db->join('wl_users', 'id, email, name, type, status', '#ui.user');
			$user = $this->db->get('single');
		}
		if($user && $password != '')
		{
            $status = $this->db->getAllDataById('wl_user_status', $user->status);
			if($user->password != $password) {
				$this->user_errors = 'Пароль невірний.';
				return false;
			}

			if(isset($_SESSION['facebook_id']) && $this->data->post('facebook') == $_SESSION['facebook_id'] && $_SESSION['facebook_id'] > 0)
			{
				$this->setAdditional($user->id, 'facebook', $_SESSION['facebook_id']);
				$this->setAdditional($user->id, 'facebook_link', $_SESSION['facebook_link']);
				$_SESSION['notify'] = new stdClass();
				$_SESSION['notify']->success = 'Профіль facebook успішно підключено.';
				if($user->status == 2)
				{
					$user->status = $status->next;
					$this->db->updateRow('wl_users', array('status' => $user->status), $user->id);
					$status = $this->db->getAllDataById('wl_user_status', $user->status);
				}
			}

			if($user->status == 1)
			{
				$this->setSession($user, false);
				$auth_id = md5($user->email.'|'.$user->password.'|auth_id|'.time());
				setcookie('auth_id', $auth_id, time() + 3600*24*31, '/');

                $update = array();
                $update['last_login'] = time();
                $update['auth_id'] = $auth_id;
                
				$this->db->updateRow('wl_users', $update, $user->id);
				return $status;
			}
			elseif($user->status != 3)
			{
				$this->setSession($user);
				return $status;
			}
			else
				$this->user_errors = 'Користувач заблокований! Зверніться до адміністрації на email '.SITE_EMAIL;
		} else
			$this->user_errors = 'Невірна пошта чи пароль.';
		return false;
    }

    public function setAdditional($user, $key, $value)
    {
    	$where['user'] = $user;
    	$where['key'] = $key;
    	$this->db->select('wl_user_info', 'id, value', $where);
    	$additionall = $this->db->get();
    	if(is_array($additionall))
    	{
    		$add = true;
    		foreach ($additionall as $info) {
    			if($info->value == $value)
    			{
    				$add = false;
    				break;
    			}
    		}
    		if($add)
    		{
    			$where['value'] = $value;
    			$where['date'] = time();
    			$this->db->insertRow('wl_user_info', $where);
    			return true;
    		}
    	}
    	elseif(is_object($additionall))
    	{
    		if($additionall->value != $value)
    			$this->db->updateRow('wl_user_info', array('value' => $value, 'date' => time()), $additionall->id);
    		return true;
    	}
    	else
    	{
    		$where['value'] = $value;
    		$where['date'] = time();
			$this->db->insertRow('wl_user_info', $where);
			return true;
    	}
    }

    public function reset($email = '')
    {
    	$user = $this->db->getAllDataById('wl_users', $email, 'email');
    	if($user)
    	{
			$data = array();
			$data['reset_key'] = $user->reset_key = md5($_POST['email'].'|'.$user->auth_id.'|'.time());
			$data['reset_expires'] = $user->reset_expires = mktime(date("H") + 2, date("i"), date("s"), date("m"), date("d"), date("Y"));//+ 2 ГОДИНИ!!!
			$this->db->updateRow('wl_users', $data, $user->id);
			$this->db->register('reset_sent', '', $user->id);
			return $user;
    	}
    	return false;
    }

	public function getPassword($id, $email, $password, $sequred = false)
	{
		if(!$sequred) $password = md5($password);
		return sha1($email . $password . SYS_PASSWORD . $id);
	}

	public function setSession($user, $updateLastLogin = true)
	{
		$_SESSION['user']->id = $user->id;
        $_SESSION['user']->name = $user->name;
        $_SESSION['user']->email = $user->email;
        $_SESSION['user']->status = $user->status;
        $_SESSION['user']->permissions = array('wl_users', 'wl_ntkd', 'wl_images', 'wl_video');

        if($user->type == 1)
            $_SESSION['user']->admin = 1; 
        else
            $_SESSION['user']->admin = 0;

        if($user->type == 2)
        {
            $_SESSION['user']->manager = 1;
            $this->db->select('wl_user_permissions as up', '*', $user->id, 'user');
            $this->db->join('wl_aliases', 'alias', '#up.permission');
            $permissions = $this->db->get('array');
            if($permissions)
                foreach($permissions as $permission)
                	$_SESSION['user']->permissions[] = $permission->alias;
        }
        else
            $_SESSION['user']->manager = 0;

        if($updateLastLogin)
            $this->db->updateRow('wl_users', array('last_login' => time()), $user->id);

        return true;
	}
	
}

?>
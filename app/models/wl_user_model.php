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
    public $new_user_type = 4; // Ід типу новозареєстрованого користувача

    /*
     * Отримуємо дані користувача з бази даних
     */
    function getInfo($id = 0, $additionall = '*', $field = 'id')
    {
    	if($id == 0 && isset($_SESSION['user']->id) && !is_string($id)) $id = $_SESSION['user']->id;
        $this->db->select('wl_users as u', '*', $id, $field);
        $this->db->join('wl_user_types', 'name as type_name', '#u.type');
    	$user = $this->db->get('single');
        if($user && $additionall)
        {
        	$info = false;
            $user->info = array();
        	if($additionall == '*')
        		$info = $this->db->getAllDataByFieldInArray('wl_user_info', $user->id, 'user');
        	else
        	{
        		$where['user'] = $user->id;
        		$where['field'] = explode(',', $additionall);
        		$info = $this->db->getAllDataByFieldInArray('wl_user_info', $where);
        	}
        	if($info)
        		foreach ($info as $i) {
        			$user->info[$i->field] = $i->value;
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
    public function add($info = array(), $additionall = array(), $new_user_type = 0, $set_password = true, $comment = '')
    {
        $status = 2;
        if(!empty($info['status']))
            $status = $info['status'];
    	$status = $this->db->getAllDataById('wl_user_status', $status);
        if(!$set_password && $status->next > 0)
            $status = $this->db->getAllDataById('wl_user_status', $status->next);
        if($new_user_type == 0)
            $new_user_type = $this->new_user_type;

    	$user = $this->db->getAllDataById('wl_users', $info['email'], 'email');
        if($user)
        {
        	if($user->type >= 5 && $set_password)
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
            elseif(!$set_password)
                $this->user_errors = 'Користувач з таким е-мейлом вже є!';
        	else
    		{
    			$this->user_errors = 'Користувач з таким е-мейлом вже є!';
    			return false;
    		}
            $user->info = array();
            if($info = $this->db->getAllDataByFieldInArray('wl_user_info', $user->id, 'user'))
                foreach ($info as $i) {
                    $user->info[$i->field] = $i->value;
                }
        }
        else
        {
        	$user = new stdClass();
        	$data = $user->info = array();
            $data['alias'] = $user->alias = $this->makeAlias($info['name']);
        	$data['email'] = $user->email = $info['email'];
	    	$data['name'] = $user->name = $info['name'];
            $data['photo'] = $user->photo = $info['photo'];
	    	$data['type'] = $user->type = $new_user_type;
	    	$data['status'] = $user->status = $status->id;
	    	if($set_password)
                $data['auth_id'] = $user->auth_id = md5($info['name'].'|'.$info['password'].'|'.$user->email);
            else
                $data['auth_id'] = $user->auth_id = '';
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
                    if(empty($user->info[$key]))
                    {
    					$info = array();
    					$info['user'] = $user->id;
    					$info['field'] = $key;
    					$info['value'] = $value;
    					$info['date'] = time();
    					$this->db->insertRow('wl_user_info', $info);
                        $user->info[$key] = $value;
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
    public function userExists($email = '', &$user = false)
    {
        $email = $this->db->sanitizeString($email);
        $user = $this->db->getQuery("SELECT `email`, `name`, `type`, `status` FROM `wl_users` WHERE `email` = '{$email}'", 'single');
        if($user)
        {
            if($user->status == 3)
                $this->user_errors = 'Користувач із даною адресою заблокований!';
            elseif($user->type == 5)
                return false;
            else
                $this->user_errors = 'Користувач з таким е-мейлом вже є!';
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
            {
				$password = $this->getPassword($user->id, $user->email, $password, $sequred);
                if($user->password != $password) {
                    $this->db->register('login_bad', 'User IP: '.$this->data->userIP(), $user->id);
                    $this->user_errors = 'Пароль невірний';
                    return false;
                }
            }
		}
		else
		{
            $where = array('field' => $key, 'value' => $password);
			$this->db->select('wl_user_info as ui', 'value as password', $where);
			$this->db->join('wl_users', '*', '#ui.user');
			$user = $this->db->get('single');
		}
        
		if($user && $password != '')
		{
            $status = $this->db->getAllDataById('wl_user_status', $user->status);

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
		}
        else
			$this->user_errors = 'Невірна пошта чи пароль.';
		return false;
    }

    public function setAdditional($user, $key, $value)
    {
    	$where['user'] = $user;
    	$where['field'] = $key;
    	$this->db->select('wl_user_info', 'id, value', $where);
        if($additionall = $this->db->get())
    	{
    		if($additionall->value != $value){
    			$this->db->updateRow('wl_user_info', array('value' => $value, 'date' => time()), $additionall->id);
                $text = 'Попереднє значення ' . $key.': '.$additionall->value;
                if($user != $_SESSION['user']->id) $text .= ' (менеджер: '. $_SESSION['user']->id. ', '.$_SESSION['user']->name.')'; 
                $this->db->register('profile_data', $text, $user);
            }
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
		$_SESSION['user']->alias = $user->alias;
        $_SESSION['user']->name = $user->name;
        $_SESSION['user']->email = $user->email;
        $_SESSION['user']->status = $user->status;
        $_SESSION['user']->type = $user->type;
        $_SESSION['user']->photo = NULL;
        if(!empty($user->photo))
            $_SESSION['user']->photo = IMG_PATH.'profile/'.$user->photo;
        $_SESSION['user']->permissions = array('wl_users', 'wl_ntkd', 'wl_photos', 'wl_video', 'wl_audio');

        if($user->type == 1)
            $_SESSION['user']->admin = 1; 
        else
            $_SESSION['user']->admin = 0;

        if($user->type == 2)
        {
        	$search_forms = array();
            $_SESSION['user']->manager = 1;
            $this->db->select('wl_user_permissions as up', '*', $user->id, 'user');
            $this->db->join('wl_aliases', 'alias', '#up.permission');
            $permissions = $this->db->get('array');
            if($permissions)
                foreach($permissions as $permission)
                {
                	if($permission->permission == 0)
                		$_SESSION['user']->permissions[] = 'wl_comments';
                	if($permission->permission > 0)
                		$_SESSION['user']->permissions[] = $permission->alias;
                	else
                		$search_forms[] = -$permission->permission;
                }
            if(!empty($search_forms))
            {
            	$_SESSION['user']->permissions[] = 'wl_forms';
            	if($forms = $this->db->getAllDataByFieldInArray('wl_forms', array('id' => $search_forms)))
            		foreach ($forms as $form) {
            			$_SESSION['user']->permissions[] = 'form_'.$form->name;
            		}
            }
        }
        else
            $_SESSION['user']->manager = 0;

        if($updateLastLogin)
            $this->db->updateRow('wl_users', array('last_login' => time()), $user->id);

        return true;
	}

    public function setPhotoByLink($from, $userId = 0, $updateSession = true)
    {
        if($userId == 0)
            $userId = $_SESSION['user']->id;

        $path = IMG_PATH;
        $path = substr($path, strlen(SITE_URL));
        $path = substr($path, 0, -1);
        if(!is_dir($path))
        {
            if(mkdir($path, 0777) == false)
                return false;
        }
        $path .= '/profile';
        if(!is_dir($path))
        {
            if(mkdir($path, 0777) == false)
                return false;
        }
        $path .= '/';
        $photoName = $userId;
        $photoName .= '-'.md5($userId.time());
        $ext = explode('.', $from);
        $photoName .= '.'.end($ext);

        if(file_put_contents($path.$photoName, file_get_contents($from)))
        {
            $this->db->updateRow('wl_users', array('photo' => $photoName), $userId);
            if($updateSession && isset($_SESSION['user']->photo))
                $_SESSION['user']->photo = $path.$photoName;
            return $photoName;
        }
        return false;
    }
	
}

?>
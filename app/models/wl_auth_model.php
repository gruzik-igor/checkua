<?php

class wl_Auth_model {

    function authByCookies()
    {
        $this->db->select('wl_users', "id, name, email, type, status, last_login", $_COOKIE['auth_id'], 'auth_id');
        if($user = $this->db->get('single'))
        {
            $_SESSION['user']->id = $user->id;
            $_SESSION['user']->name = $user->name;
            $_SESSION['user']->email = $user->email;
            $_SESSION['user']->type = $user->type;
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

            $time5min = $user->last_login + 60*5;
            if(time() > $time5min)
                $this->db->updateRow('wl_users', array('last_login' => time()), $user->id);
        }
    }

}

?>

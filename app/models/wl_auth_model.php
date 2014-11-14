<?php

class wl_Auth_model {

    function authByCookies(){
        $auth_id = $this->db->sanitizeString($_COOKIE['auth_id']);
        $this->db->executeQuery("SELECT `id`, `name`, `email`, `type`, `status` FROM `wl_users` WHERE `auth_id` = '{$auth_id}' && status = 2");
        if($this->db->numRows() == 1){
            $user = $this->db->getRows();
            $_SESSION['user']->id = $user->id;
            $_SESSION['user']->name = $user->name;
            $_SESSION['user']->email = $user->email;
            $_SESSION['user']->type = $user->type;
			$_SESSION['user']->verify = $user->status;
            $_SESSION['user']->permissions = array();
            if($user->type == 1) $_SESSION['user']->admin = 1; else $_SESSION['user']->admin = 0;
            if($user->type == 2){
                $_SESSION['user']->manager = 1;
                $permissions = $this->db->getAllDataByFieldInArray('wl_user_permissions', $user->id, 'user');
                if($permissions){
                	$aliases = $this->db->getAllData('wl_aliases');
	                $alias_list = array();
	                foreach ($aliases as $a) {
	                	$alias_list[$a->id] = $a->alias;
	                }
                    foreach($permissions as $permission) $_SESSION['user']->permissions[] = $alias_list[$permission->permission];
                }
            } else $_SESSION['user']->manager = 0;
        }
    }

}

?>

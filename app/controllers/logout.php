<?php

class Logout extends Controller {

    function index(){
		$this->db->executeQuery("UPDATE `wl_users` SET `active` = '0' WHERE `id` = '{$_SESSION['user']->id}'");
        session_destroy();
        setcookie('auth_id', '', time() - 3600, '/');
        header('Location: '.SITE_URL);
    }

}

?>

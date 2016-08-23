<?php

class Logout extends Controller {

    function index()
    {
        session_destroy();
        setcookie('auth_id', '', time() - 3600, '/');
        header ('HTTP/1.1 303 See Other');
        header('Location: '.SITE_URL);
        exit();
    }

}

?>

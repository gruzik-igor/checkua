<?php

class Profile extends Controller {

    function _remap($method, $data = array())
    {
        if (method_exists($this, $method)) {
            if(empty($data)) $data = null;
            return $this->$method($data);
        } else {
            $this->index($method);
        }
    }

    public function index($uri = false)
    {
        $_SESSION['alias']->content = 0;
        $_SESSION['alias']->code = 201;

        if($uri)
        {
            $this->load->model('wl_user_model');
            $user = $this->wl_user_model->getInfo($uri, false, 'alias');
            if($user)
            {
                $_SESSION['alias']->title = $user->name.'. Кабінет користувача';
                $_SESSION['alias']->name = $user->name;

                $this->load->page_view('profile/index_view', array('user' => $user));
            } 
            else 
                $this->load->page_404();
        } 
        elseif($this->userIs())
            $this->redirect('profile/'.$_SESSION['user']->alias);
        else
            $this->redirect('login');
    }

    public function edit()
    {
        $_SESSION['alias']->code = 201;
        if($this->userIs())
        {
            $_SESSION['alias']->title = $_SESSION['user']->name.'. Кабінет користувача';
            $_SESSION['alias']->name = 'Кабінет користувача';

            $this->load->model('wl_user_model');
            $registerDo = $this->db->getQuery("SELECT r.*, d.name, d.title_public as title_public FROM wl_user_register as r LEFT JOIN wl_user_register_do as d ON d.id = r.do WHERE r.user = {$_SESSION['user']->id} AND d.public = 1", 'array');

            $this->load->page_view('profile/edit_view', array('user' => $this->wl_user_model->getInfo(), 'registerDo' => $registerDo));
        }
        else
            $this->redirect('login');
    }

    public function saveUserInfo()
    {
        if($this->userIs())
        {
            $name = $this->data->post('name');
            $user = $_SESSION['user']->id;

            if($name)
            {
                if(mb_strlen($name, 'UTF-8') > 3){
                    $this->db->select('wl_users', 'name', $user);
                    $oldName = $this->db->get();

                    if($oldName->name != $name){
                        $this->db->updateRow('wl_users', array('name' => $name), $user);
                        $this->db->register('profile_data', 'Попереднє значення name: '.$oldName->name, $user);
                        $_SESSION['user']->name = $name;
                    }
                }
            }

            unset($_POST['name']);
            $this->load->model('wl_user_model');
            foreach ($_POST as $key => $value)
            {
                $this->wl_user_model->setAdditional($user, $key, $this->data->post($key));
            }
        }
        
        header("Location: ".SITE_URL.'profile/edit#profile');
        exit();
    }

    public function upload_avatar()
    {
        $res = array();

        if (isset($_SESSION['user']->id) && $_SESSION['user']->id > 0) {
            $error = 0;
            $name_field = 'photos';

            $path = IMG_PATH.'profile';
            if(strlen(IMG_PATH) > strlen(SITE_URL)) $path = substr($path, strlen(SITE_URL));
            if(!is_dir($path)){
                if(mkdir($path, 0777) == false){
                    $error++;
                    $res['error'] = 'Error create dir ' . $path;
                }
            }
            $path .= '/';

            if(!empty($_FILES[$name_field]['name']) && $error == 0){
                $data = array();

                $name = $_SESSION['user']->id;
                $this->load->library('image');
                $this->image->upload($name_field, $path, $name);
                $extension = $this->image->getExtension();
                $this->image->save();
                if($this->image->getErrors() == ''){
                    $this->image->loadImage($path, $name);
                    $this->image->preview(140, 140, 100);
                    $this->image->save($path, 's');
                    $this->image->loadImage($path, $name);
                    $this->image->preview(50, 50, 100);
                    $this->image->save($path, 'p');
                    $data['photo'] = $name.'.'.$extension;
                }

                if(!empty($data)){
                    $this->db->updateRow('wl_users', $data, $_SESSION['user']->id);

                    $photo['id'] = $_SESSION['user']->id;
                    $photo['date'] = date('d.m.Y H:i');
                    $photo['url'] = $path.$data['photo'];
                    $photo['thumbnailUrl'] = $path.'s_'.$data['photo'];
                    $res[] = $photo;
                }
            } else $error++;

            if($error > 0){
                $photo['result'] = false;
                $photo['error'] = "Access Denied!";
                $res[] = $photo;
            }
        }

        if(empty($res)){
            $photo['result'] = false;
            $photo['error'] = "Access Denied!";
            $res[] = $photo;
        }

        header('Content-type: application/json');
        echo json_encode(array('files' => $res));
        exit;
    }



    public function save_security()
    {
        if($this->userIs()){
            $this->load->library('validator');
            $this->validator->setRules('Поточний пароль', $this->data->post('old_password'), 'required|5..20');
            $this->validator->setRules('Новий пароль', $this->data->post('new_password'), 'required|5..20');
            $this->validator->setRules('Повторіть пароль', $this->data->post('new_password_re'), 'required|5..20');
            $this->validator->password($this->data->post('new_password'), $this->data->post('new_password_re'));
            if($this->validator->run()){
                $user = $this->db->getAllDataById('wl_users', $_SESSION['user']->id);
                $password = sha1($_SESSION['user']->email . md5($_POST['old_password']) . SYS_PASSWORD . $_SESSION['user']->id);
                if($password == $user->password){
                    $password = sha1($_SESSION['user']->email . md5($_POST['new_password']) . SYS_PASSWORD . $_SESSION['user']->id);
                    if($this->db->updateRow('wl_users', array('password' => $password), $_SESSION['user']->id)){
                        if($this->db->register('reset', $user->password)){
                            $this->load->library('mail');
                            if($this->mail->sendTemplate('reset/notify_success', $_SESSION['user']->email, array('name' => $_SESSION['user']->name))){
                                $_SESSION['notify']->success = 'Пароль змінено';
                            }
                        }
                    }
                } else {
                    $_SESSION['notify']->errors = 'Невірний поточний пароль';
                }
            } else {
                $_SESSION['notify']->errors = '<ul>'.$this->validator->getErrors('<li>', '</li>').'</ul>';
            }
            header("Location: ".SITE_URL.'profile/edit#security');
            exit();
        } else {
            header("Location: ".SITE_URL.'login');
            exit();
        }
    }

    public function register_list()
    {
        $_SESSION['alias']->code = 201;
        if($this->userIs())
        {
            $_SESSION['alias']->title = $_SESSION['user']->name.'. Реєстр дій';
            $_SESSION['alias']->name = 'Реєстр дій';
            $this->load->page_view('users/register_view');
        }
        else
            $this->redirect('login');
    }

    public function create()
    {
        $_SESSION['alias']->code = 201;

        if($_SESSION['user']->status == 1)
            $this->redirect('profile');

        switch ($this->data->uri(2)) {
            case 'step-1':
                $this->load->view('profile/create/step_1_view');
                break;

            case 'step-2':
                echo "in progress";
                break;
            
            default:
                # code...
                break;
        }
    }

    public function create_submit()
    {
        if($this->userIs() && isset($_POST['step']))
        {
            $info = array();
            $user = $this->db->getAllDataByFieldInArray('wl_user_info', $_SESSION['user']->id, 'user');
            if($user)
            {
                foreach ($user as $row) {
                    $info[$row->key] = $row->value;
                }
            }

            switch ($_POST['step']) {
                case 1:
                    $fields = array('phone');
                    foreach ($fields as $key) {
                        if(isset($_POST[$key]) && trim($_POST[$key]) != '' && !isset($info[$key]))
                        {
                            $data = array();
                            $data['user'] = $_SESSION['user']->id;
                            $data['key'] = $key;
                            $data['value'] = $this->data->post($key);
                            $data['date'] = time();
                            $this->db->insertRow('wl_user_info', $data);
                        }
                    }

                    $status = $this->db->getAllDataById('wl_user_status', $_SESSION['user']->status);
                    $this->db->updateRow('wl_users', array('status' => $status->next), $_SESSION['user']->id);

                    $this->redirect('profile/create/step-2');
                    break;
                
                default:
                    $this->redirect('profile');
                    break;
            }
        }
    }

    public function facebook()
    {
        $res = array('result' => true);

        $this->load->library('facebook');
        $user = $this->facebook->getUser();
        
        if ($user)
        {
            try {
                $user_profile = $this->facebook->api('/me?fields=email,id,name,link');
            } catch (FacebookApiException $e) {
                error_log($e);
                $user = null;
            }
        }

        if ($user)
        {
            if($this->db->getAllDataById('wl_user_info', array('key' => 'facebook', 'value' => $user_profile['id'])))
            {
                $res['error'] = 'Користувач з даним профілем facebook вже підключено!';
                $res['result'] = false;
            }
            else
            {
                $data = array();
                $data['user'] = $_SESSION['user']->id;
                $data['key'] = 'facebook';
                $data['value'] = $user_profile['id'];
                $data['date'] = time();
                $this->db->insertRow('wl_user_info', $data);
            }
        }
        else
        {
            $loginUrl = $this->facebook->getLoginUrl();
            header('Location: '.$loginUrl);
            exit;
        }

        $this->json($res);
    }

}
?>
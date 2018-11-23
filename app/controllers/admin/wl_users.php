<?php

class wl_users extends Controller {
                
    function _remap($method)
    {
        $_SESSION['alias']->name = 'Користувачі';
        $_SESSION['alias']->breadcrumb = array('Користувачі' => '');
        if (method_exists($this, $method) && $method != 'library' && $method != 'db') {
            $this->$method();
        } else {
            $this->index($method);
        }
    }

    public function index()
    {
        if($_SESSION['user']->admin == 1)
        {
            $user = $this->data->uri(2);
            if($user != '')
            {
                $this->load->model('wl_user_model');
                if(is_numeric($user))
                    $user = $this->wl_user_model->getInfo($user);
                else
                    $user = $this->wl_user_model->getInfo($user, '*', 'email');

                if(is_object($user) && $user->id > 0)
                {
                    $status = $this->db->getAllData('wl_user_status');
                    $types = $this->db->getAllDataByFieldInArray('wl_user_types', 1, 'active');

                    $_SESSION['alias']->name = 'Користувач '.$user->name;
                    $_SESSION['alias']->breadcrumb = array('Користувачі' => 'admin/wl_users', $user->name => '');
                    $this->load->admin_view('wl_users/edit_view', array('user' => $user, 'status' => $status, 'types' => $types));
                }
                else
                    $this->load->page_404();
            }
            else
                $this->load->admin_view('wl_users/list_view');
        }
    }

    public function add()
    {
        if($_SESSION['user']->admin == 1)
        {
            $_SESSION['alias']->name = 'Додати нового користувача';
            $_SESSION['alias']->breadcrumb = array('Користувачі' => 'admin/wl_users', 'Новий' => '');
            $this->load->admin_view('wl_users/add_view');
        }
    }

    public function my()
    {
        $user = $this->db->getAllDataById('wl_users', $_SESSION['user']->id);
        $_SESSION['alias']->name = 'Користувач '.$user->name;
        $_SESSION['alias']->breadcrumb = array('Користувачі' => 'admin/wl_users', $user->name => '');
        $this->load->admin_view('wl_users/profile_view', array('user' => $user));
    }

    public function changePassword()
    {
        if(isset($_POST['password']) && isset($_POST['new-password']) && isset($_POST['re-new-password'])){
            $user = $this->db->getAllDataById('wl_users', $_SESSION['user']->id);
            $password = md5($_POST['password']);
            $password = sha1($_SESSION['user']->email . $password . SYS_PASSWORD . $_SESSION['user']->id);
            if($password == $user->password){
                $this->load->library('validator');
                $this->validator->setRules('Новий пароль', $this->data->post('new-password'), 'required|5..20');
                $this->validator->password($this->data->post('new-password'), $this->data->post('re-new-password'));
                if($this->validator->run()){
                    $password = md5($_POST['new-password']);
                    $password = sha1($_SESSION['user']->email . $password . SYS_PASSWORD . $_SESSION['user']->id);
                    $this->db->updateRow('wl_users', array('password' => $password), $_SESSION['user']->id);
                    $this->db->register('reset', $user->password);
                    $_SESSION['notify']->success = 'Пароль успішно змінено!';
                } else {
                    $_SESSION['notify']->errors = $this->validator->getErrors();
                }
            } else {
                $_SESSION['notify']->errors = 'Невірний поточний пароль.';
            }
        }
        header("Location: ".SITE_URL."admin/wl_users/my");
        exit();
    }

    public function getlist()
    {
        if($_SESSION['user']->admin == 1)
        {
            $wl_users = $this->db->getQuery('SELECT u.*, t.name as type_name, s.name as status_name FROM wl_users as u LEFT JOIN wl_user_types as t ON t.id = u.type LEFT JOIN wl_user_status as s ON s.id = u.status', 'array');
            if($wl_users)
                foreach ($wl_users as $user) {
                    $user->email = '<a href="'.SITE_URL.'admin/wl_users/'.$user->email.'">'.$user->email.'</a>';
                    if($user->last_login > 0)
                        $user->last_login = (string) date('d.m.Y H:i', $user->last_login);
                    else
                        $user->last_login = 'Дані відсутні';
                }
            $this->load->json(array('data' => $wl_users));
        }
    }

    public function save()
    {
        $_SESSION['notify'] = new stdClass();
        if($_SESSION['user']->admin == 1 && isset($_POST['admin-password']))
        {
            $admin = $this->db->getAllDataById('wl_users', $_SESSION['user']->id);
            $password = md5($_POST['admin-password']);
            $password = sha1($_SESSION['user']->email . $password . SYS_PASSWORD . $_SESSION['user']->id);
            if($password == $admin->password)
            {
                $email = '';
                if($email = $this->data->post('email'))
                    $email = strtolower($email);
                $this->load->library('validator');
                $this->validator->setRules('email', $email, 'required|email');
                $this->validator->setRules('name', $this->data->post('name'), 'required');

                if($_POST['id'] == 0)
                {
                    if($_POST['typePassword'] == 'own')
                        $this->validator->setRules('Поле пароль', $this->data->post('user-password'), 'required|5..40');
                    
                    if($this->validator->run())
                    {
                        $user['email'] = $email;
                        if($this->db->getAllDataById('wl_users', $user['email'], 'email') == false)
                        {
                            $user['name'] = $this->data->post('name');
                            $user['alias'] = $this->data->latterUAtoEN($user['name']);
                            $user['type'] = $this->data->post('type');
                            $user['status'] = 1;
                            $user['last_login'] = 0;
                            $user['registered'] = time();
                            if($this->db->insertRow('wl_users', $user))
                            {
                                $id = $this->db->getLastInsertedId();
                                $do = $this->db->getAllDataById('wl_user_register_do', 'signup', 'name');
                                $register['date'] = time();
                                $register['do'] = $do->id;
                                $register['user'] = $id;
                                $register['additionally'] = 'By administrator '.$_SESSION['user']->id.' '.$_SESSION['user']->name;
                                $this->db->insertRow('wl_user_register', $register);

                                if($user['type'] == 2 && isset($_POST['permissions']) && is_array($_POST['permissions']))
                                {
                                    $register['additionally'] = 'active statuses: ';
                                    $aliases = $this->db->getAllData('wl_aliases');
                                    $alias_list = array();
                                    foreach ($aliases as $a) {
                                        $alias_list[$a->id] = $a->alias;
                                    }
                                    foreach ($_POST['permissions'] as $p) {
                                        if(is_numeric($p)){
                                            $this->db->insertRow('wl_user_permissions', array('user' => $id, 'permission' => $p));
                                            $register['additionally'] .= $alias_list[$p] .', ';
                                        }
                                    }
                                }

                                if($_POST['typePassword'] == 'own')
                                {
                                    $password = sha1($user['email'] . md5($_POST['user-password']) . SYS_PASSWORD . $id);
                                    $this->db->updateRow('wl_users', array('password' => $password), $id);
                                    $_SESSION['notify']->success = 'Користувач "'.$user['name'].'" створено успішно.';
                                }
                                else
                                {
                                    $password = bin2hex(openssl_random_pseudo_bytes(4));
                                    $close_password = sha1($user['email'] . md5($password) . SYS_PASSWORD . $id);
                                    $this->db->updateRow('wl_users', array('password' => $close_password), $id);
                                    $this->load->library('mail');
                                    $info['email'] = $user['email'];
                                    $info['name'] = $user['name'];
                                    $info['password'] = $password;
                                    $info['registered'] = $user['registered'];
                                    if($this->mail->sendTemplate('signup/by_admin_sent_password', $user['email'], $info))
                                        $_SESSION['notify']->success = 'Користувач "'.$user['name'].'" створено успішно. Пароль вислано на поштову скриньку.';
                                }

                                $this->redirect('admin/wl_users/'.$user['email']);
                            }
                        }
                        else
                            $_SESSION['notify']->errors = 'Даний email вже є у базі!';
                    }
                    else
                        $_SESSION['notify']->errors = $this->validator->getErrors();
                    $this->redirect();
                }
                elseif (is_numeric($_POST['id']) && $_POST['id'] > 0)
                {
                    if(isset($_POST['active_password']) && $_POST['active_password'] == 1)
                        $this->validator->setRules('Поле пароль', $this->data->post('password'), 'required|5..40');
                    
                    if($this->validator->run())
                    {
                        $user['email'] = $email;
                        $check = $this->db->getAllDataByFieldInArray('wl_users', $user['email'], 'email');
                        if(count($check) == 0 || $check == false || (count($check) == 1 && $check[0]->id == $_POST['id']))
                        {
                            $user['name'] = $this->data->post('name');
                            if(count($check) == 1 && isset($_POST['active_password']) && $_POST['active_password'] == 1)
                            {
                                $user['password'] = sha1($user['email'] . md5($_POST['password']) . SYS_PASSWORD . $_POST['id']);
                                $do = $this->db->getAllDataById('wl_user_register_do', 'reset_admin', 'name');
                                $register['date'] = time();
                                $register['do'] = $do->id;
                                $register['user'] = $_POST['id'];
                                $register['additionally'] = $check[0]->password. ' by administrator '.$_SESSION['user']->id.' '.$_SESSION['user']->name;
                                $this->db->insertRow('wl_user_register', $register);
                            }
                            $user['alias'] = $this->data->post('alias');
                            $user['type'] = $this->data->post('type');
                            $user['status'] = $this->data->post('status');
                            if($this->db->updateRow('wl_users', $user, $_POST['id']))
                            {
                                $register = array();
                                $this->db->deleteRow('wl_user_permissions', $_POST['id'], 'user');
                                if($user['type'] == 2 && isset($_POST['permissions']) && is_array($_POST['permissions']))
                                {
                                    $register['additionally'] = 'active statuses: ';
                                    $aliases = $this->db->getAllData('wl_aliases');
                                    $alias_list = array();
                                    foreach ($aliases as $a) {
                                        $alias_list[$a->id] = $a->alias;
                                    }
                                    foreach ($_POST['permissions'] as $p) {
                                        if(is_numeric($p))
                                        {
                                            $this->db->insertRow('wl_user_permissions', array('user' => $_POST['id'], 'permission' => $p));
                                            $register['additionally'] .= $alias_list[$p] .', ';
                                        }
                                    }
                                }
                                if(count($check) == 1 && $user['type'] != $check[0]->type)
                                {
                                    $do = $this->db->getAllDataById('wl_user_register_do', 'profile_type', 'name');
                                    $register['date'] = time();
                                    $register['do'] = $do->id;
                                    $register['user'] = $_POST['id'];
                                    $register['additionally'] .= 'user: '.$_SESSION['user']->id.'. '.$_SESSION['user']->name.', old type: '.$check[0]->type.', new type: '.$user['type'];
                                    $this->db->insertRow('wl_user_register', $register);
                                }

                                if(isset($_POST['info']))
                                {
                                    $this->load->model('wl_user_model');
                                    foreach ($_POST['info'] as $key => $value) {
                                        $this->wl_user_model->setAdditional($_POST['id'], $key, $value);
                                    }
                                }

                                $_SESSION['notify']->success = 'Дані оновлено успішно.';
                                $this->redirect('admin/wl_users/'.$user['email']);
                            }
                        }
                        else
                            $_SESSION['notify']->errors = 'Даний email вже є у базі!';
                    }
                    else
                        $_SESSION['notify']->errors = $this->validator->getErrors();
                }
            }
            else
                $_SESSION['notify']->errors = 'Невірний пароль адміністратора';
        }

        $this->redirect();
    }

    public function delete()
    {
        if($_SESSION['user']->admin == 1 && isset($_POST['admin-password']) && isset($_POST['id'])){
            $admin = $this->db->getAllDataById('wl_users', $_SESSION['user']->id);
            $password = md5($_POST['admin-password']);
            $password = sha1($_SESSION['user']->email . $password . SYS_PASSWORD . $_SESSION['user']->id);
            if($password == $admin->password){
                if($_POST['id'] == $_SESSION['user']->id) {
                    $_SESSION['notify']->errors = 'Видалити самого себе неможна!';
                } else {
                    $user = $this->db->getQuery("SELECT u.*, t.title as type_title FROM wl_users as u LEFT JOIN wl_user_types as t ON t.id = u.type WHERE u.id = {$_POST['id']}");
                    if($user) {
                        $this->db->deleteRow('wl_users', $_POST['id']);
                        $this->db->deleteRow('wl_user_info', $_POST['id'], 'user');
                        $this->db->deleteRow('wl_user_permissions', $_POST['id'], 'user');
                        $this->db->deleteRow('wl_user_register', $_POST['id'], 'user');

                        $additionally = "{$user->id}. {$user->email}. {$user->name}.  ({$user->type}) {$user->type_title}. Registered: ".date('d.m.Y H:i', $user->registered);
                        $this->db->register('user_delete', $additionally);

                        header("Location: ".SITE_URL."admin/wl_users");
                        exit();
                    } else {
                        $_SESSION['notify']->errors = 'Профіль користувача не знайдено!';
                    }
                }
            } else {
                $_SESSION['notify']->errors = 'Невірний пароль адміністратора';
            }
        }

        header("Location: ".$_SERVER['HTTP_REFERER']);
        exit();
    }

    public function export()
    {
        if($_SESSION['user']->admin == 1)
        {
            $_SESSION['alias']->name = 'Експорт користувачів';
            $_SESSION['alias']->breadcrumb = array('Користувачі' => 'admin/wl_users', 'Експорт' => '');

            require_once APP_PATH.'controllers/signup.php';
            $signup = new Signup();

            $this->load->admin_view('wl_users/export_view', array('fields_additionall' => $signup->additionall));
        }
    }

    public function export_file()
    {
        if($_SESSION['user']->admin == 1 && !empty($_POST['types']) && !empty($_POST['fields']))
        {
            $this->db->select('wl_users as u', '*', array('type' => $_POST['types']))
                        ->join('wl_user_types', 'title as type_name', '#u.type')
                        ->join('wl_user_status', 'title as status_name', '#u.status');
            if($users = $this->db->get('array'))
            {
                $a = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z');
                $goodfields = array('id' => 'ID', 'email' => 'email', 'name' => "Ім'я користувача", 'type' => 'Тип', 'type_name' => 'Тип', 'status' => 'Статус', 'status_name' => 'Статус', 'registered' => 'Дата реєстрації', 'last_login' => 'Останній вхід');
                require_once APP_PATH.'controllers/signup.php';
                $signup = new Signup();
                if($signup->additionall)
                {
                    foreach ($_POST['fields'] as $field) {
                        if(in_array($field, $signup->additionall))
                        {
                            $goodfields[$field] = $field;
                            foreach ($users as $user) {
                                $user->$field = '';
                                $this->db->select('wl_user_info', 'value', array('user' => $user->id, 'field' => $field));
                                $this->db->order('date DESC');
                                $this->db->limit(1);
                                if($info = $this->db->get('single'))
                                    $user->$field = $info->value;
                            }
                        }
                    }
                }
        
                $this->load->library('PHPExcel');

                // Set document properties
                $this->phpexcel->getProperties()->setCreator(SITE_NAME)
                                             ->setLastModifiedBy(SITE_NAME)
                                             ->setTitle("Users ".SITE_NAME);

                $this->phpexcel->setActiveSheetIndex(0);
                $this->phpexcel->getActiveSheet()->setTitle('Users');

                $x = 0;
                foreach ($_POST['fields'] as $field) {
                    if(array_key_exists($field, $goodfields) && isset($users[0]->$field))
                    {
                        $y = 1;
                        $xy = $a[$x] . $y++;
                        $this->phpexcel->getActiveSheet()->setCellValue($xy, $goodfields[$field]);
                        foreach ($users as $user) {
                            $xy = $a[$x] . $y++;
                            if($field == 'registered' || $field == 'last_login')
                                $user->$field = date('d.m.Y H:i', $user->$field);
                            $this->phpexcel->getActiveSheet()->setCellValue($xy, $user->$field);
                        }
                        $x++;
                    }
                }
 
                // Set active sheet index to the first sheet, so Excel opens this as the first sheet
                $this->phpexcel->setActiveSheetIndex(0);

                header('Cache-Control: max-age=0');
                // If you're serving to IE over SSL, then the following may be needed
                header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
                header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
                header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
                header ('Pragma: public'); // HTTP/1.0

                $date = date('dmY');
                if($_POST['file'] == 'xlsx')
                {
                    // Redirect output to a client’s web browser (Excel2007)
                    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
                    header('Content-Disposition: attachment;filename="'.SITE_NAME.'-users-'.$date.'.xlsx"');

                    $objWriter = PHPExcel_IOFactory::createWriter($this->phpexcel, 'Excel2007');
                    $objWriter->save('php://output');
                }
                elseif($_POST['file'] == 'xls')
                {
                    // Redirect output to a client’s web browser (Excel5)
                    header('Content-Type: application/vnd.ms-excel');
                    header('Content-Disposition: attachment;filename="'.SITE_NAME.'-users-'.$date.'.xls"');

                    $objWriter = PHPExcel_IOFactory::createWriter($this->phpexcel, 'Excel5');
                    $objWriter->save('php://output');
                }
                elseif($_POST['file'] == 'csv')
                {
                    // Redirect output to a client’s web browser (Excel5)
                    header('Content-Type: application/vnd.ms-excel');
                    header('Content-Disposition: attachment;filename="'.SITE_NAME.'-users-'.$date.'.csv"');

                    $objWriter = PHPExcel_IOFactory::createWriter($this->phpexcel, 'CSV')->setDelimiter(',')
                                                                  ->setEnclosure('"')
                                                                  ->setSheetIndex(0);
                    $objWriter->save('php://output');
                }
            }
        }
        else
        {
            $_SESSION['notify'] = new stdClass();
            if($_SESSION['user']->admin != 1)
                $_SESSION['notify']->errors = 'Вигрузку користувачів може виконати тільки адміністратор';
            if(empty($_POST['types']))
                $_SESSION['notify']->errors = 'Увага! Вкажіть типи користувачів до вигрузки';
            if(empty($_POST['fields']))
            {
                if(isset($_SESSION['notify']->errors))
                    $_SESSION['notify']->errors .= '</p><p>Увага! Вкажіть поля вигрузки';
                else
                    $_SESSION['notify']->errors = 'Увага! Вкажіть поля вигрузки';
            }
            $this->redirect();
        }
        exit;
    }

}

?>

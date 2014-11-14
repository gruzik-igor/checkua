<?php

class wl_users extends Controller {
				
    function _remap($method)
    {
        if (method_exists($this, $method) && $method != 'library' && $method != 'db') {
            $this->$method();
        } else {
            $this->index($method);
        }
    }

    public function index(){
        if($_SESSION['user']->admin == 1){
            if($this->data->uri(2) != ''){
                $user = $this->data->uri(2);
                $user = $this->db->sanitizeString($user);
                if(is_numeric($user)) $user = $this->db->getAllDataById('wl_users', $user);
                else $user = $this->db->getAllDataById('wl_users', $user, 'email');
                if(is_object($user) && $user->id > 0){
                    $this->load->admin_view('wl_users/edit_view', array('user' => $user));
                }
            } else {
                $this->load->admin_view('wl_users/list_view');
            }
        }
    }

    public function add()
    {
        if($_SESSION['user']->admin == 1){
            $this->load->admin_view('wl_users/add_view');
        }
    }

    public function save()
    {
        if($_SESSION['user']->admin == 1){
            $this->load->library('validator');
            $this->validator->setRules('email', $this->data->post('email'), 'required|email|3..40');
            $this->validator->setRules('name', $this->data->post('name'), 'required');
            if($_POST['id'] == 0) {
                $this->validator->setRules('Поле пароль', $this->data->post('password'), 'required|5..40');
                if($this->validator->run()){
                    $user['email'] = $this->data->post('email');
                    if($this->db->getAllDataById('wl_users', $user['email'], 'email') == false){
                        $user['name'] = $this->data->post('name');
                        $user['password'] = sha1(md5($_POST['password']) . SYS_PASSWORD);
                        $user['type'] = $this->data->post('type');
                        $user['status'] = 1;
                        $user['registered'] = time();
                        if($this->db->insertRow('wl_users', $user)){
                            $id = $this->db->getLastInsertedId();
                            if($user['type'] == 2 && isset($_POST['permissions']) && is_array($_POST['permissions'])){
                                foreach ($_POST['permissions'] as $p) {
                                    if(is_numeric($p)){
                                        $this->db->insertRow('wl_user_permissions', array('user' => $id, 'permission' => $p));
                                    }
                                }
                            }
                            $do = $this->db->getAllDataById('wl_user_register_do', 'signup', 'name');
                            $register['date'] = time();
                            $register['do'] = $do->id;
                            $register['user'] = $id;
                            $this->db->insertRow('wl_user_register', $register);
                            header("Location: ".SITE_URL.'admin/wl_users/'.$user['email']);
                            exit();
                        }
                    } else $this->load->admin_view('wl_users/add_view', array('errors' => 'Даний email вже є у базі!'));
                } else $this->load->admin_view('wl_users/add_view', array('errors' => $this->validator->getErrors()));
            } elseif (is_numeric($_POST['id']) && $_POST['id'] > 0){
                if(isset($_POST['active_password']) && $_POST['active_password'] == 1){
                    $this->validator->setRules('Поле пароль', $this->data->post('password'), 'required|5..40');
                }
                if($this->validator->run()){
                    $user['email'] = $this->data->post('email');
                    $check = $this->db->getAllDataByFieldInArray('wl_users', $user['email'], 'email');
                    if(count($check) == 0 || $check == false || (count($check) == 1 && $check[0]->id == $_POST['id'])){
                        $user['name'] = $this->data->post('name');
                        if(isset($_POST['active_password']) && $_POST['active_password'] == 1){
                            $user['password'] = sha1(md5($_POST['password']) . SYS_PASSWORD);
                            $do = $this->db->getAllDataById('wl_user_register_do', 'reset_admin', 'name');
                            $register['date'] = time();
                            $register['do'] = $do->id;
                            $register['user'] = $_POST['id'];
                            $register['additionally'] = $check[0]->password;
                            $this->db->insertRow('wl_user_register', $register);
                        }
                        $user['type'] = $this->data->post('type');
                        $user['status'] = $this->data->post('status');
                        if($this->db->updateRow('wl_users', $user, $_POST['id'])){
                            $register = array();
                            $this->db->deleteRow('wl_user_permissions', $_POST['id'], 'user');
                            if($user['type'] == 2 && isset($_POST['permissions']) && is_array($_POST['permissions'])){
                                $register['additionally'] = 'active statuses: ';
                                $aliases = $this->db->getAllData('wl_aliases');
                                $alias_list = array();
                                foreach ($aliases as $a) {
                                    $alias_list[$a->id] = $a->alias;
                                }
                                foreach ($_POST['permissions'] as $p) {
                                    if(is_numeric($p)){
                                        $this->db->insertRow('wl_user_permissions', array('user' => $_POST['id'], 'permission' => $p));
                                        $register['additionally'] .= $alias_list[$p] .', ';
                                    }
                                }
                            }
                            if($user['type'] != $check[0]->type){
                                $do = $this->db->getAllDataById('wl_user_register_do', 'profile_type', 'name');
                                $register['date'] = time();
                                $register['do'] = $do->id;
                                $register['user'] = $_POST['id'];
                                $register['additionally'] .= 'user: '.$_SESSION['user']->id.'. '.$_SESSION['user']->name.', old type: '.$check[0]->type.', new type: '.$user['type'];
                                $this->db->insertRow('wl_user_register', $register);
                            }
                            header("Location: ".SITE_URL.'admin/wl_users/'.$user['email']);
                            exit();
                        }
                    } else $this->load->admin_view('wl_users/edit_view', array('user' => $this->db->getAllDataById('wl_users', $user['email'], 'email'), 'errors' => 'Даний email вже є у базі!'));
                } else $this->load->admin_view('wl_users/edit_view', array('user' => $this->db->getAllDataById('wl_users', $_POST['email'], 'email'), 'errors' => $this->validator->getErrors()));
            }
        }
    }

}

?>

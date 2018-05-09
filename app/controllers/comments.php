<?php 

class Comments extends Controller {
				
    function _remap($method)
    {
        if (method_exists($this, $method) && $method != 'library' && $method != 'db')
            $this->$method();
        else
            $this->index($method);
    }

    public function index()
    {
        $this->load->model('wl_comments_model');
        $comments = $this->wl_comments_model->get(array('status' => array(1, 2)));
    	$this->load->page_view('@wl_comments/index_view', array('comments' => $comments, 'showAddForm' => false));
    }

    public function add()
    {
        $this->load->library('recaptcha');
        $_SESSION['notify'] = new stdClass();
        $anchor = '';

        if($this->recaptcha->check($this->data->post('g-recaptcha-response')))
        {
            $this->load->library('validator');
            $this->validator->setRules('E-mail', $this->data->post('email'), 'required|email');
            $this->validator->setRules('Name', $this->data->post('name'), 'required|3..30');
            $this->validator->setRules('system:alias', $this->data->post('alias'), 'required');
            $this->validator->setRules('system:content', $this->data->post('content'), 'required');
            if($this->validator->run())
            {
                $userId = 0;
                $name = trim($this->data->post('name'));
                if($user = $this->db->getAllDataById('wl_users', $this->data->post('email'), 'email'))
                {
                    if($name != $user->name)
                    {
                        $this->db->updateRow('wl_users', array('name' => $name), $user->id);
                        $this->db->register('profile_data', 'name before: '.$user->name, $user->id);
                    }
                    $userId = $user->id;
                }
                else
                {
                    $user = array('name' => $name);
                    $user['email'] = trim($this->data->post('email'));
                    $user['photo'] = false;
                    $user['password'] = 'auto signup '.time();
                    $this->load->model('wl_user_model');
                    if($user = $this->wl_user_model->add($user, false, 5, false, 'from comments'))
                        $userId = $user->id;
                }

                if($userId > 0)
                {
                    $this->load->model('wl_comments_model');
                    $image_names = false;
                    if($id = $this->wl_comments_model->add($userId, $image_names))
                    {
                        $anchor = '#comment-'.$id;
                        $anchor = '#comment_add_success';

                        $name_field = 'images';
                        if($image_names && !empty($_FILES[$name_field]['name']))
                        {

                            $path = IMG_PATH;
                            $path = substr($path, strlen(SITE_URL));
                            $path = substr($path, 0, -1);
                            
                            if(!is_dir($path))
                            {
                                if(mkdir($path, 0777) == false)
                                    exit('Error create dir ' . $path);
                            }
                            $path .= '/comments';
                            if(!is_dir($path))
                            {
                                if(mkdir($path, 0777) == false)
                                    exit('Error create dir ' . $path);
                            }
                            $path .= '/'.$id;
                            if(!is_dir($path))
                            {
                                if(mkdir($path, 0777) == false)
                                    exit('Error create dir ' . $path);
                            }
                            $path .= '/';

                            $this->load->library('image');
                            for ($i=0; $i < count($_FILES['images']['name']); $i++) { 
                                if(is_array($image_names))
                                    $name = $image_names[$i];
                                else
                                    $name = $image_names;
                                $this->image->uploadArray($name_field, $i, $path, $name);
                                $extension = $this->image->getExtension();
                                $this->image->resize(1280, 1280);
                                $this->image->save();
                                if($this->image->getErrors() == '')
                                {
                                    if($this->image->loadImage($path, $name, $extension))
                                    {
                                        $this->image->preview(130, 90);
                                        $this->image->save('m');
                                    }
                                }
                            }
                        }
                    }
                }
            }
            else
                $_SESSION['notify']->errors = '<ul>'.$this->validator->getErrors('<li>', '</li>').'</ul>';
        }
        else
            $_SESSION['notify']->errors = $this->text('Please fill in the captcha');
        if(isset($_SESSION['notify']->errors))
            $anchor = '#comment_add_error';
        $this->redirect($anchor);
    }

}
<?php

class likes extends Controller {
				
    function _remap($method, $data = array())
    {
        if (method_exists($this, $method))
        {
            if(empty($data))
                $data = null;
            return $this->$method($data);
        }
        else
            $this->index($method);
    }

    public function index()
    {
        if($this->userIs())
        {
            $this->wl_alias_model->setContent();

            $this->load->smodel('likes_model');
            $where = array('user' => $_SESSION['user']->id, 'status' => 1);
            $likes = $this->likes_model->getLikesWithData($where);
            $this->load->profile_view('__user_likes_view', array('likes_list' => $likes));
        }
        else
            $this->redirect('login');
    }

    public function setLike()
    {
        if($this->userIs())
        {
            if (isset($_POST['alias']) && isset($_POST['content']))
            {
                $this->load->smodel('likes_model');
                $this->load->json($this->likes_model->setLike($_SESSION['user']->id));
            }
            else
                $this->load->json('Like error: no set page alias or content');
        }
        else
            $this->load->json('no login');
    }

    public function __show_Like_Btn($data)
    {
        $alias = $_SESSION['alias']->alias_from;
        $content = NULL;
        $user = ($this->userIs()) ? $_SESSION['user']->id : 0;
        $userLike = false;
        if(is_array($data))
        {
            if(isset($data['alias']))
                $alias = $data['alias'];
            if(isset($data['content']))
                $content = $data['content'];
            if(isset($data['user']))
                $user = $data['user'];
        }
        elseif(is_numeric($data))
            $content = $data;
        if($content === NULL)
            return false;

        $this->load->smodel('likes_model');
        $where = array('alias' => $alias, 'content' => $content);
        $likes = $this->likes_model->getLikes($where);
        $likes_count = count($likes);
        if($user > 0 && $likes)
            foreach ($likes as $like) {
                if($like->user == $user)
                {
                    if($like->status == 1)
                        $userLike = true;
                    else
                        $likes_count--;
                    break;
                }
            }

        $this->load->view('__button_view', array('likes' => $likes_count, 'userLike' => $userLike, 'alias' => $alias, 'content' => $content));
    }

    public function __get_Search($content='')
    {
        return false;
    }
	
}

?>
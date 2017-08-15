<?php 

class Comments extends Controller {
				
    function _remap($method)
    {
        if (method_exists($this, $method) && $method != 'library' && $method != 'db') {
            $this->$method();
        } else {
            $this->index($method);
        }
    }

    function index()
    {
    	$this->load->page_view('@wl_comments/index_view', array('content' => false, 'alias' => false));
    }

    function add()
    {
        if($_POST){
            $response = $this->data->post('g-recaptcha-response');
            $callback = file_get_contents('https://www.google.com/recaptcha/api/siteverify?secret=6LdQrwATAAAAAEVaf3KleqOHmfbcAw09NFomGg7x&response='.$response);
            $callback = json_decode($callback);

            if($callback->success == false){
                $this->load->notify_view(array('errors' => 'Заполните каптчу "Я не робот"!'));
                exit;
            }

            $name = $this->data->post('name');
            $comment = $this->data->post('comment');
            $alias = (is_numeric($this->data->post('alias'))) ? $this->data->post('alias') : 0;
            $content = (is_numeric($this->data->post('content'))) ? $this->data->post('content') : 0;
            $status = $_SESSION['option']->default_status;
            $date_add = time();
            $mark1 = (($this->data->post('mark1') >=1)  && ($this->data->post('mark1') <= 5)) ? $this->data->post('mark1') : '0';
            $mark2 = (($this->data->post('mark2') >=1)  && ($this->data->post('mark2') <= 5)) ? $this->data->post('mark2') : '0';
            $mark3 = (($this->data->post('mark3') >=1)  && ($this->data->post('mark3') <= 5)) ? $this->data->post('mark3') : '0';
            $mark4 = (($this->data->post('mark4') >=1)  && ($this->data->post('mark4') <= 5)) ? $this->data->post('mark4') : '0';
            $mark5 = (($this->data->post('mark5') >=1)  && ($this->data->post('mark5') <= 5)) ? $this->data->post('mark5') : '0';
            $mark6 = (($this->data->post('mark6') >=1)  && ($this->data->post('mark6') <= 5)) ? $this->data->post('mark6') : '0';
           
            if(isset($name) && isset($comment)){
                $this->db->executeQuery("INSERT INTO comments (`id`, `alias`, `content`, `name`, `comment`, `mark1`, `mark2`, `mark3`, `mark4`, `mark5`, `mark6`, `status`, `date_add`) VALUES (NULL, {$alias}, {$content}, '{$name}', '{$comment}', '{$mark1}', '{$mark2}', '{$mark3}', '{$mark4}', '{$mark5}', '{$mark6}', $status, '{$date_add}')");
            }
            header('Location: ' . $_SERVER['HTTP_REFERER']);
            exit();
        }
    }

    function delete()
    {
        if($this->userCan()){
            if($_POST){
                $id = $this->data->post('id');
                $this->db->executeQuery("DELETE FROM `comments` WHERE id = $id");
            }
        }
    }

    function status_edit()
    {
        if($this->userCan()){
            if($_POST){
                $status = $this->data->post('status');
                if(isset($_POST['row'])){
                    foreach ($_POST['row'] as $row) {
                        $this->db->executeQuery("UPDATE `comments` SET `status` = $status WHERE `id` = $row");
                    }
                }

            }
        }
    }

    function reply()
    {
        if($this->userCan()){
            if($_POST){
                $comment_id = $this->data->post('id');
                $reply = $this->data->post('reply');
                $date_add = time();
                if(isset($comment_id) && isset($reply)){
                    $this->db->executeQuery("SELECT * FROM comments_reply WHERE comment = $comment_id");
                    if($this->db->numRows() == 0)
                        $this->db->executeQuery("INSERT INTO comments_reply (`comment`, `reply`, `date_add`) VALUES ($comment_id, '{$reply}', $date_add)");
                    else
                        $this->db->executeQuery("UPDATE comments_reply SET `reply` = '{$reply}', `date_add` = $date_add");
                }
            }
            header('Location: ' . $_SERVER['HTTP_REFERER'].'#tab-comments');
            exit;
        }
    }

    

}
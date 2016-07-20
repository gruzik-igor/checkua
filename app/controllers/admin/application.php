<?php

class application extends Controller {

    function _remap($method)
    {
        $_SESSION['alias']->name = 'Заявки';
        $_SESSION['alias']->breadcrumb = array('Заявки' => '');
        if (method_exists($this, $method) && $method != 'library' && $method != 'db') {
            $this->$method();
        } else {
            $this->index($method);
        }
    }
         
    public function index()
    {
        $id = $this->data->uri(2);
        if(is_numeric($id))
        {
            $application = $this->db->getAllDataById('call_order', $id);
            if(!empty($application))
            {
                $_SESSION['alias']->name = 'Заявка від '.date('d.m.Y', $application->date_add);
                $_SESSION['alias']->breadcrumb = array('Заявки' => 'admin/application', 'Детально #'.$application->id => '');
                $this->load->admin_view('application/detal_view', array('application' => $application));
            }
            else
            {
                $this->load->page_404();
            }
        }
        else
        {
            $this->load->admin_view('application/list_view');
        }
    }

    public function getlist()
    {
        if($_SESSION['user']->admin == 1){
            $call_order = $this->db->getQuery('SELECT co.*, cos.name as status_name FROM call_order AS co LEFT JOIN call_order_status AS cos ON co.status = cos.id', 'array');

            if($call_order){
                foreach ($call_order as $order) {
                    $order->link = '<a href="'.SITE_URL.'admin/application/'.$order->id.'">Детально</a>';
                    $order->date_add = (string) date('d.m.Y H:i', $order->date_add);
                }
            }
            header('Content-type: application/json');
            echo json_encode(array('data' => $call_order));
            exit;
        }
    }

    public function save()
        {
            if(isset($_POST['id']) && is_numeric($_POST['id']) && $_POST['id'] > 0)
            {
                $data['status'] = $this->data->post('status');
                $data['answer'] = $this->data->post('answer');
                $data['manager'] = $_SESSION['user']->id;
                $data['date_manage'] = time();
                $this->db->updateRow('call_order', $data, $_POST['id']);

                if($this->data->post('after') == 'all')
                {
                    header('Location:'.SITE_URL.'admin/application');
                }
                else
                {
                    $_SESSION['notify']->success = 'Заявку успішно змінено!';
                    header('Location:'.$_SERVER['HTTP_REFERER']);
                }
                exit();
            }
        }
    }

?>
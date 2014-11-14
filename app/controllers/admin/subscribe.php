<?php

class subscribe extends Controller {

	private $sent_emails = '';
				
    function _remap($method)
    {
        if (method_exists($this, $method)) {
            $this->$method();
        } else {
            $this->index($method);
        }
    }

    function index(){
		$this->load->model('subscribe_model');
		$mails = $this->subscribe_model->getAll();
		$this->load->admin_view('subscribe/all_view', array('mails' => $mails));
    }
	
	function edit(){
		$id = $this->data->uri(3);		
		if(is_numeric($id) && isset($_GET['active']) && is_numeric($_GET['active'])){
			$this->load->model('subscribe_model');
			$this->subscribe_model->setActiveR($id, $_GET['active']);
		} 
		header('Location: '.SITE_URL.'admin/subscribe');
		exit;
	}
	
	function delete(){
		if(isset($_GET['id']) && is_numeric($_GET['id'])){
			if($this->db->deleteRow('mails', $_GET['id'])){
				header('Location: '.SITE_URL.'admin/subscribe');
				exit;
			} else echo "DELETE ERROR!";
		}
	}
	
	function mail(){
		$this->load->admin_view('subscribe/mail_view');
	}
	
	function makemail(){
		$this->load->model('subscribe_model');
		$mails = $this->subscribe_model->getAll();
		if($this->sentMail() && $this->sent_emails != ''){
			$mail_from = SYS_EMAIL;
			if(isset($_POST['from']) && $_POST['from'] != '') $mail_from = $_POST['from'];
			$success = 'Розсилку з '.$mail_from.' на наступні емейли '.$this->sent_emails.' розіслано успішно!';
			$this->load->admin_view('subscribe/all_view', array('mails' => $mails, 'success' => $success));
		} else {
			$errors = 'Увага! Сталася помилка, спробуйте щераз!';
			$this->load->admin_view('subscribe/all_view', array('mails' => $mails, 'errors' => $errors));
		}
	}
	
	private function sentMail(){
		$this->load->model('subscribe_model');
		$mails = $this->subscribe_model->getListActiveMail();
		if($mails){
			foreach($mails as $m){ 
				if($m->email != ''){
					$this->load->library('mail');
					$msg_body = '<html><head></head><body>'.$_POST['mess'].'</body></html>';
					$this->mail->params(array('name' => 'Адміністрація '.SITE_NAME));
					$this->mail->message($msg_body);
					$this->mail->subject($_POST['title'].' '.SITE_NAME);
					$mail_from = SITE_EMAIL;
					if(isset($_POST['from']) && $_POST['from'] != '') $mail_from = $_POST['from'];
					$this->mail->from($mail_from);
					$this->sent_emails .= $m->email.', ';
					$this->mail->to($m->email);
					$this->mail->send();
				}
			}
			$this->sent_emails = substr($this->sent_emails, 0, -2);
			return true;
		}
		return false;
	}
	
}

?>
<?php

class Mail {

    private $to;
    private $from;
    private $subject;
    private $replyTo;
    private $message;
    private $params;

    function subject($title){
        $this->subject = $title;
    }

    function to($send_to){
        $this->to = $send_to;
    }

    function from($send_from){
        // $pos = strpos($send_from, '@');
        // $this->from = substr($send_from, 0, $pos);
        $this->from = $send_from;
        $this->replyTo = $send_from;
    }

    function message($msg){
        $this->message = $msg;
    }

    function params($pms){
        $this->params = $pms;
    }

    function send(){
        if(is_array($this->params)){
            foreach($this->params as $key => $value){
                $this->message = str_replace('{'.$key.'}', $value, $this->message);
            }
        }

        // $this->message = mb_convert_encoding ($this->message, 'windows-1251', 'utf-8');

        $headers ="Mime-Version: 1.0 \r\n";
        // $headers .= "Content-type: text/html; charset=windows-1251 \r\n";
        $headers .= "Content-type: text/html; charset=utf-8 \r\n";
        $headers .= "From: ".SITE_NAME." <".$this->from."> \r\n";
        $headers .= 'Reply-To: '.$this->replyTo;

        if(mail($this->to, $this->subject, $this->message, $headers)) {
            return true;
        } else {
            return false;
        }
    }
	
	function sendTemplate($template, $to, $data = array()){
		$path = APP_PATH.'mails'.DIRSEP.$template.'.php';
        if($_SESSION['language']){
            $path = APP_PATH.'mails'.DIRSEP.$_SESSION['language'].DIRSEP.$template.'.php';
            if(file_exists($path) == false) $path = APP_PATH.'mails'.DIRSEP.$template.'.php';
        } 
		if(file_exists($path)){
			$subject = '';
			$message = '';
            $from_mail = SITE_EMAIL;
            $from_name = 'Адміністрація '.SITE_NAME;
			require($path);
			if($message != '' && $subject != ''){
				$this->params(array('name' => $from_name));
				$this->message($message);
				$this->subject($subject);
				$this->to($to);
				$this->from($from_mail);
				
				if($this->send()) return true;
			}
		}
		return false;
	}
}
?>

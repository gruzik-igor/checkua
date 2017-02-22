<?php

class Mail {

    private $to;
    private $from;
    private $subject;
    private $replyTo;
    private $message;
    private $params;

    public function subject($title)
    {
        $this->subject = $title;
    }

    public function to($send_to)
    {
        $this->to = $send_to;
    }

    public function from($send_from)
    {
        $this->from = $send_from;
        $this->replyTo = $send_from;
    }

    public function message($msg)
    {
        $this->message = $msg;
    }

    public function params($pms)
    {
        $this->params = $pms;
        $this->params['SITE_URL'] = SITE_URL;
        $this->params['IMAGE_PATH'] = IMG_PATH;
    }

    public function send()
    {
        if(is_array($this->params))
            foreach($this->params as $key => $value) {
                $this->subject = str_replace('{'.$key.'}', $value, $this->subject);
                $this->message = str_replace('{'.$key.'}', $value, $this->message);
            }
        // $this->message = mb_convert_encoding ($this->message, 'windows-1251', 'utf-8');

        $headers ="Mime-Version: 1.0 \r\n";
        // $headers .= "Content-type: text/html; charset=windows-1251 \r\n";
        $headers .= "Content-type: text/html; charset=utf-8 \r\n";
        $headers .= "From: ".SITE_NAME." <".$this->from."> \r\n";
        $headers .= 'Reply-To: '.$this->replyTo;

        $sent_mail = new stdClass();
        $sent_mail->from = $this->from;
        $sent_mail->to = $this->to;
        $sent_mail->replyTo = $this->replyTo;
        $sent_mail->subject = $this->subject;
        $sent_mail->message = $this->message;
        $sent_mail->headers = $headers;

        if($_SERVER["SERVER_NAME"] == 'localhost')
            return $sent_mail;
        if(mail($this->to, $this->subject, $this->message, $headers))
            return $sent_mail;
        return false;
    }

	public function sendTemplate($template, $to, $data = array())
    {
		$path = APP_PATH.'mails'.DIRSEP.$template.'.php';
        if($_SESSION['language'])
        {
            $path = APP_PATH.'mails'.DIRSEP.$_SESSION['language'].DIRSEP.$template.'.php';
            if(file_exists($path) == false)
                $path = APP_PATH.'mails'.DIRSEP.$template.'.php';
        }
		if(file_exists($path))
        {
			$subject = '';
			$message = '';
            $from_mail = SITE_EMAIL;
            $from_name = 'Адміністрація '.SITE_NAME;
			require($path);
			if($message != '' && $subject != '')
            {
				$this->params(array('name' => $from_name));
				$this->message(html_entity_decode($message));
				$this->subject($subject);
				$this->to($to);
				$this->from($from_mail);

				return $this->send();
			}
		}
		return false;
	}

    public function sendMailTemplate($template, $data = array())
    {
        $from = $this->checkMail($template->from, $data);
        $to = $this->checkMail($template->to, $data);

        if($from && $to)
        {
            $this->params($data);
            $this->message(html_entity_decode($template->text));
            $this->subject($template->title);
            $this->to($to);
            $this->from($from);

            return $this->send();
        }
        return false;
    }

    public function checkMail($mail, $data = array())
    {
        switch ($mail) {
            case 'SITE_EMAIL':
                return SITE_EMAIL;
                break;

            default:
                if(substr($mail, 0, 1) == '{' && substr($mail, -1) == '}')
                    $mail = substr($mail, 1, -1);
                if(!isset($data[$mail]) && preg_match('^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,4})^', $mail))
                    return $mail;
                elseif(isset($data[$mail]) && preg_match('^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,4})^', $data[$mail]))
                    return $data[$mail];
                else
                    return false;
                break;
        }
        return false;
    }

}
?>

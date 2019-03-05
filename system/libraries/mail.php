<?php

/*
 * Версія 2.0 (05.03.2019) підключено smtp через Swift_Mailer
 */

class Mail {

    private $to;
    private $from;
    private $subject;
    private $replyTo;
    private $message;
    private $params;
    private $smtp;

    /*
     * Отримуємо дані для з'єднання з конфігураційного файлу
     */
    function __construct($cfg)
    {
        if(!empty($cfg['host']) && $cfg['host'] != '$MAILHOST')
        {
            $cfg['port'] = $cfg['port'] ?? 25;
            require_once 'swiftmailer/autoload.php';

            $transport = (new Swift_SmtpTransport($cfg['host'], $cfg['port']))
              ->setUsername($cfg['user'])
              ->setPassword($cfg['password']);

            $this->from = $this->replyTo = $cfg['user'];

            $this->smtp = new Swift_Mailer($transport);
        }
    }

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
        if(empty($this->smtp))
        {
            $this->from = $send_from;
            $this->replyTo = $send_from;
        }
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

        $headers ="Mime-Version: 1.0 \r\n";
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

        if($this->smtp)
        {
            $message = (new Swift_Message($this->subject))
                      ->setFrom($this->from)
                      ->setTo($this->to)
                      ->setBody($this->message, 'text/html');
            if($this->replyTo)
                $message->setReplyTo($this->replyTo);

            if($this->smtp->send($message))
                return $sent_mail;
        }
        else
        {
            if($_SERVER["SERVER_NAME"] == 'localhost')
                return $sent_mail;
            if(mail($this->to, $this->subject, $this->message, $headers))
                return $sent_mail;
        }
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
        if($_SESSION['alias']->service)
        {
            $folder_path = APP_PATH.'services'.DIRSEP.$_SESSION['alias']->service.DIRSEP.'mails';
            if(is_dir($folder_path))
            {
                $folder_path = APP_PATH.'services'.DIRSEP.$_SESSION['alias']->service.DIRSEP.'mails'.DIRSEP.$template.'.php';
                if(file_exists($folder_path))
                {
                    $path = $folder_path;
                    if($_SESSION['language'])
                    {
                        $folder_path = APP_PATH.'services'.DIRSEP.$_SESSION['alias']->service.DIRSEP.'mails'.DIRSEP.$_SESSION['language'].DIRSEP.$template.'.php';
                        if(file_exists($folder_path))
                            $path = $folder_path;
                    }
                }
            }
            
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
            case '{SITE_EMAIL}':
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

<?php

class MailHandler extends Controller {

    function index(){
        $owner_email = SITE_EMAIL;
        $headers = 'From:' . $_POST["email"] . "\r\n" . 'Reply-To: ' . $_POST["email"] . "\r\n" . 'Content-Type: text/plain; charset=UTF-8' . "\r\n";
        $subject = 'A message from your site visitor ' . $_POST["name"];
        $messageBody = "";
        
        if($_POST['name']!='nope'){
                $messageBody .= '<p>Visitor: ' . $_POST["name"] . '</p>' . "\n";
                $messageBody .= '<br>' . "\n";
        }
        if($_POST['email']!='nope'){
                $messageBody .= '<p>Email Address: ' . $_POST['email'] . '</p>' . "\n";
                $messageBody .= '<br>' . "\n";
        }else{
                $headers = '';
        }
        if($_POST['phone']!='nope'){            
                $messageBody .= '<p>Phone Number: ' . $_POST['phone'] . '</p>' . "\n";
                $messageBody .= '<br>' . "\n";
        }       
        if($_POST['message']!='nope'){
                $messageBody .= '<p>Message: ' . $_POST['message'] . '</p>' . "\n";
        }
        
        if($_POST["stripHTML"] == 'true'){
                $messageBody = strip_tags($messageBody);
        }
        
        try{
            if(!mail($owner_email, $subject, $messageBody, $headers)){
                throw new Exception('mail failed');
            }else{
            	$message = array();
            	$message['date'] = time();
            	$message['name'] = $_POST["name"];
            	$message['email'] = $_POST["email"];
            	$message['phone'] = $_POST["phone"];
            	$message['message'] = strip_tags($_POST["message"]);
            	if($this->db->insertRow('mail_handler', $message)){
            		echo 'success';
            	} else {
					echo 'error add to db. Mail sent';
            	}
            }
        } catch(Exception $e){
                echo $e->getMessage() ."\n";
        }
    }
	
}



?>

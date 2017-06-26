<?php

class save extends Controller {

	public $errors = array();

    function _remap($method)
    {
        if (method_exists($this, $method)) {
            $this->$method();
        } else {
            $this->index($method);
        }
    }

    public function index()
    {
    	$formName = $this->data->uri(1);

    	if($formName != '')
        {
            $form = $this->db->getAllDataById('wl_forms', $formName, 'name');
            if($form && $form->table != '' && $form->type > 0 && $form->type_data > 0)
            {
                if($form->captcha)
                {
                    $this->load->library('recaptcha');
                    if($this->recaptcha->check($this->data->post('g-recaptcha-response')) == false)
                    {
                        $this->errors[] = 'Заповніть каптчу (Я не робот)!';
                    }
                }

                $this->db->select('wl_fields as f', '*', $form->id, 'form');
                $this->db->join('wl_input_types', 'name as type_name', '#f.input_type');
                if($fields = $this->db->get('array'))
                {
                	$data = $data_id = array();
                	foreach ($fields as $field) {
                		$input_data = null;

                		if($form->type == 1)
                            $input_data = $this->data->get($field->name);
                		elseif($form->type == 2)
                            $input_data = $this->data->post($field->name);
                		if($field->required && $input_data == null)
                			$this->errors[] = "Field '{$field->title}' is required!";

                		if($input_data)
                        {
                            $data[$field->name] = $input_data;
                			$data_id[$field->name] = $field->id;
                		}
                	}

                	if(!empty($data) && empty($this->errors))
                    {
            			if($form->type_data == 1)
                        {
                            foreach ($data as $field => $value) {
                                $row['field'] = $data_id[$field];
                                $row['value'] = $value;
                                $this->db->insertRow($form->table, $row);
                            }
                        } 
                        elseif($form->type_data == 2)
                        {
                            $data['date_add'] = time();
                            $data['language'] = isset($_SESSION['language']) ? $_SESSION['language'] : null;
                            $this->db->insertRow($form->table, $data);
                            $data['id'] = $this->db->getLastInsertedId();
                        }
                	}
                    else
                    {
                        echo('<pre>Input data: <br>');
                        print_r($data);
                        echo "<br>Errors:<br>";
                        print_r($this->errors);
                        exit();
                    }
                    $where['form'] = $form->id;
                    $where['active'] = 1;

                    if($form->send_sms == 1 && $form->sms_text != '' &&($data['tel'] || $data['phone']))
                    {
                        $phone = $data['tel'] ?: $data['phone'];

                        if(substr($phone, 0, 1) == '0')
                            $phone = "+38" . $phone;
                        elseif(substr($phone, 0, 2) == '80')
                            $phone = "+3" . $phone;

                        $this->load->library('turbosms');
                        $this->turbosms->send($phone, $form->sms_text);
                    }

                	$mails = $this->db->getAllDataByFieldInArray('wl_mail_active', $where);
                    if(!empty($mails))
                    {
                        $this->load->library('mail');
                        foreach ($mails as $key => $mail) 
                        {
                            if($mail = $this->db->getAllDataById('wl_mail_templates', $mail->template))
                            {
                                $join['template'] = $mail->id;
                                if($mail->multilanguage == 1)
                                    $join['language'] = $_SESSION['language'];

                                $message = $this->db->getAllDataById('wl_mail_templats_data', $join);
                                $mail->title = $message->title;
                                $mail->text = $message->text;

                                $data['date_add'] = date('d.m.Y H:i', $data['date_add']);
                                if($sendMail = $this->mail->sendMailTemplate($mail, $data))
                                {
                                    if($mail->savetohistory == 1)
                                    {
                                        $updateHistory = array();
                                        $updateHistory['template'] = $mail->id;
                                        $updateHistory['date'] = time();
                                        $updateHistory['title'] = $sendMail->subject;
                                        $updateHistory['text'] = $sendMail->message;
                                        $updateHistory['from'] = $sendMail->from; 
                                        $updateHistory['to'] = $sendMail->to;

                                        $this->db->insertRow('wl_mail_history', $updateHistory);
                                    }
                                }
                                else
                                    exit('Error sending mail! Data saved successfully.');
                            }    
                        }
                    }
                    switch ($form->success) 
                    {
                        case '1':
                            $this->redirect();
                            break;
                        case '2':
                            $lang = $_SESSION['language'];
                            $text = $_SESSION['all_languages'] ? json_decode($form->success_data)->$lang : $form->success_data;
                            $this->load->notify_view(array('success' => $text));
                            break;
                        case '3':
                            header("Location:".SITE_URL.$form->success_data);
                            break;
                    }
                }
            }
        }
    }
}

?>
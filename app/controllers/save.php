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

    	if($formName != ''){
            $form = $this->db->getAllDataById('wl_forms', $formName, 'name');
            if($form && $form->table != '' && $form->send_mail == 1 && $form->type > 0 && $form->type_data > 0){
                $fields = $this->db->getQuery("SELECT f.*, t.name as type_name FROM wl_fields as f LEFT JOIN wl_input_types as t ON t.id = f.input_type WHERE f.form = {$form->id}", 'array');

                if($fields){
                	$data = $data_id = $this->errors = array();

                	foreach ($fields as $field) {
                		$input_data = null;

                		if($form->type == 1) $input_data = $this->data->get($field->name);
                		elseif($form->type == 2) $input_data = $this->data->post($field->name);
                		if($field->required && $input_data == null) {
                			$this->errors[] = "Field '{$field->title}' is required!";
                		}

                		if($input_data){
                            $data[$field->name] = $input_data;
                			$data_id[$field->name] = $field->id;
                		}
                	}

                	if(!empty($data) && empty($this->errors)){
            			if($form->type_data == 1){
                            foreach ($data as $field => $value) {
                                $row['field'] = $data_id[$field];
                                $row['value'] = $value;
                                $this->db->insertRow($form->table, $row);
                            }
                        } elseif($form->type_data == 2){
                           // $data['date'] = date("d.m.Y H:i", time());
                            $this->db->insertRow($form->table, $data);
                            $data['id'] = $this->db->getLastInsertedId();
                        }
                	}
                    $where['form'] = $form->id;
                    $where['active'] = 1;


                    if($form->send_sms == 1 && $form->sms_text != '' &&($data['tel'] || $data['phone'])){
                        $phone = $data['tel'] ?: $data['phone'];

                        if(substr($phone, 0, 1) == '0'){
                            $phone = "+38" . $phone;
                        } else if(substr($phone, 0, 2) == '80'){
                            $phone = "+3" . $phone;
                        }

                        $this->load->library('turbosms');
                        $this->turbosms->send($phone, $form->sms_text);
                    }

                	$mails = $this->db->getAllDataByFieldInArray('wl_mail_active', $where);
                    if(!empty($mails)){
                        $this->load->library('mail');
                        foreach ($mails as $mail) {

                            $mail = $this->db->getAllDataById('wl_mail_templates', $mail->template);

                            $join['template'] = $mail->id;
                            if($mail->multilanguage == 1)
                                $join['language'] = $_SESSION['language'];

                            $message = $this->db->getAllDataById('wl_mail_templats_data', $join);
                            $mail->title = $message->title;
                            $mail->text = $message->text;

                            if($mail)
                                if($this->mail->sendMailTemplate($mail, $data)){
                                    switch ($form->success) {
                                        case '1':
                                            $this->redirect();
                                            break;
                                        case '2':
                                            $this->load->notify_view(array('success' => $form->success_data));
                                            break;
                                        case '3':
                                            header("Location:".SITE_URL.$form->success_data);
                                            break;
                                    }
                                } else {
                                    $this->redirect();
                                }
                        }
                    }
                }
            }
        }
    }
}

?>
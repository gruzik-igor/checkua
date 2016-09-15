<?php

class wl_forms extends Controller {

    function _remap($method)
    {
        $_SESSION['alias']->name = 'Форми';
        $_SESSION['alias']->breadcrumb = array('Форми' => '');
        if (method_exists($this, $method) && $method != 'library' && $method != 'db') {
            $this->$method();
        } else {
            $this->index($method);
        }
    }

    public function index()
    {
        if($this->data->uri(2) != ''){
            $form = $this->data->uri(2);
            $form = $this->db->getAllDataById('wl_forms', $form, 'name');
            if($form){
                $field_name = $this->data->uri(3);
                if($field_name != ''){
                    $field_name = $this->db->getQuery("SELECT f.*, t.options as type_options FROM wl_fields as f LEFT JOIN wl_input_types as t ON t.id = f.input_type WHERE f.name = '{$field_name}' AND f.form = $form->id");
                    if($field_name){
                        if($field_name->type_options) $field_name->options = $this->db->getAllDataByFieldInArray('wl_fields_options', $field_name->id, 'field');

                        $fields = $this->db->getAllDataByFieldInArray('wl_fields', $form->id, 'form');

                        if($fields){
                            foreach ($fields as $f) {
                                $names[] = $f->name;
                            }
                            $diff_name = implode('","', array_diff($names, array($field_name->name)));
                        }
                        $this->load->admin_view('wl_forms/edit_field_view', array('field_name' => $field_name, 'form' => $form, 'diff_name' => $diff_name));
                    }
                    else $this->load->page_404();
                } else {
                    $_SESSION['alias']->name = 'Редагувати форму';
                    $_SESSION['alias']->breadcrumb = array('Форми' => 'admin/wl_forms', 'Редагувати' => '');

                    $fields = $this->db->getQuery("SELECT f.*, i.name as input_type_name FROM wl_fields as f LEFT JOIN wl_input_types as i ON i.id = f.input_type WHERE f.form = {$form->id}", 'array');
                    $names = '';

                    if($fields){
                        foreach ($fields as $f) {
                            $names[] = $f->name;
                        }
                        $names = implode('","', $names);
                    }

                    $checkedTemplates = $this->db->getQuery("SELECT template FROM `wl_mail_active` WHERE `form` = $form->id", 'array');

                    $allTemplates = $this->db->getQuery("SELECT t.id, td.title FROM `wl_mail_templates` as t LEFT JOIN `wl_mail_templats_data` as td ON t.id = td.template ", 'array');

                    $templates = array();
                    $known = array();
                    if(!empty($allTemplates)){
                        $templates = array_filter($allTemplates, function ($val) use (&$known) {
                            $unique = !in_array($val->id, $known);
                            $known[] = $val->id;
                            return $unique;
                        });
                    }
                    


                    if(!empty($checkedTemplates)){
                        foreach ($templates as $template) {
                            foreach ($checkedTemplates as $checkedTemplate) {
                                if($checkedTemplate->template == $template->id){
                                    $template->checked = 1;
                                    break;
                                } else $template->checked = 0;
                            }
                        }
                    }


                    $this->load->admin_view('wl_forms/edit_view', array('form' => $form, 'fields' => $fields, 'templates' => $templates, 'names' => $names));
                }
            } else $this->load->page_404();
        } else {
            $this->load->admin_view('wl_forms/list_view');
        }
    }

    public function add()
    {
        $_SESSION['alias']->name = 'Додати нову форму';
        $_SESSION['alias']->breadcrumb = array('Форми' => 'admin/wl_forms', 'Нова форма' => '');
        $this->load->admin_view('wl_forms/add_view');
    }

    public function add_save()
    {

        if(!empty($_POST)){
            if(!empty($_POST['name'])) $name = $_POST['name'];
            $captcha = ($_POST['captcha'] == 'yes' ) ? 1 : 0;
            $help = (!empty($_POST['help']))? $_POST['help'] : '';
            if(!empty($_POST['table'])) $table = $_POST['table'];
            if(!empty($_POST['type'])) $type = ($_POST['type'] == 'get') ? 1 : 2;
            if(!empty($_POST['send_mail'])) $send_mail = ($_POST['send_mail'] == 'yes') ? 1 : 0;
            if(!empty($_POST['send_sms'])) $send_sms = ($_POST['send_sms'] == 'yes') ? 1 : 0;

            if(isset($name,$table,$type)){
                $this->db->executeQuery("INSERT INTO `wl_forms` (`id` ,`name` ,`captcha` ,`help` ,`table` ,`type` ,`type_data`, `send_mail`, `send_sms`) VALUES (NULL ,  '$name',  $captcha,  '$help',  '$table',  $type,  2, $send_mail, $send_sms)");
                header("Location: ".SITE_URL."admin/wl_forms/".$name);
                exit();
            }
        }
    }

    public function add_field()
    {
        if(!empty($_POST)){

            $form = $_POST['form'];

            $formById = $this->db->getAllDataByFieldInArray('wl_fields', $form, 'form');
            $namesById = array();
            if($formById){
                foreach ($formById as $names) {
                    $namesById[] = $names->name;
                }
            }

            if(!in_array($_POST['name'], $namesById) && !empty($_POST['name'])){  //Провірка на унікальне ім'я
                $name = $_POST['name'];
            }

            if(!empty($_POST['input_type'])) $input_type = $_POST['input_type'];

            $required = ($_POST['required'] == '1')? 1 : 0;
            if(!empty($_POST['title'])) $title = $_POST['title'];

            if(isset($name, $input_type, $title)){
                $this->db->executeQuery("INSERT INTO `wl_fields` (`id`, `form`, `name`, `input_type`, `required`, `title`) VALUES (NULL, $form, '$name', $input_type, $required, '$title')");

                if(!empty($_POST['value'])){
                    $fieldId = $this->db->getLastInsertedId();
                    foreach ($_POST['value'] as $value) {
                        if(!empty($value)) $this->db->executeQuery("INSERT INTO `wl_fields_options` (`field`, `value`, `title`) VALUES($fieldId, '$value', '$value')");
                    }
                }
            }

            header("Location: ".SITE_URL."admin/wl_forms/".$_POST['form_name']);

        }
    }

    public function edit_field($value='')
    {
        if(!empty($_POST)){
            $id = $_POST['id'];
            $form = $_POST['form'];

            $formById = $this->db->getAllDataByFieldInArray('wl_fields', $form, 'form');
            foreach ($formById as $names) {
                $namesById[] = $names->name;
            }
            if(!in_array($_POST['name'], array_diff($namesById, array($_POST['field_name']))) && !empty($_POST['name'])){  //Провірка на унікальне ім'я
                    $name = $_POST['name'];
            }

            if(!empty($_POST['input_type'])) $input_type = $_POST['input_type'];
            $required = ($_POST['required'] == '1')? 1 : 0;
            if(!empty($_POST['title'])) $title = $_POST['title'];

            if(isset($name, $input_type, $title)){
                $this->db->executeQuery("UPDATE `wl_fields` SET `form` = $form, `name` = '$name', `input_type` = $input_type, `required` = $required, `title` = '$title' WHERE `wl_fields`.`id` = $id");

                $this->db->executeQuery("DELETE FROM `wl_fields_options` WHERE `field` = $id");

                if(!empty($_POST['value'])){
                    foreach ($_POST['value'] as $value) {
                        if(!empty($value)) $this->db->executeQuery("INSERT INTO `wl_fields_options` (`field`, `value`, `title`) VALUES($id, '$value', '$value')");
                    }
                }

                header("Location: ".$this->redirect());
            }
        }
    }

    public function edit_form()
    {
        $data = array();

        $data['name'] = $this->data->post('name');
        $data['captcha'] = $this->data->post('captcha');
        $data['help'] = $this->data->post('help');
        $data['table'] = $this->data->post('table');
        $data['type'] = $this->data->post('type') == 'get' ? 1 : 2;
        $data['type_data'] = 2;
        $data['send_mail'] = $this->data->post('send_mail') == 'yes' ? 1 : 0;
        $data['success'] = $this->data->post('after');
        $data['success_data'] = isset($_POST['afterValue']) ? $this->data->post('afterValue') : '';
        $data['send_sms'] = $this->data->post('send_sms') == 'yes' ? 1 : 0;
        $data['sms_text'] = isset($_POST['sms_text']) ? $this->data->post('sms_text') : '';

        if(isset($data['name'],$data['table'],$data['type'],$data['type_data'],$_POST['formId'])){
            $formId = $_POST['formId'];

            $this->db->updateRow('wl_forms', $data, $formId);

            $this->db->executeQuery("DELETE FROM `wl_mail_active` WHERE `form` = $formId");
            if(isset($_POST['templates'])){
                foreach ($_POST['templates'] as $template) {
                    $this->db->insertRow('wl_mail_active', array('form' => $formId, 'template' => $template, 'active' => 1));
                }
            }
        }

        header("Location:".$_SERVER['HTTP_REFERER']);

    }
}

?>
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

                    if($form->success == 2 && $_SESSION['all_languages']){
                        $form->success_data = json_decode($form->success_data);
                    }

                    $tableExist = $this->db->getQuery("SHOW TABLES LIKE '{$form->table}'");

                    $this->load->admin_view('wl_forms/edit_view', array('form' => $form, 'fields' => $fields, 'templates' => $templates, 'names' => $names, 'tableExist' => $tableExist));
                }
            } else $this->load->page_404();
        } else {
            $forms = $this->db->getAllData('wl_forms');
            $this->load->admin_view('wl_forms/list_view', array('forms' => $forms));
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
            if(!empty($_POST['name'])) $name = $this->data->post('name');
            $captcha = ($_POST['captcha'] == 'yes' ) ? 1 : 0;
            $title = $this->data->post('title');
            if(!empty($_POST['type'])) $type = ($_POST['type'] == 'get') ? 1 : 2;

            if(isset($name,$type)){
                $this->db->executeQuery("INSERT INTO `wl_forms` (`id` ,`name` ,`captcha` ,`title` ,`table` ,`type` ,`type_data`, `send_mail`, `send_sms`, `sidebar`) VALUES (NULL ,  '$name',  $captcha,  '$title',  '$name',  $type,  2, 0, 0, 0)");
                header("Location: ".SITE_URL."admin/wl_forms/".$name);
                exit();
            }
        }
    }

    public function add_field()
    {
        if(!empty($_POST))
        {
            $form = $this->data->post('form');
            $formName = $this->data->post('form_name');
            $formTable = $this->data->post('form_table');

            //Провірка на унікальне ім'я
            $formById = $this->db->getAllDataByFieldInArray('wl_fields', $form, 'form');
            $namesById = array();
            if($formById)
            {
                foreach ($formById as $names) {
                    $namesById[] = $names->name;
                }
            }
            if(!in_array($_POST['name'], $namesById) && !empty($_POST['name']))
                $name = $this->data->post('name');

            $input_type = $this->data->post('input_type');
            $required = ($_POST['required'] == '1') ? 1 : 0;
            $title = $this->data->post('title');

            $lastColumn = end($namesById);
            $this->db->executeQuery("ALTER TABLE `{$formTable}` ADD {$name} text AFTER `{$lastColumn}`");

            if(isset($name, $input_type, $title))
            {
                $this->db->executeQuery("INSERT INTO `wl_fields` (`id`, `form`, `name`, `input_type`, `required`, `title`) VALUES (NULL, $form, '$name', $input_type, $required, '$title')");

                if(!empty($_POST['value']))
                {
                    $fieldId = $this->db->getLastInsertedId();
                    foreach ($_POST['value'] as $value) {
                        if(!empty($value)) $this->db->executeQuery("INSERT INTO `wl_fields_options` (`field`, `value`, `title`) VALUES($fieldId, '$value', '$value')");
                    }
                }
            }
            header("Location: ".SITE_URL."admin/wl_forms/".$formName);
        }
    }

    public function edit_field($value='')
    {
        if(!empty($_POST)){
            $id = $this->data->post('id');
            $form = $this->data->post('form');
            $formName = $this->data->post('formname');
            $data = array();
            
            //Провірка на унікальне ім'я
            $formById = $this->db->getAllDataByFieldInArray('wl_fields', $form, 'form');
            foreach ($formById as $names) {
                $namesById[] = $names->name;
            }
            if(!in_array($_POST['name'], array_diff($namesById, array($_POST['field_name']))) && !empty($_POST['name']))
                $data['name'] = $this->data->post('name');
            $data['input_type'] = $this->data->post('input_type');
            $data['title'] = $this->data->post('title');
            $data['required'] = $this->data->post('required');

            if(isset($data['name']))
            {
                $this->db->updateRow('wl_fields', $data, $id);

                $this->db->deleteRow('wl_fields_options', $id, 'field');

                if(!empty($_POST['value']))
                {
                    $option = array('field' => $id);
                    foreach ($_POST['value'] as $value) {
                        if(!empty($value))
                        {
                            $option['value'] = $option['title'] = $value;
                            $this->db->insertRow('wl_fields_options', $option);
                        }
                    }
                }
                header("Location: ".SITE_URL."admin/wl_forms/".$formName.'/'.$data['name']);
            }
        }
    }

    public function edit_form()
    {
        $data = array();
        $formId = $_POST['formId'];

        $data['sidebar'] = ($this->data->post('sidebar')) ? $this->data->post('sidebar') : 0;
        $data['name'] = $this->data->post('name');
        $this->data->post('table') ? $data['table'] = $this->data->post('table') : '' ;
        $data['captcha'] = ($this->data->post('captcha')) ? $this->data->post('captcha') : 0;
        $data['title'] = $this->data->post('title');
        $data['type'] = $this->data->post('type') == 'get' ? 1 : 2;
        $data['type_data'] = 2;
        $data['send_mail'] = ($this->data->post('send_mail')) ? $this->data->post('send_mail') : 0;
        $data['success'] = $this->data->post('after');

        switch ($data['success'])
        {
            case '2':
                $data['success_data'] = $_SESSION['all_languages'] ? json_encode($_POST['lang']) :  $this->data->post('lang') ;
                break;

            case '3':
                $data['success_data'] = $this->data->post('afterValue');
                break;
            
            default:
                $data['success_data'] = '';
                break;
        }


        $data['send_sms'] = ($this->data->post('send_sms')) ? $this->data->post('send_sms') : 0;
        $data['sms_text'] = isset($_POST['sms_text']) ? $this->data->post('sms_text') : '';

        if($this->data->post('create'))
        {
            $fields = $this->db->getAllDataByFieldInArray('wl_fields', $formId, 'form');

            if($fields)
            {
                $fieldsName = array_map(function($f) { return '`'.$f->name.'` text'; }, $fields);
                $fieldsName = implode(', ', $fieldsName);

                $sql = "CREATE TABLE IF NOT EXISTS `{$data['table']}` (
                          `id` int(11) NOT NULL AUTO_INCREMENT,
                          {$fieldsName},
                          `date_add` int(11) NOT NULL,
                          `language` text,
                          PRIMARY KEY (`id`)
                        ) ENGINE=InnoDB CHARSET=utf8;";
                $this->db->executeQuery($sql);
            }
            else $_SESSION['notify']->errors = "Спочатку додайте поля";
        }

        if(isset($data['type'],$data['type_data'],$formId))
        {
            $this->db->updateRow('wl_forms', $data, $formId);

            $this->db->executeQuery("DELETE FROM `wl_mail_active` WHERE `form` = $formId");
            if(isset($_POST['templates']) && $data['send_mail'])
            {
                foreach ($_POST['templates'] as $template) 
                {
                    $this->db->insertRow('wl_mail_active', array('form' => $formId, 'template' => $template, 'active' => 1));
                }
            }
        }

        $this->redirect("admin/wl_forms/".$data['name']);
    }

    public function info()
    {
        if($name = $this->data->uri(3))
        {
            if($form = $this->db->getQuery("SELECT `id`, `table` FROM `wl_forms` WHERE `name` = '{$name}'"))
            {
                $formInfo = $this->db->getQuery("SELECT name, title FROM `wl_fields` WHERE `form` = '{$form->id}'", 'array');

                $tableInfo = $this->db->getQuery("SELECT * FROM `$form->table`", 'array');

                $this->load->admin_view('wl_forms/info_view', array('formInfo' => $formInfo, 'tableInfo' => $tableInfo));
            }
        }

        $this->load->page_404(false);
    }

    public function createMailTemplate()
    {
        $formId = $this->data->uri(3);

        if(isset($formId) && is_numeric($formId))
        {
            $data = array();
            $data['from'] = SITE_EMAIL;
            $data['to'] = SITE_EMAIL;
            $data['multilanguage'] = $_SESSION['language'] != false ? 1 : 0;
            $data['savetohistory'] = 0;

            $this->db->insertRow('wl_mail_templates', $data);
            $mailTemplateId = $this->db->getLastInsertedId();

            $this->db->insertRow('wl_mail_active', array('form' => $formId, 'template' => $mailTemplateId, 'active' => 1));
            $this->db->updateRow('wl_forms', array('send_mail' => 1), $formId);
            header('Location:'.SITE_URL.'admin/wl_mail_template/'.$mailTemplateId);
        }
        else 
        {
            $this->redirect();
        }
    }
}

?>
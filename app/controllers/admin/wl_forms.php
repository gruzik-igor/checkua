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
    	 if($_SESSION['user']->admin == 1){
            if($this->data->uri(2) != ''){
                $form = $this->data->uri(2);
                $form = $this->db->getAllDataById('wl_forms', $form, 'name');
                if($form){
                	$field_name = $this->data->uri(3);
                	if($field_name != ''){
                		$where['name'] = $field_name;
                		$where['form'] = $form->id;
                		$field_name = $this->db->getAllDataById('wl_fields', $where);
                		if($field_name)
                			$this->load->admin_view('wl_forms/edit_field_view', array('field_name' => $field_name, 'form' => $form));
                		else $this->load->page_404();
                	} else $this->load->admin_view('wl_forms/edit_view', array('form' => $form));
                } else $this->load->page_404();
            } else {
                $this->load->admin_view('wl_forms/list_view');
            }
        }
    }

    public function add()
    {
    	if($_SESSION['user']->admin == 1){
            $this->load->admin_view('wl_forms/add_view');
        }
    }

    public function add_save()
    {
    	if($_SESSION['user']->admin == 1){
	    	if(!empty($_POST)){
	    		if(!empty($_POST['name'])) $name = $_POST['name'];
	    		$captcha = ($_POST['captcha'] == 'yes' ) ? 1 : 0;
	    		$help = (!empty($_POST['help']))? $_POST['help'] : '';
	    		if(!empty($_POST['table'])) $table = $_POST['table'];
	    		if(!empty($_POST['type'])) $type = ($_POST['type'] == 'get') ? 1 : 2;
	    		if(!empty($_POST['type_data'])) $type_data = ($_POST['type_data'] == 'fields') ? 1 : 2;

	    		if(isset($name,$table,$type,$type_data)){
		    		$this->db->executeQuery("INSERT INTO  `hunky_pro`.`wl_forms` (`id` ,`name` ,`captcha` ,`help` ,`table` ,`type` ,`type_data`) VALUES (NULL ,  '$name',  $captcha,  '$help',  '$table',  $type,  $type_data)");
		    		header("Location: ".SITE_URL."admin/wl_forms/");
		    		exit();
	    		}
	    	}
	    }
    }

    public function add_field()
    {
    	if($_SESSION['user']->admin == 1){
	    	if(!empty($_POST)){
	    		$form = $_POST['form'];
	    		$user_type = $_POST['user_type'];

	    		$formById = $this->db->getAllDataByFieldInArray('wl_fields', $form, 'form'); 
	    		foreach ($formById as $names) {
	    			$namesById[] = $names->name;
	    		}
	    		if(!in_array($_POST['name'], $namesById) && !empty($_POST['name'])){  //Провірка на унікальне ім'я
	    			$name = $_POST['name'];
	    		}

	    		if(!empty($_POST['input_type'])) $input_type = $_POST['input_type'];
	    		$value = (!empty($_POST['value']))? $_POST['value'] : '';
	    		$required = ($_POST['required'] == '1')? 1 : 0;
	    		$can_change = ($_POST['can_change'] == '1')? 1 : 0;
	    		if(!empty($_POST['title'])) $title = $_POST['title'];
	    		
	    		if(isset($name, $input_type, $title)){
	    			$this->db->executeQuery("INSERT INTO `hunky_pro`.`wl_fields` (`id`, `form`, `user_type`, `name`, `input_type`, `required`, `can_change`, `title`, `value`) VALUES (NULL, $form, $user_type, '$name', $input_type, $required, $can_change, '$title', '$value')");
	    			header("Location: ".SITE_URL."admin/wl_forms/".$_POST['form_name']);
	    		}
	    	}
	    }
    }

    public function edit_field($value='')
  	{
    	if($_SESSION['user']->admin == 1){
	    	if(!empty($_POST)){
	    		$id = $_POST['id'];
	    		$form = $_POST['form'];
	    		$user_type = $_POST['user_type'];

	    		$formById = $this->db->getAllDataByFieldInArray('wl_fields', $form, 'form'); 
	    		foreach ($formById as $names) {
	    			$namesById[] = $names->name;
	    		}
	    		if(!in_array($_POST['name'], array_diff($namesById, array($_POST['field_name']))) && !empty($_POST['name'])){  //Провірка на унікальне ім'я
	    				$name = $_POST['name'];
	    		}

	    		if(!empty($_POST['input_type'])) $input_type = $_POST['input_type'];
	    		$value = (!empty($_POST['value']))? $_POST['value'] : '';
	    		$required = ($_POST['required'] == '1')? 1 : 0;
	    		$can_change = ($_POST['can_change'] == '1')? 1 : 0;
	    		if(!empty($_POST['title'])) $title = $_POST['title'];
	    		
	    		if(isset($name, $input_type, $title)){
	    			$this->db->executeQuery("UPDATE `hunky_pro`.`wl_fields` SET `form` = $form, `user_type` = $user_type, `name` = '$name', `input_type` = $input_type, `required` = $required, `can_change` = $can_change, `title` = '$title', `value` = '$value' WHERE `wl_fields`.`id` = $id");
	    			header("Location: ".SITE_URL."admin/wl_forms/");
	    		}
	    	}
	    }
    }
}
 
?>
<?php

/*

 	Service "FAQ 1.0.2"
	for WhiteLion 1.0

*/

class faq extends Controller {
				
    function _remap($method, $data = array())
    {
    	$_SESSION['alias']->breadcrumb = array($_SESSION['alias']->name => '');
        if (method_exists($this, $method)) {
        	if(empty($data)) $data = null;
            return $this->$method($data);
        } else {
        	$this->index($method);
        } 
    }

    public function index($uri)
    {
		$this->load->smodel('faq_model');
		if($_SESSION['option']->useGroups)
		{
			if($this->data->uri(3)){
				$this->edit_question($this->data->uri(3));
			} 
			elseif($this->data->uri(2))
			{
				$group = $this->faq_model->getGroupByAlias($this->data->uri(2));
				if($group){
					$group->alias_name = $_SESSION['alias']->name;
					$_SESSION['alias']->breadcrumb = array($_SESSION['alias']->name => 'admin/'.$_SESSION['alias']->alias, $group->name => '');
					$_SESSION['alias']->name .= '. Питання групи ' . $group->name;
					$faqs = $this->faq_model->getQuestions($group->id, false);
					$this->load->admin_view('faq/list_view', array('faqs' => $faqs, 'group' => $group));
				} else {
					$this->edit_question($this->data->uri(2));
				}
			}
			else
			{
				$groups = $this->faq_model->getGroups(false);
				if($groups){
					$this->load->admin_view('index_view', array('groups' => $groups));
					exit();
				}
			}
		} elseif($this->data->uri(2)) {
			$this->edit_question($this->data->uri(2));
		}

		$faqs = $this->faq_model->getQuestions(0, false);
		$this->load->admin_view('faq/list_view', array('faqs' => $faqs));
    }

	public function all()
	{
		$_SESSION['alias']->breadcrumb = array($_SESSION['alias']->name => 'admin/'.$_SESSION['alias']->alias, 'Всі питання' => '');
		$_SESSION['alias']->name .= '. Всі питання';
		$this->load->smodel('faq_model');
		$faqs = $this->faq_model->getQuestions(0, false);
		$this->load->admin_view('faq/list_view', array('faqs' => $faqs));
	}
	
	public function add(){
		$_SESSION['alias']->breadcrumb = array($_SESSION['alias']->name => 'admin/'.$_SESSION['alias']->alias, 'Додати питання' => '');
		$_SESSION['alias']->name .= '. Додати питання';
		$this->load->admin_view('faq/add_view');
	}
	
	private function edit_question($alias)
	{
		$this->load->smodel('question_model');
		$question = $this->question_model->getByAlias($alias);
		if($question){
			$_SESSION['alias']->breadcrumb = array($_SESSION['alias']->name => 'admin/'.$_SESSION['alias']->alias, 'Редагувати питання #'.$question->id => '');
			$_SESSION['alias']->name = 'Редагувати питання #'.$question->id.' "'.$question->question.'"';
			$this->load->admin_view('faq/edit_view', array('question' => $question));
		}
		$this->load->page_404();
	}
	
	public function save_question()
	{
		if(isset($_POST['id']) && is_numeric($_POST['id'])){
			$this->load->smodel('question_model');
			if($_POST['id'] == 0){
				$alias = $this->question_model->add();
				$this->redirect("admin/{$_SESSION['alias']->alias}/{$alias}");
			} else {
				$_SESSION['notify'] = new stdClass();
				$alias = $this->question_model->save($_POST['id']);
				if($alias){
					$_SESSION['notify']->success = 'Дані успішно оновлено!';
					$this->redirect("admin/{$_SESSION['alias']->alias}/{$alias}#tab-main");
				} else {
					$_SESSION['notify']->errors = $this->groups_model->errors;
					$this->redirect('#tab-main');
				}
			}
		}
	}
	
	public function delete_question()
	{
		if(isset($_POST['id']) && is_numeric($_POST['id']) && $_POST['id'] > 0){
			$_SESSION['notify'] = new stdClass();
			$this->load->smodel('question_model');
			$alias = $this->question_model->delete($_POST['id']);
			if($alias){
				$_SESSION['notify']->success = 'Групу успішно видалено!';
				$this->redirect("admin/{$_SESSION['alias']->alias}{$alias}");
			} else {
				$_SESSION['notify']->errors = $this->question_model->errors;
				$this->redirect();
			}
		}
		$this->load->page_404();
	}
	
	public function change_question_position()
	{
		if(isset($_POST['id']) && is_numeric($_POST['id']) && is_numeric($_POST['position'])){
			$this->load->model('wl_position_model');
			$this->load->smodel('question_model');
			$this->wl_position_model->table = $this->question_model->table();
			if($_SESSION['option']->useGroups && $this->data->post('group') > 0){
				$this->wl_position_model->where = "`group` = '".$this->data->post('group')."'";
			}
			if($this->wl_position_model->change($_POST['id'], $_POST['position'])){
				$this->redirect();
			}
		}
		$this->load->page_404();
	}

	public function groups()
	{
		if($this->data->uri(3)){
			$this->edit_group($this->data->uri(3));
		} else {
			$this->load->smodel('faq_model');
			$groups = $this->faq_model->getGroups(false);
			$_SESSION['alias']->name = 'Групи '.$_SESSION['alias']->name;
			$_SESSION['alias']->breadcrumb = array('Групи' => '');
			$this->load->admin_view('groups/list_view', array('groups' => $groups));
		}
	}

	public function add_group()
	{
		$_SESSION['alias']->name = 'Додати групу';
		$_SESSION['alias']->breadcrumb = array('Групи' => 'admin/'.$_SESSION['alias']->alias.'/groups', 'Додати' => '');
		$this->load->admin_view('groups/add_view');
	}

	private function edit_group($alias)
	{
		if($alias != ''){
			$this->load->smodel('groups_model');
			$group = $this->groups_model->getByAlias($alias);
			if($group){
				$_SESSION['alias']->name = 'Редагувати групу "'.$group->name.'"';
				$_SESSION['alias']->breadcrumb = array('Групи' => 'admin/'.$_SESSION['alias']->alias.'/groups', 'Редагувати групу' => '');
				$this->load->admin_view('groups/edit_view', array('group' => $group));
			}
		}
		$this->load->page_404();
	}

	public function save_group()
	{
		if(isset($_POST['id']) && is_numeric($_POST['id'])) {
			$this->load->smodel('groups_model');
			if($_POST['id'] == 0) {
				$alias = $this->groups_model->add();
				$this->redirect("admin/{$_SESSION['alias']->alias}/groups/{$alias}");
			} else {
				$_SESSION['notify'] = new stdClass();
				if($this->groups_model->save($_POST['id'])){
					$_SESSION['notify']->success = 'Дані успішно оновлено!';
					$this->redirect("admin/{$_SESSION['alias']->alias}/groups/{$this->data->post('alias')}#tab-main");
				} else {
					$_SESSION['notify']->errors = $this->groups_model->errors;
					$this->redirect('#tab-main');
				}
			}
		}
	}

	public function delete_group()
	{
		if(isset($_POST['id']) && is_numeric($_POST['id']) && $_POST['id'] > 0){
			$_SESSION['notify'] = new stdClass();
			$this->load->smodel('groups_model');
			if($this->groups_model->delete($_POST['id'])){
				$_SESSION['notify']->success = 'Групу успішно видалено!';
				$this->redirect("admin/{$_SESSION['alias']->alias}/groups");
			} else {
				$_SESSION['notify']->errors = $this->groups_model->errors;
				$this->redirect();
			}
		}
		$this->load->page_404();
	}

	public function change_group_position()
	{
		if(isset($_POST['id']) && is_numeric($_POST['id']) && is_numeric($_POST['position'])){
			$this->load->smodel('groups_model');
			$this->load->model('wl_position_model');
			$this->wl_position_model->table = $this->groups_model->table();
			if($this->wl_position_model->change($_POST['id'], $_POST['position'])){
				$this->redirect();
			}
		}
		$this->load->page_404();
	}

    public function __get_Search($content)
    {
    	$this->load->smodel('search_model');
    	return $this->search_model->getByContent($content, true);
    }
	
}

?>
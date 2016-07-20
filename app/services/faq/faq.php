<?php

/*

 	Service "FAQ 1.0.2"
	for WhiteLion 1.0

*/

class faq extends Controller {
				
    function _remap($method, $data = array())
    {
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
		$faqs = $this->faq_model->getQuestions();
		$groups = NULL;
		if($_SESSION['option']->useGroups)
		{
			$groups = $this->faq_model->getGroups();
		}
		$this->load->page_view('index_view', array('faqs' => $faqs, 'groups' => $groups));
    }

    public function __get_Search($content)
    {
    	$this->load->smodel('faq_search_model');
    	return $this->faq_search_model->getByContent($content);
    }

	public function __get_Questions($data = array())
	{
		$group = 0;
		if(isset($data['group']) && is_numeric($data['group'])) $group = $data['group'];
		if(isset($data['limit']) && is_numeric($data['limit'])) $_SESSION['option']->PerPage = $data['limit'];

		$this->load->smodel('faq_model');
		return $this->faq_model->getQuestions($group);
	}

	public function __get_Groups()
	{
		$this->load->smodel('faq_model');
		return $this->faq_model->getGroups();
	}
	
}

?>
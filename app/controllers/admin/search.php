<?php 

class Search extends Controller {

	public function index()
	{
		$data = null;
		$this->load->library('validator');
		$this->validator->setRules($this->text('search text'), $this->data->get('by'), 'required|3..100');
		if($this->validator->run())
		{
			$this->load->model('wl_search_model');
			$language_all = false;
			if($this->data->get('language-all')) $language_all = true;
			$search_data = $this->wl_search_model->get($this->data->get('by'), $language_all);
			if($search_data)
			{
				$services = array();
				$contents = array();
				$per_page = 0;

				foreach ($search_data as $search) {
					$go = false;
					if(!in_array($search->content, $contents) || (isset($_SESSION['language']) && $serch->language == $_SESSION['language']))
					{
						if(isset($_SESSION['option']->paginator_per_page))
						{
							if($_SESSION['option']->paginator_per_page > 0 && $_SESSION['option']->paginator_per_page >= $per_page)
							{
								$go = true;
							}
						} else {
							$go = true;
						}
						if(in_array($search->content, $contents)) $per_page--;
					}
					
					if($go){
						$per_page++;
						if($search->service > 0){
							if(isset($services[$search->service])){

							}
						}
					}
					
				}
			}
		}
		$this->load->page_view('search_view', array('data' => $data, 'errors' => $this->validator->getErrors));
	}

}

?>
<?php 

class Search extends Controller {

	public function index()
	{
		$data = array();
		$current = 0;
		$this->load->library('validator');
		$this->validator->setRules($this->text('search text'), $this->data->get('by'), 'required|3..100');
		if($this->validator->run())
		{
			$this->load->model('wl_search_model');
			$search_data = $this->wl_search_model->get($this->data->get('by'));
			if($search_data)
			{
				$per_page = 0;
				$start = 1;
				if($this->data->get('page') > 1 && isset($_SESSION['option']->paginator_per_page)) {
					$start = ($this->data->get('page') - 1) * $_SESSION['option']->paginator_per_page + 1;
				}

				foreach ($search_data as $search) {
					$result = $this->load->function_in_alias($search->alias, '__get_Search', $search->content);
					if($result){
						$current++;
						$result->name = $search->name;
						$result->list = $search->list;
						$result->text = $search->text;
						$result->image = false;
						if(isset($_SESSION['option']->paginator_per_page) && $_SESSION['option']->paginator_per_page > 0)
						{
							if($current >= $start && $current < ($start + $_SESSION['option']->paginator_per_page))
							{
								if(isset($result->folder) && $result->folder)
									$result->image = $this->wl_search_model->getImage($search->alias_id, $search->content, $result->folder, 'm_');
								array_push($data, $result);
							}
						} else {
							if($result->folder)
								$result->image = $this->wl_search_model->getImage($search->alias_id, $search->content, $result->folder, 'm_');
							array_push($data, $result);
						}
					}
				}
			}
		} else {
			@$_SESSION['notify']->errors = $this->validator->getErrors();
		}
		@$_SESSION['option']->paginator_total = $current;

		if(count($data) == 1)
			header("Location:".SITE_URL.$data[0]->link);

		$_SESSION['alias']->breadcrumbs = '';

		$this->load->page_view('search_view', array('data' => $data));
	}

}

?>
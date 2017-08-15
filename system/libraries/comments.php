<?php if (!defined('SYS_PATH')) exit('Access denied');

class comments extends Controller
{
	
	public function show($content = 0, $alias = 0)
	{
		$this->load->model("wl_comments_model");
		$comments = $this->wl_comments_model->get($content, $alias);

		include APP_PATH."views/@wl_comments/index_view.php";
	}

}

 ?>
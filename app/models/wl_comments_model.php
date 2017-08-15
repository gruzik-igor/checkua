<?php

class wl_comments_model {

	function get($content = -1, $alias = -1)
	{
		$where = array('status' => '!3');
		if($alias == -1)
			$where['alias'] = $_SESSION['alias']->id;
		elseif($alias > 0 && is_numeric($alias))
			$where['alias'] = $alias;

		if(isset($where['alias']))
		{
			if($content == -1)
				$where['content'] = $_SESSION['alias']->content;
			elseif($content > 0 && is_numeric($content))
				$where['content'] = $content;
		}

		return $this->db->getAllDataByFieldInArray('wl_comments', $where);
	}

}

?>

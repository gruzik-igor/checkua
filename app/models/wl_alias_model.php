<?php

class wl_alias_model
{
	public $service = false;

    public function alias($link)
    {
		$link = $this->db->sanitizeString($link);
		$_SESSION['alias'] = new stdClass();
		$_SESSION['option'] = new stdClass();
		$_SESSION['service'] = new stdClass();

		$_SESSION['alias']->alias = $link;
		$_SESSION['alias']->id = 0;
		$_SESSION['alias']->content = NULL;
		$_SESSION['alias']->code = 200;
		$_SESSION['alias']->service = false;
		$_SESSION['alias']->image = false;
		$_SESSION['alias']->js_plugins = array();
		$_SESSION['alias']->js_load = array();
		$_SESSION['alias']->js_init = array();

		if($options = $this->db->getAllDataByFieldInArray('wl_options', array('service' => 0, 'alias' => 0)))
			foreach($options as $opt) {
				$key = $opt->name;
				$_SESSION['option']->$key = $opt->value;
			}

		if($alias = $this->db->getAllDataById('wl_aliases', $link, 'alias'))
		{
			unset($_SESSION['alias-cache'][$alias->id]);

			$_SESSION['alias']->id = $alias->id;
			$_SESSION['alias']->table = $alias->table;
			if($alias->service > 0)
			{
				$this->db->executeQuery("SELECT `name`, `table` FROM `wl_services` WHERE `id` = {$alias->service} AND `active` = 1");
				if($this->db->numRows() == 1)
				{
					$service = $this->db->getRows();
					$_SESSION['alias']->service = $service->name;
					$_SESSION['service']->name = $service->name;
					$_SESSION['service']->table = $service->table;
				}
			}
			
			if($options = $this->db->getAllDataByFieldInArray('wl_options', array('service' => $alias->service, 'alias' => 0)))
				foreach($options as $opt) {
					$key = $opt->name;
					$_SESSION['option']->$key = $opt->value;
				}
			if($options = $this->db->getAllDataByFieldInArray('wl_options', array('service' => $alias->service, 'alias' => $alias->id)))
				foreach($options as $opt) {
					$key = $opt->name;
					$_SESSION['option']->$key = $opt->value;
				}

			$where = array();
			$where['alias'] = $alias->id;
			$where['content'] = 0;
			if($_SESSION['language']) $where['language'] = $_SESSION['language'];

			if($data = $this->db->getAllDataById('wl_ntkd', $where))
			{
				$_SESSION['alias']->name = $data->name;
				$_SESSION['alias']->title = $data->title;
				$_SESSION['alias']->description = $data->description;
				$_SESSION['alias']->keywords = $data->keywords;
				$_SESSION['alias']->text = $data->text;
				$_SESSION['alias']->list = $data->list;
			}
			else
			{
				$where['alias'] = 1;
				if($data = $this->db->getAllDataById('wl_ntkd', $where))
				{
					$_SESSION['alias']->name = $data->name;
					$_SESSION['alias']->title = $data->title;
					$_SESSION['alias']->description = $data->description;
					$_SESSION['alias']->keywords = $data->keywords;
					$_SESSION['alias']->text = $data->text;
					$_SESSION['alias']->list = $data->list;
				}
			}
		}
		else
		{
			$where = array();
			$where['alias'] = 1;
			if($_SESSION['language']) $where['language'] = $_SESSION['language'];

			if($data = $this->db->getAllDataById('wl_ntkd', $where))
			{
				$_SESSION['alias']->name = $data->name;
				$_SESSION['alias']->title = $data->title;
				$_SESSION['alias']->description = $data->description;
				$_SESSION['alias']->keywords = $data->keywords;
				$_SESSION['alias']->text = $data->text;
				$_SESSION['alias']->list = $data->list;
			}
			else
			{
				if($data = $this->db->getAllDataById('wl_ntkd', 1, 'alias'))
				{
					$_SESSION['alias']->name = $data->name;
					$_SESSION['alias']->title = $data->title;
					$_SESSION['alias']->description = $data->description;
					$_SESSION['alias']->keywords = $data->keywords;
					$_SESSION['alias']->text = $data->text;
					$_SESSION['alias']->list = $data->list;
				}
			}
		}

		if($_SESSION['alias']->title == '')
			$_SESSION['alias']->title = $_SESSION['alias']->name;
		if($_SESSION['alias']->description == '')
			$_SESSION['alias']->description = $this->getShortText($_SESSION['alias']->list);

		return true;
    }

    public function admin_options()
    {
		$_SESSION['admin_options'] = array();
		$admin_options = $this->db->getAllDataByFieldInArray('wl_options', array('alias' => -$_SESSION['alias']->id));
		if($admin_options)
			foreach ($admin_options as $ao) {
				if($ao->name != 'sub-menu')
					$_SESSION['admin_options'][$ao->name] = $ao->value;
			}
		return true;
    }

    private function getShortText($text, $len = 155)
    {
        $text = strip_tags(html_entity_decode($text));
        if(mb_strlen($text, 'UTF-8') > $len)
        {
            $pos = mb_strpos($text, ' ', $len, 'UTF-8');
			if($pos){
				return mb_substr($text, 0, $pos, 'UTF-8');
			} else {
				$pos = mb_strpos($text, ' ', $len - 10, 'UTF-8');
				if($pos){
					return mb_substr($text, 0, $pos, 'UTF-8');
				}
			}
        }
        return $text;
    }

}

?>

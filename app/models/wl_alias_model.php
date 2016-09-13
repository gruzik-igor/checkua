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
		$_SESSION['alias']->name = ($_GET['request']) ? $_GET['request'] : SITE_NAME;
		$_SESSION['alias']->title = $_SESSION['alias']->description = $_SESSION['alias']->keywords = $_SESSION['alias']->text = $_SESSION['alias']->list = '';
		$_SESSION['alias']->audios = $_SESSION['alias']->image = $_SESSION['alias']->images = $_SESSION['alias']->videos = false;
		$_SESSION['alias']->js_plugins = $_SESSION['alias']->js_load = $_SESSION['alias']->js_init = array();

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
				if($service = $this->db->getQuery("SELECT `name`, `table` FROM `wl_services` WHERE `id` = {$alias->service}"))
				{
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
		}
		return true;
    }

    public function setContent($content = 0, $code = 200)
    {
		if(!is_numeric($content))
			return false;
		$_SESSION['alias']->content = $content;
		$_SESSION['alias']->code = $code;

		$where = array();
		$where['alias'] = $_SESSION['alias']->id;
		$where['content'] = $content;

		$this->db->select('wl_images', '*', $where);
		$this->db->join('wl_users', 'name as user_name', '#author');
		$this->db->order('main ASC');
		$_SESSION['alias']->images = $this->db->get('array');
		if(!empty($_SESSION['alias']->images))
		{
			$sizes = $this->db->getAliasImageSizes();
			foreach ($_SESSION['alias']->images as $photo) {
				if($sizes)
					foreach ($sizes as $resize) {
						$resize_name = $resize->prefix.'_path';
						$photo->$resize_name = $_SESSION['option']->folder.'/'.$_SESSION['alias']->content.'/'.$resize->prefix.'_'.$photo->file_name;
					}
				$photo->path = $_SESSION['option']->folder.'/'.$_SESSION['alias']->content.'/'.$photo->file_name;
			}
			if(isset($_SESSION['alias']->images[0]->header_path))
				$_SESSION['alias']->image = $_SESSION['alias']->images[0]->header_path;
			else
				$_SESSION['alias']->image = $_SESSION['alias']->images[0]->path;
		}

		$this->db->select('wl_video', '*', $where);
		$this->db->join('wl_users', 'name as user_name', '#author');
		$_SESSION['alias']->videos = $this->db->get('array');

		$this->db->select('wl_audio', '*', $where);
		$this->db->join('wl_users', 'name as user_name', '#author');
		$this->db->order('position ASC');
		$_SESSION['alias']->audios = $this->db->get('array');

		if($_SESSION['language'])
			$where['language'] = $_SESSION['language'];
		if($data = $this->db->getAllDataById('wl_ntkd', $where))
		{    	
			$_SESSION['alias']->name = $data->name;
			$_SESSION['alias']->title = $data->title;
			$_SESSION['alias']->description = $data->description;
			$_SESSION['alias']->keywords = $data->keywords;
			$_SESSION['alias']->text = $data->text;
			$_SESSION['alias']->list = $data->list;

			if($_SESSION['alias']->title == '')
				$_SESSION['alias']->title = $_SESSION['alias']->name;
			if($_SESSION['alias']->description == '')
				$_SESSION['alias']->description = $this->getShortText($_SESSION['alias']->list);
		}

		return null;
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

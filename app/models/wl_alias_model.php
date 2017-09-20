<?php

class wl_alias_model
{
	public $service = false;

    public function init($alias, $link = '')
    {
		$alias = $this->db->sanitizeString($alias);
		$_SESSION['alias'] = new stdClass();
		$_SESSION['option'] = new stdClass();
		$_SESSION['service'] = new stdClass();

		$_SESSION['alias']->alias = $alias;
		$_SESSION['alias']->link = $this->db->sanitizeString($link);
		$_SESSION['alias']->id = 0;
		$_SESSION['alias']->content = NULL;
		$_SESSION['alias']->code = 200;
		$_SESSION['alias']->service = false;
		$_SESSION['alias']->name = $_SESSION['alias']->title = $_SESSION['alias']->breadcrumb_name = $alias;
		$_SESSION['alias']->description = $_SESSION['alias']->keywords = $_SESSION['alias']->text = $_SESSION['alias']->list = $_SESSION['alias']->meta = '';
		$_SESSION['alias']->audios = $_SESSION['alias']->image = $_SESSION['alias']->images = $_SESSION['alias']->videos = false;
		$_SESSION['alias']->js_plugins = $_SESSION['alias']->js_load = $_SESSION['alias']->js_init = $_SESSION['alias']->breadcrumbs = array();

		if($options = $this->db->getAllDataByFieldInArray('wl_options', array('service' => 0, 'alias' => 0)))
			foreach($options as $opt) {
				$key = $opt->name;
				$_SESSION['option']->$key = $opt->value;
			}

		if($alias = $this->db->getAllDataById('wl_aliases', $alias, 'alias'))
		{
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

		$this->db->select('wl_images as i', '*', $where);
		if($_SESSION['language'])
			$this->db->join('wl_media_text', 'text as title', array('type' => 'photo', 'content' => '#i.id', 'language' => $_SESSION['language']));
		$this->db->join('wl_users', 'name as user_name', '#author');
		$this->db->order('position ASC');
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
			$_SESSION['alias']->text = htmlspecialchars_decode($data->text);
			$_SESSION['alias']->list = $data->list;
			$_SESSION['alias']->meta = $data->meta;

			if($_SESSION['alias']->images)
				foreach ($_SESSION['alias']->images as $photo) {
					if($photo->title == '')
						$photo->title = $data->name;
				}
		}
		if(empty($_SESSION['alias']->breadcrumbs))
		{
			if($content == 0)
			{
				$_SESSION['alias']->breadcrumb_name = $_SESSION['alias']->name;
				$_SESSION['alias']->breadcrumbs = array($_SESSION['alias']->name => '');
			}
			else
			{
				$where['content'] = 0;
				if($data = $this->db->getAllDataById('wl_ntkd', $where))
				{
					$_SESSION['alias']->breadcrumb_name = $data->name;
					$_SESSION['alias']->breadcrumbs = array($data->name => $_SESSION['alias']->alias);
				}
			}
		}

		return true;
	}

	public function getVideosFromText()
	{
		$video = false;
		if(preg_match_all("#\{video-[0-9]+\}#is", $_SESSION['alias']->text, $video) > 0)
		{
			$videos = array();
			$videos_id = array();
			foreach ($video[0] as $v) {
				$id = substr($v, 7);
				$id = substr($id, 0, -1);
				$videos_id[$id] = $v;
			}
			foreach ($videos_id as $id => $text) {
				$video = $this->db->getAllDataById('wl_video', $id);
				if($video) {
					$video->replace_text = $text;
					$videos[] = $video;
				}
			}
			return $videos;
		}
		return false;
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

    public function setContentRobot($data = array())
    {
    	$ntkd = array();
    	$keys = array('title', 'description', 'keywords', 'text', 'list', 'meta');
    	$where = array('alias' => 0, 'content' => 0);
    	if($_SESSION['language'])
    		$where['language'] = $_SESSION['language'];
    	if($all = $this->db->getAllDataById('wl_ntkd_robot', $where))
    	{
    		foreach ($all as $key => $value) {
    			if(in_array($key, $keys) && $value != '')
    				$ntkd[$key] = htmlspecialchars_decode($value);
    		}
    	}
    	if($_SESSION['alias']->id > 0)
    	{
    		$where['alias'] = $_SESSION['alias']->id;
    		if($_SESSION['alias']->content > 0)
    			$where['content'] = 1;
    		else
    			$where['content'] = -1;
    		if($all = $this->db->getAllDataById('wl_ntkd_robot', $where))
	    	{
	    		foreach ($all as $key => $value) {
	    			if(in_array($key, $keys) && $value != '')
	    				$ntkd[$key] = htmlspecialchars_decode($value);
	    		}
	    	}
    	}
    	if(!empty($ntkd))
    	{
	    	$keys = array();
	    	if(!empty($data))
	    		foreach ($data as $key => $value) {
	    			$name = '{';
	    			if(is_object($value))
	    			{
	    				$name .= $key.'.';
	    				foreach ($value as $keyO => $valueO) {
	    					if(!is_object($valueO) && !is_array($valueO))
	    						$keys[$name.$keyO.'}'] = $valueO;
	    				}
	    			}
	    		}
	    	$keys['{name}'] = $_SESSION['alias']->name;
	    	$keys['{SITE_URL}'] = SITE_URL;
	    	$keys['{IMG_PATH}'] = IMG_PATH;
	    	foreach ($ntkd as $key => $value) {
	    		if($_SESSION['alias']->$key == '')
	    		{
	    			foreach ($keys as $keyR => $valueR) {
	    				$value = str_replace($keyR, $valueR, $value);
	    			}
	    			$_SESSION['alias']->$key = $value;
	    		}
	    	}
	    }
	    if($_SESSION['alias']->title == '')
			$_SESSION['alias']->title = $_SESSION['alias']->name;
		if($_SESSION['alias']->description == '')
			$_SESSION['alias']->description = $this->getShortText($_SESSION['alias']->list);
    }

    private function getShortText($text, $len = 155)
    {
        $text = strip_tags(html_entity_decode($text));
        if(mb_strlen($text, 'UTF-8') > $len)
        {
            $pos = mb_strpos($text, ' ', $len, 'UTF-8');
			if($pos)
				return mb_substr($text, 0, $pos, 'UTF-8');
			else
			{
				$pos = mb_strpos($text, ' ', $len - 10, 'UTF-8');
				if($pos)
					return mb_substr($text, 0, $pos, 'UTF-8');
			}
        }
        return $text;
    }

}

?>

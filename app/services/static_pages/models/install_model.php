<?php

class install{

	public $service = null;
	
	public $name = "static_pages";
	public $title = "Статичні сторінки";
	public $description = "";
	public $table_service = "";
	public $table_alias = "";
	public $version = "1.0";

	public $options = array();

	public $seo_name = "Статична сторінка";
	public $seo_title = "Статична сторінка";
	public $seo_description = "";
	public $seo_keywords = "";

	function alias($alias = 0, $table = '')
	{
		if($alias == 0) return false;

		if(isset($this->options['folder']) && $this->options['folder'] != ''){
			$path = IMG_PATH.$this->options['folder'];
			if(strlen(IMG_PATH) > strlen(SITE_URL)) $path = substr($path, strlen(SITE_URL));
			if(!is_dir($path)) mkdir($path, 0777);
		}

		return true;
	}

	public function setOption($option, $value, $table = '')
	{
		return true;
	}

	function install(){
		return true;
	}

	public function uninstall($alias = 0)
	{
		if(isset($_POST['content']) && $_POST['content'] == 1){
			if(isset($this->options['folder']) && $this->options['folder'] != ''){
				$path = IMG_PATH.$this->options['folder'];
				if(strlen(IMG_PATH) > strlen(SITE_URL)) $path = substr($path, strlen(SITE_URL));
				if(is_dir($path)) $this->removeDirectory($path);
			}
		}
		return true;
	}

	private function removeDirectory($dir) {
	    if ($objs = glob($dir."/*")) {
	       foreach($objs as $obj) {
	         is_dir($obj) ? $this->removeDirectory($obj) : unlink($obj);
	       }
	    }
	    rmdir($dir);
	}
	
}

?>
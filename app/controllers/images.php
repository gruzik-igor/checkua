<?php

class images extends Controller {

    function _remap($method, $data = array())
    {
        if (method_exists($this, $method)) {
            if(empty($data)) $data = null;
            return $this->$method($data);
        } else {
            $this->index($method);
        }
    }

    public function index()
    {
    	if(count($this->data->url()) == 4)
    	{
    		if($alias = $this->db->getAllDataById('wl_aliases', $this->data->uri(1), 'alias'))
    		{
    			if(is_numeric($this->data->uri(2)))
    			{
    				$name = explode('_', $this->data->uri(3));
    				if(count($name) >= 2)
    				{
    					if($sizes = $this->db->getAliasImageSizes($alias->id))
    					{
    						foreach ($sizes as $resize) {
    							if($resize->prefix != '' && $resize->prefix == $name[0])
    							{
    								$name = substr($this->data->uri(3), strlen($resize->prefix) + 1);
    								$path = IMG_PATH.$alias->alias.'/'.$this->data->uri(2).'/'.$name;
    								$path = substr($path, strlen(SITE_URL));
    								$this->load->library('image');
    								if($this->image->loadImage($path))
    								{
    									if(in_array($resize->type, array(1, 11, 12)))
				                            $this->image->resize($resize->width, $resize->height, $resize->quality, $resize->type);
				                        if(in_array($resize->type, array(2, 21, 22)))
				                            $this->image->preview($resize->width, $resize->height, $resize->quality, $resize->type);
				                        $this->image->save($resize->prefix);

				                        header("Content-type: image/".$this->image->getExtension());
				                        $path = IMG_PATH.$alias->alias.'/'.$this->data->uri(2).'/'.$this->data->uri(3);
    									$path = substr($path, strlen(SITE_URL));
				                        readfile($path);
				                        exit();
    								}
    							}
    						}
    					}
    				}
    			}
    		}
    	}
    	$this->load->page_404();
    }

}

?>
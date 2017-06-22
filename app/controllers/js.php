<?php

class js extends Controller {

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
    	if(count($this->data->url()) > 2)
    	{
            $this->db->select('wl_aliases as a', 'service', $this->data->uri(1), 'alias')
                    ->join('wl_services', 'name', '#a.service');
            if($alias = $this->db->get('single'))
                if($alias->service > 0)
        		{
                    $path = APP_PATH.'services'.DIRSEP.$alias->name.DIRSEP.'js'.DIRSEP;
                    $url = $this->data->url();
                    array_shift($url);
                    array_shift($url);
                    $path .= implode(DIRSEP, $url);
                    if(file_exists($path))
                    {
                        header("Content-Type: application/javascript");
                        readfile($path);
                        exit();
                    }
        		}
    	}
    	$this->load->page_404();
    }

}

?>
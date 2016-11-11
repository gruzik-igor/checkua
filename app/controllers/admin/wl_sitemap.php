<?php

class wl_sitemap extends Controller {
				
    public function _remap($method)
    {
        $_SESSION['alias']->name = 'Карта сайту';
        $_SESSION['alias']->breadcrumb = array('Site Map' => '');
        if (method_exists($this, $method) && $method != 'library' && $method != 'db')
            $this->$method();
        else
            $this->index($method);
    }

    public function index()
    {

    }

    public function generate()
    {
        $this->load->model('wl_cache_model');
        if($sitemap = $this->wl_cache_model->SiteMap(true))
        {
            $this->load->library('SitemapGenerator');
            echo('<pre>');
            print_r($sitemap);
        }
    }

}

?>
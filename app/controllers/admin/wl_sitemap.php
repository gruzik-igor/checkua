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
            foreach ($sitemap as $url) {
                if($url->link == 'main') $url->link = '';
                $this->sitemapgenerator->addUrl(SITE_URL.$url->link, date('c', $url->time), $url->changefreq, $url->priority/10);
            }
            try {
                // create sitemap
                $this->sitemapgenerator->createSitemap();
                // write sitemap as file
                $this->sitemapgenerator->writeSitemap();
                // update robots.txt file
                $this->sitemapgenerator->updateRobots();
                // submit sitemaps to search engines
                // $result = $sitemap->submitSitemap("yahooAppId");
                // shows each search engine submitting status
                // echo "<pre>";
                // print_r($result);
                // echo "</pre>";
                
            }
            catch (Exception $exc) {
                echo $exc->getTraceAsString();
            }
        }
    }

}

?>
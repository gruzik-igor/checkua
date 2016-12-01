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
        $id = $this->data->uri(2);
        if(is_numeric($id))
        {
            if($sitemap = $this->db->getAllDataById('wl_sitemap', $id))
            {
                $_SESSION['alias']->name = 'SiteMap '.$sitemap->link;
                $_SESSION['alias']->breadcrumb = array('SiteMap' => 'admin/wl_sitemap', $sitemap->link => '');
                $sitemap->name = '';
                $where = array('alias' => $sitemap->alias, 'content' => $sitemap->content);
                if($_SESSION['language'])
                    $where['language'] = $_SESSION['language'];

                if($sitemap->alias > 0)
                {
                    $this->db->select('wl_ntkd', 'name', $where);
                    if($ntkd = $this->db->get())
                    {
                        $_SESSION['alias']->breadcrumb = array('SiteMap' => 'admin/wl_sitemap', $ntkd->name => '');
                        $sitemap->name = $ntkd->name;
                    }
                }

                $this->db->select('wl_statistic_pages as s', '`day`, `unique`, `views`', $where);
                $this->db->order('id DESC');
                $start = 0;
                $_SESSION['option']->paginator_per_page = 30;
                if(isset($_GET['page']) && is_numeric($_GET['page']) && $_GET['page'] > 1)
                    $start = ($_GET['page'] - 1) * $_SESSION['option']->paginator_per_page;
                $this->db->limit($start, $_SESSION['option']->paginator_per_page);
                $statistic = $this->db->get('array');

                $this->load->admin_view('wl_sitemap/edit_view', array('sitemap' => $sitemap, 'wl_statistic' => $statistic));
            }
            else
                $this->load->page_404();
        }
        elseif($id == '')
        {
            $start = 0;
            $_SESSION['option']->paginator_per_page = 50;
            $this->db->select('wl_sitemap', 'id, link, alias, language, code, time, changefreq, priority');
            if(isset($_GET['page']) && is_numeric($_GET['page']) && $_GET['page'] > 1)
                $start = ($_GET['page'] - 1) * $_SESSION['option']->paginator_per_page;
            $this->db->limit($start, $_SESSION['option']->paginator_per_page);
            $sitemap = $this->db->get('array');
            $this->load->admin_view('wl_sitemap/index_view', array('sitemap' => $sitemap));
        }
        else
            $this->load->page_404();
        
        $_SESSION['alias']->id = 0;
        $_SESSION['alias']->alias = 'admin';
        $_SESSION['alias']->table = '';
        $_SESSION['alias']->service = '';
    }

    public function save()
    {
        if(isset($_POST['id']) && $_POST['id'] > 0)
        {
            $data = array('time' => time(), 'data' => NULL);
            $data['code'] = $this->data->post('code');
            switch ($data['code']) {
                case '200':
                case '201':
                    $data['priority'] = $this->data->post('priority') * 10;
                    $data['changefreq'] = $this->data->post('changefreq');
                    if(empty($_POST['active']) && $data['priority'] > 0)
                        $data['priority'] *= -1;
                    break;
                
                case '301':
                    $data['data'] = $this->data->post('redirect');
                    break;
            }
            if($_SESSION['language'] && isset($_POST['all_languages']) && $_POST['all_languages'] == 1)
            {
                $this->db->select('wl_sitemap', 'alias, content', $_POST['id']);
                $sitemap = $this->db->get();
                $this->db->select('wl_sitemap', 'id', array('alias' => $sitemap->alias, 'content' => $sitemap->content));
                $sitemaps = $this->db->get('array');
                foreach ($sitemaps as $map) {
                    $this->db->updateRow('wl_sitemap', $data, $map->id);
                }
                $_SESSION['notify'] = new stdClass();
                $_SESSION['notify']->success = 'Дані оновлено!';
            }
            elseif($this->db->updateRow('wl_sitemap', $data, $_POST['id']))
            {
                $_SESSION['notify'] = new stdClass();
                $_SESSION['notify']->success = 'Дані оновлено!';
            }
        }
        $this->redirect();
    }

    public function delete()
    {
        if(isset($_POST['id']) && $_POST['id'] > 0)
        {
            if($_POST['code_hidden'] == $_POST['code_open'])
            {
                $this->db->select('wl_sitemap', 'id, link, alias, content', $_POST['id']);
                if($sitemap = $this->db->get())
                {
                    if($sitemap->alias == 0)
                        $this->db->deleteRow('wl_statistic_pages', array('alias' => $sitemap->alias, 'content' => $sitemap->content));
                    if($_SESSION['language'] && isset($_POST['all_languages']) && $_POST['all_languages'] == 1)
                        $this->db->deleteRow('wl_sitemap', array('alias' => $sitemap->alias, 'content' => $sitemap->content));
                    else
                        $this->db->deleteRow('wl_sitemap', $sitemap->id);
                    $_SESSION['notify'] = new stdClass();
                    $_SESSION['notify']->success = 'Дані <strong>'.SITE_URL.$sitemap->link.'</strong> успішно видалено!';
                    $this->redirect('admin/wl_sitemap');
                }
            }
            else
            {
                $_SESSION['notify'] = new stdClass();
                $_SESSION['notify']->errors = 'Невірний код безпеки!';
            }
        }
        $this->redirect();
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
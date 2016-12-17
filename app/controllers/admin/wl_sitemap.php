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
                    $where['language'] = $sitemap->language;

                if($sitemap->alias > 0)
                {
                    $this->db->select('wl_ntkd', 'name', $where);
                    if($ntkd = $this->db->get())
                    {
                        $_SESSION['alias']->breadcrumb = array('SiteMap' => 'admin/wl_sitemap', $ntkd->name => '');
                        $sitemap->name = $ntkd->name;
                    }
                }
                else
                    $where['content'] = $sitemap->id;

                $this->db->select('wl_statistic_pages as s', '`day`, `unique`, `views`', $where);
                $this->db->order('id DESC');
                $start = 0;
                $_SESSION['option']->paginator_per_page = ($sitemap->code < 299) ? 30 : 10;
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
            
            if(count($_GET) == 1 || (count($_GET) == 2 && isset($_GET['page'])))
                $where = '';
            else
            {
                $where = array();
                if($this->data->get('alias') == 'yes')
                    $where['alias'] = '>0';
                if($this->data->get('alias') == 'no')
                    $where['alias'] = '0';
                if($code = $this->data->get('code'))
                    $where['code'] = $code;
            }
            $this->db->select('wl_sitemap', 'id, link, alias, language, code, time, changefreq, priority', $where);
            if(isset($_GET['sort']) && $_GET['sort'] == 'down')
                $this->db->order('id DESC');
            if(isset($_GET['page']) && is_numeric($_GET['page']) && $_GET['page'] > 1)
                $start = ($_GET['page'] - 1) * $_SESSION['option']->paginator_per_page;
            $this->db->limit($start, $_SESSION['option']->paginator_per_page);
            $sitemap = $this->db->get('array', false);
            $_SESSION['option']->paginator_total = $this->db->get('count');
            
            $this->load->admin_view('wl_sitemap/index_view', array('sitemap' => $sitemap));
        }
        else
            $this->load->page_404();
        
        $_SESSION['alias']->id = 0;
        $_SESSION['alias']->alias = 'admin';
        $_SESSION['alias']->table = '';
        $_SESSION['alias']->service = '';
    }

    public function add_redirect()
    {
        $this->load->admin_view('wl_sitemap/add_redirect_view');
    }

    public function save_add_redirect()
    {
        $_SESSION['notify'] = new stdClass();
        if(!empty($_POST['from']) && isset($_POST['to']))
        {
            $sitemap = $this->db->getAllDataById('wl_sitemap', $this->data->post('from'), 'link');
            if($sitemap)
            {
                $_SESSION['notify']->errors = 'Лінк за даною адресою <strong>'.SITE_URL.$sitemap->link.'</strong> вже існує <a href="'.SITE_URL.'admin/wl_sitemap/'.$sitemap->id.'" class="btn btn-success btn-xs">Редагувати</a>';
                $this->redirect();
            }
            else
            {
                $data = array();
                $data['link'] = $this->data->post('from');
                $data['data'] = $this->data->post('to');
                $data['code'] = 301;
                $data['time'] = time();
                $data['changefreq'] = 'never';
                $data['alias'] = $data['content'] = $data['priority'] = 0;
                $this->db->insertRow('wl_sitemap', $data);
                $_SESSION['notify']->success = 'Лінк успішно додано <a href="'.SITE_URL.'admin/wl_sitemap/'.$sitemap->id.'" class="btn btn-success btn-xs">Редагувати</a>';
                $this->redirect('admin/wl_sitemap?sort=down');
            }
        }
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
                $this->db->select('wl_sitemap', 'id, link, alias, content, code', $_POST['id']);
                if($sitemap = $this->db->get())
                {
                    $this->db->deleteRow('wl_sitemap_from', $sitemap->id, 'sitemap');
                    if($sitemap->alias == 0)
                        $this->db->deleteRow('wl_statistic_pages', array('alias' => 0, 'content' => $sitemap->id));
                    if($_SESSION['language'] && isset($_POST['all_languages']) && $_POST['all_languages'] == 1)
                        $this->db->deleteRow('wl_sitemap', array('alias' => $sitemap->alias, 'content' => $sitemap->content));
                    else
                        $this->db->deleteRow('wl_sitemap', $sitemap->id);
                    $_SESSION['notify'] = new stdClass();
                    $_SESSION['notify']->success = 'Дані <strong>'.SITE_URL.$sitemap->link.'</strong> успішно видалено!';
                    $this->redirect('admin/wl_sitemap?code='.$sitemap->code);
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

    public function cache()
    {
        $id = $this->data->uri(3);
        if(is_numeric($id))
        {
            if($sitemap = $this->db->getAllDataById('wl_sitemap', $id))
            {
                if($sitemap->data)
                {
                    if(extension_loaded('zlib'))
                        echo ( gzdecode ($sitemap->data) );
                    else
                        echo ( $sitemap->data );
                }
                else
                    $this->load->notify_view(array('errors' => 'За адресою <strong>'.SITE_URL.$sitemap->link.'</strong> дані Cache-сторінки відсутні.'));
            }
            else
                $this->load->page_404();
        }
        else
            $this->load->page_404();
    }

    public function cleanCache()
    {
        if(isset($_POST['id']) && $_POST['id'] > 0)
        {
            if($_POST['code_hidden'] == $_POST['code_open'])
            {
                $this->db->updateRow('wl_sitemap', array('data' => NULL), $_POST['id']);
                $_SESSION['notify'] = new stdClass();
                $_SESSION['notify']->success = 'Cache сторінки успішно видалено!';
            }
            else
            {
                $_SESSION['notify'] = new stdClass();
                $_SESSION['notify']->errors = 'Невірний код безпеки!';
            }
        }
        $this->redirect();
    }

    public function multi_edit()
    {
        if(!empty($_POST['sitemap-ids']) && !empty($_POST['do']))
        {
            $post_ids = explode(',', $_POST['sitemap-ids']);
            $ids = array();
            foreach ($post_ids as $id) {
                if(is_numeric($id) && $id > 0)
                    $ids[] = $id;
            }
            if($_SESSION['language'] && !empty($_POST['all_languages']) && $_POST['all_languages'] == 1)
            {
                $this->db->select('wl_sitemap', 'id, alias, content', array('id' => $ids));
                $seleted_ids = $this->db->get('array');
                foreach ($seleted_ids as $map) {
                    if(!in_array($map->id, $ids))
                    {
                        $this->db->select('wl_sitemap', 'id', array('alias' => $map->alias, 'content' => $map->content));
                        $ml_ids = $this->db->get('array');
                        foreach ($ml_ids as $ml) {
                            if(!in_array($ml->id, $ids))
                                $ids[] = $ml->id;
                        }
                    }
                }
            }

            if(!empty($ids))
            {
                $data = array();

                if($_POST['do'] == 'clearCache')
                    $data['data'] = NULL;
                elseif($_POST['do'] == 'delete')
                {
                    $this->db->deleteRow('wl_sitemap', array('id' => $ids));
                    foreach ($ids as $id) {
                        $this->db->deleteRow('wl_sitemap_from', $id, 'sitemap');
                        $this->db->deleteRow('wl_statistic_pages', array('alias' => 0, 'content' => $id));
                    }
                    $_SESSION['notify'] = new stdClass();
                    $_SESSION['notify']->success = 'Дані успішно видалено!';
                }
                elseif($_POST['do'] == 'save')
                {
                    if(!empty($_POST['active-code']) && $_POST['active-code'] == 1)
                        $data['code'] = $this->data->post('code');
                    if(empty($data) || $data['code'] != 404)
                    {
                        if(!empty($_POST['active-changefreq']) && $_POST['active-changefreq'] == 1)
                            $data['changefreq'] = $this->data->post('changefreq');
                        if(!empty($_POST['active-priority']) && $_POST['active-priority'] == 1)
                            $data['priority'] = $this->data->post('priority') * 10;

                        if(!empty($_POST['active-index']) && $_POST['active-index'] == 1)
                        {
                            if(!empty($_POST['index']) && $_POST['index'] == 1)
                            {
                                if(isset($data['priority']))
                                {
                                    if($data['priority'] < 0)
                                        $data['priority'] *= -1;
                                }
                                else
                                {
                                    $this->db->executeQuery('UPDATE `wl_sitemap` SET `priority` = `priority` * -1 WHERE `id` IN ('.implode(', ', $ids).') AND `priority` < 0');
                                }
                            }
                            else
                            {
                                if(isset($data['priority']))
                                {
                                    if($data['priority'] > 0)
                                        $data['priority'] *= -1;
                                    else
                                        $data['priority'] = -2;
                                }
                                else
                                {
                                    $this->db->executeQuery('UPDATE `wl_sitemap` SET `priority` = `priority` * -1 WHERE `id` IN ('.implode(', ', $ids).') AND `priority` > 0');
                                    $this->db->executeQuery('UPDATE `wl_sitemap` SET `priority` = -2 WHERE `id` IN ('.implode(', ', $ids).') AND `priority` = 0');
                                }
                                unset($data['changefreq']);
                            }
                        }
                    }
                }
                if(!empty($data))
                {
                    $data['time'] = time();
                    $this->db->updateRow('wl_sitemap', $data, array('id' => $ids));
                    $_SESSION['notify'] = new stdClass();
                    $_SESSION['notify']->success = 'Дані успішно оновлено!';
                }
            }
        }
        $this->redirect();
    }

    public function generate()
    {
        $this->load->admin_view('wl_sitemap/generate_view');
    }

    public function save_generate()
    {
        $fields = array('sitemap_active' => 0, 'sitemap_autosent' => 0);
        foreach ($fields as $key => $value) {
            if(isset($_POST[$key]) && $_POST[$key] == 1) $value = 1;
            $this->db->updateRow('wl_options', array('value' => $value), array('service' => 0, 'alias' => 0, 'name' => $key));
        }
        $_SESSION['notify'] = new stdClass();
        $_SESSION['notify']->success = 'Загальні налаштування SiteMap успішно оновлено!';
        $this->redirect();
    }

    public function start_generate()
    {
        $_SESSION['notify'] = new stdClass();
        if($this->data->post('code_hidden') == $this->data->post('code_open') && $this->data->post('code_hidden') > 0)
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

                    $this->db->updateRow('wl_options', array('value' => time()), array('service' => 0, 'alias' => 0, 'name' => 'sitemap_lastgenerate'));
                    $_SESSION['notify']->success = 'SiteMap успішно згенеровано!';

                    if($this->data->post('sent') == 1)
                    {
                        // submit sitemaps to search engines
                        // $result = $this->sitemapgenerator->submitSitemap("yahooAppId");
                        $result = $this->sitemapgenerator->submitSitemap();
                        $this->db->updateRow('wl_options', array('value' => time()), array('service' => 0, 'alias' => 0, 'name' => 'sitemap_lastsent'));
                        // shows each search engine submitting status
                        $_SESSION['notify']->success .= "<br><br><pre>";
                        $_SESSION['notify']->success .= print_r($result, true);
                        $_SESSION['notify']->success .= "</pre>";
                    }
                }
                catch (Exception $exc) {
                    $_SESSION['notify']->errors = $exc->getTraceAsString();
                }
            }
        }
        else
            $_SESSION['notify']->errors = 'Невірний код безпеки!';
        $this->redirect();
    }

}

?>
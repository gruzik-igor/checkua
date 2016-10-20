<!-- begin #sidebar -->
<div id="sidebar" class="sidebar">
<!-- begin sidebar scrollbar -->
<div data-scrollbar="true" data-height="100%">
<!-- begin sidebar user -->
<ul class="nav">
    <li class="nav-profile">
        <div class="image">
            <a href="<?=SITE_URL?>admin/wl_users/my"><img src="<?=SITE_URL?>style/admin/images/user-<?=$_SESSION['user']->type?>.jpg" alt="" /></a>
        </div>
        <div class="info">
            <?=$_SESSION['user']->name?>
            <small>Адміністратор</small>
            <small>Сьогодні: <?=date("d.m.Y H:i")?></small>
        </div>
    </li>
</ul>
<!-- end sidebar user -->
<!-- begin sidebar nav -->
<ul class="nav">
    <li class="nav-header">Панель навігації:</li>
    <li <?=($_SESSION['alias']->alias == 'admin')?'class="active"':''?>>
        <a href="<?=SITE_URL?>admin">
            <i class="fa fa-laptop"></i>
            <span>Домашня сторінка</span>
        </a>
    </li>
    <?php
    $this->db->select('wl_aliases', 'id, alias, admin_ico', array('admin_order' => '>0'));
    $this->db->join('wl_services', 'name as service_name', '#service');
    $this->db->order('admin_order DESC');
    if($wl_aliases = $this->db->get('array'))
    {
        $sub_menus = array();
        if($sub_menus_data = $this->db->getAllDataByFieldInArray('wl_options', array('alias' => '<0', 'name' => 'sub-menu')))
            foreach ($sub_menus_data as $sm) {
                $sm->alias *= -1;
                $sub_menus[$sm->alias][] = $sm;
            }

        foreach ($wl_aliases as $wl_alias)
            if($this->userCan($wl_alias->alias))
            {
                if($wl_alias->id > 1)
                {
                    $where = array('alias' => $wl_alias->id, 'content' => '0');
                    if($_SESSION['language'])
                        $where['language'] = $_SESSION['language'];
                    $name = $this->db->getAllDataById('wl_ntkd', $where);
                    if($name)
                        $wl_alias->name = $name->name;
                    else
                        $wl_alias->name = $wl_alias->alias;
                }
                else
                    $wl_alias->name = 'Головна сторінка';

                $ico = 'fa-file';
                if($wl_alias->admin_ico != '') $ico = $wl_alias->admin_ico;

                $sub_menu = false;
                if(isset($sub_menus[$wl_alias->id]) && is_array($sub_menus[$wl_alias->id]) && !empty($sub_menus[$wl_alias->id]))
                  $sub_menu = $sub_menus[$wl_alias->id];
    ?>
        <li class="<?=($_SESSION['alias']->alias == $wl_alias->alias)?'active':''?> <?=($sub_menu)?'has-sub':''?>">
            <?php if($sub_menu) { ?>
                <a href="javascript:;">
                    <b class="caret pull-right"></b>
                    <i class="fa <?=$ico?>"></i>
                    <span><?=$wl_alias->name?></span>
                </a>
                <ul class="sub-menu">
                    <?php
                    echo('<li');
                    if($this->data->uri(2) == '') echo(' class="active"');
                    echo('><a href="'.SITE_URL.'admin/'.$wl_alias->alias.'">Головна сторінка</a>');
                    echo('</li>');
                    foreach ($sub_menu as $sm) {
                        $sb = unserialize($sm->value);
                        if(isset($sb['alias']) && $sb['name'])
                        {
                            echo('<li');
                            if($this->data->uri(2) == $sb['alias'])
                                echo(' class="active"');
                            echo('><a href="'.SITE_URL.'admin/'.$wl_alias->alias.'/'.$sb['alias'].'">'.$sb['name'].'</a>');
                            echo('</li>');
                        }
                    } ?>
            </ul>
          <?php } else { ?>
                <a href="<?=SITE_URL?>admin/<?=$wl_alias->alias?>"><i class="fa <?=$ico?>"></i> <span><?=$wl_alias->name?></span></a>
          <?php } ?>
        </li>
    <?php }
    }
    if($_SESSION['user']->admin == 1){ ?>
        <li <?=($_SESSION['alias']->alias == 'wl_users')?'class="active"':''?>><a href="<?=SITE_URL?>admin/wl_users"><i class="fa fa-group"></i> Користувачі</a></li>
        <li <?=($_SESSION['alias']->alias == 'wl_statistic')?'class="active"':''?>><a href="<?=SITE_URL?>admin/wl_statistic"><i class="fa fa-area-chart"></i> Статистика сайту</a></li>
        <li class="has-sub <?=(in_array($_SESSION['alias']->alias, array('wl_ntkd', 'wl_aliases', 'wl_services', 'wl_images', 'wl_register', 'wl_language_words', 'wl_forms', 'wl_mail_template')))?'active':''?>">
            <a href="javascript:;">
                <b class="caret pull-right"></b>
                <i class="fa fa-cogs"></i>
                <span>Налаштування</span>
            </a>
            <ul class="sub-menu">
                <li <?=($_SESSION['alias']->alias == 'wl_ntkd')?'class="active"':''?>><a href="<?=SITE_URL?>admin/wl_ntkd">SEO</a></li>
                <li <?=($_SESSION['alias']->alias == 'wl_images')?'class="active"':''?>><a href="<?=SITE_URL?>admin/wl_images">Розміри зображень</a></li>
                <li <?=($_SESSION['alias']->alias == 'wl_forms')?'class="active"':''?>><a href="<?=SITE_URL?>admin/wl_forms">Форми</a></li>
                <li <?=($_SESSION['alias']->alias == 'wl_mail_template')?'class="active"':''?>><a href="<?=SITE_URL?>admin/wl_mail_template">Розсилка</a></li>
                <li <?=($_SESSION['alias']->alias == 'wl_aliases')?'class="active"':''?>><a href="<?=SITE_URL?>admin/wl_aliases">Адреси</a></li>
                <li <?=($_SESSION['alias']->alias == 'wl_services')?'class="active"':''?>><a href="<?=SITE_URL?>admin/wl_services">Сервіси</a></li>
                <li <?=($_SESSION['alias']->alias == 'wl_language_words')?'class="active"':''?>><a href="<?=SITE_URL?>admin/wl_language_words">Мультимовність</a></li>
                <li <?=($_SESSION['alias']->alias == 'wl_register')?'class="active"':''?>><a href="<?=SITE_URL?>admin/wl_register">Реєстр</a></li>
            </ul>
        </li>
    <?php } ?>

    <!-- begin sidebar minify button -->
    <li><a href="javascript:;" class="sidebar-minify-btn" data-click="sidebar-minify"><i class="fa fa-angle-double-left"></i></a></li>
    <!-- end sidebar minify button -->
</ul>
<!-- end sidebar nav -->
</div>
<!-- end sidebar scrollbar -->
</div>
<div class="sidebar-bg"></div>
<!-- end #sidebar -->
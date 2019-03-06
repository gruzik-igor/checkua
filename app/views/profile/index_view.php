<link href="<?=SITE_URL?>style/css/profile.css" id="theme" rel="stylesheet" />
<link href="<?=SITE_URL?>style/css/app.css" id="theme" rel="stylesheet" />

<main class="page-content">

    <div class="container">
        <div class="content profile">
            <div class="row">
                <!--Left Sidebar-->
                <div class="col-md-3 md-margin-bottom-40">
                    <?php $avatar = ($user->photo > 0)? IMG_PATH.'profile/'.$user->id.'.jpg' : IMG_PATH.'empty-avatar.jpg'; ?>
                    <img class="img-responsive profile-img margin-bottom-20" id="photo" src="<?=$avatar ?>">
                    <ul class="list-group sidebar-nav-v1 margin-bottom-40" id="sidebar-nav-1">
                        <li class="list-group-item <?=($_SESSION['alias']->alias == 'profile') ? 'active' : ''?>">
                            <a href="<?= SITE_URL?>profile"><i class="fa fa-user"></i> <?=$this->text('Профіль')?></a>
                        </li>
                        <?php if($this->userIs()) { ?>
                            <li class="list-group-item">
                                <a href="<?=SITE_URL?>profile/edit"><i class="fa fa-cog"></i> <?=$this->text('Редагувати профіль')?></a>
                            </li>
                            <?php $where_alias = array('alias' => '#ac.alias2', 'content' => '0');
                            if($_SESSION['language'])
                                $where_alias['language'] = $_SESSION['language'];
                            $this->db->select('wl_aliases_cooperation as ac', 'alias2 as id', array('alias1' => '<0', 'type' => '__link_profile'));
                            $this->db->join('wl_aliases', 'alias, admin_ico as ico', '#ac.alias2');
                            $this->db->join('wl_ntkd', 'name', $where_alias);
                            $this->db->order('alias1');

                            if($links = $this->db->get('array'))
                            foreach ($links as $link) { ?>
                                <li class="list-group-item <?=($_SESSION['alias']->id == $link->id) ? 'active' : ''?>">
                                    <a href="<?=SITE_URL.$link->alias?>"><i class="fa <?=$link->ico?>"></i> <?=$link->name?></a>
                                </li>
                            <?php } ?>
                        <?php } if($this->userCan()) { ?>
                            <li class="list-group-item">
                                <a href="<?=SITE_URL?>admin"><i class="fa fa-cogs"></i> Панель керування</a>
                            </li>
                        <?php } ?>
                        <li class="list-group-item">
                            <a href="<?=SITE_URL?>logout"> <i class="fa fa-sign-out" aria-hidden="true"></i> Вийти</a>
                        </li>
                    </ul>   
                </div>
                <!--End Left Sidebar-->
                
                <!-- Profile Content -->
                <div class="col-md-9">

                    <div class="row">
                        <div class="profile-body margin-bottom-20">
                            <h1><?=$user->name?></h1>
                            <?php if($user->email) {?>
                            <span><?=$user->email?></span>
                            <?php } ?>
                        </div>
                    </div>
                    <?php if(!empty($_SESSION['notify']->errors)): ?>
                       <div class="alert alert-danger fade in">
                            <span class="close" data-dismiss="alert">×</span>
                            <h4><?=(isset($_SESSION['notify']->title)) ? $_SESSION['notify']->title : 'Помилка!'?></h4>
                            <p><?=$_SESSION['notify']->errors?></p>
                        </div>
                    <?php elseif(!empty($_SESSION['notify']->success)): ?>
                        <div class="alert alert-success fade in">
                            <span class="close" data-dismiss="alert">×</span>
                            <i class="fa fa-check fa-2x pull-left"></i>
                            <h4><?=(isset($_SESSION['notify']->title)) ? $_SESSION['notify']->title : 'Успіх!'?></h4>
                            <p><?=$_SESSION['notify']->success?></p>
                        </div>
                    <?php endif; unset($_SESSION['notify']); ?>
                    <div class="row">
                    <?php if(isset($sub_page))
                            require_once $sub_page;
                    ?>
                    </div>
                </div>
                <!-- End Profile Content -->
            </div>
        </div>
    </div>
</main>
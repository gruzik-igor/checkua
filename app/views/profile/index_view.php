<link href="<?=SITE_URL?>style/css/profile.css" id="theme" rel="stylesheet" />
<link href="<?=SITE_URL?>style/css/app.css" id="theme" rel="stylesheet" />

<main class="page-content">

    <div class="container">
        <div class="content profile">
            <div class="row">
                <!--Left Sidebar-->
                <div class="col-md-3 md-margin-bottom-40">
                    <?php $avatar = (isset($user->id)) ? $user->id : 0;
                    $avatar = ($this->userIs() && $avatar == 0) ? $_SESSION['user']->id : 0;
                    $avatar = ($avatar > 0) ? 'profile/'.$avatar . '.jpg' : 'empty-avatar.jpg';
                    if(file_exists(IMG_PATH.$avatar)) { ?>
                        <img class="img-responsive profile-img margin-bottom-20" id="photo" src="<?=IMG_PATH.$avatar ?>">
                    <?php } ?>
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
                    </ul>   
                </div>
                <!--End Left Sidebar-->
                
                <!-- Profile Content -->
                <div class="col-md-9">
                    <div class="profile-body">

                        <?php if(isset($content))
                                echo $content;
                            else {
                         ?>

                        <div class="profile-bio">
                            <div class="row">
                                <h2><?=$user->name?></h2>
                                <?php if($user->email) {?>
                                <span><strong>Email:</strong> <?=$user->email?></span>
                                <?php } ?>
                            </div>    
                        </div><!--/end row-->

                        <?php } ?>

                        <hr>   
                    </div>
                </div>
                <!-- End Profile Content -->
            </div>
        </div>
    </div>
</main>
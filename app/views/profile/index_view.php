<link href="<?=SITE_URL?>style/css/profile.css" id="theme" rel="stylesheet" />
<link href="<?=SITE_URL?>style/css/app.css" id="theme" rel="stylesheet" />

<main class="page-content">
    <div class="container">
        <div class="content profile">
            <div class="row">
                <!--Left Sidebar-->
                <div class="col-md-3 md-margin-bottom-40">
                    <img class="img-responsive profile-img margin-bottom-20" id="photo" src="<?= ($user->photo > 0)? IMG_PATH.'profile/'.$user->id.'.jpg' : IMG_PATH.'empty-avatar.jpg'  ?>" alt="Фото" title="Фото" >

                    <ul class="list-group sidebar-nav-v1 margin-bottom-40" id="sidebar-nav-1">
                        <li class="list-group-item active">
                            <a href="<?= SITE_URL?>profile"><i class="fa fa-user"></i> <?=$this->text('Профіль')?></a>
                        </li>
                        <?php if($this->userIs()) { ?>
                            <li class="list-group-item">
                                <a href="<?=SITE_URL?>profile/edit"><i class="fa fa-cog"></i> <?=$this->text('Редагувати профіль')?></a>
                            </li>
                            <li class="list-group-item">
                                <a href="<?=SITE_URL?>profile/orders"><i class="fa fa-shopping-cart"></i> <?=$this->text('Історія замовлень')?></a>
                            </li>
                        <?php } ?>
                    </ul>   
                </div>
                <!--End Left Sidebar-->
                
                <!-- Profile Content -->
                <div class="col-md-9">
                    <div class="profile-body">
                        <div class="profile-bio">
                            <div class="row">
                                <h2><?=$user->name?></h2>
                                <?php if($user->email) {?>
                                <span><strong>Email:</strong> <?=$user->email?></span>
                                <?php } ?>
                            </div>    
                        </div><!--/end row-->

                        <hr>   
                    </div>
                </div>
                <!-- End Profile Content -->
            </div>
        </div>
    </div>
</main>
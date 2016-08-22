<div id="header" class="header navbar navbar-default navbar-fixed-top">
    <!-- begin container -->
    <div class="container">
        <!-- begin navbar-header -->
        <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#header-navbar">
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a href="<?=SITE_URL?>" class="navbar-brand">
                <span><img src="<?=SERVER_URL?>style/admin/images/whitelion-black.png" style="height:30px" alt="White Lion CMS"></span>
                <span class="brand-text">
                    White Lion CMS
                </span>
            </a>
        </div>
        <!-- end navbar-header -->
        <!-- begin navbar-collapse -->
        <div class="collapse navbar-collapse" id="header-navbar">
            <ul class="nav navbar-nav navbar-right">
                <li><a href="<?=SITE_URL?>">ГОЛОВНА</a></li>
                <?php if($this->userIs()) { ?>
                    <li><a href="<?=SITE_URL?>profile">КАБІНЕТ</a></li>
                    <?php if($this->userCan()) { ?>
                        <li><a href="<?=SITE_URL?>admin">ADMIN</a></li>
                    <?php } ?>
                    <li><a href="<?=SITE_URL?>logout">ВИЙТИ</a></li>
                <?php } else { ?>
                    <li><a href="<?=SITE_URL?>login">УВІЙТИ</a></li>
                <?php } ?>
            </ul>
        </div>
        <!-- end navbar-collapse -->
    </div>
    <!-- end container -->
</div>
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
                <li><a href="<?=SITE_URL?>"><?= $this->text('ГОЛОВНА', 0); ?></a></li>
                <?php if($this->userIs()) { ?>
                    <li><a href="<?=SITE_URL?>profile"><?= $this->text('КАБІНЕТ', 0); ?></a></li>
                    <?php if($this->userCan()) { ?>
                        <li><a href="<?=SITE_URL?>admin">ADMIN</a></li>
                    <?php } ?>
                    <li><a href="<?=SITE_URL?>logout"><?= $this->text('ВИЙТИ', 0); ?></a></li>
                <?php } else { ?>
                    <li><a href="<?=SITE_URL?>login"><?= $this->text('УВІЙТИ', 0); ?></a></li>
                <?php } 
                    //$this->load->function_in_alias('cart', '__show_minicart');
                ?>
            </ul>
        </div>
        <!-- end navbar-collapse -->
    </div>
    <!-- end container -->
</div>
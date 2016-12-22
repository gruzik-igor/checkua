<?php if(!empty($_SESSION['notify']->error) || !empty($_SESSION['notify']->success)) { ?>
<div class="row">
    <!-- begin col-6 -->
    <div class="col-md-6">
    	<?php if(!empty($_SESSION['notify']->success)) { ?>
	    	<div class="alert alert-success fade in m-b-15">
				<?=$_SESSION['notify']->success?>
				<span class="close" data-dismiss="alert">&times;</span>
			</div>
		<?php } if(!empty($_SESSION['notify']->error)) { ?>
			<div class="alert alert-danger fade in m-b-15">
				<strong>Помилка!</strong>
				<?=$_SESSION['notify']->error?>
				<span class="close" data-dismiss="alert">&times;</span>
			</div>
		<?php } ?>
    </div>
</div>
<?php unset($_SESSION['notify']->success, $_SESSION['notify']->error); } ?>

<div class="row">
    <div class="col-md-12">
        <ul class="nav nav-tabs" id="myTab">
            <li class="active"><a data-target="#tabs-main" href="#tabs-main" data-toggle="tab">Загальні дані</a></li>
            <li><a data-target="#tabs-register" href="#tabs-register" data-toggle="tab">Реєстр дій</a></li>
            <li><a data-target="#tabs-edit" href="#tabs-edit" data-toggle="tab">Редагувати</a></li>
          
        </ul>

        <div class="tab-content col-md-12">
            <div class="tab-pane active" id="tabs-main">
                <?php require_once 'tabs/_tabs-main.php'; ?>
            </div>
            <div class="tab-pane" id="tabs-register">
                <?php require_once 'tabs/_tabs-register.php'; ?>
            </div>
            <div class="tab-pane" id="tabs-edit">
                <?php require_once 'tabs/_tabs-edit.php'; ?>
            </div>
        </div>
    </div>
</div>

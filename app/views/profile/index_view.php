<link href="<?=SITE_URL?>style/css/profile.css" id="theme" rel="stylesheet" />
<link href="<?=SITE_URL?>style/css/app.css" id="theme" rel="stylesheet" />

<div class="container">
    <div class="content profile">

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
            <!--Left Sidebar-->
            <div class="col-md-3 md-margin-bottom-40">
                <img class="img-responsive profile-img margin-bottom-20" id="photo" src="<?= ($user->photo > 0)? IMG_PATH.'profile/'.$user->id.'.jpg' : IMG_PATH.'empty-avatar.jpg'  ?>" alt="Фото" title="Фото" >

                <ul class="list-group sidebar-nav-v1 margin-bottom-40" id="sidebar-nav-1">
                    <li class="list-group-item active">
                        <a href="http://htmlstream.com/preview/unify-v1.8/page_profile_me.html"><i class="fa fa-user"></i> Профіль</a>
                    </li>
                    <li class="list-group-item">
                        <a href="http://htmlstream.com/preview/unify-v1.8/page_profile_users.html"><i class="fa fa-group"></i> Спільнота</a>
                    </li>                                        
                    <li class="list-group-item">
                        <a href="http://htmlstream.com/preview/unify-v1.8/page_profile_projects.html"><i class="fa fa-cubes"></i> Проекти</a>
                    </li>
                    <li class="list-group-item">
                        <a href="http://htmlstream.com/preview/unify-v1.8/page_profile_comments.html"><i class="fa fa-comments"></i> Коментарі</a>
                    </li>                                        
                    <li class="list-group-item">
                        <a href="http://htmlstream.com/preview/unify-v1.8/page_profile_history.html"><i class="fa fa-history"></i> Історія</a>
                    </li>
                    <?php if($this->userIs()) { ?>
                        <li class="list-group-item">
                            <a href="<?=SITE_URL?>profile/edit"><i class="fa fa-cog"></i> Редагувати профіль</a>
                        </li>
                    <?php } ?>
                </ul>   

                <div class="panel-heading-v2 overflow-h">
                    <h2 class="heading-xs pull-left"><i class="fa fa-bar-chart-o"></i> Task Progress</h2>
                    <a href="#"><i class="fa fa-cog pull-right"></i></a>
                </div>
                <h3 class="heading-xs">Web Design <span class="pull-right">92%</span></h3>
                <div class="progress progress-u progress-xxs">
                    <div style="width: 92%" aria-valuemax="100" aria-valuemin="0" aria-valuenow="92" role="progressbar" class="progress-bar progress-bar-u">
                    </div>
                </div>
                <h3 class="heading-xs">Unify Project <span class="pull-right">85%</span></h3>
                <div class="progress progress-u progress-xxs">
                    <div style="width: 85%" aria-valuemax="100" aria-valuemin="0" aria-valuenow="85" role="progressbar" class="progress-bar progress-bar-blue">
                    </div>
                </div>
                <h3 class="heading-xs">Sony Corporation <span class="pull-right">64%</span></h3>
                <div class="progress progress-u progress-xxs margin-bottom-40">
                    <div style="width: 64%" aria-valuemax="100" aria-valuemin="0" aria-valuenow="64" role="progressbar" class="progress-bar progress-bar-dark">
                    </div>
                </div>
            </div>
            <!--End Left Sidebar-->
            
            <!-- Profile Content -->
            <div class="col-md-9">
                <div class="profile-body">
                    <div class="profile-bio">
                        <div class="row">
                            <h2><?=$user->name?></h2>
                            <span><strong>Професія:</strong> Web Developer</span>
                            <span><strong>Статус:</strong> <?=$user->type_name?></span>
                            <hr>
                            <p>Lorem ipsum dolor sit amet, 
consectetur adipiscing elit. Suspendisse eget massa nec turpis congue 
bibendum. Integer nulla felis, porta suscipit nulla et, dignissim 
commodo nunc. Morbi a semper nulla.</p>
                            <p>Proin mauris odio, pharetra quis 
ligula non, vulputate vehicula quam. Nunc in libero vitae nunc ultricies
tincidunt ut sed leo. Sed luctus dui ut congue consequat. Cras 
consequat nisl ante, nec malesuada velit pellentesque ac. Pellentesque 
nec arcu in ipsum iaculis convallis.</p>
                        </div>    
                    </div><!--/end row-->
                            
                    <hr>

                    <div class="row">
                        <!--Social Icons v3-->
                        <div class="col-sm-6 sm-margin-bottom-30">
                            <div class="panel panel-profile">
                                <div class="panel-heading overflow-h">
                                    <h2 class="panel-title heading-sm pull-left"><i class="fa fa-pencil"></i> Social Contacts <small>(option 1)</small></h2>
                                    <a href="#"><i class="fa fa-cog pull-right"></i></a>
                                </div>
                                <div class="panel-body">
                                     <ul class="list-unstyled social-contacts-v2">
                                        <li><i class="rounded-x tw fa fa-twitter"></i> <a href="#">edward.rooster</a></li>
                                        <li><i class="rounded-x fb fa fa-facebook"></i> <a href="#">Edward Rooster</a></li>
                                        <li><i class="rounded-x sk fa fa-skype"></i> <a href="#">edwardRooster77</a></li>
                                        <li><i class="rounded-x gp fa fa-google-plus"></i> <a href="#">rooster77edward</a></li>
                                        <li><i class="rounded-x gm fa fa-envelope"></i> <a href="#">edward77@gmail.com</a></li>
                                    </ul>
                                </div>       
                            </div>
                        </div>
                        <!--End Social Icons v3-->

                        <!--Skills-->
                        <div class="col-sm-6 sm-margin-bottom-30">
                            <div class="panel panel-profile">
                                <div class="panel-heading overflow-h">
                                    <h2 class="panel-title heading-sm pull-left"><i class="fa fa-lightbulb-o"></i> Skills</h2>
                                    <a href="#"><i class="fa fa-cog pull-right"></i></a>
                                </div>
                                <div class="panel-body">
                                    <small>HTML/CSS</small>
                                    <small>92%</small>
                                    <div class="progress progress-u progress-xxs">
                                        <div style="width: 92%" aria-valuemax="100" aria-valuemin="0" aria-valuenow="92" role="progressbar" class="progress-bar progress-bar-u">
                                        </div>
                                    </div>

                                    <small>Photoshop</small>
                                    <small>77%</small>
                                    <div class="progress progress-u progress-xxs">
                                        <div style="width: 77%" aria-valuemax="100" aria-valuemin="0" aria-valuenow="77" role="progressbar" class="progress-bar progress-bar-u">
                                        </div>
                                    </div>

                                    <small>PHP</small>
                                    <small>85%</small>
                                    <div class="progress progress-u progress-xxs">
                                        <div style="width: 85%" aria-valuemax="100" aria-valuemin="0" aria-valuenow="85" role="progressbar" class="progress-bar progress-bar-u">
                                        </div>
                                    </div>

                                    <small>Javascript</small>
                                    <small>81%</small>
                                    <div class="progress progress-u progress-xxs">
                                        <div style="width: 81%" aria-valuemax="100" aria-valuemin="0" aria-valuenow="81" role="progressbar" class="progress-bar progress-bar-u">
                                        </div>
                                    </div>  
                                </div>
                            </div>
                        </div>    
                        <!--End Skills-->            
                    </div><!--/end row-->    

                    <hr>                                

                    <!--Timeline-->
                    <div class="panel panel-profile">
                        <div class="panel-heading overflow-h">
                            <h2 class="panel-title heading-sm pull-left"><i class="fa fa-briefcase"></i> Experience</h2>
                            <a href="#"><i class="fa fa-cog pull-right"></i></a>
                        </div>
                        <div class="panel-body margin-bottom-40">
                            <ul class="timeline-v2 timeline-me">
                                <li>
                                    <time datetime="" class="cbp_tmtime"><span>Mobile Design</span> <span>2012 - Current</span></time>
                                    <i class="cbp_tmicon rounded-x hidden-xs"></i>
                                    <div class="cbp_tmlabel">
                                        <h2>BFC NYC Partners</h2>
                                        <p>Winter purslane courgette 
pumpkin quandong komatsuna fennel green bean cucumber watercress. 
Peasprouts wattle seed rutabaga okra yarrow cress avocado grape.</p> 
                                    </div>
                                </li>
                                <li>
                                    <time datetime="" class="cbp_tmtime"><span>Web Designer</span> <span>2007 - 2012</span></time>
                                    <i class="cbp_tmicon rounded-x hidden-xs"></i>
                                    <div class="cbp_tmlabel">
                                    <h2>Freelance</h2>
                                        <p>Caulie dandelion maize lentil collard greens radish arugula sweet pepper water spinach kombu courgette lettuce.</p>
                                    </div>
                                </li>
                                <li>
                                    <time datetime="" class="cbp_tmtime"><span>Photodesigner</span> <span>2003 - 2007</span></time>
                                    <i class="cbp_tmicon rounded-x hidden-xs"></i>
                                    <div class="cbp_tmlabel">
                                    <h2>Toren Condo</h2>
                                        <p>Caulie dandelion maize lentil
 collard greens radish arugula sweet pepper water spinach kombu 
courgette lettuce. Celery coriander bitterleaf epazote radicchio 
shallot.</p>
                                    </div>
                                </li>
                            </ul>
                        </div>    
                    </div>
                    <!--End Timeline-->

                    <hr>

                    <div class="row">
                        <!--Design Skills-->
                        <div class="col-sm-6">
                            <div class="panel panel-profile">
                                <div class="panel-heading overflow-h">
                                    <h2 class="panel-title heading-sm pull-left"><i class="fa fa-pencil"></i> Language Skills</h2>
                                    <a href="#"><i class="fa fa-cog pull-right"></i></a>
                                </div>
                                <div class="panel-body">
                                    <div class="row">
                                         <div class="p-chart col-sm-6 col-xs-6 sm-margin-bottom-10">
                                            <div class="circle margin-bottom-20" id="circle-4"><div class="circles-wrp" style="position:relative; display:inline-block;"><svg width="70" height="70"><path fill="transparent" stroke="#eee" stroke-width="2" d="M 34.99307511107436 1.0000007052071638 A 34 34 0 1 1 34.95277468210556 1.0000327975253782 Z"></path><path fill="transparent" stroke="#72c02c" stroke-width="2" d="M 34.99307511107436 1.0000007052071638 A 34 34 0 1 1 3.395134154018084 47.53524849604051 "></path></svg><div class="circles-text-wrp" style="position:absolute; top:0; left:0; text-align:center; width:100%; font-size:17.5px; height:70px; line-height:70px;"><span class="circles-number"><span class="circles-number">69</span></span><span class="circles-text">%</span></div></div></div>
                                            <h3 class="heading-xs">Engagement Score</h3>
                                            <p>Celery coriander bitterleaf epazote radicchio shallot.</p>
                                        </div> 
                                        <div class="p-chart col-sm-6 col-xs-6">
                                            <div class="circle margin-bottom-20" id="circle-5"><div class="circles-wrp" style="position:relative; display:inline-block;"><svg width="70" height="70"><path fill="transparent" stroke="#eee" stroke-width="2" d="M 34.99307511107436 1.0000007052071638 A 34 34 0 1 1 34.95277468210556 1.0000327975253782 Z"></path><path fill="transparent" stroke="#9B6BCC" stroke-width="2" d="M 34.99307511107436 1.0000007052071638 A 34 34 0 1 1 4.215417757692592 20.56706903755819 "></path></svg><div class="circles-text-wrp" style="position:absolute; top:0; left:0; text-align:center; width:100%; font-size:17.5px; height:70px; line-height:70px;"><span class="circles-number"><span class="circles-number">82</span></span><span class="circles-text">%</span></div></div></div>
                                            <h3 class="heading-xs">Progfile Completness</h3>
                                            <p>Celery coriander bitterleaf epazote radicchio shallot.</p>
                                        </div>
                                    </div>
                                </div>       
                            </div>
                        </div>
                        <!--End Design Skills-->            
                    </div><!--/end row-->
                </div>
            </div>
            <!-- End Profile Content -->
        </div>
    </div>
</div>
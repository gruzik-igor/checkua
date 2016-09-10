<div class="intro">
    <div class="dtable hw100">
        <div class="dtable-cell hw100">
            <div class="container text-center">
                <h1 class="intro-title animated fadeInDown"> Пошук автозапчастин </h1>
                <p class="sub animateme fittext3 animated fadeIn">KIA & HYUNDAI Оригінали та Замінники</p>
                <div class="row search-row animated fadeInUp">
                    <form action="<?=SITE_URL?>parts/search">
                        <div class="col-lg-8 col-sm-8 search-col relative locationicon">
                            <i class="fa fa-pencil icon-append"></i>
                            <input type="text" name="article" class="form-control locinput input-rel searchtag-input has-icon" placeholder="Артикул" value="<?=$this->data->get('article')?>" required="required">
                        </div>
                        <div class="col-lg-4 col-sm-4 search-col">
                            <button class="btn btn-primary btn-search btn-block"><i class="fa fa-search"></i><strong> Знайти</strong></button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
if($products){ 
?>
<div class="row">
    <div class="col-md-12">
        <div class="panel panel-inverse">
            <div class="panel-heading">
                <div class="panel-heading-btn">
                    <a href="<?=SITE_URL.'admin/'.$_SESSION['alias']->alias?>/add<?=(isset($group))?'?group='.$group->id:''?>" class="btn btn-warning btn-xs"><i class="fa fa-plus"></i> <?=$_SESSION['admin_options']['word:product_add']?></a>
                    
                    <?php if($_SESSION['option']->useGroups == 1) { ?>
                        <a href="<?=SITE_URL.'admin/'.$_SESSION['alias']->alias?>/all" class="btn btn-info btn-xs">До всіх <?=$_SESSION['admin_options']['word:products_to_all']?></a>
                        <a href="<?=SITE_URL.'admin/'.$_SESSION['alias']->alias?>/groups" class="btn btn-info btn-xs">До всіх <?=$_SESSION['admin_options']['word:groups_to_all']?></a>
                    <?php } ?>

                    <a href="<?=SITE_URL.'admin/'.$_SESSION['alias']->alias?>/options" class="btn btn-info btn-xs">До всіх <?=$_SESSION['admin_options']['word:options_to_all']?></a>

                    <a href="<?=SITE_URL.'admin/wl_ntkd/'.$_SESSION['alias']->alias?><?=(isset($group))?'/-'.$group->id:''?>" class="btn btn-info btn-xs">SEO</a>
                </div>
                <h4 class="panel-title"><?=(isset($group))?$_SESSION['alias']->name .'. Список '.$_SESSION['admin_options']['word:products_to_all']:'Список всіх '.$_SESSION['admin_options']['word:products_to_all']?></h4>
            </div>
            <?php if(isset($group)) { ?>
                <div class="panel-heading">
                        <a href="<?=SITE_URL.'admin/'.$_SESSION['alias']->alias?>" class="btn btn-info btn-xs"><?=$group->alias_name?></a> 
                        <?php if(!empty($group->parents)) {
                            $link = SITE_URL.$_SESSION['alias']->alias;
                            foreach ($group->parents as $parent) { 
                                $link .= '/'.$parent->link;
                                echo '<a href="'.$link.'" class="btn btn-info btn-xs">'.$parent->name.'</a> ';
                            }
                        } ?>
                        <span class="btn btn-warning btn-xs"><?=$_SESSION['alias']->name?></span> 
                </div>
            <?php } ?>
            <div class="panel-body">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered nowrap" width="100%">
                        <thead>
                            <tr>
                                <th><?=($_SESSION['option']->ProductUseArticle) ? 'Артикул' : 'Id'?></th>
                                <th>Назва</th>
                                <th>Ціна (у.о.)</th>
                                <?php if($_SESSION['option']->useAvailability == 1) { ?>
                                    <th>Наявність</th>
                                <?php } if($_SESSION['option']->useGroups == 1 && $_SESSION['option']->ProductMultiGroup == 1) { ?>
                                    <th>Групи</th>
                                <?php } ?>
                                <th>Редаговано</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            foreach($products as $a) { ?>
                                <tr>
                                    <td><a href="<?=SITE_URL.$a->link?>"><?=($_SESSION['option']->ProductUseArticle) ? $a->article : $a->id?></a></td>
                                    <td>
                                        <a href="<?=SITE_URL.$a->link?>"><?=$a->name?></a> 
                                    </td>
                                    <td><?=$a->price?></td>
                                    <?php if($_SESSION['option']->useAvailability == 1) { ?>
                                        <td><?=$a->availability_name?></td>
                                    <?php }
                                    if($_SESSION['option']->useGroups == 1 && $_SESSION['option']->ProductMultiGroup == 1) {
                                        echo("<td>");
                                        if(!empty($a->group) && is_array($a->group)) {
                                            foreach ($a->group as $group) {
                                                echo('<a href="'.SITE_URL.$_SESSION['alias']->alias.'/'.$group->link.'">'.$group->name.'</a> ');
                                            }
                                        } else {
                                            echo("Не визначено");
                                        }
                                        echo("</td>");
                                    }
                                    ?>
                                    <td><?=date("d.m.Y H:i", $a->date_edit)?></td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
                <?php
                $this->load->library('paginator');
                echo $this->paginator->get();
                ?>
            </div>
        </div>
    </div>
</div>
<?php } else { ?>
    <div class="alert alert-danger fade in">
        <h4>Помилка!</h4>
        <p>Перевірте артикул! За даним артикулом товарів не знайдено!</p>
    </div>
<?php } ?>
<div class="row">
	<div class="row search-row">
        <form action="<?=SITE_URL.'admin/'.$_SESSION['alias']->alias?>/search">
            <div class="col-lg-8 col-sm-8 search-col">
                <input type="text" name="<?=($_SESSION['option']->ProductUseArticle) ? 'article' : 'id'?>" class="form-control" placeholder="<?=($_SESSION['option']->ProductUseArticle) ? 'Артикул' : 'ID'?>" value="<?=$this->data->get('article')?>" required="required">
            </div>
            <div class="col-lg-4 col-sm-4 search-col">
                <button class="btn btn-primary btn-search btn-block"><i class="fa fa-search"></i><strong> Знайти</strong></button>
            </div>
        </form>
    </div>
</div>

<!-- begin row -->
<div class="row">
    <!-- begin col-12 -->
    <div class="col-md-12">
        <div class="panel panel-inverse">
            <div class="panel-heading">
                <div class="panel-heading-btn">
                	<a href="<?=SITE_URL.'admin/'.$_SESSION['alias']->alias?>/add<?=(isset($group))?'?group='.$group->id:''?>" class="btn btn-warning btn-xs"><i class="fa fa-plus"></i> <?=$_SESSION['admin_options']['word:product_add']?></a>
					
                    <?php if($_SESSION['option']->useGroups == 1){ ?>
						<a href="<?=SITE_URL.'admin/'.$_SESSION['alias']->alias?>/all" class="btn btn-info btn-xs">До всіх <?=$_SESSION['admin_options']['word:products_to_all']?></a>
						<a href="<?=SITE_URL.'admin/'.$_SESSION['alias']->alias?>/groups" class="btn btn-info btn-xs">До всіх <?=$_SESSION['admin_options']['word:groups_to_all']?></a>
					<?php } ?>

					<a href="<?=SITE_URL.'admin/'.$_SESSION['alias']->alias?>/options" class="btn btn-info btn-xs">До всіх <?=$_SESSION['admin_options']['word:options_to_all']?></a>

					<a href="<?=SITE_URL.'admin/wl_ntkd/'.$_SESSION['alias']->alias?>/edit" class="btn btn-success btn-xs">SEO головна</a> 
					<a href="<?=SITE_URL.'admin/wl_ntkd/'.$_SESSION['alias']->alias?>/seo_robot" class="btn btn-success btn-xs">SEO робот</a>

                    <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-default" data-click="panel-expand"><i class="fa fa-expand"></i></a>
                    <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-success" data-click="panel-reload"><i class="fa fa-repeat"></i></a>
                </div>
                <h4 class="panel-title"><?=$_SESSION['alias']->name?>. Групи/підгрупи</h4>
            </div>
            <?php if(isset($group)) { ?>
                <div class="panel-heading">
	            		<a href="<?=SITE_URL.'admin/'.$_SESSION['alias']->alias?>" class="btn btn-info btn-xs"><?=$group->alias_name?></a> 
						<?php if(!empty($group->parents)) {
							$link = SITE_URL.'admin/'.$_SESSION['alias']->alias;
							foreach ($group->parents as $parent) { 
								$link .= '/'.$parent->alias;
								echo '<a href="'.$link.'" class="btn btn-info btn-xs">'.$parent->name.'</a> ';
							}
						} ?>
						<span class="btn btn-warning btn-xs"><?=$_SESSION['alias']->name?></span> 
	            </div>
	        <?php } ?>
			<div class="panel-body">
                <div class="table-responsive">
                    <table id="data-table" class="table table-striped table-bordered nowrap" width="100%">
                        <thead>
                            <tr>
								<th>Назва</th>
								<th>Адреса</th>
								<th>Active</th>
                            </tr>
                        </thead>
                        <tbody>
						<?php if(!empty($groups)){ $max = count($groups); foreach($groups as $g){ ?>
						<tr>
							<td><a href="<?=SITE_URL.'admin/'.$g->link?>"><?=$g->name?></a></td>
							<td><a href="<?=SITE_URL.$g->link?>">/<?=$g->link?>/*</a></td>
							<td style="backgroung-color:<?=($g->active == 1)?'green':'red'?>; color:white"><center><?=$g->active?></center></td>
						</tr>
						<?php } } ?>
						</tbody>
					</table>
				</div>

				<?php if(!empty($products))
				{
					echo '<h4 title="Перенесіть товари в кінцеву групу">Увага! Товари не в кінцевій групі!</h4>';
					$search = true;
					require_once 'products/__products-list.php';
				}
				?>
			</div>
		</div>
	</div>
</div>

<style type="text/css">
	.search-row {
	    max-width: 800px;
	    margin-left: auto;
	    margin-right: auto;
	}
	.search-row .search-col {
	    padding: 0;
	    position: relative;
	}
	.search-row .search-col:first-child .form-control {
	    border: 1px solid #16A085;
	    border-radius: 3px 0 0 3px;
	    margin-bottom: 20px;
	}
</style>
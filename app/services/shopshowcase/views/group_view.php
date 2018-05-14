<div class="page-head content-top-margin">
    <div class="container">
        <div class="row">
            <div class="col-md-8 col-sm-7">
                <ol class="breadcrumb">
                    <li><a href="<?= SITE_URL?>"><?=$this->text('Головна',0)?></a></li>
                    <?php
                    foreach($_SESSION['alias']->breadcrumbs as $breadcrumb_name => $breadcrumb_link)
                    {
                        if($breadcrumb_link == '') echo('<li class="active">'.$_SESSION['alias']->name.'</li>');
                        else echo('<li><a href="'.SITE_URL.$breadcrumb_link.'">'.$breadcrumb_name.'</a></li>');
                    }
                    ?>
                </ol>
            </div>
        </div>
    </div>
</div>

<section class="section products-grid second-style">
	<div class="container">
		<div class="row">
			<div class="col-md-3">
				<div class="shop-sidebar shop-sidebar-left">
					<div class="widgets">
						<?php if($subgroups) {?>
						<div class="widget widget-categories">
							<h3 class="widget-title"><?=$this->text('Категорії')?></h3>
							<ul>
								<?php foreach ($subgroups as $subgroup) { ?>
								<li>
									<a href="<?= SITE_URL.$subgroup->link?>"><?= $subgroup->name ?></a>
								</li>
								<?php } ?>
							</ul>
						</div>
						<?php } ?>
					</div><!-- /.widgets -->
				</div><!-- /.shop-sidebar -->
			</div>

			<div class="col-md-<?= $subgroups ? '9' : '12'?>">
				<div class="row"  id="productRow">
					<?php if($products) { foreach (array_chunk($products, ($subgroups ? '3' : '4') ) as $column) { ?>
					<div class="clearfix">
					<?php foreach ($column as $product) { ?>
					<div class="product col-md-<?= $subgroups ? '4' : '3'?> col-sm-6 col-xs-12">
						<div class="inner-product">
		                    <?php if($product->photo != '') { ?>
		                    <div class="product-thumbnail">
		                          <a href="<?=SITE_URL.$product->link?>"><img src="<?=IMG_PATH.$product->m_photo?>" class="img-responsive" alt="<?=$product->name?>">
		                          <?php if($product->old_price != '0'){ ?>
		                          	<img src="<?=IMG_PATH?>sale.png" class="saleclass" alt="АКЦІЯ!">
		                          <?php } ?>
		                          </a>
		                    </div>
		                    <?php } ?>
		                    <div class="product-details text-center">
		                        <div class="product-btns">
		                            <span data-toggle="tooltip" data-placement="top" title="<?=$this->text('Переглянути',0)?>">
		                                <a href="<?=SITE_URL.$product->link?>" class="li-icon view-details"><i class="lil-search"></i></a>
		                            </span>
		                        </div>
		                    </div>
		                </div>
		                <h3 class="product-title"><a href="<?=SITE_URL.$product->link?>" title="<?= str_replace($product->article, '', $product->name)?>" ><?= $product->list ?></a></h3>
		                <p class="product-price">
		                    <?php if($product->old_price != 0) { ?>
		                    <del>
		                        <span class="amount"><?=$product->old_price?> грн </span>
		                    </del>
		                    <?php } ?>
		                    <ins>
		                        <span class="amount"><?=$product->price?> грн </span>
		                    </ins>
		                </p>
					</div>
					<?php } ?>
					</div>
					<?php } } ?>
				</div>
 				
				<?php  if(isset($_SESSION['option']->paginator_total) && $_SESSION['option']->paginator_total > 12) {?>
				<div class="clearfix text-center">
					<button class="btn btn-default" onclick="showMore(2, <?= isset($subGroupsForAjax) ? '['.implode(',', $subGroupsForAjax).']' : $group->id ?>, '<?=(isset($_GET['sort'])) ? $_GET['sort'] : ''?>')" id="showMore">
			            <?=$this->text('Показати наступні 12 товарів',0)?>
			        </button>
				</div>
		        <?php } ?>

			</div>

		</div><!-- /.row -->
	</div><!-- /.container -->
</section><!-- /.products-grid -->
<section>
    <div class="container">
        <div class="row">
            <h4><?=$_SESSION['alias']->list?></h4>
            <p><?=html_entity_decode($_SESSION['alias']->text)?></p>
        </div>
    </div>
</section>

<?php
	$_SESSION['alias']->js_load[] = 'js/catalog.js';
?>
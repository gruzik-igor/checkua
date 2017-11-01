<div class="container content">
	<div class="row">
		<div class="text-center hidden" id="loading">
			<img src="<?= IMG_PATH?>ajax-loader.gif" alt="">
		</div>
			<?php
			if(!empty($groups))
			{
				$i = 0;
				echo('<div class="row illustration-v2 margin-bottom-30">');
				foreach ($groups as $group) {
					if($i % 3 == 0)
					{
						echo('</div>');
						echo('<div class="row illustration-v2 margin-bottom-30">');
					}
			?>
					<div class="col-md-4">
						<div class="product-img product-img-brd">
							<a href="<?=SITE_URL.$group->link?>"><img class="full-width img-responsive" src="<?=IMG_PATH.$group->photo?>" alt="<?=$group->name .' '. SITE_NAME?>"></a>
							<a class="product-review" href="<?=SITE_URL.$group->link?>"><?=$group->name?></a>
						</div>
					</div>
			<?php
					$i++;
				}
				echo('</div>');
			} else if(!empty($subgroups))
			{
				/*$i = 0;
				echo('<div class="row illustration-v2 margin-bottom-30">');
				foreach ($subgroups as $subgroup) {
					if($i % 3 == 0)
					{
						echo('</div>');
						echo('<div class="row illustration-v2 margin-bottom-30">');
					}
			?>
					<div class="col-md-4">
						<div class="product-img product-img-brd">
							<a href="<?=SITE_URL.$subgroup->link?>"><img class="full-width img-responsive" src="<?=IMG_PATH.$subgroup->photo?>" alt="<?=$subgroup->name .' '. SITE_NAME?>"></a>
							<a class="product-review" href="<?=SITE_URL.$subgroup->link?>"><?=$subgroup->name?></a>
						</div>
					</div>
			<?php
					$i++;
				}
				echo('</div>'); */
			}
		else if(empty($subgroups) && $filterExists) { ?>

		<div class="col-md-2 filter-by-block md-margin-bottom-60">
			<h1><?=$this->text('Фільтр')?></h1>
			<form>
				<input type="hidden" name="show" value="<?=(isset($_GET['show'])) ? $this->data->get('show') : ''?>">
				<input type="hidden" name="sort" value="<?=(isset($_GET['sort'])) ? $this->data->get('sort') : ''?>">
				<input type="hidden" name="per_page" value="<?=(isset($_GET['per_page'])) ? $this->data->get('per_page') : ''?>">
				<?php 
				$count = 1;
				foreach ($filters as $filter) { ?>

				<div class="panel-group" id="accordion-v<?= $count?>">
					<div class="panel panel-default">
						<div class="panel-heading">
							<h2 class="panel-title">
								<a data-toggle="collapse" data-parent="#accordion" href="#collapse<?= $count?>">
									<?=$filter->name?>
									<i class="fa fa-angle-down"></i>
								</a>
							</h2>
						</div>
						<div id="collapse<?= $count?>" class="panel-collapse collapse in">
							<div class="panel-body">
								<ul class="list-unstyled checkbox-list">
									<?php foreach ($filter->values as $value) {
										$checked = '';
										if(isset($_GET[$filter->alias]) && is_array($_GET[$filter->alias]) && in_array($value->id, $_GET[$filter->alias])) $checked = 'checked';
										?>
										<li>
											<label class="checkbox">
												<input type="checkbox" name="<?=$filter->alias?>[]" value="<?=$value->id?>" <?=$checked?> />
												<i></i>
												<?=$value->name?>
												<small><a href="<?=$this->data->get_link($filter->alias.'[]', $value->id)?>">(<?=$value->count?>)</a></small>
											</label>
										</li>
									<?php } ?>
								</ul>
							</div>
						</div>
					</div>
				</div>
				<?php $count++; } ?>
				<button type="submit" class="btn-u btn-brd btn-brd-hover btn-u-lg btn-u-sea-shop btn-block"><?=$this->text('Відфільтрувати')?></button>
			</form>
		</div>
		<?php } ?>

		<div class="col-md-<?= empty($subgroups) && $filterExists ? '10' : '12' ?>">
			<div class="row margin-bottom-5">
				<div class="col-sm-4 result-category">
					<h2><?=$this->text('Знайдено')?></h2>
					<small class="shop-bg-red badge-results">
						<?php
						if(isset($_SESSION['option']->paginator_total))
						{
							echo($_SESSION['option']->paginator_total);
							if($_SESSION['option']->paginator_total == 0) echo('');
							elseif($_SESSION['option']->paginator_total == 1) echo $this->text(' товар');
							elseif($_SESSION['option']->paginator_total > 1 && $_SESSION['option']->paginator_total < 5) echo $this->text(' товари');
							else echo $this->text(' товарів');
						} else echo(0);
						?>
					</small>
				</div>
				<div class="col-sm-8">
					<ul class="list-inline clear-both">
						<li class="grid-list-icons">
							<a href="<?=$this->data->get_link('show')?>"><i class="fa fa-th"></i></a>
							<a href="<?=$this->data->get_link('show', 'list')?>"><i class="fa fa-th-list"></i></a>
						</li>
						<li class="sort-list-btn">
							<h3>Сортувати за :</h3>
							<div class="btn-group">
								<?php $sort = array('' => 'Авто', 'price_up' => 'Ціна ↑', 'price_down' => 'Ціна ↓'); ?>
								<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
									<?=(isset($_GET['sort'])) ? $sort[$_GET['sort']] : $sort['']?> <span class="caret"></span>
								</button>
								<ul class="dropdown-menu" role="menu">
									<?php foreach ($sort as $key => $value) { ?>
										<li><a href="<?=$this->data->get_link('sort', $key)?>"><?=$value?></a></li>
									<?php } ?>
								</ul>
							</div>
						</li>
						<?php /* ?>
						<li class="sort-list-btn">
							<h3>Кількість :</h3>
							<div class="btn-group">
								<?php $sort = array('' => 30, 20 => 20, 10 => 10); ?>
								<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
									<?=(isset($_GET['per_page'])) ? $sort[$_GET['per_page']] : 30?> <span class="caret"></span>
								</button>
								<ul class="dropdown-menu" role="menu">
									<?php foreach ($sort as $key => $value) { ?>
										<li><a href="<?=$this->data->get_link('per_page', $key)?>"><?=$value?></a></li>
									<?php } ?>
								</ul>
							</div>
						</li>
						*/ ?>
					</ul>
				</div>
			</div><!--/end result category-->

			<div class="filter-results">
				<?php if(isset($_GET['show']) && $_GET['show'] == 'list' && !empty($products)) { foreach ($products as $product) { ?>
					<div class="list-product-description product-description-brd margin-bottom-5">
						<div class="row">
							<?php if($product->photo != '') { ?>
								<div class="col-sm-4">
									<a href="<?=SITE_URL.$product->link?>">
										<img class="img-responsive sm-margin-bottom-20" src="<?=IMG_PATH.$product->m_photo?>" alt="<?=$product->article?> <?=$product->name?>">
									</a>
								</div>
							<?php } ?>
							<div class="col-sm-<?=($product->photo != '')?8:12?> product-description">
								<div class="overflow-h margin-bottom-5">
									<ul class="list-inline overflow-h">
										<li><h4 class="title-price"><a href="<?=SITE_URL.$product->link?>"><?= str_replace($product->article, '', $product->name) ?></a></h4></li>
										<?php if(isset($product->options['4-brend'])) {?>
										<li><span class="gender text-uppercase"><?= $product->options['4-brend']->value ?></span></li>
										<?php } ?>
									</ul>
									<div class="margin-bottom-10">
										<span class="title-price margin-right-10"><?=$product->price?> грн.</span><br>
										<?php if($product->old_price != 0) { ?>
										<span class="title-price shop-red line-through"><?= $product->old_price ?> грн.</span>
										<?php } ?>
									</div>
									<p class="margin-bottom-20"><?=$product->list?></p>
									<a href="<?=SITE_URL.$product->link?>" class="btn-u btn-u-sea-shop"><?=$this->text('Детальніше')?></a>
								</div>
							</div>
						</div>
					</div>
				<?php } } else { ?>
					<div id="productRow">
						<div class="row illustration-v2 margin-bottom-5">
							<?php
							$i = 0;
							if(!empty($products))
							foreach ($products as $product) {
								if($i % 4 == 0 && $i != 0)
								{
									echo('</div>');
									echo('<div class="row illustration-v2 margin-bottom-5">');
								}
							?>
								<div class="col-md-3 padding-left-5 padding-right-5">
									<div class="product-full-brd">
										<div class="product-img product-img-brd">
											<?php if(!empty($product->m_photo)) { ?>
												<a href="<?=SITE_URL.$product->link?>">
													<img class="full-width img-responsive" src="<?=IMG_PATH.$product->m_photo?>" alt="<?=$product->article?> <?=$product->name?>">
												</a>
											<?php } if($product->old_price != 0) { ?>
											<div class="shop-rgba-red rgba-banner line-through"><?= $product->old_price ?> грн</div>
											<!-- Все, що додано менш ніж 2 тижні тому - новинка -->
											<?php } else if (($product->date_add + 1209600) > time()) { ?>
												<div class="shop-rgba-dark-green rgba-banner">Новинка!</div>
											<?php } ?>

										</div>
										<div class="product-description product-description-brd margin-bottom-5">
											<div class="overflow-h margin-bottom-5">
												<div class="">
													<h4 class="title-price product-name-overflow"><a href="<?=SITE_URL.$product->link?>" title="<?=$product->name?>"><?= str_replace($product->article, '', $product->name) ?></a></h4>
												</div>
												<div class="product-price">
													<span class="title-price"><?=$product->price?> грн</span>
												</div>
											</div>
										</div>
									</div>
								</div>
							<?php
								$i++;
							}
							?>
						</div>
					</div>
				<?php } ?>
			</div>
			<?php  if(isset($_SESSION['option']->paginator_total) && $_SESSION['option']->paginator_total > 20 && empty($subgroups)) {?>
			<div class="text-center">
				<button class="btn-u btn-u-sea-shop btn-u-lg" onclick="showMore(2, <?= isset($group) ? $group->id : 0 ?>, '<?=(isset($_GET['sort'])) ? $_GET['sort'] : ''?>')" id="showMore">
		            <?=$this->text('Показати наступні 20 товарів')?>
		            <i class="fa fa-chevron-down " aria-hidden="true"></i>
		        </button>
			</div>
	        <?php } else if(!empty($subgroups)) { ?>
	        <div class="text-center">
				<?php
		        $this->load->library('paginator');
		        $this->paginator->ul_class = 'pagination pagination-v2';
		        echo $this->paginator->get();
		        ?>
			</div>
			<?php } ?>
		</div>
	</div>
</div>

<?php
	$_SESSION['alias']->js_load[] = 'js/catalog.js?v=1.11';
?>
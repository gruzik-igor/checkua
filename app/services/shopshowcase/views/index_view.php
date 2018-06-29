<div class="container content">
	<div class="row" style="margin-top: 120px">
			<?php
			if(!empty($groups))
			{
				$i = 0;
				echo('<div class="row illustration-v2 margin-bottom-30">');
				foreach ($groups as $g) {
					if($i % 4 == 0)
					{
						echo('</div>');
						echo('<div class="row illustration-v2 margin-bottom-30">');
					}
			?>
					<div class="col-md-3">
						<div class="product-img product-img-brd">
							<a href="<?=SITE_URL.$g->link?>">
								<?php if($g->photo) {?>
								<img class="full-width img-responsive" src="<?=IMG_PATH.$g->mmg_photo?>" alt="<?=$g->name .' '. SITE_NAME?>">
								<?php } ?>
							</a>
							<a class="product-review" href="<?=SITE_URL.$g->link?>"><h4 style="padding: 5px 0 40px"><?=$g->name?></h4></a>
						</div>
					</div>
			<?php
					$i++;
				}
				echo('</div>');
			} else if(!empty($subgroups))
			{
				$i = 0;
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
				echo('</div>');
			}
			else { ?>
			<div class="col-md-3 filter-by-block md-margin-bottom-60">
			<h1>Фільтр</h1>
			<form>
			<input type="hidden" name="show" value="<?=(isset($_GET['show'])) ? $this->data->get('show') : ''?>">
			<input type="hidden" name="sort" value="<?=(isset($_GET['sort'])) ? $this->data->get('sort') : ''?>">
			<input type="hidden" name="per_page" value="<?=(isset($_GET['per_page'])) ? $this->data->get('per_page') : ''?>">
			<?php $filters = false;
			if(isset($group))
			 	$filters = $this->shop_model->getOptionsToGroup($group->id);

			if($filters)
			{
				foreach ($filters as $filter) { usort($filter->values, function($a, $b) { return strcmp($a->name, $b->name); }); if(!empty($filter->values)) {
			?>
			<div class="panel-group" id="accordion">
				<div class="panel panel-default">
					<div class="panel-heading">
						<h2 class="panel-title">
							<a data-toggle="collapse" data-parent="#accordion" href="#collapseOne">
								<?=$filter->name?>
								<i class="fa fa-angle-down"></i>
							</a>
						</h2>
					</div>
					<div id="collapseOne" class="panel-collapse collapse in">
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
			<?php } } } ?>

			<button type="submit" class="btn-u btn-brd btn-brd-hover btn-u-lg btn-u-sea-shop btn-block">Відфільтрувати</button>
			</form>
		</div>

		<div class="col-md-9">
			<div class="row margin-bottom-5">
				<div class="col-sm-4 result-category">
					<h2>Знайдено</h2>
					<small class="shop-bg-red badge-results">
						<?php
						if(isset($_SESSION['option']->paginator_total))
						{
							echo($_SESSION['option']->paginator_total);
							if($_SESSION['option']->paginator_total == 0) echo('');
							elseif($_SESSION['option']->paginator_total == 1) echo(' товар');
							elseif($_SESSION['option']->paginator_total > 1 && $_SESSION['option']->paginator_total < 5) echo(' товари');
							else echo(' товарів');
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
										<li><h4 class="title-price"><a href="<?=SITE_URL.$product->link?>"><?= $product->name ?></a></h4></li>

									</ul>
									<div class="margin-bottom-10">
										<span class="title-price margin-right-10"><?=$product->price?> грн.</span><br>
										<?php if($product->old_price != 0) { ?>
										<span class="title-price shop-red line-through"><?= $product->old_price ?> грн.</span>
										<?php } ?>
									</div>
									<p class="margin-bottom-20"><?=$product->list?></p>
									<a href="<?=SITE_URL.$product->link?>" class="btn-u btn-u-sea-shop">Детальніше</a>
								</div>
							</div>
						</div>
					</div>
				<?php } } else { ?>
					<div class="row illustration-v2 margin-bottom-30">
						<?php
						$i = 0;
						if(!empty($products))
						foreach ($products as $product) {
							if($i % 3 == 0)
							{
								echo('</div>');
								echo('<div class="row illustration-v2 margin-bottom-5">');
							}
						?>
							<div class="col-md-4">
								<div class="product-full-brd">
									<div class="product-img product-img-brd">
										<?php if($product->photo != '') { ?>
											<a href="<?=SITE_URL.$product->link?>">
												<img class="full-width img-responsive" src="<?=IMG_PATH.$product->m_photo?>" alt="<?=$product->article?> <?=$product->name?>">
											</a>
										<?php } ?>
										<!-- Все, що додано менш ніж 2 тижні тому - новинка -->
										<?php if (($product->date_add + 1209600) > time()) { ?>
											<div class="shop-rgba-dark-green rgba-banner">Новинка!</div>
										<?php } ?>
									</div>
									<div class="product-description product-description-brd margin-bottom-30">
										<div class="overflow-h margin-bottom-5">
											<div class="">
												<h4 class="title-price"><a href="<?=SITE_URL.$product->link?>"><?= str_replace($product->article, '', $product->name) ?></a></h4>
											</div>
											<div class="product-price">
												<span class="title-price"><?=$product->price?> грн.</span>
												<?php if($product->old_price != 0) { ?>
												<span class="title-price shop-red line-through"><?= $product->old_price ?> грн.</span>
												<?php } ?>
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
				<?php } ?>
			</div>
			<?php } ?>
		</div>
	</div>
</div>

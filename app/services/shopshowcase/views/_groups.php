<div class="row">
	<div class="filter-results">
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
						<a href="<?=SITE_URL.$group->link?>"><img class="full-width img-responsive" src="<?=IMG_PATH.$group->g_photo?>" alt="<?=$group->name .' '. SITE_NAME?>"></a>
						<a class="product-review" href="<?=SITE_URL.$group->link?>"><?=$group->name?></a>
						<a class="add-to-cart" href="<?=SITE_URL.$group->link?>"><i class="fa fa-shopping-cart"></i>Детальніше</a>
					</div>
				</div>
		<?php
				$i++;
			}
			echo('</div>');
		}
		?>
	</div><!--/end filter resilts-->

	<div class="text-center">
		<?php
        $this->load->library('paginator');
        echo $this->paginator->get();
        ?>
	</div><!--/end pagination-->
</div><!--/end row-->

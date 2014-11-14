<!-- ========== Gallery ========== -->
<div class="gallery bg-1">
	<div class="container">
	<h3><?=$_SESSION['alias']->name?></h3>
	<?php 
		echo html_entity_decode($_SESSION['alias']->text);
		if(isset($articles)){
	?>
		<ul class="row thumbs">
			<?php foreach($articles as $article){ ?>
				<li class="grid_6">
					<div class="box">
					  <div class="maxheight1">
					    <div class="gallery_block">
					    	<?php if($article->photo > 0) { ?>
					    		<a href="images/gallery/page-3_img-1_b.jpg" class="thumbs_img">
							        <div>
							          	<div class="lbHover"><div></div></div>
							          	<img src="<?=IMG_PATH.$_SESSION['option']->folder.'/s_'.$article->photo?>.jpg" alt="<?=$article->name?>">
							        </div>
								</a>
							<?php } ?>       
					      <div class="gallery_text">
					        <p class="p__title"><?=$article->name?></p>
					        <p><?=mb_substr(strip_tags(html_entity_decode($article->text, ENT_QUOTES, 'utf-8')), 0, 367, 'utf-8')?>..</p>
					        <div class="btn_wrapper">
					          <a href="<?=SITE_URL.$_SESSION['alias']->alias.'/'.$article->link?>" class="btn">
					            <div class="btn_slide_wrapper">
					              <div class="btn_main">Детальніше</div>
					              <div class="btn_slide"><div>Детальніше</div></div>
					            </div>
					          </a>
					        </div>
					      </div>
					    </div>
					  </div>
					</div>
				</li>
			<?php } ?>
		</ul>
		<?php
		if(isset($_SESSION['option']->count_all_articles) && $_SESSION['option']->count_all_articles > count($articles)){
			$pages = (integer)($_SESSION['option']->count_all_articles / $_SESSION['option']->PerPage);
			if($_SESSION['option']->count_all_articles % $_SESSION['option']->PerPage > 0) $pages++;
			$page = 0;
			if(isset($_GET['page']) && is_numeric($_GET['page']) && $_GET['page'] > 1) $page = $_GET['page'] - 1;
			if($pages > 1){ ?>
				<!--Pagination-->
	            <ul id="pagination-digg">
			        <li class="previous<?=($page == 0)?'-off':''?>">
			        	<?php 
			        	if($page == 1) echo '<a href="'.SITE_URL.$_GET['request'].'">';
			        	elseif($page > 1) echo '<a href="'.SITE_URL.$_GET['request'].'?page='.$page.'">';
			        	echo "Попередня";
			        	if($page > 0) echo "</a>";
			        	?>
			        </li>
					<?php for($i = 0; $i < $pages; $i++){
						if($i == $page) echo '<li class="active">'.($i + 1).'</li>';
						elseif($i == 0) echo '<li><a href="'.SITE_URL.$_GET['request'].'">1</a></li>';
						else echo '<li><a href="'.SITE_URL.$_GET['request'].'?page='.($i + 1).'">'.($i + 1).'</a></li>';
					} $pages--; ?>
					<li class="next<?=($page == $pages)?'-off':''?>">
						<?php
						if($page < $pages) echo '<a href="'.SITE_URL.$_GET['request'].'?page='.($page+2).'">';
			        	echo "Наступна";
			        	if($page < $pages) echo "</a>";
			        	?>
					</li>
				</ul> 
	            <!--/End Pagination--> 
	            <div class="clear"> </div>
		
			<?php }
		}
	}
?>
	</div>
</div>



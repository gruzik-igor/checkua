<?php 
if(isset($articles)){
	
	$url = $this->data->url();
	$url = implode('/', $url);

	foreach ($articles as $el) { 
		?>		
        <div class="post type-post status-publish format-standard hentry category-poxodi-karpatami">
			<h2 class="entry-title"><a href="<?=SITE_URL.$url.'/'.$el->link?>" title="<?=$el->name?>" rel="bookmark"><?=$el->name?></a></h2>
			<table style="background:#fff; " width="100%">
				<tr>
					<?php if($el->photo != ''){ ?>
						<td valign="top" style="padding:10px;width:360px">
							<div class="featured-image-blog">
								<img src="<?=IMG_PATH.$_SESSION['option']->folder?>/s_<?=$el->photo?>.jpg" class="attachment-post-thumbnail colorbox-961 " alt="<?=$el->name?>" />	
							</div>			
						</td>
					<?php } ?>
					<td valign="top" style="padding:10px;padding-left: 0px;width:350px">
						<div class="entry-summary" >
							<?php echo mb_substr( strip_tags( html_entity_decode($el->text, ENT_QUOTES, 'utf-8') ), 0, 300, 'utf-8') ?>
							<a href="<?=SITE_URL.$url.'/'.$el->link?>" style="color:#f1a655;">Читати повністю <span class="meta-nav">→</span></a>		
						</div>
					</td>
				</tr>
			</table>
	    </div>
	<?php	}

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

} ?>

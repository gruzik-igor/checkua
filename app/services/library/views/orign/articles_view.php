<?php 
	echo html_entity_decode($_SESSION['alias']->text);
	if(isset($articles)){
		foreach($articles as $article){ ?>
			<div class="inside">
				<div class="f-left offset">
					<div class="date"><?=date("d.m.y", $article->date)?></div>
				</div>
			<div class="f-left news">
				<span>
					<h2><a href="<?=SITE_URL.$_SESSION['alias']->alias.'/'.$article->link?>"><?=$article->name?></a></h2>
				</span>
				<p><?=mb_substr(strip_tags(html_entity_decode($article->text, ENT_QUOTES, 'utf-8')), 0, 367, 'utf-8')?>..</p>
			</div>
			</div>	
			<?php if($article->photo > 0) { ?>
				<img src="<?=IMG_PATH.$_SESSION['option']->folder.'/s_'.$article->photo?>.jpg" alt="<?=$article->name?>" />
			<?php } ?>  
		<?php }
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
<link rel="stylesheet" href="<?=SITE_URL?>style/index.css">

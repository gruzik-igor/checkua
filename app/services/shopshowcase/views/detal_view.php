<!-- ========== Blog ========== -->
<div class="blog">
  	<div class="container">
  		<?php if($this->userCan($_SESSION['alias']->alias)){ $url = implode('/', $this->data->url()); ?>
			<a href="<?=SITE_URL.'admin/'.$url?>" style="float:right">Редагувати</a>
		<?php } ?>
	    <h3>
	    	<?php if(isset($product)){ ?>
	    		<a href="<?=SITE_URL.$_SESSION['alias']->alias?>"><?=$product->alias_name?></a> -> 
	    		<?php if(!empty($product->parents)){
	    			$link = SITE_URL.$_SESSION['alias']->alias;
	    			foreach ($product->parents as $parent) { $link .= '/'.$parent->link; ?>
						<a href="<?=$link?>"><?=$parent->name?></a> -> 
	    	<?php } } }?>
	    	<?=$_SESSION['alias']->name?>
	    </h3>
		<div class="post">
			<?php if($product->photo > 0){ ?>
	      		<a href="<?=IMG_PATH.$_SESSION['option']->folder?>/<?=$product->photo?>.jpg" rel="group">
	      			<img src="<?=IMG_PATH.$_SESSION['option']->folder?>/s_<?=$product->photo?>.jpg" alt="<?=$product->name?>">
	      		</a>
	      	<?php } ?>
	      	<div class="post_text">
	        	<p class="p__title"><?=$product->name?></p>
		        <div class="post_info">
		        	<p><strong>Ціна: <?=$product->price?> грн</strong></p>
					<time pubdate title="Опубліковано"><?=date('d.m.Y H:i', $product->date)?></time>
					<!-- <p class="post_author">post by <a href="#">Tom</a></p> -->
		        </div>
		        <div class="post_info">
					<?php 
						if(!empty($product->options)){
							foreach ($product->options as $option) {
								echo("<p>{$option->name}: {$option->value} {$option->sufix}</p>");
							}
						} 
					?>
		        </div>
		        <div class="post_info">
		        	Наявність: <span style="color:<?=$product->availability_color?>"><?=$product->availability_name?></span>
		        </div>
		        <p><?php echo html_entity_decode($_SESSION['alias']->text, ENT_QUOTES, 'utf-8') ?></p>
	      	</div>
	      	<div style="clear: both"></div>
	    </div>
  	</div>
</div>

<script type="text/javascript" src="<?=SITE_URL?>assets/fancybox/jquery.fancybox-1.3.4.pack.js"></script>
<link rel="stylesheet" href="<?=SITE_URL?>assets/fancybox/jquery.fancybox-1.3.4.css" type="text/css" media="screen" />
<script type="text/javascript" src="<?=SITE_URL?>assets/fancybox/jquery.easing-1.3.pack.js"></script>
<script type="text/javascript" src="<?=SITE_URL?>assets/fancybox/jquery.mousewheel-3.0.4.pack.js"></script>
<script type="text/javascript">
	$("a[rel=group]").fancybox({
		'transitionIn'		: 'elastic',
		'transitionOut'		: 'elastic',
		'titlePosition' 	: 'over',
		'speedIn'		:	600, 
		'speedOut'		:	200, 
		'titleFormat'		: function(title, currentArray, currentIndex, currentOpts) {
			return '<span id="fancybox-title-over">Image ' + (currentIndex + 1) + ' / ' + currentArray.length + (title.length ? ' &nbsp; ' + title : '') + '</span>';
		}
	});
</script>
<h1><?=$_SESSION['alias']->name?></h1>

<?php 
	echo html_entity_decode($_SESSION['alias']->text);
	
?>
<div class="zagal blockHeight" >
	<div  class="f-left categ">
		<a href="<?=SITE_URL.$_SESSION['alias']->alias?>">Всі</a>     
	</div>		                
</div>
<?php

	if(isset($categories)){
		foreach ($categories as $category) { ?>
			<div class="zagal blockHeight" >
			    <div  class="f-left categ">
			    	<a href="<?=SITE_URL.$_SESSION['alias']->alias.'/'.$category->link?>"><?=$category->name?></a>     
			    </div>		                
			</div>
		<?php }
	}
?>
 
<div class="clear"> </div>

<?php require_once('articles_view.php'); ?>
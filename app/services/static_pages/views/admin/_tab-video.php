<div class="row">
	<div class="col-md-3"></div>
	<div class="col-md-6">
		<form method="post" action="<?=SITE_URL?>admin/wl_video/save">
			<input type="hidden" name="alias" value="<?=$_SESSION['alias']->id?>">
			<input type="hidden" name="content" value="0">
			<div class="input-group">
		        <span class="input-group-addon">Додати відео:</span>
		        <input type="text" name="video" placeholder="Адреса відеозапису" class="form-control" required>
				<div class="input-group-btn">
					<button type="submit" class="btn btn-primary">Додати</button>
				</div>
			</div>
		</form>
		<center>Підтримуються сервіси youtu.be, youtube.com, vimeo.com</center>
	</div>
</div>

<?php
 	if(!empty($article->videos)) { 
		foreach($article->videos as $video){ ?>
			<div class="f-left center video">
				<?php if($video->site == 'youtube'){ ?>
					<a href="https://www.youtube.com/watch?v=<?=$video->link?>">
						<img src="https://img.youtube.com/vi/<?=$video->link?>/mqdefault.jpg">
					</a>
				<?php } elseif($video->site == 'vimeo'){ 
					$vimeo = false;
					@$vimeo = unserialize(file_get_contents("http://vimeo.com/api/v2/video/$video->link.php"));
					if($vimeo){
					?>
					<a href="https://vimeo.com/<?=$video->link?>">
						<img src="<?=$vimeo[0]['thumbnail_large']?>">
					</a>
				<?php } } ?>
				<a href="<?=SITE_URL?>admin/wl_video/delete?id=<?=$video->id?>" class="<?=$_SESSION['alias']->alias?>">Видалити</a>
			</div>
<?php 	} } ?>

<div style="clear:both"></div>

<style type="text/css">
	.video {
		width: 380px;
		padding: 10px;
	}
	.video img {
		width: 380px;
		height: 214px;
	}
	.f-left {
		float: left;
	}
	.center {
		text-align: center;
	}
</style>
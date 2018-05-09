<?php if($comments) { ?>

<div class="reviews-block mb-4">
	<h5 class="reviews-block-title font-weight-bold mb-3" id="reviews-airport">Reviews <?=$_SESSION['alias']->name?></h5>

	<?php if(!empty($_SESSION['notify']->success)): ?>
	    <div id="comment_add_success" class="alert alert-success">
	        <span class="close" data-dismiss="alert">×</span>
	        <i class="fa fa-check fa-2x pull-left"></i>
	        <h4><?=(isset($_SESSION['notify']->title)) ? $_SESSION['notify']->title : 'Success!'?></h4>
	        <p><?=$_SESSION['notify']->success?></p>
	    </div>
	<?php endif; ?>

	<ul class="reviews-list list-unstyled mb-0">
		<?php foreach ($comments as $comment) { ?>
			<li class="row" id="comment-<?=$comment->id?>">
				<div class="col-12 col-xl-2">
					<?php for($i = 0; $i < $comment->rating; $i++) { ?>
						<i class="fa fa-star" aria-hidden="true"></i>
					<?php } for($i = $comment->rating; $i < 5; $i++) { ?>
						<i class="fa fa-star-o" aria-hidden="true"></i>
					<?php } ?>
				</div>
				<div class="col-12 col-xl-10">
					<div class="reviews-author-date mb-2">
						<span><?=$comment->user_name?></span>
						<time datetime="2017-11-30 05:14"><?=date('F j, Y \a\t g:i a', $comment->date_add)?></time>
					</div>
					<p class="reviews-article mb-0">
						<?=nl2br($comment->comment)?>
						<?php if($comment->images) {
							echo('<p>');
							$comment->images = explode('|||', $comment->images);
							foreach ($comment->images as $image) {
								echo '<a href="'.IMG_PATH.'comments/'.$comment->id.'/'.$image.'" rel="lightbox"><img src="'.IMG_PATH.'comments/'.$comment->id.'/m_'.$image.'" alt="'.$_SESSION['alias']->name.'"></a>';
							}
							echo('</p>');
						} ?>
					</p>
					<?php $this->wl_comments_model->paginator = false;
					if($replays = $this->wl_comments_model->get(array('parent' => $comment->id, 'status' => '<3')))
						foreach ($replays as $replay) { ?>
							<hr>
							<div class="reviews-author-date mb-2">
								<span><?=$replay->user_name?></span>
								<time datetime="2017-11-30 05:14"><?=date('F j, Y \a\t g:i a', $replay->date_add)?></time>
							</div>
							<p class="reviews-article mb-0">
								<?=nl2br($replay->comment)?>
							</p>
					<?php } ?>
				</div>
			</li>
		<?php } ?>
	</ul>
</div>

<?php } ?>
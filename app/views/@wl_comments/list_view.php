<?php if($comments) {
	$count = 0;
	$_SESSION['alias']->js_load[] = 'assets/white-lion/comment-reviews.js';
?>

<div id="hotel-reviews">

    <div class="guest-reviews">
        <h2>Отзывы</h2>

        <?php foreach ($comments as $comment) { $count++; ?>

			<div class="guest-review table-wrapper" <?= ($count > 5) ? 'hidden' : '' ?>>
				<div class="col-xs-3 col-md-2 author table-cell">
					<p class="name"><?= $comment->name?></p>
					<p class="date"><?=date("d.m.Y H:i",$comment->date_add)?></p>
				</div>
				<div class="col-xs-9 col-md-10 table-cell comment-container">
					<div class="comment-content">
						<?=$comment->comment?>
					</div>

					<?php $reply = $this->db->getQuery("SELECT * FROM comments_reply WHERE comment = $comment->id");
					if($reply) { ?>
						<hr>
						<div class="answer comment-header clearfix">
							<h4 class="comment-title">Администрация <?=date("d.m.Y H:i",$reply->date_add)?></h4>
						</div>
						<div class="comment-content">
							<?=$reply->reply?>
						</div>
					<?php }
					if($this->userCan()) { ?>
						<div style="clear:both">
							<br>
							<button id="reply" onclick="reply(<?= $comment->id?>)">Ответить</button>
							<div id="replyBlock_<?= $comment->id?>" hidden>
								<form action="<?=SITE_URL?>comments/reply" method="POST">
									<input type="hidden" name="id" value="<?= $comment->id?>">
									<textarea name="reply" id="" cols="50" rows="5" style="margin: 10px 0 10px 0"></textarea><br>	
									<input type="submit" value="Отправить">
								</form>
								
							</div>
						</div>
					<?php } ?>

				</div>
			</div>

		<?php } ?>

    </div>

    <?php if($count >= 6) { ?>
    	<a class="button full-width btn-large show_feedbacks"><?=$this->text('Завантажити більше', 0)?></a>
    <?php } ?>

</div>

<?php } ?>
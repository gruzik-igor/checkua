<div id="hotel-write-review form">
    <form action="<?=SITE_URL?>comments/add" method="POST" class="review-form">
    	<input type="hidden" name="content" value="<?= $_SESSION['alias']->content?>">
 		<input type="hidden" name="alias" value="<?= $_SESSION['alias']->id?>">

        <div class="form-group col-md-5 no-float no-padding">
			<h4 class="title">Автор</h4>
            <input type="text" name="name" class="input-text full-width important" value="" placeholder="Ваше ім'я" required />
        </div>
        <div class="form-group">
            <h4 class="title">Відгук</h4>
            <textarea name="comment" class="input-text full-width important" placeholder="Ваш відгук (мінімум 200 символів)" rows="5" required></textarea>
        </div>
        <?php $this->load->library('recaptcha');
        $this->recaptcha->form(); ?>
        <div class="form-group col-md-5 no-float no-padding no-margin">
            <button type="submit" class="btn-large full-width submitBtn">Додати відгук</button>
        </div>   
    </form>
</div>

<?php $_SESSION['alias']->js_load[] = 'assets/white-lion/comment-add.js'; ?>
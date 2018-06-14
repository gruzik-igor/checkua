<?php if(!empty($_SESSION['notify']->errors)) { ?>
   <div id="comment_add_error" class="alert alert-danger">
        <span class="close" data-dismiss="alert">×</span>
        <h4><?=(isset($_SESSION['notify']->title)) ? $_SESSION['notify']->title : 'Error!'?></h4>
        <p><?=$_SESSION['notify']->errors?></p>
    </div>
<?php } unset($_SESSION['notify']); ?>

<div class="add-your-review mb-3 mb-md-5">
    <h5 class="reviews-block-title font-weight-bold mb-3" id="addreview">Add your own review</h5>
    <form action="<?=SITE_URL?>comments/add" method="POST" class="review-form" enctype="multipart/form-data">
        <input type="hidden" name="content" value="<?= $content?>">
        <input type="hidden" name="alias" value="<?= $alias?>">
        <input type="hidden" name="image_name" value="<?= $image_name?>">

        <div class="form-group row">
            <div class="col-md-6 rating">
                <label for="rating" class="mb-0 align-middle">Rating</label>
                <div class="d-inline-block align-middle">
                    <span><input type="radio" name="rating" id="str5" value="5"><label for="str5"></label></span>
                    <span><input type="radio" name="rating" id="str4" value="4"><label for="str4"></label></span>
                    <span><input type="radio" name="rating" id="str3" value="3"><label for="str3"></label></span>
                    <span><input type="radio" name="rating" id="str2" value="2"><label for="str2"></label></span>
                    <span><input type="radio" name="rating" id="str1" value="1"><label for="str1"></label></span>
                </div>
            </div>
            <div class="col-md-6 text-md-right mt-2 mt-md-0">
                <label class="image-review-style mb-0" for="image-review">Choose a image <i class="fa fa-download" aria-hidden="true"></i></label>
                <input type="file" name="images[]" accept="image/jpg,image/jpeg,image/png" multiple id="image-review">
            </div>
        </div>
        <div class="form-group">
            <textarea class="form-control rounded-0" name="comment" id="review-text" rows="6" placeholder="Review" required><?=$this->data->re_post('comment')?></textarea>
        </div>
        <?php if($this->userIs()) { ?>
            <div class="form-group row">
                <div class="col-12 col-md-4 mt-md-0 mt-3">
                    <input class="review-btn btn w-100 rounded-0" type="submit" value="Add review">
                </div>
            </div>
        <?php } else { ?>
            <div class="form-group row">
                <?php $this->load->library('recaptcha');
                    $this->recaptcha->form(); ?>
            </div>
            <div class="form-group row">
                <div class="col-6 col-md-4">
                    <input class="form-control rounded-0" type="text" name="name" placeholder="Name*" value="<?=$this->data->re_post('name')?>" required>
                </div>
                <div class="col-6 col-md-4">
                    <input class="form-control rounded-0" type="email" name="email" placeholder="Email*" value="<?=$this->data->re_post('email')?>" required>
                </div>
                <div class="col-12 col-md-4 mt-md-0 mt-3">
                    <input class="review-btn btn w-100 rounded-0" type="submit" value="Add review">
                </div>
            </div>
        <?php } ?>
    </form>
</div>
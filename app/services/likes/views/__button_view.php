<?php 
if(isset($_SESSION['alias']->alias_from) && $_SESSION['alias']->alias_from != $_SESSION['alias']->id)
    $_SESSION['alias-cache'][$_SESSION['alias']->alias_from]->alias->js_load[] = 'js/'.$_SESSION['alias']->alias.'/likes.js';
else
    $_SESSION['alias']->js_load[] = 'js/'.$_SESSION['alias']->alias.'/likes.js';
?>
<link href="<?=SERVER_URL?>assets/font-awesome/css/font-awesome.min.css" rel="stylesheet" />
<?php if($userLike == 1) { ?>
    <style type="text/css">
        .fa-heart {
            color: red;
        }
    </style>
<?php } ?>
<button type="button" style="border:none; background: rgba(0,0,0,0)" onclick="setLike(<?=$alias?>, <?=$content?>)">
    <i id="pageLikesFavicon" class="fa fa-heart"></i>
</button><span id="pageLikesCount"> <?=$likes?> </span>
 
<script type="text/javascript">
    LIKE_URL = '<?=SERVER_URL.$_SESSION['alias']->alias?>/setlike';
    LIKE_ERROR_USER_NOT_LOGIN = '<?=$this->text('Please login', 0)?>';
</script>
<!--=== Content Part ===-->
<div class="content container">
<?php
if($groups)
{
    require '_groups.php';
}
else
{
    require '_products.php';
}
?>
</div><!--/end container-->
<!--=== End Content Part ===-->
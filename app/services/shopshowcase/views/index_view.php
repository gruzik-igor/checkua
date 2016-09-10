<!--=== Content Part ===-->
<div class="content container">
<?php
if($products)
{
    require '_products.php';
}
else
{
    require '_groups.php';
}
?>
</div><!--/end container-->
<!--=== End Content Part ===-->
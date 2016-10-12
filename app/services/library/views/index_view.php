<div class="container">
    <h1><?=$_SESSION['alias']->alias?> index page</h1>
    <h4>$articles:</h4>
    <pre><?php print_r($articles); ?></pre>
    <?php if($groups) { ?>
        <h4>$groups:</h4>
        <pre><?php print_r($groups); ?></pre>
    <?php } ?>
    <h4>$_SESSION['alias']:</h4>
    <pre><?php print_r($_SESSION['alias']); ?></pre>
</div>
<pre>
    <h1><?=$_SESSION['alias']->alias?>: ShopShowCase product detal view</h1>
    <?php
    echo("<h2>Product</h2>");
    if($product)
        print_r($product);
    else
        echo("not set");
    ?>
</pre>
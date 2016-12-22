<pre>
	<h1><?=$_SESSION['alias']->alias?>: ShopShowCase groups view</h1>
	<?php
	echo("<h2>Products</h2>");
	if($products)
		print_r($products);
	else
		echo("not set");
	if(isset($subgroups))
	{
		echo("<h2>SubGroups</h2>");
		print_r($subgroups);
	}
	?>
</pre>
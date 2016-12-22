<pre>
	<h1><?=$_SESSION['alias']->alias?>: ShopShowCase index view</h1>
	<?php
	echo("<h2>Products</h2>");
	if($products)
		print_r($products);
	else
		echo("not set");
	if(isset($groups))
	{
		echo("<h2>Groups</h2>");
		print_r($groups);
	}
	?>
</pre>
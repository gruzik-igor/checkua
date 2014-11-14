<?php 

$localhost = $_SERVER['SERVER_NAME'];
if($localhost)
{
	$uri = explode("/", $_SERVER['REQUEST_URI']);
	if($uri[1] != '')
	{
		$LOCAL_SITE_URL = $uri[1]."/";
	}
}

?>
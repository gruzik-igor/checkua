<?php

if($_SERVER['SERVER_NAME'] == "localhost")
{
	if($LOCAL_SITE_URL != '') $LOCAL_SITE_URL = substr($LOCAL_SITE_URL, 0, -1);
	if($uri[1] != '' && $uri[1] != $LOCAL_SITE_URL)
	{
		if(empty($_POST))
		{
			require "form.php";
			require APP_PATH.'views/notify_view.php';
			exit ();
		} 
		elseif(isset($_POST['do']) && $_POST['do'] == 1)
		{
			$handle = fopen(getcwd().DIRSEP."LOCAL_SITE_URL.php", "w+");
			$text = "<?php \$LOCAL_SITE_URL = '{$uri[1]}/'; ?>";
		 	fwrite($handle, $text);
		}
	}
}

?>
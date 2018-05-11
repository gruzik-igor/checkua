<?php 

class Recaptcha {
	
	private $secret = false;
	public $public = false; 


	function __construct($data)
	{
		if(isset($data['secret']))
		{
			$this->public = $data['public'];
			$this->secret = $data['secret'];
		}
	}


    function check($response)
    {
    	if($this->secret)
    	{
	    	$siteVerifyUrl = "https://www.google.com/recaptcha/api/siteverify?";

	    	$callback = file_get_contents($siteVerifyUrl.'secret='.$this->secret.'&response='.$response);
	    	$callback = json_decode($callback);
	    	if($callback->success == true)
	    		return true;
	    }
	    return false;
    }

    function form()
    {
    	echo "<script src='https://www.google.com/recaptcha/api.js'></script>";
    	echo '<div class="g-recaptcha" data-sitekey="'.$this->public.'"></div>';
    }
}

?>
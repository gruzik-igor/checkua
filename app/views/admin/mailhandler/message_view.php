<div class="site1">
	<div class="title site">Зворотній зв'язок</div>
</div>
<br>
<?php $message = $this->db->getAllDataById('mail_handler', $this->data->uri(2));
if($message){
	?>
Visitor: <?=$message->name?> <br>
Email Address: <?=$message->email?> <br>
Phone Number: <?=$message->phone?> <br>
Date: <?=date("d.m.Y H:i", $message->date)?> <br>
Message: <?=$message->message?> <br>
<?php } else echo "Невірний номер листа!"; ?>
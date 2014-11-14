<a href="<?=SITE_URL?>wl_services">До списку всіх сервісів</a>
<button style="float:right" onClick="showUninstalForm()">Видалити сервіс "<?=$service->name?>"</button>
<br>
<br>
<div id="uninstall-form" style="background: rgba(236, 0, 0, 0.68); padding: 10px; display: none;">
	<form action="<?=SITE_URL?>admin/wl_services/uninstall" method="POST">
		Ви впевнені що бажаєте видалити сервіс "<b><?=$service->name?></b>"?
		<br><br>
		<input type="checkbox" name="content" value="1" id="content" checked><label for="content">Видалити весь контент, що пов'язаний з сервісом (дані користувачів)</label>
		<br>
		<input type="hidden" name="id" value="<?=$service->id?>">
		<input type="submit" value="Деінсталювати" style="margin-left:25px; float:left;">
	</form>
	<button style="margin-left:25px" onClick="showUninstalForm()">Скасувати</button>
	<div class="clear"></div>
</div>
<table cellspacing="0">
	<tr class="top">
		<th>id</th>
		<th>alias</th>
		<th>table</th>
		<th>options</th>
		<th>active</th>
	</tr>
	<?php foreach ($aliases as $alias) { ?>
		<tr>
			<td><?=$alias->id?></td>
			<td><a href="<?=SITE_URL.$alias->alias?>"><?=$alias->alias?></a></td>
			<td><?=$alias->table?></td>
			<td><?=$alias->options?></td>
			<td><?=$alias->active?></td>
		</tr>
	<?php } ?>
</table>
<br>
<?php

foreach ($service as $key => $value) {
	echo($key .': '. $value .'<br>');
}

$path = APP_PATH.'services'.DIRSEP.$service->name.DIRSEP.'views/admin_view.php';
if(file_exists($path)){
	echo('<br>');
    require_once($path);
    echo('<br>');
}

if($options){
	echo('<br>');
	foreach ($options as $opt) if($opt->alias == 0) {
		echo($opt->name .': <input type="text" id="opt-'.$opt->id.'" value="'. $opt->value .'" onchange="saveOption('.$opt->id.')"><br>');
	}
}

?>
<script type="text/javascript">
	function saveOption (id) {
		$('#loading').css("display", "block");
		$.ajax({
			url: "<?=SITE_URL?>admin/wl_services/saveOption",
			type: 'POST',
			data: {
				id: id,
				value :  $('#opt-'+id).val(),
				json : true
			},
			success: function(res){
				if(res['result'] == false){
					alert(res['error']);
				} else alert('Дані успішно збережено!')
				$('#loading').css("display", "none");
			},
			error: function(){
				alert("Помилка! Спробуйте ще раз!");
				$('#loading').css("display", "none");
			},
			timeout: function(){
				alert("Помилка! Спробуйте ще раз!");
				$('#loading').css("display", "none");
			}
		});
	}

	function showUninstalForm () {
		if($('#uninstall-form').is(":hidden")){
			$('#uninstall-form').slideDown("slow");
		} else {
			$('#uninstall-form').slideUp("fast");
		}
	}
</script>
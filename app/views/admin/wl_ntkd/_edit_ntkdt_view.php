<?php

	$name = '';
	$ntkd;

	$this->load->model('wl_ntkd_model');
	if($_SESSION['language']){
	    $current_languade = $_SESSION['language'];
	    foreach ($_SESSION['all_languages'] as $lang) {
	        $_SESSION['language'] = $lang;
	        $ntkd[$lang] = $this->wl_ntkd_model->get($_SESSION['alias']->alias, $content, false);
	    }
	    $_SESSION['language'] = $current_languade;
	    $name = $ntkd[$current_languade]->name;
    } else {
        $ntkd = $this->wl_ntkd_model->get($_SESSION['alias']->id, $content, false);
        $name = $ntkd->name;
    }

?>

<h1><?=$name?></h1>

<?php

if($_SESSION['language']){ ?>
	<div id="tabs">
	    <ul>
		    <?php foreach ($_SESSION['all_languages'] as $lang) { ?>
	            <li><a href="#tab-<?=$lang?>"><?=$lang?></a></li>
	        <?php } ?>
    	</ul>
		<?php foreach ($_SESSION['all_languages'] as $lang) { ?>
			<div id="tab-<?=$lang?>">
				<table>
					<tr>
						<td>name:</td>
						<td><input type="text" onChange="save('name', this, '<?=$lang?>')" value="<?=$ntkd[$lang]->name?>"></td>
					</tr>
					<tr>
						<td>title</td>
						<td><input type="text" onChange="save('title', this, '<?=$lang?>')" value="<?=$ntkd[$lang]->title?>"></td>
					</tr>
					<tr>
						<td>keywords</td>
						<td><input type="text" onChange="save('keywords', this, '<?=$lang?>')" value="<?=$ntkd[$lang]->keywords?>"></td>
					</tr>
				</table>
				description<br>
				<textarea onChange="save('description', this, '<?=$lang?>')"><?=$ntkd[$lang]->description?></textarea>
				<br>
				text<br>
				<textarea class="t-big" onChange="save('text', this, '<?=$lang?>')" id="editor-<?=$lang?>"><?=$ntkd[$lang]->text?></textarea>
				<button onClick="saveText('<?=$lang?>')">Зберегти текст</button>
			</div>
		<?php } ?>
	</div>

	<link rel="stylesheet" href="//code.jquery.com/ui/1.11.0/themes/smoothness/jquery-ui.css">
	<script src="//code.jquery.com/ui/1.10.4/jquery-ui.js"></script>
	<script>
	    $(function() {
	        $( "#tabs" ).tabs();
	    });
	</script>

<?php } else { ?>
	<table>
		<tr>
			<td>name:</td>
			<td><input type="text" onChange="save('name', this)" value="<?=$ntkd->name?>"></td>
		</tr>
		<tr>
			<td>title</td>
			<td><input type="text" onChange="save('title', this)" value="<?=$ntkd->title?>"></td>
		</tr>
		<tr>
			<td>keywords</td>
			<td><input type="text" onChange="save('keywords', this)" value="<?=$ntkd->keywords?>"></td>
		</tr>
	</table>
	description<br>
	<textarea onChange="save('description', this)"><?=$ntkd->description?></textarea>
	<br>
	text<br>
	<textarea class="t-big" onChange="save('text', this)" id="editor"><?=$ntkd->text?></textarea>
	<button onClick="saveText(false)">Зберегти текст</button>
<?php }

?>

<br>
<br>
<br>
<div class="clear"></div>

<div id="saveing">
	<img src="<?=SITE_URL?>style/images/icon-loading.gif">
</div>

<script type="text/javascript" src="<?=SITE_URL?>assets/ckeditor/ckeditor.js"></script>
<script type="text/javascript" src="<?=SITE_URL?>assets/ckfinder/ckfinder.js"></script>
<script type="text/javascript">
	<?php if($_SESSION['all_languages']) foreach($_SESSION['all_languages'] as $lng) echo "CKEDITOR.replace( 'editor-{$lng}' ); "; else echo "CKEDITOR.replace( 'editor' ); "; ?>
		CKFinder.setupCKEditor( null, {
		basePath : '<?=SITE_URL?>assets/ckfinder/',
		filebrowserBrowseUrl : '<?=SITE_URL?>assets/ckfinder/ckfinder.html',
		filebrowserImageBrowseUrl : '<?=SITE_URL?>assets/ckfinder/ckfinder.html?type=Images',
		filebrowserFlashBrowseUrl : '<?=SITE_URL?>assets/ckfinder/ckfinder.html?type=Flash',
		filebrowserUploadUrl : '<?=SITE_URL?>assets/ckfinder/core/connector/asp/connector.asp?command=QuickUpload&type=Files',
		filebrowserImageUploadUrl : '<?=SITE_URL?>assets/ckfinder/core/connector/asp/connector.asp?command=QuickUpload&type=Images',
		filebrowserFlashUploadUrl : '<?=SITE_URL?>assets/ckfinder/core/connector/asp/connector.asp?command=QuickUpload&type=Flash',
	});
</script>

<script type="text/javascript">
	var data;
	function save (field, e, lang) {
	    $('#saveing').css("display", "block");
	    var value = '';
	    if(e != false) value = e.value;
	    else value = data;
        $.ajax({
            url: "<?=SITE_URL?>admin/wl_ntkd/save",
            type: 'POST',
            data: {
            	alias: '<?=$_SESSION['alias']->id?>',
            	content: '<?=$content?>',
                field: field,
                data: value,
                language: lang,
                json: true
            },
            success: function(res){
                if(res['result'] == false){
                    alert(res['error']);
                }
                $('#saveing').css("display", "none");
            },
            error: function(){
                alert("Помилка! Спробуйте ще раз!");
                $('#saveing').css("display", "none");
            },
            timeout: function(){
                alert("Помилка: Вийшов час очікування! Спробуйте ще раз!");
                $('#saveing').css("display", "none");
            }
        });
	}
	function saveText(lang){
		if(lang != false){
			data = CKEDITOR.instances['editor-'+lang].getData();
		} else {
			data = CKEDITOR.instances['editor'].getData();
		}
		save('text', false, lang);
	}
</script>

<style type="text/css">
	input[type="text"]{
		width: 715px;
	}
	textarea{
		width: 100%;
		height: 100px;
	}
	textarea.t-big{
		height: 450px;
	}
	#saveing {
		left: 40%;
		top: 35%;
		position: fixed;
		display: none;
	}
</style>
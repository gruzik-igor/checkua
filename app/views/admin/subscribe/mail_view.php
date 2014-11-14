<a href="<?=SITE_URL?>admin/subscribe" style="float:right">До списку всіх email</a>
<h1>Зробити розсилку</h1>
<form action="<?=SITE_URL?>admin/subscribe/makemail" method="post">
	З якого email робити розсилку: <input type="email" name="from" value="<?=SITE_EMAIL?>" required><br>
	Тема листа: <input type="text" name="title" value="" required><br>
	Текст листа:<br>
	<textarea id="editor" name="mess"></textarea><br>
	<input type="submit" value="Зробити розсилку всім активним!">
</form>

<script type="text/javascript" src="<?=SITE_URL?>assets/ckeditor/ckeditor.js"></script>
<script type="text/javascript" src="<?=SITE_URL?>assets/ckfinder/ckfinder.js"></script>
<script type="text/javascript">
	CKEDITOR.replace( 'editor' );
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
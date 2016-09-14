<div class="row">
    <div class="col-md-12">
<?php

	$name = '';
	if(!isset($ntkd)){
		$this->load->model('wl_ntkd_model');
		if($_SESSION['language']){
		    $current_languade = $_SESSION['language'];
		    foreach ($_SESSION['all_languages'] as $lang) {
		        $_SESSION['language'] = $lang;
		        $ntkd[$lang] = $this->wl_ntkd_model->get($alias->alias, $content, false);
		    }
		    $_SESSION['language'] = $current_languade;
		    $name = $ntkd[$current_languade]->name;
	    } else {
	        $ntkd = $this->wl_ntkd_model->get($alias->alias, $content, false);
	        $name = $ntkd->name;
	    }
	} else {
		if($_SESSION['language']){
			$data = array();
			if(is_array($ntkd)){
				foreach ($ntkd as $n) {
					$data[$n->language] = $n;
					if($n->language == $_SESSION['language']) $name = $n->name;
				}
			}
			
			if(count($data) != count($_SESSION['all_languages'])){
				foreach ($_SESSION['all_languages'] as $lang) {
					if(empty($data[$lang])){
						@$data[$lang]->name = '';
						$data[$lang]->title = '';
						$data[$lang]->keywords = '';
						$data[$lang]->description = '';
						$data[$lang]->text = '';
					}
				}
			}
			$ntkd = $data;
		}
	}


	if($_SESSION['language']){ ?>
		<ul class="nav nav-tabs">
		    <?php foreach ($_SESSION['all_languages'] as $lang) { ?>
		    	<li class="<?=($_SESSION['language'] == $lang) ? 'active' : ''?>"><a href="#language-tab-<?=$lang?>" data-toggle="tab" aria-expanded="true"><?=$lang?></a></li>
	        <?php } ?>
    	</ul>
    	<div class="tab-content">
			<?php foreach ($_SESSION['all_languages'] as $lang) { ?>
				<div class="tab-pane fade <?=($_SESSION['language'] == $lang) ? 'active in' : ''?>" id="language-tab-<?=$lang?>">
					<label class="col-md-2 control-label">Назва сторінки:</label>
					<div class="col-md-4">
                        <input type="text" onChange="save('name', this, '<?=$lang?>')" value="<?=$ntkd[$lang]->name?>" class="form-control">
                    </div>
					<button type="button" class="btn btn-info" onclick="showEditTKD('<?=$lang?>')">Редагувати title, keywords, description</button>
					<div class="row m-t-5" id="tkd-<?=$lang?>" style="display:none">
    					<div class="col-md-12">
							<label class="col-md-2 control-label">title:</label>
							<div class="col-md-4">
		                        <input type="text" onChange="save('title', this, '<?=$lang?>')" value="<?=$ntkd[$lang]->title?>" placeholder="<?=$ntkd[$lang]->name?>" class="form-control">
		                    </div>
		                    <label class="col-md-2 control-label">keywords:</label>
							<div class="col-md-4">
		                        <input type="text" onChange="save('keywords', this, '<?=$lang?>')" value="<?=$ntkd[$lang]->keywords?>" class="form-control">
		                    </div>
		                    <label class="col-md-2 control-label m-t-5">description: (max 155)</label>
							<div class="col-md-10 m-t-5">
		                        <input class="form-control" onChange="save('description', this, '<?=$lang?>')" value="<?=$ntkd[$lang]->description?>" maxlength="155">
		                    </div>
    					</div>
    				</div>
    				<div class="row m-t-5">
	    				<dic class="col-md-12">
	    					<label class="col-md-2 control-label m-t-5">Короткий опис (анонс у списку):</label>
							<div class="col-md-10 m-t-5">
		                        <textarea class="form-control" onChange="save('list', this, '<?=$lang?>')"><?=$ntkd[$lang]->list?></textarea>
		                    </div>
	    				</dic>
	    			</div>
					<div class="row m-t-5">
						<dic class="col-md-12">
							<label class="control-label">Вміст сторінки:</label><br>
							<textarea class="t-big" onChange="save('text', this, '<?=$lang?>')" id="editor-<?=$lang?>"><?=$ntkd[$lang]->text?></textarea>
							<button class="btn btn-success m-t-5" onClick="saveText('<?=$lang?>')"><i class="fa fa-save"></i> Зберегти текст вмісту сторінки</button>
						</dic>
					</div>
				</div>
			<?php } ?>
		</div>
	<?php } else { ?>
		<label class="col-md-2 control-label">Назва сторінки:</label>
		<div class="col-md-4">
            <input type="text" onChange="save('name', this)" value="<?=$ntkd->name?>" class="form-control">
        </div>
		<button type="button" class="btn btn-info" onclick="showEditTKD('lang')">Редагувати title, keywords, description</button>
		<div class="row m-t-5" id="tkd-lang" style="display:none">
			<div class="col-md-12">
				<label class="col-md-2 control-label">title:</label>
				<div class="col-md-4">
                    <input type="text" onChange="save('title', this)" value="<?=$ntkd->title?>" placeholder="<?=$ntkd->name?>" class="form-control">
                </div>
                <label class="col-md-2 control-label">keywords:</label>
				<div class="col-md-4">
                    <input type="text" onChange="save('keywords', this)" value="<?=$ntkd->keywords?>" class="form-control">
                </div>
                <label class="col-md-2 control-label m-t-5">description: (max 155)</label>
				<div class="col-md-10 m-t-5">
                    <input class="form-control" onChange="save('description', this)" value="<?=$ntkd->description?>" maxlength="155">
                </div>
			</div>
		</div>
		<div class="row m-t-5">
			<dic class="col-md-12">
				<label class="col-md-2 control-label m-t-5">Короткий опис (анонс у списку):</label>
				<div class="col-md-10 m-t-5">
	                <textarea class="form-control" onChange="save('list', this)"><?=$ntkd->list?></textarea>
	            </div>
			</dic>
		</div>
		<div class="row m-t-5">
			<dic class="col-md-12">
				<label class="control-label">Вміст сторінки:</label><br>
				<textarea class="t-big" onChange="save('text', this)" id="editor"><?=$ntkd->text?></textarea>
				<button class="btn btn-success m-t-5" onClick="saveText(false)"><i class="fa fa-save"></i> Зберегти текст вмісту сторінки</button>
			</dic>
		</div>
	<?php } ?>

	</div>
</div>

<script type="text/javascript" src="<?=SITE_URL?>assets/ckeditor/ckeditor.js"></script>
<script type="text/javascript" src="<?=SITE_URL?>assets/ckfinder/ckfinder.js"></script>
<script type="text/javascript">
	<?php if($_SESSION['language']) foreach($_SESSION['all_languages'] as $lng) echo "CKEDITOR.replace( 'editor-{$lng}' ); "; else echo "CKEDITOR.replace( 'editor' ); "; ?>
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
	    var value = '';
	    if(e != false) value = e.value;
	    else value = data;
        $.ajax({
            url: "<?=SITE_URL?>admin/wl_ntkd/save",
            type: 'POST',
            data: {
            	alias: '<?=$alias->id?>',
            	content: '<?=$content?>',
                field: field,
                data: value,
                language: lang,
                json: true
            },
            success: function(res){
                if(res['result'] == false){
                    $.gritter.add({title:"Помилка!",text:res['error']});
                } else {
                	language = '';
                	if(lang) language = lang;
                	$.gritter.add({title:field+' '+language,text:"Дані успішно збережено!"});
                }
            },
            error: function(){
                $.gritter.add({title:"Помилка!",text:"Помилка! Спробуйте ще раз!"});
            },
            timeout: function(){
            	$.gritter.add({title:"Помилка!",text:"Помилка: Вийшов час очікування! Спробуйте ще раз!"});
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
	function showEditTKD (lang) {
		if($('#tkd-'+lang).is(":hidden")){
			$('#tkd-'+lang).slideDown("slow");
	    } else {
			$('#tkd-'+lang).slideUp("fast");
	    }
	}
</script>

<style type="text/css">
	textarea.t-big{
		height: 450px;
	}
</style>
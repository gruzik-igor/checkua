<?php if($_SESSION['language'] && $lang) { ?>
	<div class="input-group">
	    <span class="input-group-addon">Назва</span>
	    <?php if($_SESSION['option']->ProductUseArticle) {
	    	$ntkd[$lang]->name = substr($ntkd[$lang]->name, 0, (strlen($product->article) + 1) * -1);
	    	$pageNames[$lang] = $ntkd[$lang]->name;
	    ?>
	    <input type="text" value="<?=$ntkd[$lang]->name?>" class="form-control" placeholder="Назва" onChange="saveNameWithArticle(this, '<?=$lang?>')">
	    <?php } else { ?>
	    	<input type="text" value="<?=$ntkd[$lang]->name?>" class="form-control" placeholder="Назва" onChange="save('name', this, '<?=$lang?>')">
	    <?php } ?>
	</div>
	<small onClick="showEditTKD('<?=$lang?>')" class="badge badge-info">Редагувати title, keywords, description</small>
	<div id="tkd-<?=$lang?>" class="tkd">
		<div class="input-group">
		    <span class="input-group-addon">title</span>
		    <input type="text" value="<?=$ntkd[$lang]->title?>" class="form-control" placeholder="<?=$ntkd[$lang]->name?>" onChange="save('title', this, '<?=$lang?>')">
		</div>
		<div class="input-group">
		    <span class="input-group-addon">keywords</span>
		    <input type="text" value="<?=$ntkd[$lang]->keywords?>" class="form-control" placeholder="keywords" onChange="save('keywords', this, '<?=$lang?>')">
		</div>
		<div class="input-group">
		    <span class="input-group-addon">description</span>
		    <input type="text" value="<?=$ntkd[$lang]->description?>" class="form-control" placeholder="<?=$ntkd[$lang]->list?>" onChange="save('description', this, '<?=$lang?>')" maxlength="155">
		    <span class="input-group-addon">max: 155</span>
		</div>
	</div>

	<?php if(!empty($options_parents)) { ?>
		<h3>Властивості <?=$_SESSION['admin_options']['word:product_to_delete']?></h3>
		<?php 			
			foreach ($options_parents as $option_id) {
				$options = $this->options_model->getOptions($option_id);
				if($options)
				{
					foreach ($options as $option) {
						if($option->type_name == 'text' || $option->type_name == 'textarea')
						{
							$value = '';
							if(isset($product_options[$option->id][$lang])) $value = $product_options[$option->id][$lang];
							echo('<label>'.$option->name);
							if($option->type_name == 'textarea')
							{
								if($option->sufix != '')
									echo("({$option->sufix})");
								echo(':</label>');
								echo('<textarea onChange="saveOption(this, \''.$option->name.' '.$lang.'\')" name="option-'.$option->id.'-'.$lang.'">'.$value.'</textarea>');
							}
							else
							{
								echo(':</label>');
								if($option->sufix != '')
									echo('<div class="input-group">');
								echo('<input type="text" onChange="saveOption(this, \''.$option->name.' '.$lang.'\')" name="option-'.$option->id.'-'.$lang.'" value="'.$value.'" class="form-control">');
								if($option->sufix != '')
								{
									echo("<span class=\"input-group-addon\">{$option->sufix}</span>");
									echo('</div>');
								}
							}
						}
					}
				}
			}
		}
		$_SESSION['alias']->js_init[] = "CKEDITOR.replace( 'editor-{$lang}' );";
	?>
	<br>
	<label class="control-label">Короткий опис:</label><br>
	<textarea onChange="save('list', this, '<?=$lang?>')"><?=$ntkd[$lang]->list?></textarea>
	<h3>Опис:</h3>
	<textarea id="editor-<?=$lang?>"><?=$ntkd[$lang]->text?></textarea>
	<button class="btn btn-success m-t-5" onClick="saveText('<?=$lang?>')"><i class="fa fa-save"></i> Зберегти текст опису сторінки</button>
<?php } else { ?>
	<div class="input-group">
	    <span class="input-group-addon">Назва</span>
	    <?php if($_SESSION['option']->ProductUseArticle) { $ntkd->name = substr($ntkd->name, 0, (strlen($product->article) + 1) * -1); ?>
	    	<input type="text" value="<?=$ntkd->name?>" class="form-control" placeholder="Username" onChange="saveNameWithArticle(this)">
	    <?php } else { ?>
	    	<input type="text" value="<?=$ntkd->name?>" class="form-control" placeholder="Username" onChange="save('name', this)">
	    <?php } ?>
	</div>
	<small onClick="showEditTKD('block')" class="badge badge-info">Редагувати title, keywords, description</small>
	<div id="tkd-block" class="tkd">
		<div class="input-group">
		    <span class="input-group-addon">title</span>
		    <input type="text" value="<?=$ntkd->title?>" class="form-control" placeholder="<?=$ntkd->name?>" onChange="save('title', this)">
		</div>
		<div class="input-group">
		    <span class="input-group-addon">keywords</span>
		    <input type="text" value="<?=$ntkd->keywords?>" class="form-control" placeholder="keywords" onChange="save('keywords', this)">
		</div>
		<div class="input-group">
		    <span class="input-group-addon">description</span>
		    <input type="text" value="<?=$ntkd->description?>" class="form-control" placeholder="<?=$ntkd->list?>" onChange="save('description', this)" maxlength="155">
		    <span class="input-group-addon">max: 155</span>
		</div>
	</div>
	<label class="control-label">Короткий опис:</label><br>
	<textarea onChange="save('list', this)" class="form-control"><?=$ntkd->list?></textarea>
	<label>Опис:</label><br>
	<textarea onChange="save('text', this)" id="editor"><?=html_entity_decode($ntkd->text, ENT_QUOTES, 'utf-8')?></textarea>
	<button class="btn btn-success m-t-5" onClick="saveText(false)"><i class="fa fa-save"></i> Зберегти текст опису сторінки</button>

<?php $_SESSION['alias']->js_init[] = "CKEDITOR.replace( 'editor' );"; } ?>
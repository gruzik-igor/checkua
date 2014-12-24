<form action="<?=SITE_URL.'admin/'.$_SESSION['alias']->alias?>/save_product_ntkd" method="POST">
	<input type="hidden" name="id" value="<?=$product->id?>">
	<?php if($_SESSION['language'] && $lang){ ?>
		<input type="hidden" name="language" value="<?=$lang?>">

		<input type="submit" value="Зберегти" style="float: right">
		<label>Назва:</label> <input type="text" name="name-<?=$lang?>" value="<?=$ntkd[$lang]->name?>"><br>
		<br>
		<small style="text-align: center; cursor: pointer; display: block" onClick="showTKD('<?=$lang?>')">Редагувати title, keywords, description</small>
		<br>
		<div id="tkd-<?=$lang?>" class="tkd">
			<label>title:</label> <input type="text" name="title-<?=$lang?>" value="<?=$ntkd[$lang]->title?>"><br>
			<label>keywords:</label> <input type="text" name="keywords-<?=$lang?>" value="<?=$ntkd[$lang]->keywords?>"><br>
			<label>description:</label><br>
			<textarea name="description-<?=$lang?>"><?=$ntkd[$lang]->description?></textarea>
		</div>

		<?php if(!empty($options_parents)) { ?>
			<h3>Властивості <?=$admin_words['products']?></h3>
			<?php 			
				foreach ($options_parents as $option_id) {
					$options = $this->shop_model->getOptions($option_id);
					if($options){
						foreach ($options as $option) if($option->type_name == 'text' || $option->type_name == 'textarea') {
							$value = '';
							if(isset($product_options[$option->id][$lang])) $value = $product_options[$option->id][$lang];
							echo('<label>'.$option->name.':</label>');
							if($option->type_name == 'textarea'){
								echo('<textarea name="option-'.$option->id.'-'.$lang.'">'.$value.'</textarea> <br>');
							} else {
								echo('<input type="text" name="option-'.$option->id.'-'.$lang.'" value="'.$value.'" class="options"> <br>');
							}
							echo($option->sufix.'<br>');
						}
					}
				}
			}
		?>
		<h3>Опис:</h3>
		<textarea name="text-<?=$lang?>" id="editor-<?=$lang?>"><?=$ntkd[$lang]->text?></textarea>
	<?php } else { ?>

		<input type="submit" value="Зберегти" style="float: right">
		<label>Назва:</label> <input type="text" name="name" value="<?=$ntkd->name?>"><br>
		<br>
		<small style="text-align: center; cursor: pointer; display: block" onClick="showTKD('block')">Редагувати title, keywords, description</small>
		<br>
		<div id="tkd-block" class="tkd">
			<label>title:</label> <input type="text" name="title" value="<?=$ntkd->title?>"><br>
			<label>keywords:</label> <input type="text" name="keywords" value="<?=$ntkd->keywords?>"><br>
			<label>description:</label><br>
			<textarea name="description"><?=$ntkd->description?></textarea>
		</div>
		<label>Опис:</label><br>
		<textarea name="text" id="editor"><?=html_entity_decode($ntkd->text, ENT_QUOTES, 'utf-8')?></textarea>
	<?php } ?>
	<input type="submit" value="Зберегти">
</form>

<style>
	input[type="text"] {
		width: 300px;
		height: 30px;
	}
	label {
		width: 100px;
	}
	.tkd {
		border: 1px solid black;
		padding: 10px;
		display: none;
	}
	textarea {
		width: 100%;
		height: 100px;
	}
</style>

<script>
function showTKD (lang) {
	if($('#tkd-'+lang).is(":hidden")){
		$('#tkd-'+lang).slideDown("slow");
	} else {
		$('#tkd-'+lang).slideUp("slow");
	}
}
</script>
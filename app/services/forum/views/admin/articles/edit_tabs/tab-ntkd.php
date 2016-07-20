<?php if($_SESSION['language'] && $lang){ ?>
	<label>Назва:</label> <input type="text" onChange="save('name', this, '<?=$lang?>')" value="<?=$ntkd[$lang]->name?>" class="form-control"><br>
	<br>
	<small style="text-align: center; cursor: pointer; display: block" onClick="showEditTKD('<?=$lang?>')">Редагувати title, keywords, description</small>
	<br>
	<div id="tkd-<?=$lang?>" class="tkd">
		<label>title:</label> <input type="text" onChange="save('title', this, '<?=$lang?>')" value="<?=$ntkd[$lang]->title?>" class="form-control"><br>
		<label>keywords:</label> <input type="text" onChange="save('keywords', this, '<?=$lang?>')" value="<?=$ntkd[$lang]->keywords?>" class="form-control"><br>
		<label>description:</label><br>
		<textarea onChange="save('description', this, '<?=$lang?>')" class="form-control"><?=$ntkd[$lang]->description?></textarea>
	</div>
	<br>
	<label class="control-label">Короткий опис (анонс):</label><br>
	<textarea onChange="save('list', this, '<?=$lang?>')"><?=$ntkd[$lang]->list?></textarea>
	<h3>Опис:</h3>
	<textarea id="editor-<?=$lang?>"><?=$ntkd[$lang]->text?></textarea>
	<button class="btn btn-success m-t-5" onClick="saveText('<?=$lang?>')"><i class="fa fa-save"></i> Зберегти текст опису сторінки</button>

<?php } else { ?>

	<label>Назва:</label> <input type="text" onChange="save('name', this)" value="<?=$ntkd->name?>" class="form-control"><br>
	<br>
	<small style="text-align: center; cursor: pointer; display: block" onClick="showEditTKD('block')">Редагувати title, keywords, description</small>
	<br>
	<div id="tkd-block" class="tkd">
		<label>title:</label> <input type="text" onChange="save('title', this)" value="<?=$ntkd->title?>" class="form-control"><br>
		<label>keywords:</label> <input type="text" onChange="save('keywords', this)" value="<?=$ntkd->keywords?>" class="form-control"><br>
		<label>description:</label><br>
		<textarea onChange="save('description', this)" class="form-control"><?=$ntkd->description?></textarea>
	</div>
	<br>
	<label class="control-label">Короткий опис (анонс):</label><br>
	<textarea onChange="save('list', this)" class="form-control"><?=$ntkd->list?></textarea>
	<label>Опис:</label><br>
	<textarea onChange="save('text', this)" id="editor-lang"><?=html_entity_decode($ntkd->text, ENT_QUOTES, 'utf-8')?></textarea>
	<button class="btn btn-success m-t-5" onClick="saveText('lang')"><i class="fa fa-save"></i> Зберегти текст опису сторінки</button>

<?php } ?>

<style>
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
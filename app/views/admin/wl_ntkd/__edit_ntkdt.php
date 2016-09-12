<?php
if(!$_SESSION['language'] || $_SESSION['language'] == $language)
{
	$ntkd = new stdClass();
	$ntkd->name = $_SESSION['alias']->name;
	$ntkd->title = $_SESSION['alias']->title;
	$ntkd->keywords = $_SESSION['alias']->keywords;
	$ntkd->description = $_SESSION['alias']->description;
	$ntkd->text = $_SESSION['alias']->text;
	$ntkd->list = $_SESSION['alias']->list;
}
else
{
	$where = array();
	$where['alias'] = $_SESSION['alias']->id;
	$where['content'] = $_SESSION['alias']->content;
	if(isset($language))
		$where['language'] = $language;
	$ntkd = $this->db->getAllDataById('wl_ntkd', $where);
}

?>
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
<textarea onChange="save('list', this)"><?=$ntkd->list?></textarea>
<label>Опис:</label><br>
<textarea onChange="save('text', this)" id="editor"><?=html_entity_decode($ntkd->text, ENT_QUOTES, 'utf-8')?></textarea>
<button class="btn btn-success m-t-5" onClick="saveText(false)"><i class="fa fa-save"></i> Зберегти текст опису сторінки</button>
<?php $_SESSION['alias']->js_load[] = 'assets/switchery/switchery.min.js'; ?>
<link rel="stylesheet" href="<?=SITE_URL?>assets/switchery/switchery.min.css" />

<div class="col-lg-12">
    <div class="col-lg-12">
		<ul class="nav nav-tabs" id="myTab">
			<li class="active"><a data-target="#tabs-main" data-toggle="tab">Загальні дані</a></li>
			<?php if($mailTemplate->multilanguage == 1 && !empty($_SESSION['all_languages'])) foreach($_SESSION['all_languages'] as $lang) {?>
			<li><a data-target="#tabs-<?= $lang?>" data-toggle="tab">Текст шаблону - <?= $lang?></a></li>
			<?php } else { ?>
			<li><a data-target="#tabs-text" data-toggle="tab">Текст шаблону</a></li>
			<?php } ?>
		</ul>

		<div class="tab-content">
			<div class="tab-pane active" id="tabs-main">
				<form action="<?=SITE_URL?>admin/wl_mail_template/save" method="POST" class="form-horizontal">
					<input type="hidden" name="mailTemplateId" value="<?= $mailTemplate->id?>">
					<table>
						<div class="form-group">
							<label class="col-md-3 control-label">Від</label>
							<div class="col-md-9">
								<input type="text" class="form-control" name="from" value="<?= $mailTemplate->from?>" placeholder="<?= SITE_EMAIL?>" required>
							</div>
						</div>
						<div class="form-group">
							<label class="col-md-3 control-label">До</label>
							<div class="col-md-9">
								<input type="text" class="form-control" name="to" value="<?= $mailTemplate->to?>" placeholder="to" required>
							</div>
						</div>
						<div class="form-group">
							<label class="col-md-3 control-label">Зберегти в історію</label>
							<div class="col-md-9">
								<input type="checkbox" class="form-control" data-render="switchery" name="saveToHistory" value="1" <?= $mailTemplate->savetohistory == 1 ? 'checked' : '' ?>>
							</div>
						</div>
						<div class="form-group">
							<label class="col-md-3 control-label">Форма</label>
							<div class="col-md-9">
								<?php if($allForms) foreach($allForms as $form) { ?>
									<label><input type="checkbox" name="form[]" value="<?= $form->id?>" <?= (isset($form->checked) && $form->checked == 1) ? 'checked' : '' ?> ><?= $form->name?></label><br>
								<?php } ?>
							</div>
						</div>
						<div class="form-group">
	                    	<div class="col-md-3"></div>
	                        <div class="col-md-9">
	                        	<input type="submit" class="btn btn-sm btn-warning " value="Зберегти">
							</div>
						</div>
					</table>
				</form>
			</div>

			<?php if($mailTemplate->multilanguage == 1 && !empty($_SESSION['all_languages'])) foreach($_SESSION['all_languages'] as $lang) {?>
			<div class="tab-pane" id="tabs-<?= $lang?>">
				<form action="<?=SITE_URL?>admin/wl_mail_template/saveText" method="POST" class="form-horizontal">
					<input type="hidden" name="language" value="<?= $lang?>">
					<input type="hidden" name="template" value="<?= $mailTemplate->id?>">
					<table>
						<div class="form-group">
							<label class="col-md-3 control-label">Заголовок</label>
							<div class="col-md-9">
								<input type="text" class="form-control" name="title" value="<?= isset($mailTemplateData[$lang]->title) ? $mailTemplateData[$lang]->title : '' ?>" required>
							</div>
						</div>
						<div class="form-group">
							<label class="col-md-3 control-label">Текст</label>
							<div class="col-md-9">
								<textarea  class="form-control" rows="10" name="text" required><?= isset($mailTemplateData[$lang]->text) ? $mailTemplateData[$lang]->text : '<html><head><title></title></head><body></body></html>' ?></textarea>
							</div>
						</div>
						<div class="form-group">
							<label class="col-md-3 control-label">Слова</label>
							<div class="col-md-9">
								<span>SITE_URL, IMAGE_PATH<?php if(!empty($fields)) foreach($fields as $field) echo (", ".$field->name); ?></span>
							</div>
						</div>
						<div class="form-group">
	                    	<div class="col-md-3"></div>
	                        <div class="col-md-9">
	                        	<input type="submit" class="btn btn-sm btn-warning " value="Зберегти">
							</div>
						</div>
					</table>
				</form>
			</div>

			<?php } else { ?>
			<div class="tab-pane" id="tabs-text">
				<form action="<?=SITE_URL?>admin/wl_mail_template/saveText" method="POST" class="form-horizontal">
					<input type="hidden" name="language" value="">
					<input type="hidden" name="template" value="<?= $mailTemplate->id?>">
					<table>
						<div class="form-group">
							<label class="col-md-3 control-label">Заголовок</label>
							<div class="col-md-9">
								<input type="text" class="form-control" name="title" value="<?= isset($mailTemplateData[0]->title) ? $mailTemplateData[0]->title : '' ?>" required>
							</div>
						</div>
						<div class="form-group">
							<label class="col-md-3 control-label">Текст</label>
							<div class="col-md-9">
								<textarea  class="form-control" rows="10" name="text" required><?= isset($mailTemplateData[0]->text) ? $mailTemplateData[0]->text : "<html><head>\n<title>\n\n</title>\n</head><body>\n<p>\n\n</p>\n</body></html>" ?></textarea>
							</div>
						</div>
						<div class="form-group">
							<label class="col-md-3 control-label">Слова</label>
							<div class="col-md-9">
								<span>SITE_URL, IMAGE_PATH<?php if(is_array($fields) || $fields->name != '') foreach($fields as $field) echo (", ".$field->name); ?></span>
							</div>
						</div>
						<div class="form-group">
	                    	<div class="col-md-3"></div>
	                        <div class="col-md-9">
	                        	<input type="submit" class="btn btn-sm btn-warning " value="Зберегти">
							</div>
						</div>
					</table>
				</form>
			</div>
			<?php } ?>
		</div>
    </div>
</div>
<style>
.tab-content .tab-pane small {
    margin: 0;
    width: 30px;
}
</style>


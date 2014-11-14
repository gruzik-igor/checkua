<div class="f-right inline">
	<a href="<?=SITE_URL.'admin/'.$_SESSION['alias']->alias?>/all">До всіх статей</a>
	<a href="<?=SITE_URL.'admin/'.$_SESSION['alias']->alias?>/categories">До всіх категорій</a>
</div>

<h1>Додати статтю</h1>

<form action="<?=SITE_URL.'admin/'.$_SESSION['alias']->alias?>/save" method="POST" enctype="multipart/form-data">
	<input type="hidden" name="id" value="0">
	<table>
		<!-- <tr>
			<td>Фото</td>
			<td><input type="file" name="photo" class="border-0"></td>
		</tr> -->
		<?php if($_SESSION['option']->useCategories){
			$this->load->smodel('articles_model');
			$categories = $this->articles_model->getCategories();
			if($categories){
				echo "<tr><td>Оберіть категорію</td><td>";
				if($_SESSION['option']->articleMultiCategory){
					foreach ($categories as $c) { ?>
						<input type="checkbox" name="category[]" value="<?=$c->id?>" id="category-<?=$c->id?>">
						<label for="category-<?=$c->id?>"><?=$c->name?></label>
						<br>
					<?php }
				} else {
					echo('<select name="category">');
					foreach ($categories as $c) { 
						$selected = '';
						if(isset($_GET['category']) && $_GET['category'] == $c->id) $selected = 'selected';
						?>
						<option value="<?=$c->id?>" <?=$selected?>><?=$c->name?></option>
					<?php }
					echo('</select>');
				}
				echo "</td></tr>";
			}
		}
		if($_SESSION['language']) foreach ($_SESSION['all_languages'] as $lang) { ?>
			<tr>
				<td>Назва <?=$lang?></td>
				<td><input type="text" name="name_<?=$lang?>" value="" required></td>
			</tr>
		<?php } else { ?>
			<tr>
				<td>Назва</td>
				<td><input type="text" name="name" value="" required></td>
			</tr>
		<?php } ?>
		<tr>
			<td colspan="2" class="center"><input type="submit" value="Додати"></td>
		</tr>
	</table>
</form>
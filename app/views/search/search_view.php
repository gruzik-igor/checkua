<div style="width : 200px; margin : 0 auto; margin-top: 10%">
	<form action="<?=SITE_URL?>search" method="GET">
		<label for="search">Що шукаємо:</label>
		<input type="text" id="search" name="search"></br>
		<?php if($_SESSION['language']){ ?>
		<label for="search_checkbox">
		<input type="checkbox" id="search_checkbox" name="search_checkbox">розирений пошук</label></br>
		<?php } ?>
		<input type="submit" value="Пошук">
	</form>
	<br>
	<div style="margin-top: 10%; margin-bottom: 30%">
		<?php 
			if(!empty($data)){
				foreach ($data as $d) {
					$link = SITE_URL;
					if ($d->link != 'main') {
						$link .= $d->link;
						if ($d->content > 0) {
							$link .= "/".$d->content;
						}
					}
					echo "<pre>";
					echo "<a href='{$link}'>{$d->name}</a>";
					print_r($d->description);
				}
			}
		?>
	</div>
</div>
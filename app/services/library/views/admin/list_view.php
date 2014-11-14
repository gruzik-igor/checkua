<div class="f-right inline">
	<a href="<?=SITE_URL.'admin/'.$_SESSION['alias']->alias?>/add<?=(isset($category))?'?category='.$category->id:''?>">Додати статтю</a>
	<?php if($_SESSION['option']->useCategories == 1){ ?>
		<a href="<?=SITE_URL.'admin/'.$_SESSION['alias']->alias?>/all">До всіх статей</a>
		<a href="<?=SITE_URL.'admin/'.$_SESSION['alias']->alias?>/categories">До категорій</a>
	<?php } ?>
</div>

<h1>Список всіх статей</h1>

<table cellspacing="0">
	<tr class="top">
		<th>Id</th>
		<th>Name</th>
		<th>Link</th>
		<?php if($_SESSION['option']->useCategories == 1 && $_SESSION['option']->articleMultiCategory == 0){ 
			$categories = $this->articles_model->getCategories(false);
			?>
			<th>Group</th>
		<?php } ?>
		<th>Author</th>
		<th>Date</th>
		<th>Active</th>
		<th>Змінити порядок</th>
		<th></th>
	</tr>
	<?php if(!empty($articles)){ $max = count($articles); foreach($articles as $a){ ?>
	<tr>
		<td><?=$a->id?></td>
		<td><a href="<?=SITE_URL.'admin/'.$_SESSION['alias']->alias?>/edit/<?=$a->id?>"><?=$a->name?></td>
		<td><a href="<?=SITE_URL.$_SESSION['alias']->alias.'/'.$a->link?>"><?=$_SESSION['alias']->alias.'/'.$a->link?></a></td>
		<?php if($_SESSION['option']->useCategories == 1 && $_SESSION['option']->articleMultiCategory == 0){ ?>
		<td>
			<select onchange="changeCategory(this)">
				<?php if(isset($categories)) foreach ($categories as $c) {
					echo('<option value="'.$c->id.'"');
					if($c->id == $a->category) echo(' selected');
					echo('>'.$c->name.'</option>');
				} ?>
			</select>
		</td>
		<?php } ?>
		<td><a href="<?=SITE_URL.'admin/wl_users/'.$a->user?>"><?=$a->user_name?></a></td>
		<td><?=date("d.m.Y H:i", $a->date)?></td>
		<td bgcolor="<?=($a->active == 1)?'green':'red'?>" style="color:white"><center><?=$a->active?></center></td>
		<td><form method="POST" action="<?=SITE_URL.'admin/'.$_SESSION['alias']->alias?>/changeposition"><input type="hidden" name="id" value="<?=$a->id?>"><input type="number" name="position" min="1" max="<?=$max?>" value="<?=$a->position?>" onchange="this.form.submit();" autocomplete="off"></form></td>
	</tr>
	<?php } } ?>
</table>

<script type="text/javascript">
	function changeCategory(e, id){
		$.ajax({
			url: "<?=SITE_URL.$_SESSION['alias']->alias?>/changeCategory",
			type: 'POST',
			data: {
				category :  e.value,
				id :  <?=$a->id?>,
				json : true
			},
			success: function(res){
				if(res['result'] == false){
					alert('Помилка! Спробуйте щераз');
				}
			}
		});
	}
</script>

<style type="text/css">
	input[type="number"]{
		min-width: 50px;
	}
	select {
		width: 250px;
	}
</style>
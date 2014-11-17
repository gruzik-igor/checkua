<div class="f-right inline">
	<a href="<?=SITE_URL.'admin/'.$_SESSION['alias']->alias?>/add<?=(isset($group))?'?group='.$group->id:''?>">Додати товар</a>
	<?php if($_SESSION['option']->useGroups == 1){ ?>
		<a href="<?=SITE_URL.'admin/'.$_SESSION['alias']->alias?>/all">До всіх товарів</a>
		<a href="<?=SITE_URL.'admin/'.$_SESSION['alias']->alias?>/groups">До груп товарів</a>
	<?php } ?>
	<a href="<?=SITE_URL.'admin/'.$_SESSION['alias']->alias?>/options">До всіх параметрів</a>
</div>

<h1><?=(isset($group))?$_SESSION['alias']->name .'. Список товарів':'Список всіх товарів'?></h1>
<?php
	$url = $this->data->url();
	if($this->data->uri(2) == 'all'){
		$url = $_SESSION['alias']->alias .'/';
	} else {
		array_shift($url);
		$url = implode('/', $url);
		$url .= '/';
	}
	$availabilities = null;
	$where_language = '';
	if($_SESSION['language']) $where_language = "AND n.language = '{$_SESSION['language']}'";
	$this->db->executeQuery("SELECT a.*, n.name FROM {$_SESSION['service']->table}_availability as a LEFT JOIN {$_SESSION['service']->table}_availability_name as n ON n.availability = a.id {$where_language} WHERE a.active = 1 ORDER BY a.position ASC");
	if($this->db->numRows() > 0){
		$availabilities = $this->db->getRows('array');
	}
?>
<table cellspacing="0">
	<?php if(isset($group)){ ?>
		<tr class="f_title"><td colspan="9">
		<a href="<?=SITE_URL.'admin/'.$_SESSION['alias']->alias?>"><?=$group->alias_name?></a> -> 
		<?php if(!empty($group->parents)){
			$link = SITE_URL.'admin/'.$_SESSION['alias']->alias;
			foreach ($group->parents as $parent) { $link .= '/'.$parent->link; ?>
				<a href="<?=$link?>"><?=$parent->name?></a> -> 
	<?php } echo($_SESSION['alias']->name); } echo("</td></tr>"); }?>
	<tr class="top">
		<th>Id</th>
		<th>Name</th>
		<th>Link</th>
		<?php if($_SESSION['option']->useGroups == 1 && $_SESSION['option']->ProductMultiGroup == 0){ 
			$categories = $this->shop_model->getGroups(-1, false);
			?>
			<th>Group</th>
		<?php } ?>
		<th>Наявність</th>
		<th>Author Date</th>
		<th>Active</th>
		<th>Змінити порядок</th>
		<th></th>
	</tr>
	<?php if(!empty($products)){ $max = count($products); foreach($products as $a){ ?>
	<tr>
		<td><?=$a->id?></td>
		<td><a href="<?=SITE_URL.'admin/'.$url.$a->link?>"><?=$a->name?></td>
		<td><a href="<?=SITE_URL.$url.$a->link?>"><?=$a->link?></a></td>
		<?php if($_SESSION['option']->useGroups == 1 && $_SESSION['option']->ProductMultiGroup == 0){ ?>
		<td>
			<select onchange="changeCategory(this, <?=$a->id?>)">
				<option value="0">Немає</option>
				<?php if(isset($categories)) foreach ($categories as $c) {
					echo('<option value="'.$c->id.'"');
					if($c->id == $a->group) echo(' selected');
					echo('>'.$c->name.'</option>');
				} ?>
			</select>
		</td>
		<?php } ?>
		<td>
			<?php if(isset($availabilities)){ ?>
				<select onchange="changeAvailability(this, <?=$a->id?>)">
					<?php foreach ($availabilities as $availability) {
						$selected = '';
						if($a->availability == $availability->id) $selected = 'selected'; ?>
						<option value="<?=$availability->id?>" style="color:<?=$availability->color?>" <?=$selected?>><?=$availability->name?></option>
					<?php } ?>
				</select>
			<?php } ?>
		</td>
		<td><a href="<?=SITE_URL.'admin/wl_users/'.$a->user?>"><?=$a->user_name?></a> <?=date("d.m.Y H:i", $a->date)?></td>
		<td bgcolor="<?=($a->active == 1)?'green':'red'?>" style="color:white"><center><?=$a->active?></center></td>
		<td><form method="POST" action="<?=SITE_URL.'admin/'.$_SESSION['alias']->alias?>/changeposition"><input type="hidden" name="id" value="<?=$a->id?>"><input type="number" name="position" min="1" max="<?=$max?>" value="<?=$a->position?>" onchange="this.form.submit();" autocomplete="off"></form></td>
	</tr>
	<?php } } ?>
</table>

<script type="text/javascript">
	function changeAvailability(e, id){
		$.ajax({
			url: "<?=SITE_URL.$_SESSION['alias']->alias?>/changeAvailability",
			type: 'POST',
			data: {
				availability :  e.value,
				id :  id,
				json : true
			},
			success: function(res){
				if(res['result'] == false){
					alert('Помилка! Спробуйте щераз');
				}
			}
		});
	}
	function changeCategory(e, id){
		$.ajax({
			url: "<?=SITE_URL.$_SESSION['alias']->alias?>/changeGroup",
			type: 'POST',
			data: {
				group :  e.value,
				id :  id,
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
		max-width: 200px;
	}
</style>
<?php $productOrder = false;
if(isset($_SESSION['option']->productOrder))
{
	$_SESSION['option']->productOrder = trim($_SESSION['option']->productOrder);
	$order = explode(' ', $_SESSION['option']->productOrder);
	if((count($order) == 2 && $order[0] == 'position' && in_array($order[1], array('asc', 'ASC', 'desc', 'DESC'))) || (count($order) == 1 && $order[0] == 'position'))
		$productOrder = true;
}
?>
<div class="table-responsive">
    <table id="data-table" class="table table-striped table-bordered nowrap" width="100%">
        <thead>
            <tr>
            	<?php if(!isset($search) && $productOrder) echo "<th></th>"; ?>
                <th><?=($_SESSION['option']->ProductUseArticle) ? 'Артикул' : 'Id'?></th>
				<th>Назва</th>
				<th>Ціна (у.о.)</th>
				<?php if($_SESSION['option']->useAvailability == 1) { 
					$this->db->select($this->shop_model->table('_availability').' as a');
					$name = array('availability' => '#a.id');
					if($_SESSION['language']) $name['language'] = $_SESSION['language'];
					$this->db->join($this->shop_model->table('_availability_name'), 'name', $name);
					$availability = $this->db->get();
					?>
					<th>Наявність</th>
				<?php } if($_SESSION['option']->useGroups == 1 && $_SESSION['option']->ProductMultiGroup == 1) { ?>
					<th>Групи</th>
				<?php } ?>
				<th>Автор</th>
				<th>Редаговано</th>
				<th>Стан</th>
            </tr>
        </thead>
        <tbody>
        	<?php foreach($products as $a) { ?>
				<tr id="<?=($_SESSION['option']->ProductMultiGroup && isset($a->position_id)) ? $a->position_id : $a->id?>">
					<?php if(!isset($search) && $productOrder) { ?>
						<td class="move sortablehandle"><i class="fa fa-sort"></i></td>
					<?php } ?>
					<td><a href="<?=SITE_URL.'admin/'.$a->link?>"><?=($_SESSION['option']->ProductUseArticle) ? $a->article : $a->id?></a></td>
					<td>
						<?php if(isset($a->admin_photo)) {?>
						<a href="<?=SITE_URL.'admin/'.$a->link?>"><img src="<?= IMG_PATH.$a->admin_photo?>" width="90" alt=""></a>
						<?php } ?>
						<a href="<?=SITE_URL.'admin/'.$a->link?>"><?=$a->name?></a> 
						<a href="<?=SITE_URL.$a->link?>"><i class="fa fa-eye"></i></a>
					</td>
					<td><?=$a->price?></td>
					<?php if($_SESSION['option']->useAvailability == 1) { ?>
						<td>
							<select onchange="changeAvailability(this, <?=$a->id?>)" class="form-control">
								<?php if(!empty($availability)) foreach ($availability as $c) {
									echo('<option value="'.$c->id.'"');
									if($c->id == $a->availability) echo(' selected');
									echo('>'.$c->name.'</option>');
								} ?>
							</select>
						</td>
					<?php }
					if($_SESSION['option']->useGroups == 1 && $_SESSION['option']->ProductMultiGroup == 1) {
						echo("<td>");
						if(!empty($a->group) && is_array($a->group)) {
                            foreach ($a->group as $group) {
                                echo('<a href="'.SITE_URL.'admin/'.$group->link.'">'.$group->name.'</a> ');
                            }
                        } else {
                            echo("Не визначено");
                        }
                        echo("</td>");
                    	}
                    ?>
					<td><a href="<?=SITE_URL.'admin/wl_users/'.$a->author_edit?>"><?=$a->user_name?></a></td>
					<td><?=date("d.m.Y H:i", $a->date_edit)?></td>
					<td style="background-color:<?=($a->active == 1)?'green':'red'?>;color:white"><?=($a->active == 1)?'активний':'відключено'?></td>
				</tr>
			<?php } ?>
        </tbody>
    </table>
</div>
<div class="pull-right">Товарів у групі: <strong><?=$_SESSION['option']->paginator_total?></strong></div>
<?php
$this->load->library('paginator');
echo $this->paginator->get();
?>
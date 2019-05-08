<?php $_SESSION['alias']->js_load[] = 'assets/switchery/switchery.min.js'; ?>
<link rel="stylesheet" href="<?=SITE_URL?>assets/switchery/switchery.min.css" />

<?php $productOrder = false;
if(isset($_SESSION['option']->productOrder) && empty($_GET['sort']))
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
				<th>Автор / Редаговано</th>
				<th><div class="btn-group">
					<?php $sort = array('' => 'Авто', 'active_on' => 'Активні згори ↑', 'active_off' => 'Активні знизу ↓'); ?>
					<button type="button" class="btn btn-info btn-xs dropdown-toggle" data-toggle="dropdown">
						<?=(isset($_GET['sort'])) ? $sort[$_GET['sort']] : 'Стан'?> <span class="caret"></span>
					</button>
					<ul class="dropdown-menu" role="menu">
						<?php foreach ($sort as $key => $value) { ?>
							<li><a href="<?=$this->data->get_link('sort', $key)?>"><?=$value?></a></li>
						<?php } ?>
					</ul>
				</div></th>
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
						<?php if(!empty($a->admin_photo)) {?>
						<a href="<?=SITE_URL.'admin/'.$a->link?>"><img src="<?= IMG_PATH.$a->admin_photo?>" width="90" class="pull-left" alt=""></a>
						<?php }
						if($_SESSION['option']->ProductUseArticle)
						{
							$name = explode(' ', $a->name);
							$article = array_pop($name);
							if($article == $a->article)
								$a->name = implode(' ', $name);
						}
						?>
						<a href="<?=SITE_URL.'admin/'.$a->link?>"><strong><?=($_SESSION['option']->ProductUseArticle) ? mb_strtoupper($a->article) : $a->id?></strong></a> <br>
						<a href="<?=SITE_URL.'admin/'.$a->link?>"><?=$a->name?></a>
						<a href="<?=SITE_URL.$a->link?>"><i class="fa fa-eye"></i></a>
					</td>
					<td><?=$a->price?> <?=($a->old_price) ? "<del>{$a->old_price}</del>" : ''?></td>
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
						$active = 0;
						if(!empty($a->group) && is_array($a->group)) {
							$allG = count($a->group); $iG = 0;
                            foreach ($a->group as $g) {
                                echo('<a href="'.SITE_URL.'admin/'.$g->link.'">'.$g->name.'</a>');
                                if(++$iG < $allG)
                                	echo ", ";
                                if($g->active)
                                    $active++;
                            }
                        } else {
                            echo("Не визначено");
                        }
                        echo("</td>");
                    	}
                    ?>
					<td><a href="<?=SITE_URL.'admin/wl_users/'.$a->author_edit?>"><?=$a->user_name?></a> <br> <?=date("d.m.Y H:i", $a->date_edit)?></td>
					
					<?php if((!isset($search) && $productOrder) || isset($_GET['sort'])) { ?>
						<td>
							<input type="checkbox" data-render="switchery" <?=($a->active == 1) ? 'checked' : ''?> value="1" onchange="changeActive(this, <?=$a->id?>, <?=(isset($group)) ? $group->id : 0 ?>)" />
						</td>
					<?php } else { 
						if($_SESSION['option']->useGroups && $_SESSION['option']->ProductMultiGroup && !empty($a->group) && is_array($a->group))
                        {
                            $color = 'success';
                            $color_text = 'активний';
                            if($active == 0)
                            {
                                $color = 'danger';
                                $color_text = 'відключено';
                            }
                            elseif($active < count($a->group))
                            {
                                $color = 'warning';
                                $color_text = 'частково активний';
                            }
                        }
					?>
						<td class="<?=$color?>"><?=$color_text?></td>
					<?php } ?>
				</tr>
			<?php } ?>
        </tbody>
    </table>
</div>
<div class="pull-right">Товарів у групі: <strong><?=$_SESSION['option']->paginator_total?></strong>. Активних товарів: <strong><?=$_SESSION['option']->paginator_total_active?></strong></div>
<?php
$this->load->library('paginator');
echo $this->paginator->get();
?>
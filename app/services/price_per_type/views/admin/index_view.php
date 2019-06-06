<?php $ntkd = array('alias' => '#a.alias1', 'content' => 0);
if($_SESSION['language'])
	$ntkd['language'] = $_SESSION['language'];
$shops = $this->db->select('wl_aliases_cooperation as a', 'alias1 as id', array('alias2' => $_SESSION['alias']->id, 'type' => 'marketing'))
					->join('wl_ntkd', 'name', $ntkd)
					->get('array');
if($shops) {
	$userTypes = $this->db->getAllDataByFieldInArray('wl_user_types', 1, 'active');
	$pptData = $this->db->getAllData($_SESSION['service']->table);
	foreach ($shops as $shop) {
		$shopData = array();
		if($pptData)
			foreach ($pptData as $row) {
				if($row->shop_alias == $shop->id)
					$shopData[$row->user_type] = array('change_price' => $row->change_price, 'price' => $row->price);
			}
		?>
<div class="row">
    <div class="col-md-12">
        <div class="panel panel-inverse">
            <div class="panel-heading">
                <h4 class="panel-title">Автоматична зміна ціни для <strong><?=$shop->name?></strong></h4>
            </div>
			<div class="panel-body">
                <div class="table-responsive">
                    <table id="data-table" class="table table-striped table-bordered nowrap" width="100%">
                        <thead>
                            <tr>
								<th>Тип користувача</th>
								<th>Режим зміни відносно базової ціни</th>
								<th>Зміна ціни на</th>
                            </tr>
                        </thead>
                        <tbody>
                        	<?php if($userTypes) foreach ($userTypes as $type) {
                        		$change_price = '+';
                        		$price = 0;
                        		if(isset($shopData[$type->id]))
                        		{
                        			$change_price = $shopData[$type->id]['change_price'];
                        			$price = $shopData[$type->id]['price'];
                        		} ?>
                        		<tr>
                        			<th><?=$type->title?></th>
                        			<td>
                        				<select class="form-control" id="change_price-<?=$shop->id?>-<?=$type->id?>" onchange="saveChangePrice(<?=$shop->id?>, <?=$type->id?>, '<?=$type->title?>')">
                        					<option value="+">+ додати фіксовані у.о.</option>
                        					<option value="*" <?=($change_price == '*') ? 'selected' : ''?>>* помножити на коефіцієнт</option>
                        				</select>
                        			</td>
                        			<td>
                        				<input type="number" step="0.001" value="<?=$price?>" id="price-<?=$shop->id?>-<?=$type->id?>" class="form-control" onchange="saveChangePrice(<?=$shop->id?>, <?=$type->id?>, '<?=$type->title?>')">
                        			</td>
                        		</tr>
                        	<?php } ?>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>

<script>
	function saveChangePrice(shop_id, type_id, label) {
		$('#saveing').css("display", "block");
	    $.ajax({
	      url: "<?=SITE_URL?>admin/<?=$_SESSION['alias']->alias?>/saveForShop",
	      type: 'POST',
	      data: {
	        shop_id: shop_id,
	        type_id: type_id,
	        change_price: document.getElementById('change_price-'+shop_id+'-'+type_id).value,
	        price: document.getElementById('price-'+shop_id+'-'+type_id).value,
	        json: true
	      },
	      success: function(res){
	        if(res['result'] == false){
	            $.gritter.add({title:"Помилка!", text:label + ' ' + res['error']});
	        } else {
	          $.gritter.add({title:label, text:"Дані успішно збережено!"});
	        }
	        $('#saveing').css("display", "none");
	      },
	      error: function(){
	        $.gritter.add({title:"Помилка!", text:"Помилка! Спробуйте ще раз!"});
	        $('#saveing').css("display", "none");
	      },
	      timeout: function(){
	        $.gritter.add({title:"Помилка!", text:"Помилка: Вийшов час очікування! Спробуйте ще раз!"});
	        $('#saveing').css("display", "none");
	      }
	    });
	}
</script>
<?php }
} else { ?>
	<div class="alert alert-warning">
        <h4>Увага! До сервісу керування ціною не підключено жодного магазину</h4>
        <p><a href="<?=SITE_URL.'admin/wl_aliases/add_admin_option/'.$_SESSION['alias']->alias?>">Підключіть магазин як плагін маркетингу (Додати адресу співпраці)</a></p>
    </div>
<?php } ?>
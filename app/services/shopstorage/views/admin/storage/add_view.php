<?php if(isset($_SESSION['notify'])){ 
require APP_PATH.'views/admin/notify_view.php';
} ?>
      
<div class="row">
    <div class="col-md-6">
        <div class="panel panel-inverse">
            <div class="panel-heading">
            	<div class="panel-heading-btn">
					<a href="<?=SITE_URL.'admin/'.$_SESSION['alias']->alias?>/all" class="btn btn-info btn-xs">До всіх товарів складу</a>
            	</div>
                <h4 class="panel-title">Додати накладну товару</h4>
            </div>
            <div class="panel-body">
            	<form action="<?=SITE_URL.'admin/'.$_SESSION['alias']->alias?>/save" method="POST" enctype="multipart/form-data">
					<input type="hidden" name="id" value="0">
	                <div class="table-responsive">
	                    <table class="table table-striped table-bordered nowrap" width="100%">
	                    	<?php if($_SESSION['option']->productUseArticle) { ?>
	                    		<tr>
									<th>Артикул товару</th>
									<td>
										<input type="text" id="article" name="article" value="" class="form-control" required onchange="getProduct(this)">
										<input type="hidden" id="id" name="product-id" value="" required>
									</td>
								</tr>
							<?php } else { ?>
								<tr>
									<th>ID товару</th>
									<td><input type="text" name="product-id" value="" class="form-control" required onchange="getProduct(this)"></td>
								</tr>
							<?php } ?>
							<tr>
								<th>Ціна прихідна</th>
								<td><input type="number" name="price_in" id="price_in" value="0" min="0" step="0.01" onchange="setPrice(this.value, true)" class="form-control productIS" required disabled="disabled"></td>
							</tr>
							<tr>
								<th>Кількість</th>
								<td><input type="number" name="amount" value="1" min="1" class="form-control productIS" required disabled="disabled"></td>
							</tr>
							<?php if($_SESSION['option']->markUpByUserTypes) { ?>
								<tr>
									<th colspan="2"><center>Ціна вихідна</center></th>
								</tr>
								<?php
								$groups = $this->db->getAllDataByFieldInArray('wl_user_types', 1, 'active');
								foreach($groups as $group) { if($group->id > 1) { ?>
									<tr>
										<td><?=$group->title?> (Націнка <?=(isset($storage->markup[$group->id]))?$storage->markup[$group->id] : 0?>%)</td>
										<td>
											<input type="number" name="price_out-<?=$group->id?>" id="price_out-<?=$group->id?>" value="0" min="0" step="0.01" class="form-control price_out productIS" disabled="disabled">
										</td>
									</tr>
								<?php } } } else { ?>
								<tr>
									<th>Ціна вихідна</th>
									<td>
										<input type="number" name="price_out" id="price_out" value="0" min="0" step="0.01" class="form-control productIS" required disabled="disabled">
										<input type="hidden" id="markup" value="<?=(isset($storage->markup))?$storage->markup : 0?>">
									</td>
								</tr>
							<?php } ?>
							<tr>
								<th>Дата приходу</th>
								<td><input type="text" name="date_in" value="<?=date('d.m.Y')?>" class="form-control productIS" required disabled="disabled"></td>
							</tr>
							<tr>
								<td>
									Після збереження:
								</td>
								<td id="after_save">
									<input type="radio" name="to" value="new" id="to_new" checked="checked"><label for="to_new">додати нову накладну</label>
									<input type="radio" name="to" value="edit" id="to_edit"><label for="to_edit">проглянути накладну</label>
								</td>
							</tr>
							<tr>
								<td></td>
								<td><input type="submit" class="btn btn-sm btn-success productIS" value="Додати" disabled="disabled"></td>
							</tr>
	                    </table>
	                </div>
	            </form>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="panel panel-inverse">
            <div class="panel-heading">
                <h4 class="panel-title">Інформація про товар</h4>
            </div>
            <div class="panel-body" id="product">
                <div id="product-info" class="table-responsive" style="display:none">
                    <table class="table table-striped table-bordered nowrap" width="100%">
	                	<tr>
							<th>Виробник</th>
							<td id="product-manufacturer"></td>
						</tr>
	                	<?php if($_SESSION['option']->productUseArticle) { ?>
                    		<tr>
								<th>Артикул товару</th>
								<td id="product-article"></td>
							</tr>
						<?php } else { ?>
							<tr>
								<th>ID товару</th>
								<td id="product-id"></td>
							</tr>
						<?php } ?>
						<tr>
							<th>Назва</th>
							<td id="product-name"></td>
						</tr>
						<tr>
							<th>Стандартна ціна</th>
							<td id="product-price"></td>
						</tr>
						<tr>
							<th>Оригінал</th>
							<td id="product-orign"></td>
						</tr>
						<tr>
							<th>Активність</th>
							<td id="product-active"></td>
						</tr>
	                </table>
                </div>

                <div id="product-array" class="table-responsive" style="display:none">
                    <table id="products" class="table table-striped table-bordered nowrap" width="100%">
                    	<tr>
                    		<th>Виробник</th>
                    	<?php if($_SESSION['option']->productUseArticle) { ?>
								<th>Артикул</th>
						<?php } else { ?>
								<th>ID товару</th>
						<?php } ?>
							<th>Назва</th>
							<th>Оригінал</th>
						</tr>
                    </table>
                </div>

                <div class="alert alert-info fade in" id="product-alert">
			        <h4>Увага!</h4>
			        <p>Введіть <?=($_SESSION['option']->productUseArticle)?'артикул':'ID'?> товару</p>
			    </div>
            </div>
        </div>
    </div>
</div>




<script type="text/javascript">
	function getProduct (e) {
	    $('#saveing').css("display", "block");
	    $.ajax({
	        url: SITE_URL+"admin/<?=$_SESSION['alias']->alias?>/getProduct<?=($_SESSION['option']->productUseArticle)?'ByArticle':'ById'?>",
	        type: 'POST',
	        data: {
	            product: e.value,
	            json: true
	        },
	        success: function(res) {
	            if(res['result'] == false) {
	                alert(res['error']);
	            } else if(Array.isArray(res)) {
	            	$('#product-alert').slideUp('fast');
	            	$('#product-info').slideUp('fast');
	            	$('#product-array').slideDown('slow');
	            	$('.products-row').remove();
	            	$('.productIS').attr('disabled', 'disabled');
	            	for (var i = 0; i < res.length; i++) {
	            		<?php if($_SESSION['option']->productUseArticle) { ?>
		                	var td = res[i].article;
		                <?php } else { ?>
		                	var td = res[i].id;
		                <?php } ?>
		                var vyrobnyk = '';
		                var orign = 'Так';
		                if(res[i].orign == 0)
		                	orign = 'Ні';
		                if(typeof res[i].manufacturer_name != 'undefined') vyrobnyk = res[i].manufacturer_name;
	            		$('#products tr:last').after('<tr class="products-row"><td>'+vyrobnyk+'</td><td>'+td+' <button onClick="setProduct('+res[i].id+')">Обрати</button></td><td>'+res[i].name+'</td><td>'+orign+'</td></tr>');
	            	}
	            } else {
	            	$('#product-alert').slideUp('fast');
	            	$('#product-array').slideUp('fast');
	            	$('#product-info').slideDown('slow');
	                <?php if($_SESSION['option']->productUseArticle) { ?>
	                	$('#product-article').html(res.article);
	                <?php } else { ?>
	                	$('#product-id').html(res.id);
	                <?php } ?>
	                $('#id').val(res.id);
	                $('#product-manufacturer').html(res.manufacturer_name);
	                $('#product-name').html(res.name);
	                $('#product-price').html(res.price);
	                $('#product-orign').html(res.orign);
	                $('#product-active').html(res.active);
	                <?php if($_SESSION['option']->markUpByUserTypes && isset($storage->markup[0]) && $storage->markup[0] > 0) { ?>
	                	$('#price_out-0').val(res.price);
	                	res.price = (100 * Math.floor(res.price) / <?=$storage->markup[0] + 100?>).toFixed(2);
	                <?php } ?>
	                $('#price_in').val(res.price);
	                setPrice(res.price, false);
	                $('.productIS').attr('disabled', false);
	            }
	            $('#saveing').css("display", "none");
	        },
	        error: function(){
	            alert("Помилка! Спробуйте ще раз!");
	            $('#saveing').css("display", "none");
	        },
	        timeout: function(){
	            alert("Помилка: Вийшов час очікування! Спробуйте ще раз!");
	            $('#saveing').css("display", "none");
	        }
	    });
	}
	function setProduct (id) {
	    $('#saveing').css("display", "block");
	    $.ajax({
	        url: SITE_URL+"admin/<?=$_SESSION['alias']->alias?>/getProductById",
	        type: 'POST',
	        data: {
	            product: id,
	            json: true
	        },
	        success: function(res) {
	            if(res['result'] == false) {
	                alert(res['error']);
	            } else {
	            	$('#product-alert').slideUp('fast');
	            	$('#product-array').slideUp('fast');
	            	$('#product-info').slideDown('slow');
	                <?php if($_SESSION['option']->productUseArticle) { ?>
	                	$('#article').val(res.article);
	                	$('#product-article').html(res.article);
	                <?php } else { ?>
	                	$('#product-id').html(res.id);
	                <?php } ?>
	                $('#id').val(res.id);
	                $('#product-manufacturer').html(res.manufacturer_name);
	                $('#product-name').html(res.name);
	                $('#product-price').html(res.price);
	                $('#product-orign').html(res.orign);
	                $('#product-active').html(res.active);
	                <?php if($_SESSION['option']->markUpByUserTypes && isset($storage->markup[0]) && $storage->markup[0] > 0) { ?>
	                	$('#price_out-0').val(res.price);
	                	res.price = (100 * Math.floor(res.price) / <?=$storage->markup[0] + 100?>).toFixed(2);
	                <?php } ?>
	                $('#price_in').val(res.price);
	                setPrice(res.price, false);
	                $('.productIS').attr('disabled', false);
	            }
	            $('#saveing').css("display", "none");
	        },
	        error: function(){
	            alert("Помилка! Спробуйте ще раз!");
	            $('#saveing').css("display", "none");
	        },
	        timeout: function(){
	            alert("Помилка: Вийшов час очікування! Спробуйте ще раз!");
	            $('#saveing').css("display", "none");
	        }
	    });
	}
	function setPrice(price, setDefault) {
		<?php if($_SESSION['option']->markUpByUserTypes) { foreach($groups as $group){ ?>
			$('#price_out-<?=$group->id?>').val((<?=(isset($storage->markup[$group->id]))?$storage->markup[$group->id] : 0?> * price / 100 + Math.floor(price)).toFixed(2));
		<?php } ?>
			if(setDefault) $('#price_out-0').val((<?=(isset($storage->markup[0]))?$storage->markup[0] : 0?> * price / 100 + Math.floor(price)).toFixed(2));
		<?php } else { ?>
			$('#price_out').val((<?=(isset($storage->markup))?$storage->markup : 0?> * price / 100 + Math.floor(price)).toFixed(2));
		<?php } ?>
	}
</script>
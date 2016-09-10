<?php if(isset($_SESSION['notify'])){ 
require APP_PATH.'views/admin/notify_view.php';
} ?>
      
<div class="row">
    <div class="col-md-6">
        <div class="panel panel-inverse">
            <div class="panel-heading">
            	<div class="panel-heading-btn">
					<a href="<?=SITE_URL.'admin/'.$_SESSION['alias']->alias?>/all" class="btn btn-info btn-xs">До всіх накладних</a>
            	</div>
                <h4 class="panel-title">Додати накладну</h4>
            </div>
            <div class="panel-body">
            	<form action="<?=SITE_URL.'admin/'.$_SESSION['alias']->alias?>/save" method="POST" enctype="multipart/form-data">
					<input type="hidden" name="id" value="0">
	                <div class="table-responsive">
	                    <table class="table table-striped table-bordered nowrap" width="100%">
	                    	<?php if($_SESSION['option']->productUseArticle) { ?>
	                    		<tr>
									<th>Артикул <?=$_SESSION['admin_options']['word:product_to']?></th>
									<td>
										<input type="text" name="article" value="" class="form-control" required onchange="getProduct(this)">
										<input type="hidden" id="id" name="product-id" value="" required>
									</td>
								</tr>
							<?php } else { ?>
								<tr>
									<th>ID <?=$_SESSION['admin_options']['word:product_to']?></th>
									<td><input type="text" name="product-id" value="" class="form-control" required></td>
								</tr>
							<?php } ?>
							<tr>
								<th>Ціна прихідна</th>
								<td><input type="number" name="price_in" id="price_in" value="0" min="0" onchange="setPrice(this.value)" class="form-control" required></td>
							</tr>
							<tr>
								<th>Кількість</th>
								<td><input type="number" name="amount" value="1" min="1" class="form-control" required></td>
							</tr>
							<?php if($_SESSION['option']->markUpByUserTypes) { ?>
								<tr>
									<th colspan="2"><center>Ціна вихідна</center></th>
								</tr>
								<?php
								$groups = $this->db->getAllDataByFieldInArray('wl_user_types', 1, 'active');
								foreach($groups as $group){ ?>
									<tr>
										<td><?=$group->title?> (Націнка <?=(isset($storage->markup[$group->id]))?$storage->markup[$group->id] : 0?>%)</td>
										<td>
											<input type="number" name="price_out-<?=$group->id?>" id="price_out-<?=$group->id?>" value="0" min="0" step="0.01" class="form-control price_out">
										</td>
									</tr>
								<?php }
							} else { ?>
								<tr>
									<th>Ціна вихідна</th>
									<td>
										<input type="number" name="price_out" id="price_out" value="0" min="0" step="0.01" class="form-control" required>
										<input type="hidden" id="markup" value="<?=(isset($storage->markup))?$storage->markup : 0?>">
									</td>
								</tr>
							<?php } ?>
							<tr>
								<th>Дата приходу</th>
								<td><input type="text" name="date_in" value="<?=date('d.m.Y')?>" class="form-control" required></td>
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
								<td><input id="submit" type="submit" class="btn btn-sm btn-success" value="Додати" disabled="disabled"></td>
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
                <h4 class="panel-title">Інформація про <?=$_SESSION['admin_options']['word:product']?></h4>
            </div>
            <div class="panel-body" id="product">
	                <div id="product-info" class="table-responsive" style="display:none">
	                    <table class="table table-striped table-bordered nowrap" width="100%">
	                    	<?php if($_SESSION['option']->productUseArticle) { ?>
	                    		<tr>
									<th>Артикул <?=$_SESSION['admin_options']['word:product_to']?></th>
									<td id="product-article"></td>
								</tr>
							<?php } else { ?>
								<tr>
									<th>ID <?=$_SESSION['admin_options']['word:product_to']?></th>
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
								<th>Група</th>
								<td id="product-group"></td>
							</tr>
							<tr>
								<th>Активність</th>
								<td id="product-active"></td>
							</tr>
	                    </table>
	                </div>
	                <div class="alert alert-info fade in" id="product-alert">
				        <h4>Увага!</h4>
				        <p>Введіть <?=($_SESSION['option']->productUseArticle)?'артикул':'ID'?> <?=$_SESSION['admin_options']['word:product_to']?></p>
				    </div>
	            </form>
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
	            } else {
	            	$('#product-alert').slideUp('fast');
	            	$('#product-info').slideDown('slow');
	                <?php if($_SESSION['option']->productUseArticle) { ?>
	                	$('#product-article').html(res.article);
	                <?php } else { ?>
	                	$('#product-id').html(res.id);
	                <?php } ?>
	                $('#id').val(res.id);
	                $('#product-name').html(res.name);
	                $('#product-price').html(res.price);
	                $('#product-group').html(res.group_name);
	                $('#product-active').html(res.active);
	                $('#price_in').val(res.price);
	                setPrice(res.price);
	                $('#submit').attr('disabled', false);
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
	function setPrice(price) {
		<?php if($_SESSION['option']->markUpByUserTypes) { foreach($groups as $group){ ?>
			$('#price_out-<?=$group->id?>').val(<?=(isset($storage->markup[$group->id]))?$storage->markup[$group->id] : 0?> * price / 100 + Math.floor(price));
		<?php } } else { ?>
			$('#price_out').val(<?=(isset($storage->markup))?$storage->markup : 0?> * price / 100 + Math.floor(price));
		<?php } ?>
	}
</script>
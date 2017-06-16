<div class="row">
    <div class="col-md-12">
        <div class="panel panel-inverse">
            <div class="panel-heading">
                <div class="panel-heading-btn">
                </div>
                <h4 class="panel-title">Комплекти</h4>
            </div>
            <div class="panel-body">
            	<div class="table-responsive">
                    <table class="table table-striped table-bordered nowrap" width="100%">
                    	<thead>
                            <tr>
                                <th><?=($_SESSION['option']->ProductUseArticle) ? 'Артикул' : 'Id'?></th>
								<th>Назва</th>
                                <th>Ціна (у.о.)</th>
								<th></th>
                            </tr>
	                    </thead>
	                    <tbody>
                    	<?php if(isset($similarProducts['products'])) foreach($similarProducts['products'] as $similarProduct) { ?>
							<tr>
								<td><a href="<?=SITE_URL.'admin/'.$_SESSION['alias']->alias.'/'.$similarProduct->alias?>"><?=($_SESSION['option']->ProductUseArticle) ? $similarProduct->article : $similarProduct->id?></a></td>
								<td><a href="<?=SITE_URL.'admin/'.$_SESSION['alias']->alias.'/'.$similarProduct->alias?>"><?= $similarProduct->product_name ?></a></td>
                                <td><?= $similarProduct->price ?></td>
								<td class="text-center"><button type="button" class="btn btn-sm btn-danger" onclick="deleteSimilarProduct(<?= $product->id .','. $similarProduct->id?>, this);" >X</button></td>
							</tr>
						<?php } ?>
	                    </tbody>
                    </table>
                </div>
                <form class="col-md-6 form-inline" method="POST" action="<?=SITE_URL.'admin/'.$_SESSION['alias']->alias?>/addSimilarProduct" >
                    <input type="hidden" name="product" value="<?= $product->id ?>">
                    <div class="form-group m-r-10">
                        <label class="control-label">Додати продукт</label>
                    </div>
                    <div class="form-group m-r-10">
                        <input type="text" class="form-control" name="article" value="" placeholder="article">
                    </div>
                    <div class="form-group m-r-10">
                        <input type="submit" class="btn btn-success" value="Додати">
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    function deleteSimilarProduct(productId, similarProduct, btn) {
        if(confirm("Ви впевнені, що хочете видалити товар з комплекту?"))
        {
            $.ajax({
                url: "<?=SITE_URL?>admin/<?=$_SESSION['alias']->alias?>/deleteSimilarProduct",
                type: "POST",
                data: {
                    productId : productId,
                    similarProduct : similarProduct
                },
                success : function (res) 
                {
                    $(btn).closest('tr').hide('slow', function(){ $(this).remove(); });
                }
            })
        }
    }
</script>

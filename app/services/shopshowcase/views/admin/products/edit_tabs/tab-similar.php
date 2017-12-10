<div class="row">
    <div class="col-md-12">
        <div class="panel panel-inverse">
            <div class="panel-heading">
                <div class="panel-heading-btn">
                </div>
                <h4 class="panel-title">Схожі продукти</h4>
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
								<td class="text-center"><button type="button" class="btn btn-sm btn-danger" onclick="deleteSimilarProduct(<?= $similarProduct->id?>, this);" >X</button></td>
							</tr>
						<?php } ?>
	                    </tbody>
                    </table>
                </div>
                <form class="col-md-6 form-inline" method="POST" action="<?=SITE_URL.'admin/'.$_SESSION['alias']->alias?>/addSimilarProduct" >
                    <input type="hidden" name="group" value="<?= $similarProducts['group'] ? $similarProducts['group'] : 0 ?>">
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

                <?php if($similarProducts['group']) {?>
                <div class="col-md-12">
                    <h4 class="text-center">Опис для всіх схожих продуктів</h4>
                    <?php if($_SESSION['language']){ ?>
                        <ul class="nav nav-tabs">
                            <?php foreach ($_SESSION['all_languages'] as $lang) { ?>
                                <li class="<?=($_SESSION['language'] == $lang) ? 'active' : ''?>"><a href="#language-tab-<?=$lang?>" data-toggle="tab" aria-expanded="true"><?=$lang?></a></li>
                            <?php } ?>
                        </ul>
                        <div class="tab-content">
                            <?php foreach ($_SESSION['all_languages'] as $lang) { ?>
                            <div class="tab-pane fade <?=($_SESSION['language'] == $lang) ? 'active in' : ''?>" id="language-tab-<?=$lang?>">
                                <form class="form-vertical" method="POST" action="<?=SITE_URL.'admin/'.$_SESSION['alias']->alias?>/saveSimilarText" >
                                    <input type="hidden" name="language" value="<?= $lang?>">
                                    <input type="hidden" name="group" value="<?= $similarProducts['group'] ? $similarProducts['group'] : 0 ?>">
                                    <div class="form-group">
                                        <textarea class="t-big" name="text" id="editorSimilar-<?=$lang?>"></textarea>
                                    </div>
                                    <div class="form-group">
                                        <div class="checkbox">
                                            <label>
                                                <input type="checkbox" id="all" name="all" value="1" checked> Перезаписувати існуючий текст?
                                            </label>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <input type="submit" class="btn btn-success" value="Зберегти">
                                    </div>
                                </form>
                            </div>
                            <?php } ?>
                        </div>
                    <?php } else { ?>
                        <form class="form-vertical" method="POST" action="<?=SITE_URL.'admin/'.$_SESSION['alias']->alias?>/saveSimilarText" >
                            <input type="hidden" name="group" value="<?= $similarProducts['group'] ? $similarProducts['group'] : 0 ?>">
                            <div class="form-group">
                                <textarea class="t-big" name="text" id="editorSimilar"></textarea>
                            </div>
                            <div class="form-group">
                                <div class="checkbox">
                                    <label>
                                        <input type="checkbox" id="all" name="all" value="1" checked>  Перезаписувати існуючий текст?
                                    </label>
                                </div>
                            </div>
                            <div class="form-group">
                                <input type="submit" class="btn btn-success" value="Зберегти">
                            </div>
                        </form>
                    <?php } ?>
                </div>
                <?php } ?>

            </div>
        </div>
    </div>
</div>

<script type="text/javascript" src="<?=SITE_URL?>assets/ckeditor/ckeditor.js"></script>
<script type="text/javascript" src="<?=SITE_URL?>assets/ckfinder/ckfinder.js"></script>
<script type="text/javascript">
    <?php if($_SESSION['language']) foreach($_SESSION['all_languages'] as $lng){ echo "CKEDITOR.replace( 'editorSimilar-{$lng}' ); ";} else echo "CKEDITOR.replace( 'editorSimilar' ); "; ?>
        CKFinder.setupCKEditor( null, {
        basePath : '<?=SITE_URL?>assets/ckfinder/',
        filebrowserBrowseUrl : '<?=SITE_URL?>assets/ckfinder/ckfinder.html',
        filebrowserImageBrowseUrl : '<?=SITE_URL?>assets/ckfinder/ckfinder.html?type=Images',
        filebrowserFlashBrowseUrl : '<?=SITE_URL?>assets/ckfinder/ckfinder.html?type=Flash',
        filebrowserUploadUrl : '<?=SITE_URL?>assets/ckfinder/core/connector/asp/connector.asp?command=QuickUpload&type=Files',
        filebrowserImageUploadUrl : '<?=SITE_URL?>assets/ckfinder/core/connector/asp/connector.asp?command=QuickUpload&type=Images',
        filebrowserFlashUploadUrl : '<?=SITE_URL?>assets/ckfinder/core/connector/asp/connector.asp?command=QuickUpload&type=Flash',
    });
</script>
<script>
    function deleteSimilarProduct(productId, btn) {
        if(confirm("Ви впевнені, що хочете видалити товар зі схожих продуктів?"))
        {
            $.ajax({
                url: "<?=SITE_URL?>admin/<?=$_SESSION['alias']->alias?>/deleteSimilarProduct",
                type: "POST",
                data: {
                    productId : productId
                },
                success : function (res) 
                {
                    $(btn).closest('tr').hide('slow', function(){ $(this).remove(); });
                }
            })
        }
    }
</script>

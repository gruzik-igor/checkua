<!-- begin container -->
<div class="container">
    <!-- begin row -->
    <div class="row row-space-30">
        <h1>LiqPay server side test</h1>

        <form action="<?=SITE_URL?>test/liqpay/go" method="post" class="form-horizontal">
			<div class="form-group">
                <label class="control-label col-md-3">server_url <span class="text-danger">*</span></label>
                <div class="col-md-9">
                    <input type="text" class="form-control" name="server_url" value="<?=$this->data->re_post('server_url')?>">
                </div>
            </div>
            <div class="form-group">
                <label class="control-label col-md-3">order_id <span class="text-danger">*</span></label>
                <div class="col-md-9">
                    <input type="text" class="form-control" name="order_id" value="<?=$this->data->re_post('order_id')?>">
                </div>
            </div>
            <div class="form-group">
                <label class="control-label col-md-3">amount <span class="text-danger">*</span></label>
                <div class="col-md-9">
                    <input type="text" class="form-control" name="amount" value="<?=$this->data->re_post('amount')?>">
                </div>
            </div>
            <div class="form-group">
                <label class="control-label col-md-3">status <span class="text-danger">*</span></label>
                <div class="col-md-9">
                	<select name="status" class="form-control">
                		<option value="success" <?=($this->data->re_post('status') == 'success') ? 'selected' : 'selected' ?>>success</option>
                		<option value="sandbox" <?=($this->data->re_post('sandbox') == 'sandbox') ? 'selected' : '' ?>>sandbox</option>
                		<option value="processing" <?=($this->data->re_post('status') == 'processing') ? 'selected' : '' ?>>processing</option>
                		<option value="failure" <?=($this->data->re_post('status') == 'failure') ? 'selected' : '' ?>>failure</option>
                	</select>
                </div>
            </div>
            <div class="form-group">
                <label class="control-label col-md-3">private_key <span class="text-danger">*</span></label>
                <div class="col-md-9">
                    <input type="text" class="form-control" name="private_key" value="<?=$this->data->re_post('private_key')?>">
                </div>
            </div>
            <div class="form-group">
            	<button type="submit" class="btn btn-sm btn-success col-md-offset-4 col-md-2">Test!</button>
            </div>
		</form>

        <p><?=html_entity_decode($_SESSION['alias']->text)?></p>
    </div>
    <!-- end row -->

    <?php if(isset($res)) 
    {
    	echo('<div class="row row-space-30 m-40">');
    	echo($res);
    	echo('</div>');
    }
    ?>
</div>
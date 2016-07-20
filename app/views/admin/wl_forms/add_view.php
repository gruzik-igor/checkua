<div class="row">
    <div class="col-md-6">
        <div class="panel panel-inverse">
            <div class="panel-heading">
            	<div class="panel-heading-btn">
                	<a href="<?=SITE_URL?>admin/wl_forms" class="btn btn-info btn-xs">До всіх форм</a>
                </div>
                <h4 class="panel-title">Додати форму</h4>
            </div>
            <div class="panel-body">
				<form action="<?=SITE_URL?>admin/wl_forms/add_save" method="POST" class="form-horizontal">
					<table>
						<div class="form-group">
							<label class="col-md-3 control-label">name*</label>
							<div class="col-md-9">
								<input type="text" class="form-control" name="name" placeholder="name" required>
							</div>
						</div>
						<div class="form-group">
							<label class="col-md-3 control-label">captcha</label>
							<div class="col-md-9">
								<input type="radio" name="captcha" value="yes">Так
								<input type="radio" name="captcha" value="no" checked>Ні
							</div>
						</div>
						<div class="form-group">
							<label class="col-md-3 control-label">help</label>
							<div class="col-md-9">
								<input type="text" class="form-control" name="help" placeholder="help">
							</div>
						</div>
						<div class="form-group">
							<label class="col-md-3 control-label">table*</label>
							<div class="col-md-9">
								<input type="text" class="form-control" name="table" placeholder="table" required>
							</div>
						</div>
						<div class="form-group">
							<label class="col-md-3 control-label">type*</label>
							<div class="col-md-9">
								<input type="radio" name="type" value="get" required checked>GET
								<input type="radio" name="type" value="post">POST
							</div>
						</div>
						<div class="form-group">
							<label class="col-md-3 control-label">type_data*</label>
							<div class="col-md-9">
								<input type="radio" name="type_data" value="fields" required>fields
								<input type="radio" name="type_data" value="values" checked>values
							</div>
						</div>
						<div class="form-group">
	                    	<div class="col-md-3"></div>
	                        <div class="col-md-9">
	                        	<input type="submit" class="btn btn-sm btn-warning " value="Додати">
							</div>
						</div>
					</table>
				</form>
            </div>
        </div>
    </div>
</div>
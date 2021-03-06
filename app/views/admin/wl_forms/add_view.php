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
								<small>англ. літери</small>
							</div>
						</div>
						<div class="form-group">
							<label class="col-md-3 control-label">captcha</label>
							<div class="col-md-9">
								<label><input type="radio" name="captcha" value="yes">Так</label>
								<label><input type="radio" name="captcha" value="no" checked>Ні</label>
							</div>
						</div>
						<div class="form-group">
							<label class="col-md-3 control-label">title</label>
							<div class="col-md-9">
								<input type="text" class="form-control" name="title" placeholder="title" required>
								<small>назва у боковому меню</small>
							</div>
						</div>
						<div class="form-group">
							<label class="col-md-3 control-label">type*</label>
							<div class="col-md-9">
								<label><input type="radio" name="type" value="get" required>GET</label>
								<label><input type="radio" name="type" value="post" checked>POST</label>
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
<div class="row">
    <div class="col-md-6">
        <div class="panel panel-inverse">
            <div class="panel-heading">
            	<div class="panel-heading-btn">
					<a href="<?=SITE_URL.'admin/'.$_SESSION['alias']->alias?>" class="btn btn-info btn-xs">До активних валют</a>
            	</div>
                <h4 class="panel-title">Додати валюту</h4>
            </div>
            <div class="panel-body">
	            <form action="<?=SITE_URL.'admin/'.$_SESSION['alias']->alias?>/save" method="POST">
	            	<input type="hidden" name="id" value="0">
	                <div class="table-responsive">
	                    <table class="table table-striped table-bordered nowrap" width="100%">
							<tr>
								<th>Код</th>
								<td>
									<select name="code" class="form-control">
										<option value="UAH">UAH</option>
										<option value="USD">USD</option>
										<option value="EUR">EUR</option>
										<option value="RUR">RUR</option>
									</select>
								</td>
							</tr>
							<tr>
								<th>Коефіціент (курс) відносно базової валюти</th>
								<td>
									<input type="number" name="currency" value="1" min="0" step="0.01" class="form-control">
								</td>
							</tr>
							<tr>
								<td></td>
								<td><input type="submit" class="btn btn-sm btn-success" value="Додати"></td>
							</tr>
	                    </table>
	                </div>
                </form>
            </div>
        </div>
    </div>
</div>
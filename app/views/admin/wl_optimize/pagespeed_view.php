<div class="row">
    <div class="col-md-6">
        <div class="panel panel-inverse">
            <div class="panel-heading">
                <h4 class="panel-title">Відновити структуру файлів за optimized_contents.zip</h4>
            </div>
            <div class="panel-body">
    	        <form enctype="multipart/form-data" method="POST" class="form-horizontal">
                    <div class="form-group">
                        <div class="col-md-10">
                            <input type="file" name="optimized_contents" class="form-control" required>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-sm btn-warning ">Аналізувати</button>
                        </div>
                    </div>
    	        </form>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="panel panel-inverse">
            <div class="panel-heading">
                <h4 class="panel-title">PageSpeed Insights</h4>
            </div>
            <div class="panel-body">
    	        <a href="https://developers.google.com/speed/pagespeed/insights/?url=<?=SITE_URL?>" target="_blank" class="btn btn-sm btn-info ">Перевірити сайт за допомогою Google PageSpeed Insights</a>
            </div>
        </div>
    </div>
</div>

<?php if($manifest) { ?>
<div class="row">
    <div class="panel panel-inverse">
        <div class="panel-heading">
            <h4 class="panel-title">Файли за optimized_contents.zip</h4>
        </div>
        <div class="panel-body">
        	<table class="table table-striped table-bordered nowrap" width="100%">
                <thead>
                    <tr>
                        <th>Файл</th>
                        <th>Розмір до</th>
                        <th>Розмір після</th>
                        <th>Економія</th>
                    </tr>
                </thead>
                <tbody>
                	<?php foreach ($manifest as $file) { ?>
                		<tr>
                			<td><?=$file['to']?></td>
                			<td><?=$file['to_size']?> Kb</td>
                			<td><?=$file['from_size']?> Kb</td>
                			<td><?=$file['to_size'] - $file['from_size']?> Kb <small><?=round($file['to_size'] / $file['from_size'], 1)?>%</small></td>
                		</tr>
                	<?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php } ?>
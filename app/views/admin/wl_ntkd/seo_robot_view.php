<div class="row">
    <div class="col-md-12">
        <div class="panel panel-inverse">
            <div class="panel-heading">
                <div class="panel-heading-btn">

                </div>
                <h4 class="panel-title">SEO robot:</h4>
            </div>
            <div class="panel-body" id="seo_robot">
                <div class="col-md-9">
                    <ul class="nav nav-tabs">
                        <li class="active"><a href="#article" data-toggle="tab" aria-expanded="true">Стаття / товар детально</a></li>
                        <li><a href="#groups" data-toggle="tab" aria-expanded="true">Група статтей / товарів </a></li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane active" id="article">
                            <?php if($_SESSION['language']){ ?>
                                <ul class="nav nav-tabs">
                                    <?php foreach ($_SESSION['all_languages'] as $lang) { ?>
                                        <li class="<?=($_SESSION['language'] == $lang) ? 'active' : ''?>"><a href="#language-tab-<?=$lang?>" data-toggle="tab" aria-expanded="true"><?=$lang?></a></li>
                                    <?php } ?>
                                </ul>
                                <div class="tab-content">
                                    <?php foreach ($_SESSION['all_languages'] as $lang) { ?>
                                        <div class="tab-pane fade <?=($_SESSION['language'] == $lang) ? 'active in' : ''?>" id="language-tab-<?=$lang?>">
                                            <form  class="form-horizontal ">
                                                <table>
                                                    <div class="form-group">
                                                        <label class="col-md-2 control-label">Title</label>
                                                        <div class="col-md-10">
                                                            <input type="text" class="form-control" name="title" value="" placeholder="title" >
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label class="col-md-2 control-label">Description</label>
                                                        <div class="col-md-10">
                                                            <input type="text" class="form-control" name="description" value="" placeholder="description" >
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label class="col-md-2 control-label">Keywords</label>
                                                        <div class="col-md-10">
                                                            <input type="text" class="form-control" name="keywords" value="" placeholder="keywords" >
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label class="col-md-2 control-label">Meta</label>
                                                        <div class="col-md-10">
                                                            <input type="text" class="form-control" name="meta" value="" placeholder="meta" required>
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label class="col-md-2 control-label">List</label>
                                                        <div class="col-md-10">
                                                            <input type="text" class="form-control" name="list" value="" placeholder="list" required>
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label class="col-md-2 control-label">Text</label>
                                                        <dic class="col-md-10">
                                                            <textarea class="t-big" id="editor-<?=$lang?>"></textarea>
                                                        </dic>
                                                    </div>

                                                    <div class="form-group">
                                                        <div class="col-md-2"></div>
                                                        <div class="col-md-10">
                                                            <input type="submit" class="btn btn-sm btn-warning " value="Зберегти">
                                                        </div>
                                                    </div>
                                                </table>
                                            </form>
                                        </div>
                                    <?php } ?>
                                </div>
                            <?php } else { ?>
                            <form  class="form-horizontal ">
                                <table>
                                    <div class="form-group">
                                        <label class="col-md-2 control-label">Title</label>
                                        <div class="col-md-10">
                                            <input type="text" class="form-control" name="title" value="" placeholder="title" >
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-md-2 control-label">Description</label>
                                        <div class="col-md-10">
                                            <input type="text" class="form-control" name="description" value="" placeholder="description" >
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-md-2 control-label">Keywords</label>
                                        <div class="col-md-10">
                                            <input type="text" class="form-control" name="keywords" value="" placeholder="keywords" >
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-md-2 control-label">Meta</label>
                                        <div class="col-md-10">
                                            <input type="text" class="form-control" name="meta" value="" placeholder="meta" required>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-md-2 control-label">List</label>
                                        <div class="col-md-10">
                                            <input type="text" class="form-control" name="list" value="" placeholder="list" required>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-md-2 control-label">Text</label>
                                        <dic class="col-md-10">
                                            <textarea class="t-big" id="editor"></textarea>
                                        </dic>
                                    </div>

                                    <div class="form-group">
                                        <div class="col-md-2"></div>
                                        <div class="col-md-10">
                                            <input type="submit" class="btn btn-sm btn-warning " value="Зберегти">
                                        </div>
                                    </div>
                                </table>
                            </form>
                            <?php } ?>
                        </div>
                        <div class="tab-pane " id="groups">
                           <?php if($_SESSION['language']){ ?>
                                <ul class="nav nav-tabs">
                                    <?php foreach ($_SESSION['all_languages'] as $lang) { ?>
                                        <li class="<?=($_SESSION['language'] == $lang) ? 'active' : ''?>"><a href="#language-tab2-<?=$lang?>" data-toggle="tab" aria-expanded="true"><?=$lang?></a></li>
                                    <?php } ?>
                                </ul>
                                <div class="tab-content">
                                    <?php foreach ($_SESSION['all_languages'] as $lang) { ?>
                                        <div class="tab-pane fade <?=($_SESSION['language'] == $lang) ? 'active in' : ''?>" id="language-tab2-<?=$lang?>">
                                            <form  class="form-horizontal ">
                                                <table>
                                                    <div class="form-group">
                                                        <label class="col-md-2 control-label">Title</label>
                                                        <div class="col-md-10">
                                                            <input type="text" class="form-control" name="title" value="" placeholder="title" >
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label class="col-md-2 control-label">Description</label>
                                                        <div class="col-md-10">
                                                            <input type="text" class="form-control" name="description" value="" placeholder="description" >
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label class="col-md-2 control-label">Keywords</label>
                                                        <div class="col-md-10">
                                                            <input type="text" class="form-control" name="keywords" value="" placeholder="keywords" >
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label class="col-md-2 control-label">Meta</label>
                                                        <div class="col-md-10">
                                                            <input type="text" class="form-control" name="meta" value="" placeholder="meta" required>
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label class="col-md-2 control-label">List</label>
                                                        <div class="col-md-10">
                                                            <input type="text" class="form-control" name="list" value="" placeholder="list" required>
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label class="col-md-2 control-label">Text</label>
                                                        <dic class="col-md-10">
                                                            <textarea class="t-big" id="editor2-<?=$lang?>"></textarea>
                                                        </dic>
                                                    </div>

                                                    <div class="form-group">
                                                        <div class="col-md-2"></div>
                                                        <div class="col-md-10">
                                                            <input type="submit" class="btn btn-sm btn-warning " value="Зберегти">
                                                        </div>
                                                    </div>
                                                </table>
                                            </form>
                                        </div>
                                    <?php } ?>
                                </div>
                            <?php } else { ?>
                            <form  class="form-horizontal">
                                <table>
                                    <div class="form-group">
                                        <label class="col-md-2 control-label">Title</label>
                                        <div class="col-md-10">
                                            <input type="text" class="form-control" name="title" value="" placeholder="title" >
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-md-2 control-label">Description</label>
                                        <div class="col-md-10">
                                            <input type="text" class="form-control" name="description" value="" placeholder="description" >
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-md-2 control-label">Keywords</label>
                                        <div class="col-md-10">
                                            <input type="text" class="form-control" name="keywords" value="" placeholder="keywords" >
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-md-2 control-label">Meta</label>
                                        <div class="col-md-10">
                                            <input type="text" class="form-control" name="meta" value="" placeholder="meta" required>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-md-2 control-label">List</label>
                                        <div class="col-md-10">
                                            <input type="text" class="form-control" name="list" value="" placeholder="list" required>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-md-2 control-label">Text</label>
                                        <dic class="col-md-10">
                                            <textarea class="t-big" id="editor2"></textarea>
                                        </dic>
                                    </div>

                                    <div class="form-group">
                                        <div class="col-md-2"></div>
                                        <div class="col-md-10">
                                            <input type="submit" class="btn btn-sm btn-warning " value="Зберегти">
                                        </div>
                                    </div>
                                </table>
                            </form>
                            <?php } ?>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="panel panel-inverse" data-sortable-id="ui-buttons-1" -="">
                        <div class="panel-heading">
                            <div class="panel-heading-btn">
                            </div>
                            <h4 class="panel-title">Слова</h4>
                        </div>
                        <div class="panel-body" id="words">
                            <button type="button" class="btn btn-default">{page}</button>
                            <button type="button" class="btn btn-default">{name}</button>
                            <button type="button" class="btn btn-default">{test}</button>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<script type="text/javascript" src="<?=SITE_URL?>assets/ckeditor/ckeditor.js"></script>
<script type="text/javascript" src="<?=SITE_URL?>assets/ckfinder/ckfinder.js"></script>
<script type="text/javascript">
    <?php if($_SESSION['language']) foreach($_SESSION['all_languages'] as $lng){ echo "CKEDITOR.replace( 'editor-{$lng}' ); ";echo "CKEDITOR.replace( 'editor2-{$lng}' ); ";} else echo "CKEDITOR.replace( 'editor' ); "; echo "CKEDITOR.replace( 'editor2' );"; ?>
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
    document.onreadystatechange = function () {
        if (document.readyState == "complete") {

            $('#seo_robot  input').on('click',function (e) {
                $('#seo_robot').find('#wordTarget').removeAttr('id');
                var $target = $(event.target).attr('id', 'wordTarget');
            })

            $('#words').on('click', function (e) {
                var $wordTarget = $('#wordTarget');
                if($wordTarget.length){
                    var buttonText = event.target.textContent;
                    var wordTargetValue = $wordTarget.val();
                    var cursorPos = $('#wordTarget').prop('selectionStart');

                    var textBefore = wordTargetValue.substring(0,  cursorPos );
                    var textAfter  = wordTargetValue.substring( cursorPos, wordTargetValue.length );
                    $('#wordTarget').val( textBefore + buttonText + textAfter );

                    $('#wordTarget').focus()
                    wordTarget.setSelectionRange(cursorPos + buttonText.length, cursorPos + buttonText.length);
                }
            })
       }
     }
</script>
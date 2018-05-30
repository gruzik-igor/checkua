var data;
function save (field, e, lang) {
    $('#saveing').css("display", "block");
    var value = '';
    if(e != false) value = e.value;
    else value = data;

    $.ajax({
        url: SITE_URL + "admin/wl_ntkd/save",
        type: 'POST',
        data: {
            alias: ALIAS_ID,
            content: CONTENT_ID,
            field: field,
            data: value,
            language: lang,
            additional_table : ADDITIONAL_TABLE,
            additional_table_id : ADDITIONAL_TABLE_ID,
            additional_fields : ADDITIONAL_FIELDS,
            json: true
        },
        success: function(res){
            if(res['result'] == false) {
                $.gritter.add({title:"Помилка!",text:res['error']});
            } else {
                language = '';
                if(lang) language = lang;
                $.gritter.add({title:field+' '+language,text:"Дані успішно збережено!"});
            }
            $('#saveing').css("display", "none");
        },
        error: function(){
            $.gritter.add({title:"Помилка!",text:"Помилка! Спробуйте ще раз!"});
            $('#saveing').css("display", "none");
        },
        timeout: function(){
            $.gritter.add({title:"Помилка!",text:"Помилка: Вийшов час очікування! Спробуйте ще раз!"});
            $('#saveing').css("display", "none");
        }
    });
}

CKFinder.setupCKEditor( null, {
	basePath : SITE_URL + 'assets/ckfinder/',
	filebrowserBrowseUrl : SITE_URL + 'assets/ckfinder/ckfinder.html',
	filebrowserImageBrowseUrl : SITE_URL + 'assets/ckfinder/ckfinder.html?type=Images',
	filebrowserFlashBrowseUrl : SITE_URL + 'assets/ckfinder/ckfinder.html?type=Flash',
	filebrowserUploadUrl : SITE_URL + 'assets/ckfinder/core/connector/asp/connector.asp?command=QuickUpload&type=Files',
	filebrowserImageUploadUrl : SITE_URL + 'assets/ckfinder/core/connector/asp/connector.asp?command=QuickUpload&type=Images',
	filebrowserFlashUploadUrl : SITE_URL + 'assets/ckfinder/core/connector/asp/connector.asp?command=QuickUpload&type=Flash',
});

function saveText(lang)
{
	if(lang != false){
		data = CKEDITOR.instances['editor-'+lang].getData();
	} else {
		data = CKEDITOR.instances['editor'].getData();
	}
	save('text', false, lang);
}
function showEditTKD (lang) {
	if($('#tkd-'+lang).is(":hidden")){
		$('#tkd-'+lang).slideDown("slow");
	} else {
		$('#tkd-'+lang).slideUp("fast");
	}
}

if(ALIAS_FOLDER) {
    $(function () {
        'use strict';

        // Initialize the jQuery File Upload widget:
        $('#fileupload').fileupload({
            url: SITE_URL+'admin/wl_photos/add/'+CONTENT_ID,
            formData: {
                ALIAS_ID : ALIAS_ID,
                ALIAS_FOLDER : ALIAS_FOLDER,
                PHOTO_FILE_NAME : PHOTO_FILE_NAME,
                PHOTO_TITLE : PHOTO_TITLE,
                additional_table : ADDITIONAL_TABLE,
                additional_table_id : ADDITIONAL_TABLE_ID,
                additional_fields : ADDITIONAL_FIELDS,
                json : true
            },
            autoUpload: true,
            acceptFileTypes: /(\.|\/)(jpe?g|png)$/i
        });
    });

    $( "#PHOTOS tbody.files" ).sortable({
          handle: ".sortablehandle",
          update: function( event, ui ) {
                $('#saveing').css("display", "block");
                $.ajax({
                    url: SITE_URL+"admin/wl_photos/change_position",
                    type: 'POST',
                    data: {
                        alias: ALIAS_ID,
                        content: CONTENT_ID,
                        id: ui.item.attr('id'),
                        position: ui.item.index(),
                        json: true
                    },
                    success: function(res){
                        if(res['result'] == false){
                            alert(res['error']);
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
        });
    $( "#PHOTOS tbody.files" ).disableSelection();

    document.getElementById('PHOTOS').onclick = function (event) {
        event = event || window.event;
        var target = event.target || event.srcElement;
        if(target.tagName == "IMG")
        {
            var link = target.src ? target.parentNode : target,
                options = {index: link, event: event},
                links = this.getElementsByTagName('a');
            blueimp.Gallery(links, options);
        }
    };
};

function savePhoto(id, e)
{
    $('#pea-saveing-'+id).css("display", "block");
    $.ajax({
        url: SITE_URL+"admin/wl_photos/save",
        type: 'POST',
        data: {
            photo: id,
            name: e.name,
            title: e.value,
            additional_table : ADDITIONAL_TABLE,
            additional_table_id : ADDITIONAL_TABLE_ID,
            additional_fields : ADDITIONAL_FIELDS,
            json: true
        },
        success: function(res) {
            if(res['result'] == false) {
                alert(res['error']);
            } else {
                if(e.name == 'main') {
                    $('.PHOTO_MAIN').attr('disabled', false);
                    e.setAttribute('disabled', 'disabled');
                    PHOTO_MAIN = id;
                }
            }
            $('#pea-saveing-'+id).css("display", "none");
        },
        error: function(){
            alert("Помилка! Спробуйте ще раз!");
            $('#pea-saveing-'+id).css("display", "none");
        },
        timeout: function(){
            alert("Помилка: Вийшов час очікування! Спробуйте ще раз!");
            $('#pea-saveing-'+id).css("display", "none");
        }
    });
}

function deletePhoto(id){
	if (confirm("Ви впевнені, що хочете видалити фотографію? \nУВАГА, інформація відновленню НЕ ПІДЛЯГАЄ!")) {
		$('#pea-saveing-'+id).css("display", "block");
		$.ajax({
			url: SITE_URL+"admin/wl_photos/delete",
			type: 'POST',
			data: {
				photo: id,
                ALIAS_FOLDER : ALIAS_FOLDER,
                additional_table : ADDITIONAL_TABLE,
	            additional_table_id : ADDITIONAL_TABLE_ID,
	            additional_fields : ADDITIONAL_FIELDS,
				json: true
			},
			success: function(res){
				if(res['result'] == false){
					alert(res['error']);
				} else {
                    $("#photo-"+id).remove();
                    if(res.main)
                    {
                        $('.PHOTO_MAIN').attr('disabled', false);
                        PHOTO_MAIN = res.main;
                        $('#photo-'+res.main+' button.PHOTO_MAIN').attr('disabled', 'disabled')
                    }
                }
			},
			error: function(){
                alert("Помилка! Спробуйте ще раз!");
                $('#pea-saveing-'+id).css("display", "none");
            },
            timeout: function(){
                alert("Помилка: Вийшов час очікування! Спробуйте ще раз!");
                $('#pea-saveing-'+id).css("display", "none");
            }
		});
	}
}


$("#modal-groupsTree").on("show.bs.modal", function(e) {
    $.ajax({
        url: ALIAS_ADMIN_URL+'_get_groupsTree?product='+CONTENT_ID,
        success: function(res){
            $("#modal-groupsTree").find(".modal-body").html(res);

            var to = false;
            $('#search').keyup(function () {
                if(to) { clearTimeout(to); }
                to = setTimeout(function () {
                    var v = $('#search').val();
                    $('#jstree').jstree(true).search(v);
                }, 250);
            });

            $('#jstree')
                .on("changed.jstree", function (e, data) {
                    $('#selected').val(data.selected);
                })
                .jstree(
                    {plugins: ["wholerow", "checkbox", "search"]}
                ); 
        },
        error: function(){
            alert("Помилка! Спробуйте ще раз!");
        },
        timeout: function(){
            alert("Помилка: Вийшов час очікування! Спробуйте ще раз!");
        }
    });
});


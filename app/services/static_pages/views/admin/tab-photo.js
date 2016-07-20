$(function () {
    'use strict';

    // Initialize the jQuery File Upload widget:
    $('#fileupload').fileupload({
        url: ALIAS_URL+'photo_add/',
        autoUpload: true,
        acceptFileTypes: /(\.|\/)(jpe?g|png)$/i
    });

});

function savePhoto(id, e){
    $('#pea-saveing-'+id).css("display", "block");
    $.ajax({
        url: ALIAS_URL+"photo_save",
        type: 'POST',
        data: {
            photo: id,
            name: e.name,
            title: e.value,
            json: true
        },
        success: function(res){
            if(res['result'] == false){
                alert(res['error']);
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
			url: ALIAS_URL+"photo_delete",
			type: 'POST',
			data: {
				photo: id,
				json: true
			},
			success: function(res){
				if(res['result'] == false){
					alert(res['error']);
				} else $("#photo-"+id).remove();
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
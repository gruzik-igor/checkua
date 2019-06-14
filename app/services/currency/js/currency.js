$(function () {
	$('#ModalEditCurrency').on('show.bs.modal', function (event) {
		var button = $(event.relatedTarget),
			id = button.data('currencyid'),
            code = button.data('currencyсode'),
			title = button.attr('title');

		var modal = $(this);
		modal.find('.modal-title').html(title);
		modal.find('#currencyId').val(id);
        modal.find('#currencyCode').val(code);
		modal.find('#currencyValue').val($('#currency-'+id).text());
	});
});

function updateCurrency() {
	$('#saveing').css("display", "block");

    $.ajax({
        url: ALIAS_ADMIN_URL + "save",
        type: 'POST',
        data: {
            id: currencyId.value,
            currency: document.forms.FormEditCurrency['currency'].value,
            json: true
        },
        success: function(res){
            if(res['success'])
            {
                $('#currency-'+currencyId.value).text(document.forms.FormEditCurrency['currency'].value);
                $.gritter.add({title:"Курс валют!",text:'Валюту <strong>'+ currencyCode.value +'</strong> оновлено'});
            }
            else
            	$.gritter.add({title:"Помилка!",text:res['error']});
            $('#saveing').css("display", "none");
            $('#ModalEditCurrency').modal('hide');
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
    return false;
}

function setDefault(id, code) {
    $('#saveing').css("display", "block");

    $.ajax({
        url: ALIAS_ADMIN_URL + "save",
        type: 'POST',
        data: {
            id: id,
            default: 1,
            json: true
        },
        success: function(res){
            if(res['success'])
                $.gritter.add({title:"Курс валют!",text:'Валюту <strong>'+code+'</strong> встановлено по замовчуванню'});
            else
                $.gritter.add({title:"Помилка!",text:res['error']});
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
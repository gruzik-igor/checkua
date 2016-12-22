$(function () {
	$('#ModalEditCurrency').on('show.bs.modal', function (event) {
		var button = $(event.relatedTarget),
			id = button.data('currencyid'),
			value = button.data('currencyvalue'),
			title = button.attr('title');

		var modal = $(this);
		modal.find('.modal-title').html(title);
		modal.find('#currencyId').val(id);
		modal.find('#currencyValue').val(value);
	});
});
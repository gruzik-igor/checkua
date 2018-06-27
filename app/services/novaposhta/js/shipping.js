$("#shipping-cities").removeClass('hidden');
$("#shipping-cities input").attr('required', 'required');

$("#shipping-cities").autocomplete({
    source: cities,
    select: function (event, ui) {
        var address = ui.item.value;

        $("#shipping-novaposhta").empty().append('<option selected disabled="" value="">Виберіть відділення</option>');
        $.each(warehouse_by_city[address], function(i, p) {
             $("#shipping-novaposhta").append($('<option></option>').val('№'+p.number+' : '+p.address).html('№'+p.number+' : '+p.address));
        });
    }
});
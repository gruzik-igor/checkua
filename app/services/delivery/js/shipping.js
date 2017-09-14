function changeInfo(el) {

    $("#shipping-address").attr('placeholder', placeholders[$(el).val()]);
    $("#shipping-info").text(information[$(el).val()]);

    if($("#shipping-method option:selected").text().toLowerCase() != 'нова пошта'.toLowerCase()){
        $("#shipping-department").addClass('hidden').empty();
        $("#shipping-department-other").removeClass('hidden');
    }
    else {
        $("#shipping-department-other, #novaPoshtaDepartments").addClass('hidden');
        $("#shipping-cities").val('');
    }
}

$("#shipping-cities").autocomplete({
    source: cities,
    select: function (event, ui) {
        var address = ui.item.value;

        geocodeAddress(geocoder, map, address);

        $("#novaPoshtaDepartments").removeClass('hidden');

        if($("#shipping-method option:selected").text().toLowerCase() == 'нова пошта'.toLowerCase()){
            $("#shipping-department-other").addClass('hidden');
            $("#shipping-department").removeClass('hidden').empty().append('<option selected disabled="" value="">Виберіть відділення</option>');

            $.each(warehouse_by_city[ui.item.value], function(i, p) {
                 $("#shipping-department").append($('<option></option>').val('№'+p.number+' : '+p.address).html('№'+p.number+' : '+p.address));
            });
        }
        else {
            $("#shipping-department").addClass('hidden').empty();
            $("#shipping-department-other").removeClass('hidden');
        }

    }
});

function initMap() {
    map = new google.maps.Map(document.getElementById('map'), {
        zoom: 12,
        center: {lat: 50.4501, lng: 30.5234}
    });
    geocoder = new google.maps.Geocoder();

    marker = new google.maps.Marker({
        map: map
    });

    var address = $("#shipping-department").val() ? ($("#shipping-cities").val() + ' нова пошта ' + $("#shipping-department").val()).replace(/[{()}]/g, ' ').replace('обл.', 'область').replace('р-н', 'район') : $("#shipping-cities").val();

    document.getElementById('shipping-department').addEventListener('change', function() {
        var address = ($("#shipping-cities").val() + ' нова пошта ' + $("#shipping-department").val()).replace(/[{()}]/g, ' ').replace('обл.', 'область').replace('р-н', 'район');
        geocodeAddress(geocoder, map, address);
    });

    geocodeAddress(geocoder, map, address);
  }

function geocodeAddress(geocoder, resultsMap, address) {
    var address = address;
    geocoder.geocode({'address': address}, function(results, status) {
        if (status === 'OK') {
            resultsMap.setCenter(results[0].geometry.location);

            marker.setPosition(results[0].geometry.location);

            $("#map").css('visibility', 'visible');
        } else {
            console.log('Geocode was not successful for the following reason: ' + status);
        }
    });
}
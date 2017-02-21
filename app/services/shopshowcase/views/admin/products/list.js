$( "#data-table tbody" ).sortable({
      handle: ".sortablehandle",
      update: function( event, ui ) {
            $('#saveing').css("display", "block");
            $.ajax({
                url: ALIAS_ADMIN_URL+"change_position",
                type: 'POST',
                data: {
                    id: ui.item.attr('id'),
                    position: ui.item.index(),
                    json: true
                },
                success: function(res){
                    if(res['result'] == false){
                        alert("Помилка! Спробуйте ще раз!");
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
$( "#data-table tbody.files" ).disableSelection();

function changeAvailability(e, id) {
    $.ajax({
        url: ALIAS_ADMIN_URL+"changeAvailability",
        type: 'POST',
        data: {
            availability :  e.value,
            id :  id,
            json : true
        },
        success: function(res){
            if(res['result'] == false){
                alert('Помилка! Спробуйте щераз');
            }
        }
    });
}
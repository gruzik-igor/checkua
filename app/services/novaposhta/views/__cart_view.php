<div class="form-group" id="shipping-novaposhta" >
    <label><?=$this->text('Відділення')?></label>
    <select name="shipping-novaposhta" class="form-control" required>
        <option selected disabled value="">Введіть місто</option>
    </select>
</div>

<?php $novaposhta_selected = $this->data->re_post('shipping-novaposhta');
if(empty($novaposhta_selected) && $userShipping && $userShipping->department)
    $novaposhta_selected = $userShipping->department; 
$this->load->js_init('initShipping()'); ?>
<script>
    var warehouse_by_city = <?= $warehouse_by_city ?>;
    var novaposhta_selected = '<?=$novaposhta_selected?>';

    function initShipping() {
        $("#shipping-cities").removeClass('hidden');
        $("#shipping-cities input").attr('required', 'required');

        $("#shipping-cities input").autocomplete({
            source: cities,
            select: function (event, ui) {
                var address = ui.item.value;

                $("#shipping-novaposhta select").empty().append('<option selected disabled="" value="">Виберіть відділення</option>');
                $.each(warehouse_by_city[address], function(i, p) {
                     $("#shipping-novaposhta select").append($('<option></option>').val('№'+p.number+' : '+p.address).html('№'+p.number+' : '+p.address));
                });
            }
        });

        var address = $("#shipping-cities input").val();
        if(address != '')
        {
            $("#shipping-novaposhta select").empty().append('<option selected disabled="" value="">Виберіть відділення</option>');
            $.each(warehouse_by_city[address], function(i, p) {
                var value = '№'+p.number+' : '+p.address;
                if(value == novaposhta_selected)
                    $("#shipping-novaposhta select").append($('<option></option>').val(value).html(value).attr('selected', 'selected'));
                else
                    $("#shipping-novaposhta select").append($('<option></option>').val(value).html(value));
            });
        }
    }
</script>
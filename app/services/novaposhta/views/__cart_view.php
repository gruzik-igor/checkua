<div class="form-group" id="shipping-novaposhta" >
    <label><?=$this->text('Відділення')?></label>
    <select name="shipping-novaposhta" class="form-control" required></select>
</div>

<script>
    var warehouse_by_city = <?= $warehouse_by_city ?>;

<?php if(isset($_POST['ajax'])) { ?>
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
<?php }
else
    $_SESSION['alias-cache'][$_SESSION['alias']->alias_from]->alias->js_load[] = 'js/'.$_SESSION['alias']->alias.'/shipping.js'; ?>
</script>
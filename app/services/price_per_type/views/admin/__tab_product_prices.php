<?php $userTypes = $this->db->getAllDataByFieldInArray('wl_user_types', 1, 'active');
    $shopData = $productData = array();
    if($rows = $this->db->getAllDataByFieldInArray($_SESSION['service']->table, $product->wl_alias, 'shop_alias'))
        foreach ($rows as $row) {
            $shopData[$row->user_type] = array('change_price' => $row->change_price, 'price' => $row->price);
        }
    if($rows = $this->db->getAllDataByFieldInArray($_SESSION['service']->table.'_product', array('product_alias' => $product->wl_alias, 'product_id' => $product->id)))
        foreach ($rows as $row) {
            $productData[$row->user_type] = array('change_price' => $row->change_price, 'price' => $row->price);
        }
        ?>
<table id="data-table" class="table table-striped table-bordered nowrap" width="100%">
    <thead>
        <tr>
            <th>Тип користувача</th>
            <th>Режим зміни відносно базової ціни</th>
            <th>Зміна ціни на</th>
        </tr>
    </thead>
    <tbody>
        <?php if($userTypes) foreach ($userTypes as $type) {
            $change_price = '+';
            $price = 0;
            if(isset($shopData[$type->id]))
            {
                $change_price = $shopData[$type->id]['change_price'];
                $price = $shopData[$type->id]['price'];
            }
            if(isset($productData[$type->id]))
            {
                $change_price = $productData[$type->id]['change_price'];
                $price = $productData[$type->id]['price'];
            } ?>
            <tr>
                <th>
                    <?=$type->title?> <br>
                    <label><input type="checkbox" onchange="updatePPT(this, <?=$type->id?>)" <?=(isset($productData[$type->id]))?'':'checked'?>> Стандартна націнка</label>
                </th>
                <td>
                    <select class="form-control" id="change_price-ppt-<?=$type->id?>" onchange="savePPTChangePrice(<?=$type->id?>, '<?=$type->title?>')" <?=(isset($productData[$type->id]))?'':'disabled'?>>
                        <option value="+">+ додати фіксовані у.о.</option>
                        <option value="*" <?=($change_price == '*') ? 'selected' : ''?>>* помножити на коефіцієнт</option>
                        <option value="=" <?=($change_price == '=') ? 'selected' : ''?>>= точне значення</option>
                    </select>
                </td>
                <td>
                    <input type="number" step="0.001" value="<?=$price?>" id="price-ppt-<?=$type->id?>" class="form-control" onchange="savePPTChangePrice(<?=$type->id?>, '<?=$type->title?>')" <?=(isset($productData[$type->id]))?'':'disabled'?>>
                </td>
            </tr>
        <?php } ?>
    </tbody>
</table>

<script>
    function updatePPT(e, type_id, label) {
        if(e.checked)
        {
            $('#change_price-ppt-'+type_id+', #price-ppt-'+type_id).attr("disabled", "disabled");

            $('#saveing').css("display", "block");
            $.ajax({
              url: "<?=SITE_URL?>admin/<?=$_SESSION['alias']->alias?>/deleteForProduct",
              type: 'POST',
              data: {
                shop_id: <?=$product->wl_alias?>,
                product_id: <?=$product->id?>,
                type_id: type_id,
                json: true
              },
              success: function(res){
                if(res['result'] == false){
                    $.gritter.add({title:"Помилка!", text:label + ' ' + res['error']});
                } else {
                  $.gritter.add({title:label, text:"Дані успішно збережено!"});
                }
                $('#saveing').css("display", "none");
              },
              error: function(){
                $.gritter.add({title:"Помилка!", text:"Помилка! Спробуйте ще раз!"});
                $('#saveing').css("display", "none");
              },
              timeout: function(){
                $.gritter.add({title:"Помилка!", text:"Помилка: Вийшов час очікування! Спробуйте ще раз!"});
                $('#saveing').css("display", "none");
              }
            });
        }
        else
            $('#change_price-ppt-'+type_id+', #price-ppt-'+type_id).attr("disabled", false);
    }
    function savePPTChangePrice(type_id, label) {
        $('#saveing').css("display", "block");
        $.ajax({
          url: "<?=SITE_URL?>admin/<?=$_SESSION['alias']->alias?>/saveForProduct",
          type: 'POST',
          data: {
            shop_id: <?=$product->wl_alias?>,
            product_id: <?=$product->id?>,
            type_id: type_id,
            change_price: document.getElementById('change_price-ppt-'+type_id).value,
            price: document.getElementById('price-ppt-'+type_id).value,
            json: true
          },
          success: function(res){
            if(res['result'] == false){
                $.gritter.add({title:"Помилка!", text:label + ' ' + res['error']});
            } else {
              $.gritter.add({title:label, text:"Дані успішно збережено!"});
            }
            $('#saveing').css("display", "none");
          },
          error: function(){
            $.gritter.add({title:"Помилка!", text:"Помилка! Спробуйте ще раз!"});
            $('#saveing').css("display", "none");
          },
          timeout: function(){
            $.gritter.add({title:"Помилка!", text:"Помилка: Вийшов час очікування! Спробуйте ще раз!"});
            $('#saveing').css("display", "none");
          }
        });
    }
</script>
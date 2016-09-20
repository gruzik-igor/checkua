<div class="container">
    <div class="row">
        <div class="col-lg-6">
            <div class="form-group">
                <label for="name">Служба доставки</label>
                <select id="shipping-method" class="form-control" required onchange="changePlaceholder(this)">
                	<option value="0" disabled <?=($delivery->method == 0) ? 'selected' : ''?>>Не вказано</option>
                	<?php if($methods) foreach ($methods as $method) { ?>
                		<option value="<?=$method->id?>" <?php if($delivery->method == $method->id){ echo 'selected'; $placeholder = $method->placeholder; } ?> ><?=$method->name?></option>
                	<?php } ?>
                </select>
            </div>
            <div class="form-group">
                <label>Адреса доставки/№ відділення</label>
                <textarea id="shipping-address" placeholder="<?= $placeholder ?>" class="form-control" required><?=$delivery->address?></textarea>
            </div>
            <div class="form-group">
                <label><input type="checkbox" id="shipping-default" value="1">
                Використовувати ці дані, як інформацію по доставці за замовчуванням</label>
            </div>
        </div>
    </div>
</div>

<script>
    function changePlaceholder(el) {
        var placeholders = {
            <?php if($methods) foreach ($methods as $method)
                echo "\"$method->id\"" . ' : ' . ($method->placeholder != '' ? "\"$method->placeholder\"" : '"Адреса доставки/№ відділення"')  . ', ';
            ?>
        }
        $("#shipping-address").attr('placeholder', placeholders[$(el).val()]);
        
    }
</script>
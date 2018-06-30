<div class="row">
	<h3 class="title mt0"><?=$this->text('Доставка')?></h3>
</div>

<?php $shippingType = $shippings[0]->type;
$shippingInfo = $shippings[0]->info;
$shippingWlAlias = $shippings[0]->wl_alias;
if(count($shippings) > 1) {
    $selected = $this->data->re_post('shipping-method');
    if(empty($selected) && $userShipping && $userShipping->method)
        $selected = $userShipping->method;
    ?>
    <div class="form-group">
        <label for="shipping-method"><?=$this->text('Служба доставки')?></label>
        <select name="shipping-method" class="form-control" required onchange="changeShipping(this)">
            <?php foreach ($shippings as $method) { ?>
                <option value="<?=$method->id?>" <?php if($selected == $method->id) { echo 'selected'; $shippingType = $method->type; $shippingInfo = $method->info; $shippingWlAlias = $method->wl_alias; } ?> ><?=$method->name?></option>
            <?php } ?>
        </select>
    </div>
<?php } else { ?>
    <input type="hidden" name="shipping-method" value="<?=$shippings[0]->id?>">
<?php } ?>

<div class="alert alert-warning" id="shipping-info" <?=(empty($shippingInfo)) ? 'style="display:none"':''?>>
    <?=$shippingInfo?>
</div>

<?php $city = $this->data->re_post('shipping-city');
if(empty($city) && $userShipping && $userShipping->city)
    $city = $userShipping->city; ?>
<div class="form-group <?= $shippingType == 1 || $shippingType == 2 ? '' : 'hidden' ?>" id="shipping-cities">
    <label><?=$this->text('Місто')?></label>
    <input type="text" name="shipping-city" list="shipping-cities-list" class="form-control" placeholder="<?=$this->text('Місто')?>" value="<?= $city ?>" <?= $shippingType == 1 || $shippingType == 2 ? 'required' : '' ?>>
    <datalist id="shipping-cities-list"></datalist>
</div>

<?php $department = $this->data->re_post('shipping-department');
if(empty($department) && $userShipping && $userShipping->department)
    $department = $userShipping->department; ?>
<div class="form-group <?= $shippingType == 2 ? '' : 'hidden' ?>" id="shipping-departments" >
    <label><?=$this->text('Відділення')?></label>
    <input type="text" name="shipping-department" class="form-control" value="<?= $department ?>" placeholder="<?=$this->text('Введіть номер/адресу відділення')?>" <?= $shippingType == 2 ? 'required' : '' ?>>
</div>

<?php $address = $this->data->re_post('shipping-address');
if(empty($address) && $userShipping && $userShipping->address)
    $address = $userShipping->address; ?>
<div class="form-group <?= $shippingType == 1 ? '' : 'hidden' ?>" id="shipping-address">
    <label><?=$this->text('Адреса')?></label>
    <textarea class="form-control" name="shipping-address" placeholder="<?=$this->text('Поштовий індекс, вул. Київська 12, кв. 3')?>" rows="3" <?= $shippingType == 1 ? 'required' : '' ?>><?= $address ?></textarea>
</div>

<div id="Shipping_to_cart">
	<?php if($shippingWlAlias != $_SESSION['alias']->id)
		$this->load->function_in_alias($shippingWlAlias, '__get_Shipping_to_cart', $userShipping); ?>
</div>

<div class="row">
	<label class="col-md-3"><?=$this->text('Отримувач')?></label>
</div>
<div class="row">
	<div class="form-group col-sm-6">
		<div class="required">
            <?php $userName = $this->data->re_post('recipient');
            if(empty($userName))
            {
                $userName = $userShipping && $userShipping->userName ? $userShipping->userName : '';
                if($userName == '' && $this->userIs())
                    $userName = $_SESSION['user']->name;
            }
            $phone = $this->data->re_post('phone');
            if(empty($phone) && $userShipping && $userShipping->userPhone)
                $phone = $userShipping->userPhone;
             ?>
			<input type="text" name="recipient" id="recipientName" class="form-control" placeholder="<?=$this->text('Ім\'я Прізвище отримувача')?>" value="<?= $userName ?>" required>
		</div>
	</div>
	<div class="form-group col-sm-6">
		<div class="required">
			<input type="phone" name="phone" class="form-control" placeholder="<?=$this->text('+380********* (Контактний номер)')?>" value="<?= $phone ?>" required>
		</div>
	</div>
</div>

<script>
var shippingsTypes = {
    <?php if($shippings) foreach ($shippings as $method)
        echo "\"$method->id\"" . ' : "' . $method->type. '", ';
    ?>
};
var shippingsInformation = {
    <?php if($shippings) foreach ($shippings as $method)
    {
        $method->info = nl2br($method->info);
        $method->info = preg_replace("/[\n\r]/","", $method->info); 
        echo "\"$method->id\"" . ' : ' . ($method->info != '' ? "\"$method->info\"" : '""')  . ', ';
    }
    ?>
};
</script>
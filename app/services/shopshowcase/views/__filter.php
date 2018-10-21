<div class="col-sm-3 dn-xs" id="product-filter">
    <div class="widget">
        <form>

        <?php if($products)
            foreach ($products as $product) {
                if($product->old_price > $product->price) {
         ?>
            <ul class="list list-unstyled">
                <li>
                    <div class="checkbox-input checkbox-default">
                        <input id="sale" class="styled" type="checkbox" name="sale" value="1" <?= isset($_GET['sale']) ? 'checked' : '' ?> />
                        <label for="sale">
                             <?= $this->text('Акційні'); ?></span>
                        </label>
                    </div>
                </li>
            </ul>
        <?php break; }
        }

        foreach ($filters as $filter) {
            usort($filter->values, function($a, $b) { return strcmp($a->name, $b->name); }); 
            if(count($filter->values) > 1) { ?>
                <h6 class="subtitle"><?=$filter->name?></h6>
                <ul class="list list-unstyled">
                    <?php foreach ($filter->values as $value) {
                        $checked = '';
                        if(isset($_GET[$filter->alias]) && is_array($_GET[$filter->alias]) && in_array($value->id, $_GET[$filter->alias])) $checked = 'checked';
                        ?>
                        <li>
                            <div class="checkbox-input checkbox-default">
                                <input id="filter-<?=$filter->id?>-<?=$value->id?>" class="styled" type="checkbox" name="<?=$filter->alias?>[]" value="<?=$value->id?>" <?=$checked?> >
                                <label for="filter-<?=$filter->id?>-<?=$value->id?>">
                                    <?=$value->name?></span>
                                </label>
                            </div>
                        </li>
                    <?php } ?>
                </ul>
            <?php } } ?>

            <?php if($products)
            $money = array(); $x = 0;
            foreach ($products as $product) {
                $money[$x] = $product->price;
            $x++;} 
            sort($money); foreach ($money as $minmoney) {
                $min = $minmoney;
                break;
            }
            rsort($money); foreach ($money as $maxmoney) {
                $max = $maxmoney;
                break;
            }
            if($max > $min) { ?>
                <h6 class="subtitle"><?=$this->text('Вартість')?></h6>
                <div class="price-range" data-start-min="<?=$min?>" data-start-max="<?=$max?>" data-min="0" data-max="<?=$max + $min?>" data-step="0.01">
                    <input type="hidden" name="price_min" id="mimim">
                    <input type="hidden" name="price_max" id="maxi">
                    <div class="ui-range-values">
                        <div class="ui-range-value-min">
                            <span></span>грн
                            <input type="hidden">
                        </div> -
                        <div class="ui-range-value-max">
                            <span></span>грн
                            <input type="hidden">
                        </div>
                    </div>
                    <div class="ui-range-slider"></div>
                </div>
            <?php } ?>
            <button type="submit" class="btn btn-default btn-block btn-md"><?=$this->text('Знайти')?></button>
        </form>
    </div>
</div>
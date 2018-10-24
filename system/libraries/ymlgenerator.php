<?php

include 'YmlGenerator/Model/Offer/OfferGroupTrait.php';
include 'YmlGenerator/Model/Offer/OfferGroupAwareInterface.php';
include 'YmlGenerator/Model/Offer/OfferInterface.php';
include 'YmlGenerator/Model/Offer/AbstractOffer.php';
include 'YmlGenerator/Model/Offer/OfferSimple.php';
include 'YmlGenerator/Model/Offer/OfferParam.php';
include 'YmlGenerator/Model/Category.php';
include 'YmlGenerator/Model/Currency.php';
include 'YmlGenerator/Model/Delivery.php';
include 'YmlGenerator/Model/ShopInfo.php';
include 'YmlGenerator/Settings.php';
include 'YmlGenerator/Generator.php';

use Bukashk0zzz\YmlGenerator\Model\Offer\OfferSimple;
use Bukashk0zzz\YmlGenerator\Model\Offer\OfferParam;
use Bukashk0zzz\YmlGenerator\Model\Category;
use Bukashk0zzz\YmlGenerator\Model\Currency;
use Bukashk0zzz\YmlGenerator\Model\Delivery;
use Bukashk0zzz\YmlGenerator\Model\ShopInfo;
use Bukashk0zzz\YmlGenerator\Settings;
use Bukashk0zzz\YmlGenerator\Generator;

class ymlgenerator extends Controller {

    private $groupsInList = array();

    public function createYml($products, $groups)
    {
        set_time_limit(0);

        $currencies = $categories = array();

        $file = 'php://output';
        $settings = (new Settings())->setOutputFile($file);

        $shopInfo = (new ShopInfo())
            ->setName(SITE_NAME)
            ->setUrl(SITE_URL);

        $currencies[] = (new Currency())
            ->setId('UAH')
            ->setRate(1);
        // $currencies[] = (new Currency())
        //     ->setId('USD')
        //     ->setRate(30);

        if($groups)
            foreach ($groups as $group) 
            {
                if(!in_array($group->id, $this->groupsInList))
                {
                    $categories[] = (new Category())
                        ->setId($group->id)
                        ->setParentId($group->parent)
                        ->setName($group->name);
                    $this->groupsInList[] = $group->id;
                }
            }

        $offers = [];
        foreach ($products as $product) 
        {
            if(!empty($product->group) && is_array($product->group))
                foreach ($product->group as $group)
                {
                    $productId = $product->id .'-'.$group->id;

                    $offerSimple = (new OfferSimple())
                        ->setId($productId)
                        // ->setId($product->id)
                        ->setSellingType('u')
                        ->setUrl(SITE_URL.$product->link)
                        ->setPrice($product->price)
                        ->setCurrencyId('UAH')
                        ->setDelivery(true)
                        ->setName($product->name)
                        ->setPictures($product->images)
                        ->setVendorCode($product->article)
                        ->setCategoryId($group->id)
                        ->setDescription($product->text);
                    if(!empty($product->vendor))
                        $offerSimple->setVendor($product->vendor);

                    if($product->old_price > 0 && $product->old_price > $product->price)
                        $offerSimple->setOldPrice($product->old_price);

                    if($product->useAvailability)
                    {
                        if($product->availability == 1)
                            $offerSimple->setAvailable(true);
                        if($product->availability == 2)
                            $offerSimple->setAvailable(false);
                    }
                    else
                        $offerSimple->setAvailable(true);

                    if(!empty($product->options))
                        foreach ($product->options as $name => $value)
                        {
                            $offerSimple->addParam((new OfferParam())
                                ->setName($name)
                                ->setValue($value));
                        }

                    if(!empty($product->prices) && is_array($product->prices))
                        $offerSimple->addParam((new OfferParam())
                                ->setName('prices')
                                ->setValue($product->prices));

                    $offerSimple->addParam((new OfferParam())
                                ->setName('Состояние')
                                ->setValue('Новое'));

                    $offers[] = $offerSimple;
                }
            else
            {
                $offerSimple = (new OfferSimple())
                    ->setId($product->id)
                    ->setSellingType('u')
                    ->setUrl(SITE_URL.$product->link)
                    ->setPrice($product->price)
                    ->setCurrencyId('UAH')
                    ->setDelivery(true)
                    ->setName($product->name)
                    ->setPictures($product->images)
                    ->setVendorCode($product->article)
                    ->setDescription($product->text);
                if(!empty($product->group))
                    $offerSimple->setCategoryId($product->group);
                if($product->old_price > 0 && $product->old_price > $product->price)
                    $offerSimple->setOldPrice($product->old_price);
                if(!empty($product->vendor))
                    $offerSimple->setVendor($product->vendor);

                if($product->useAvailability)
                {
                    if($product->availability == 1)
                        $offerSimple->setAvailable(true);
                    if($product->availability == 2)
                        $offerSimple->setAvailable(false);
                }
                else
                    $offerSimple->setAvailable(true);

                if(!empty($product->options))
                    foreach ($product->options as $name => $value)
                    {
                        $offerSimple->addParam((new OfferParam())
                            ->setName($name)
                            ->setValue($value));
                    }

                if(!empty($product->prices) && is_array($product->prices))
                    $offerSimple->setPrices($product->prices);

                $offerSimple->addParam((new OfferParam())
                            ->setName('Состояние')
                            ->setValue('Новое'));

                $offers[] = $offerSimple;
            }
        }

        // header("Content-Type: text/xml");
        header("Content-Type: text/xml; charset=windows-1251");

        (new Generator($settings))->generate(
            $shopInfo,
            $currencies,
            $categories,
            $offers
        );

        // $this->showTime();
        exit;
    }

    private function showTime()
    {
        $mem_end = memory_get_usage();
        $time_end = microtime(true);
        $time = $time_end - $GLOBALS['time_start'];
        $mem = $mem_end - $GLOBALS['mem_start'];
        $mem = round($mem/1024, 5);
        if($mem > 1024)
        {
            $mem = round($mem/1024, 5);
            $mem = (string) $mem . ' Мб';
        }
        else
            $mem = (string) $mem . ' Кб';

        $after = ($_SESSION['cache']) ? 'Cache активний' : 'Cache відключено';
        echo '<hr><center>Час виконання: '.round($time, 5).' сек. Використанок памяті: '.$mem.'. Запитів до БД: '.$this->db->count_db_queries.'. '.$after.'</center>';
    }
}
?>
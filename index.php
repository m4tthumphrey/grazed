<?php

require 'lib.php';

$products = get_products();
$boxes = json_decode(file_get_contents('json/boxes.json'))->boxes;

// pre 5.3
foreach ($products as $product) {
    $product->frequency = 0;
    $product->sendSoon = 0;
}

foreach ($boxes as $box) {
    foreach ($box->products as $product) {
        if (array_key_exists($product, $products)) {
            $products[$product]->frequency++;

            if (in_array($product, (array)$box->sendSoonProducts)) {
                $products[$product]->sendSoon++;
            }

        }
    }
}

$products = array_sort($products, 'frequency', SORT_DESC);

echo '<ul>';
foreach ($products as $product) {
    if ($product->frequency) {
        $send_soon_percentage = round(($product->sendSoon /$product->frequency) * 100).'%';
        echo '<li style="float: left; width: 250px; height: 120px; font-size: 11px; font-family: helvetica; text-align: center;">'.$product->productName.'<br>had '.$product->frequency.' times, '.$send_soon_percentage.' send soon<br><img style="width: 100px; margin-top: 5px;" src="'.$product->thumb.'"></li>';
    }
}
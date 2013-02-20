<?php

require 'lib.php';

$product_ids = array();
foreach (range('a', 'z') as $i) {
    $id_json = file_get_contents('json/searches/'.$i.'.json');
    $id_data = json_decode($id_json);

    if ($id_data->success) {
        $product_ids = array_merge($product_ids, $id_data->products);
    }
}

$product_ids = array_unique($product_ids);

foreach ($product_ids as $id) {
    $data = file_get_contents($product_details.$id.'.json');
    file_put_contents('json/products/'.$id.'.json', $data);
}
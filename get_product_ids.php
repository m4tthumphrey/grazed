<?php

require 'lib.php';

$product_ids = array();
foreach (range('a', 'z') as $i) {
    $id_json = file_get_contents($product_search.$i);
    $id_data = json_decode($id_json);

    if ($id_data->success) {
        $product_ids = array_merge($product_ids, $id_data->products);
        file_put_contents('searches/json/'.$i.'.json', $id_json);
    }
}
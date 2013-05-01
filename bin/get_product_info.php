<?php

require '../app/bootstrap.php';

$product_ids = array();
foreach (array('a', 'e', 'i', 'o', 'u') as $i) {
    $id_json = file_get_contents(JSON_PATH.'/searches/'.$i.'.json');
    $id_data = json_decode($id_json);

    if ($id_data->success) {
        $product_ids = array_merge($product_ids, $id_data->products);
    }
}

$product_ids = array_unique($product_ids);

foreach ($product_ids as $id) {
    $data = file_get_contents(PRODUCT_DETAILS_URL.$id.'.json');
    file_put_contents(JSON_PATH.'/products/'.$id.'.json', $data);
}
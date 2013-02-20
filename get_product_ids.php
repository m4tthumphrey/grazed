<?php

require 'lib.php';

foreach (array('a', 'e', 'i', 'o', 'u') as $i) {
    $id_json = file_get_contents($product_search.$i);
    $id_data = json_decode($id_json);

    if ($id_data->success) {
        file_put_contents('json/searches/'.$i.'.json', $id_json);
    }
}
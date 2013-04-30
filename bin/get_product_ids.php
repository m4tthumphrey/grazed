<?php

require '../app/bootstrap.php';

foreach (array('a', 'e', 'i', 'o', 'u') as $i) {
    $id_json = file_get_contents(PRODUCT_SEARCH_URL.$i);
    $id_data = json_decode($id_json);

    if ($id_data->success) {
        file_put_contents(JSON_PATH.'/searches/'.$i.'.json', $id_json);
    }
}
<?php

require '../app/bootstrap.php';

$product_ids = array();
foreach (array('a', 'e', 'i', 'o', 'u') as $i) {
    $data = file_get_contents(JSON_PATH.'/searches/'.$i.'.json');
    $json = json_decode($data);

    if ($json->success) {
        $product_ids = array_merge($product_ids, $json->products);
    }
}

$product_ids = array_unique($product_ids);
$client = new Guzzle\Http\Client('http://www.graze.com');

foreach ($product_ids as $id) {
    $request = $client->get('/api/products/details?p='.$id);
    $response = $client->send($request);
    $data = $response->getBody();

    file_put_contents(JSON_PATH.'/products/'.$id.'.json', $data);
    printf("Product #%s details downloaded...\n", strtoupper($id));
}
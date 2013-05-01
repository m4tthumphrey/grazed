<?php

require '../app/bootstrap.php';

$client = new Guzzle\Http\Client('http://www.graze.com');
$product_ids = array();

foreach (array('a', 'e', 'i', 'o', 'u') as $i) {
    $request = $client->get('/api/products/search?q='.$i);
    $response = $client->send($request);
    $data = $response->getBody();
    $json = json_decode($data);

    if ($json->success) {
        printf("Product list beginning with %s downloaded...\n", strtoupper($i));
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
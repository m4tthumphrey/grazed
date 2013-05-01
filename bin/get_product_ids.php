<?php

require '../app/bootstrap.php';

$client = new Guzzle\Http\Client('http://www.graze.com');

foreach (array('a', 'e', 'i', 'o', 'u') as $i) {
    $request = $client->get('/api/products/search?q='.$i);
    $response = $client->send($request);
    $data = $response->getBody();
    $json = json_decode($data);

    if ($json->success) {
        file_put_contents(JSON_PATH.'/searches/'.$i.'.json', $data);
        printf("Product list beginning with %s downloaded...\n", strtoupper($i));
    }
}
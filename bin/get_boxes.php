<?php

require '../app/bootstrap.php';

$client = new Guzzle\Http\Client('https://www.graze.com', array(
    'redirect.disable' => true // damn sob!
));

$request = $client->get(sprintf(
    '/auth/login?email=%s&password=%s&autologin=1', 
    GRAZE_EMAIL, 
    GRAZE_PASSWORD
));

$response = $client->send($request);
$cookies = explode('; ', $response->getSetCookie());

foreach ($cookies as $cookie) {
    if (preg_match('/'.COOKIE_NAME.'=(\w{32})/', $cookie, $match)) {
        $hash = $match[1];
        break;
    }
}

$request = $client->get('/m/boxes/?range=0,5000');
$request->addCookie(COOKIE_NAME, $hash);

$boxes = $request->send()->getBody();

file_put_contents(JSON_PATH.'/boxes.json', $boxes);
print "Box history downloaded\n";
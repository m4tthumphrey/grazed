<?php

require 'lib.php';
require 'config.php';
require_once 'vendor/autoload.php';

$client = new Guzzle\Http\Client('https://www.graze.com', array(
    'redirect.disable' => true // damn sob!
));

$request = $client->get(sprintf(
    '/auth/login?email=%s&password=%s&autologin=1', 
    $username, 
    $password
));

$response = $client->send($request);
$cookies = $response->getSetCookie(COOKIE_NAME);

foreach ($cookies as $cookie) {
    if (preg_match('/'.COOKIE_NAME.'=(\w{32})/', $cookie, $match)) {
        $hash = $match[1];
        break;
    }
}

$request = $client->get('/m/boxes/?range=0,5000');
$request->addCookie(COOKIE_NAME, $hash);

$boxes = $request->send()->json();

d($boxes);
<?php

require 'lib.php';

$username = '';
$password = '';
$login_url = sprintf('https://www.graze.com/auth/login/?email=%s&password=%s&autologin=1', $username, $password);

$curl = curl_init($login_url);
curl_setopt_array($curl, array(
    CURLOPT_HEADER => true,
    CURLOPT_SSL_VERIFYHOST => false,
    CURLOPT_SSL_VERIFYPEER => false,
    CURLOPT_RETURNTRANSFER => true
));

if (($response = curl_exec($curl)) === false) {
    echo curl_error($curl);
    exit;
}

list($headers, $body) = explode("\r\n\r\n", $response, 2);

d($headers, 1);
$headers = parse_headers($headers);
$hash = null;
foreach ($headers['Set-Cookie'] as $header) {
    if (preg_match('/auth_autologin=(\w{32})/', $header, $match)) {
        $hash = $match[1];
        break;
    }
}

if (!$hash) {
    echo 'Invalid login!';
    exit;
}

$curl = curl_init($boxes_list);

curl_setopt_array($curl, array(
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_COOKIE => 'auth_autologin='.$hash
));

if (($response = curl_exec($curl)) === false) {
    echo curl_error($curl);
    exit;
}

echo $response;
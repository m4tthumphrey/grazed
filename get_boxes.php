<?php

require 'lib.php';

function generateKey($id)
{
    global $chars;

    $id = intval($id);
    $length = strlen($chars);
    $code = '';

    while ($id > $length - 1) {
        $code = $chars[fmod($id, $length)].$code;
        $id = floor($id / $length);
    }

    return $chars[$id].$code;
}

$chars = implode(array_merge(
    range('A', 'Z'),
    range(0, 9)
));

// Maybe not!
for ($i = 1679616; $i <= 999999999; $i++) {
    $k = generateKey($i);
    $box_json = file_get_contents($box_contents.$i);
    $box_data = json_decode($box_json);

    if ($box_data->success) {
        file_put_contents('json/boxes/'.$k.'.json', $box_json);
    }

    if ($i == 1679666) {
        break;
    }
}
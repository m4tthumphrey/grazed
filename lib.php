<?php

error_reporting(E_ALL);
ini_set('display_errors', 'on');
set_time_limit(0);

function d($what, $html = false)
{
    $what = print_r($what, 1);

    if ($html) {
        echo '<pre>'.$what.'</pre>';
        return;
    }

    echo $what;
}

function array_sort($array, $on, $order = SORT_ASC) {
    uasort($array, function($a, $b) use ($on, $order) {
        if (is_array($a) and is_array($b)) {
            if ($a[$on] == $b[$on])
                return 0;
            else
                return ($order === SORT_ASC ? $a[$on] > $b[$on] : $a[$on] < $b[$on]) ? 1 : -1;
        } else {
            if ($a->$on == $b->$on)
                return 0;
            else
                return ($order === SORT_ASC ? $a->$on > $b->$on : $a->$on < $b->$on) ? 1 : -1;
        }
    });
    return $array;
}

function get_products()
{
    $products = array();
    if ($handle = opendir('json/products/')) {
        while (false !== ($f = readdir($handle))) {
            if (preg_match('/\.json$/', $f)) {
                $data = json_decode(file_get_contents('json/products/'.$f))->details;
                $products[$data->productId] = $data;
            }
        }
        closedir($handle);
    }

    return $products;
}

$product_details = 'http://www.graze.com/api/products/details?p=';
$box_contents = 'http://www.graze.com/api/box/contents?k=';
$product_search = 'http://www.graze.com/api/products/search?q=';
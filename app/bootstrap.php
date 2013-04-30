<?php

error_reporting(E_ALL);
ini_set('display_errors', 'on');
set_time_limit(0);

require_once dirname(__DIR__).'/app/config.php'; 

require_once APP_PATH.'/app/auth.php';
require_once APP_PATH.'/vendor/autoload.php';

function d($what, $html = true)
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
    if ($handle = opendir(JSON_PATH.'/products/')) {
        while (false !== ($f = readdir($handle))) {
            if (preg_match('/\.json$/', $f)) {
                $data = json_decode(file_get_contents(JSON_PATH.'/products/'.$f))->details;
                $products[$data->productId] = $data;
            }
        }
        closedir($handle);
    }

    return $products;
}

function parse_headers($headers)
{
    $return = array();
    foreach (explode(PHP_EOL, $headers) as $header) {
        if (preg_match('/\:/', $header)) {
            list ($name, $value) = explode(':', $header);
            $return[$name][] = $value;
        }
    }

    return $return;
}
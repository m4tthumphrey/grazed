<?php

require 'lib.php';

$products = get_products();
$boxes = json_decode(file_get_contents('json/boxes.json'))->boxes;

// pre 5.3
foreach ($products as &$product) {
    $product->frequency = 0;
    $product->sendSoon = 0;
    $product->sendSoonPercentage = 0;
    $product->nutritionTotals = array();
    $product->lastSent = null;
    $product->firstSent = null;

    foreach ($product->nutritionInfo as $info) {
        $product->nutritionTotals[$info->id] = 0;
    }
}

$total_spent = 0;
$friends_fed = array();
$discount = 0;
$box_cost = 3.49;
$addresses = array();
$days_of_week = array();
$box_count = 0;
$free_boxes = 0;
$old_products = array();
$products_received = array();
$products_not_received = array();
$unsent_boxes = array();

foreach ($boxes as &$box) {
    if (!$box->sent) {
        $unsent_boxes[] = $box;
        continue;
    }

    $date = new DateTime($box->date);
    $addresses[] = $box->address;
    $days_of_week[] = $date->format('l');

    foreach ($box->products as $product_id) {
        if (array_key_exists($product_id, $products)) {
            $products_received[] = $product_id;
            $products[$product_id]->frequency++;

            if (in_array($product_id, (array)$box->sendSoonProducts)) {
                $products[$product_id]->sendSoon++;
            }
            $products[$product_id]->sendSoonPercentage = round(($products[$product_id]->sendSoon / $products[$product_id]->frequency) * 100);
            foreach ($products[$product_id]->nutritionInfo as $info) {
                $products[$product_id]->nutritionTotals[$info->id] += $info->value;
            }

            $products[$product_id]->firstSent = $date;
            if (!$products[$product_id]->lastSent) {
                $products[$product_id]->lastSent = $date;
            }
        } else {
            $old_products[] = $product_id;
        }
    }

    $cost = $box_cost;
    if (isset($box->discount)) {
        if (preg_match('/free/', $box->discount)) {
            $discount += $cost;
            $cost = 0;
            $free_boxes++;
        } elseif (preg_match('/half price/', $box->discount)) {
            $cost /= 2;
            $discount += $cost;
        } elseif (preg_match('/(\w+) on this box for feeding your friend \(([\w\s]+)\)/', $box->discount, $match)) {
            $cost -= $match[0];
            $discount += $cost;
            $friends_fed[] = $match[2];
        }
    }

    $total_spent += $cost;
    $box_count++;
}

$products = array_sort($products, 'frequency', SORT_DESC);

$calories = 0;
foreach ($products as $product) {
    if (in_array($product->productId, $products_received)) {
        $calories += $product->nutritionTotals['energyKcal'];
    } else {
        $products_not_received[] = $product;
    }
}

$products_not_received = array_sort($products_not_received, 'weight', SORT_DESC);

$last = $boxes[0];
$first = end($boxes);

$average_cals = round($calories / $box_count, 2);

$from = new DateTime($first->date);

$address_counts = array_count_values($addresses);
$days_of_week = array_count_values($days_of_week);
$old_products = array_count_values($old_products);

arsort($address_counts);
arsort($days_of_week);
arsort($old_products);

?>

<!doctype html>
<html>
<head>
    <style>
        body {
            font-family: sans-serif;
        }

        .products li {
            float: left;
            font-size: 12px;
            width: 150px;
            height: 170px;
            position: relative;
            list-style-type: none;
            margin: 5px;
            text-align: center;
        }

        .products li img {
            width: 100px;
        }

        .clear {
            clear: both;
        }
    </style>
    <script src="http://maps.google.com/maps/api/js?sensor=false" type="text/javascript"></script>
    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.7/jquery.min.js" type="text/javascript"></script>
</head>
<body>

<h1><?php echo number_format($box_count) ?> boxes containing <?php echo number_format($calories) ?> calories, consumed since <?php echo $from->format('jS F Y') ?>, averaging <?php echo $average_cals ?> calories per box (that we know about!)</h1>
<h2>Total spent: &pound;<?php echo number_format($total_spent, 2) ?></h2>
<?php if (count($unsent_boxes) > 0) : ?>
<h2><?php echo count($unsent_boxes) ?> boxes couldn't be sent for whatever reason...</h2>
<?php endif; ?>
<?php if (count($friends_fed)) : ?>
<h2><?php echo count($friends_fed) ?> friends fed</h2>
<ul>
    <?php foreach ($friends_fed as $friend) : ?>
    <li><?php echo $friend ?></li>
    <?php endforeach; ?>
</ul>
<h2>You earnt <?php echo $free_boxes ?> free boxes and a saved a total of &pound;<?php echo number_format($discount, 2) ?>. That equates to about <?php echo number_format(floor($discount / $box_cost)) ?> free boxes overall!</h2>
<h2>Delivered to <?php echo count($address_counts) ?> different addresses</h2>
<ul>
    <?php foreach ($address_counts as $address => $count) : ?>
    <li>Delivered to <?php echo $address ?> <?php echo $count ?> times</li>
    <?php endforeach; ?>
</ul>
<div id="map" style="width:800px;height:500px"></div>
<h2>Days of week</h2>
<ul>
    <?php foreach ($days_of_week as $day => $count) : ?>
    <li>Delivered on <?php echo $day ?> <?php echo $count ?> times</li>
    <?php endforeach; ?>
</ul>
<?php endif; ?>
<h2>Food</h2>
<ul class="products">
    <?php foreach ($products as $product) : if (!$product->frequency) continue; ?>
    <li>
        <img src="<?php echo $product->thumb ?>"><br>
        <?php echo $product->productName ?><br>
        had <?php echo $product->frequency ?> times, <?php echo $product->sendSoonPercentage ?>% send soon<br>
        <?php echo $product->nutritionTotals['energyKcal'] ?> calories consumed<br>
        between <?php echo $product->firstSent->format('jS M Y') ?> and <?php echo $product->lastSent->format('jS M Y') ?>
    </li>
    <?php endforeach; ?>
</ul>
<div class="clear"></div>
<h2>You received <?php echo count($old_products) ?> different foods that are no longer available and you've never received these foods!</h2>
<ul class="products">
    <?php foreach ($products_not_received as $product) : ?>
    <li>
        <img src="<?php echo $product->thumb ?>"><br>
        <?php echo $product->productName ?>
    </li>
    <?php endforeach; ?>
</ul>
<div class="clear"></div>
<script>
    var geocoder;
    var map;
    var latlng = [];
    var x = 0;

    function check() {
        x++;
        if (x == <?php echo count($address_counts) ?>) {
            var latlngbounds = new google.maps.LatLngBounds();
            for (i = 0; i < latlng.length; i++) {
                latlngbounds.extend(latlng[i]);
            }
            map.setCenter(latlngbounds.getCenter());
            map.fitBounds(latlngbounds);
        }
    }

    function init()
    {
        geocoder = new google.maps.Geocoder();
        map = new google.maps.Map(document.getElementById('map'), {
            center: google.maps.LatLng(-34.397, 150.644),
            mapTypeId: google.maps.MapTypeId.ROADMAP
        });

        <?php foreach ($address_counts as $address => $count) : ?>
        addMarker('<?php echo $address ?>');
        <?php endforeach; ?>
    }

    function addMarker(address)
    {
        geocoder.geocode( { 'address': address }, function(results, status) {
            if (status == google.maps.GeocoderStatus.OK) {
                var location = results[0].geometry.location;
                var marker = new google.maps.Marker({
                    map: map,
                    position: location,
                    animation: google.maps.Animation.DROP,
                    title: address
                });

                latlng.push(location);
                check();
            }
        });
    }

    $(document).ready(function() {
        init();
    });
</script>
</body>
</html>
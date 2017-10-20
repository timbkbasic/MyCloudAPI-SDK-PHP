<?php

define('MCAPI_CONFIG_PATH', '.');
require 'bootstrap.php';

use \MyCloud\Api\Model\Order;

// $order = new Order();
// print var_export( $order, true ) . PHP_EOL;

$order = Order::get(1);
print "ORDER: " . print_r($order, true) . PHP_EOL;

print "MCNumber: " . $order->mc_number . PHP_EOL;
print "Name: " . $order->name . PHP_EOL;
print "Phone: " . $order->phone_number . PHP_EOL;


$items = $order->getOrderItems();
print "ITEMS: " . print_r($items, true) . PHP_EOL;

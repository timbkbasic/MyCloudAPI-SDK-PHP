<?php

define('MCAPI_CONFIG_PATH', '.');
require 'bootstrap.php';

use \MyCloud\Api\Model\Order;

$orders = Order::all( array(), null );

foreach ( $orders as $order ) {
	print "ORDER[" . $order->id . "]" . PHP_EOL;
	print "   status " . $order->status . PHP_EOL;
	print "   shopId " . $order->shop_id . PHP_EOL;
	print "   mcNumber " . $order->mc_number . PHP_EOL;
	print "   Order # " . $order->order_number . PHP_EOL;
	print "   Weight " . $order->weight . PHP_EOL;
	print "   customer[" . $order->customer_id . "]" . PHP_EOL;
	print "      Name " . $order->name . PHP_EOL;
	print "      Address " . $order->address . PHP_EOL;
	print "      PostCode " . $order->postcode . PHP_EOL;
	print "      Phone # " . $order->phone_number . PHP_EOL;
	print "   --- Order Items ------------------------" . PHP_EOL;
	foreach ( $order->getOrderItems() as $order_item ) {
		print "   ITEM[" . $order_item->id . "]" . PHP_EOL;
		print "      Price " . $order_item->price . PHP_EOL;
		print "      Quantity " . $order_item->quantity . PHP_EOL;
	}
}

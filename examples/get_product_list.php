<?php

define('MCAPI_CONFIG_PATH', '.');
require 'bootstrap.php';

use \MyCloud\Api\Model\Product;

$products = Product::all( array(), null );

print "Retrieved " . count($products) . " products." . PHP_EOL;
foreach ( $products as $product ) {
	print "PRODUCT[" . $product->id . "]" . PHP_EOL;
	print "   shopId " . $product->shop_id . PHP_EOL;
	print "   SKU " . $product->mc_sku . PHP_EOL;
	print "   Name " . $product->name . PHP_EOL;
	print "   Description " . $product->description . PHP_EOL;
	print "   SupplierRef " . $product->supplier_ref . PHP_EOL;
	print "   Reference[1] " . $product->reference_1 . PHP_EOL;
	print "   Reference[2] " . $product->reference_2 . PHP_EOL;
	print "   Reference[3] " . $product->reference_3 . PHP_EOL;
	print "   Reference[4] " . $product->reference_4 . PHP_EOL;
}

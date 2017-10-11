<?php

require 'bootstrap.php';

use \MyCloud\Api\Model\Order;

$order = new Order();
print var_export( $order, true ) . PHP_EOL;


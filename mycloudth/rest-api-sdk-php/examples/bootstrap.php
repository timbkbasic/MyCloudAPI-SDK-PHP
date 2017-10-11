<?php

$vendorPath = dirname(dirname(dirname(dirname(__FILE__)))) . DIRECTORY_SEPARATOR . 'vendor';
$composerAutoload = $vendorPath . DIRECTORY_SEPARATOR . 'autoload.php';

$reason = '';
$loadable = TRUE;
if ( ! file_exists($vendorPath) ) {
	$loadable = FALSE;
	$reason = "The 'vendor' folder is missing. (expected at '" . $vendorPath . "')";
}
if ( ! file_exists($composerAutoload) ) {
	$loadable = FALSE;
	$reason = "The 'autoload.php' file is missing. (expected at '" . $composerAutoload . "')";
}

if ( $loadable ) {
	require $composerAutoload;
} else {
	print "Could not bootstrap the example program:" . PHP_EOL;
	print "   " . $reason . PHP_EOL;
	print "Check that you installed with composer, or copied the distribution correctly." . PHP_EOL;
	exit(1);
}

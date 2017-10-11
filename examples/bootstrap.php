<?php

function root_dir( $dir, $levels ) {
	if ( $levels == 0 ) {
		return $dir;
	} else {
		return root_dir( dirname($dir), --$levels );
	}
}

$reason = '';
$loadable = TRUE;

// First, we will assume that this is a standard composer distribution and
// look for the autoload.php in that context...
$topDir = root_dir( __FILE__, 2 );
print "1) Path[top] '" . $topDir . "'" . PHP_EOL;
$composerAutoload = $topDir . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';
if ( ! file_exists($composerAutoload) ) {
	print "   '" . $composerAutoload . "' NOT FOUND". PHP_EOL;
	$topDir = root_dir( __FILE__, 1 );
	print "2) Path[top] '" . $topDir . "'" . PHP_EOL;
	$composerAutoload = $topDir . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';
	if ( ! file_exists($composerAutoload) ) {
		$loadable = FALSE;
		$reason = "The 'autoload.php' file was not found. (expected at '" . $composerAutoload . "')";
	}
}

if ( $loadable ) {
	"Loading: '" . $composerAutoload . "'" . PHP_EOL;
	require $composerAutoload;
} else {
	print "Could not bootstrap the example program:" . PHP_EOL;
	print "   " . $reason . PHP_EOL;
	print "Check that you installed with composer, or copied the distribution correctly." . PHP_EOL;
	exit(1);
}

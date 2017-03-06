<?php
/**
 * Zettacast is a simple, fast and lightweight PHP Framework. We aim to help
 * you make your project come true easily and as fast as possible.
 * @package Zettacast
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @license MIT License
 * @copyright 2015-2017 Rodrigo Siqueira
 */

/*
 * Sets error reporting level and display errors settings. It is recommended to
 * change these values when in production use.
 */
ini_set('display_errors', 1);
error_reporting(~0);

/*
 * Bootstraping framework. Initializing all functions, objects and handlers
 * needed for a correct Zettacast execution. Besides that, an autoload function
 * is specified so one no longer needs explicitly include class files.
 */
$fwork = require __DIR__.'/../zettacast/bootstrap.php';
		 require __DIR__.'/../app/bootstrap.php';

/*
 * Now that we have our framework on, we must take care of the incoming HTTP
 * request and produce the expected response from it.
 */
require APPPATH.'/http/index/index.php';
/*
$kernel = $zcast->make(Zettacast\HTTP\Kernel::class);

$response = $kernel->handle(
	$request = Zettacast\HTTP\Request::capture()
);

$response->send();
$kernel->terminate($request, $response);
*/

<?php
/**
 * Zettacast index file.
 * Zettacast is a simple, fast and lightweight PHP Framework. We aim to help
 * you make your project come true easily and as fast as possible.
 * @package Zettacast
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @license MIT License
 * @copyright 2015-2018 Rodrigo Siqueira
 */

/*
 * Ensure the current directory is pointing to the framework's front
 * controller's directory. That MUST BE your public directory.
 */
chdir(dirname(__DIR__));

/*
 * Sets error reporting level and display errors settings. It is recommended to
 * change these values when in production use.
 */
error_reporting(~0);
ini_set('display_errors', 1);

/**#@+
 * Let us define some constant values. Firstly, we have to make it clear that
 * Zettacast is booted and when it happened. These constants actually do not
 * mean much, but may be useful when using Zettacast along with other
 * frameworks. Although we cannot imagine why you would do such a thing, haha.
 * @var mixed Zettacast initialization constants.
 */
define('ZETTACAST', 'Zettacast');
define('ZETTATIME', microtime(true));
/**#@-*/

/**
 * This constant hold the path of the document root, where all framework and
 * applications files can be found from.
 * @var string Document root.
 */
define('ROOTPATH', realpath(dirname(__DIR__)));

/**#@+
 * These constants hold the path of the framework's directories. You should
 * modify these constants every time you change the location or name of the
 * framework's folders.
 * @var string Framework's directories constants.
 */
define('APPPATH', ROOTPATH.'/app');
define('SRCPATH', ROOTPATH.'/src');
define('TMPPATH', ROOTPATH.'/tmp');
define('CFGPATH', ROOTPATH.'/config');
define('WWWPATH', realpath(__DIR__));
/**#@-*/

/*
 * Bootstraping framework. Initializing all functions, objects and handlers
 * needed for a correct Zettacast execution. Besides that, an autoload function
 * is specified so one no longer needs explicitly include class files.
 */
require SRCPATH.'/bootstrap.php';
require APPPATH.'/bootstrap.php';

/*
 * Now that we have our framework on, we must take care of the incoming HTTP
 * request and produce the expected response from it.
 */
/** @var \Zettacast\Http\Kernel $kernel */
#$kernel = zetta(Zettacast\Contract\Http\Kernel::class);
#$request = Zettacast\Http\Request::capture();
#$response = $kernel->handle($request);

#$response->send();
#$kernel->commit($request, $response);

$uri = zetta('uri', ROOTPATH);
var_dump($uri);

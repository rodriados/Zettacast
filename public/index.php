<?php
/**
 * Zettacast index file.
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
ini_set('display_errors', 0);
error_reporting(~0);

/**#@+
 * Let us define some constant values. Firstly, we have to make it clear that
 * Zettacast is booted and when it happened. These constants actually do not
 * mean much, but may useful when using Zettacast along with other frameworks.
 * Although we cannot imagine why you would do such a thing, haha.
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
define('DOCROOT', realpath(dirname(__DIR__)));

/*
 * Bootstraping framework. Initializing all functions, objects and handlers
 * needed for a correct Zettacast execution. Besides that, an autoload function
 * is specified so one no longer needs explicitly include class files.
 */
require DOCROOT.'/src/bootstrap.php';
require DOCROOT.'/app/bootstrap.php';

print "Okay!";

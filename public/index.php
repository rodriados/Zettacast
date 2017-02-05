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

/**#@+
 * These constants hold the path of the application's and framework's
 * directories. You should alter these constants every time you change the
 * location or name of the folders.
 * @var string Directory constants.
 */
define('DOCROOT', realpath(dirname(__DIR__)));
define('APPPATH', DOCROOT.'/app');
define('FWORKPATH', DOCROOT.'/zettacast');
define('PUBLICPATH', DOCROOT.'/public');
/**#@-*/

/*
 * Bootstraping framework. Initializing all functions, objects and handlers
 * needed for a correct Zettacast execution. Besides that, an autoload function
 * is specified so one no longer needs explicitly include class files.
 */
require FWORKPATH.'/bootstrap.php';

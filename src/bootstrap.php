<?php
/**
 * Zettacast bootstrap file.
 * @package Zettacast
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @license MIT License
 * @copyright 2015-2017 Rodrigo Siqueira
 */

/**#@+
 * Let us define some constant values. Firstly, we have to make it clear that
 * Zettacast is booted and when it happened. These constants actually do not
 * mean much, but may useful when using Zettacast along with other frameworks.
 * Although we cannot imagine why you would do such a thing, haha.
 * @var mixed Zettacast initialization constants.
 */
define('ZETTACAST', 'Zettacast');
define('ZBOOTTIME', microtime(true));
define('ZBOOTMEMO', memory_get_usage(true));
#define('ZDEBUG', true);
/**#@-*/

/**#@+
 * These constants hold the path of the application's and framework's
 * directories. You should alter these constants every time you change the
 * location or name of the folders.
 * @var string Directory constants.
 */
define('DOCROOT',    realpath(dirname(__DIR__)));
define('APPPATH',    DOCROOT.'/app');
define('PKGPATH',    DOCROOT.'/package');
define('FWORKPATH',  DOCROOT.'/src');
define('PUBLICPATH', DOCROOT.'/public');
/**#@-*/

/*
 * Creates a loader object. This object will be responsible for loading all
 * requested classes, interfaces or the like for the framework when needed.
 */
require FWORKPATH.'/Helper/functions.php';
require FWORKPATH.'/Autoload/Autoload.php';
$loader = new Zettacast\Autoload\Autoload;

/*
 * Starts a new Zettacast framework instance. From now on, all objects can have
 * their instances built with dependency injection, that is, you will not need
 * to be worried with instantiating complex objects: we can do it for you.
 */
zetta()->share(Zettacast\Autoload\Autoload::class, $loader);

/*
 * Creates all needed bindings for framework's dependency injector. This will
 * make the modules loosely coupled, and easily testable.
 */
require FWORKPATH.'/bindings.php';

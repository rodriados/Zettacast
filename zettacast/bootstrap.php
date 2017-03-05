<?php
/**
 * Zettacast bootstrap file.
 * @package Zettacast
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @license MIT License
 * @copyright 2015-2017 Rodrigo Siqueira
 */

/*
 * Let us define some constant values. Firstly, we have to make it clear that
 * Zettacast is booted and when it happened. These constants actually do not
 * mean much, but may useful when using Zettacast along with other frameworks.
 * Although we cannot imagine why you would do such a thing, haha.
 */
define('ZETTACAST', 'Zettacast');
define('ZBOOTTIME', microtime(true));

/*
 * Creates a loader object. This object will be responsible for loading all
 * requested classes, interfaces or the like for the framework when needed.
 */
require __DIR__.'/helper/functions.php';
require __DIR__.'/autoload/autoload.php';
$loader = new \Zettacast\Autoload\Autoload(__DIR__);

/*
 * Starts a new Zettacast framework instance. From now on, all objects can have
 * their instances built with dependency injection, that is, you will not need
 * to be worried with instantiating complex objects: we can do it for you.
 */
require __DIR__.'/zettacast.php';
$zcast = Zettacast::instance(
	new Zettacast(realpath(dirname(__DIR__)))
);

/*
 * Let us now share our autoload instance with the framework so it knows the
 * object instance responsible for loading all of its components.
 */
$zcast->share(\Zettacast\Autoload\Autoload::class, $loader);

/*
 * Return the framework instance. The instance is given back to the script so
 * the framework can be bootstrapped separately from where the application is
 * actually running.
 */
return $zcast;

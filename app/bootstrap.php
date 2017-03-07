<?php
/**
 * App bootstrap file.
 * @package Zettacast
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @license MIT License
 * @copyright 2015-2017 Rodrigo Siqueira
 */
use Zettacast\Facade\Autoload;

/*
 * This allows you to add your application's or any other classes to the
 * autoloader object and easily access these classes from anywhere.
 */
Autoload::object([
	// Add here the classes you want to add. Example:
	//'Math' => APP.'/class/math.php',
]);

/*
 * This allows you to add your application's or any other namespaces to the
 * autoloader object and easily access these namespaces from anywhere.
 */
Autoload::space([
	// Add here the namespaces you want to add. Example:
	//'Mail' => APP.'/class/mail',
]);

/*
 * This allows you to add alias to your application's classes or any other
 * objects and easily access them from anywhere.
 */
Autoload::alias([
	// Add here the alias you want to add. Example:
	// 'SendMail' => 'App\Package\Mail\Send'
]);

/*
 * Sets application's locale, timezone and encoding. These values will be used
 * only if no data can be found in a configuration file.
 */
Zettacast::$locale = 'en_US';
Zettacast::$timezone = 'UTC';
Zettacast::$encoding = 'UTF-8';

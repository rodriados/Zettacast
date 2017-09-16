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
Autoload::addClass([
	// Add here the classes you want to add. Example:
	//'Math' => APP.'/class/math.php',
]);

/*
 * This allows you to add your application's or any other namespaces to the
 * autoloader object and easily access these namespaces from anywhere.
 */
Autoload::addNamespace([
	// Add here the namespaces you want to add. Example:
	//'Mail' => APP.'/class/mail',
]);

/*
 * This allows you to add alias to your application's classes or any other
 * objects and easily access them from anywhere.
 */
Autoload::addAlias([
	// Add here the alias you want to add. Example:
	// 'SendMail' => 'App\Package\Mail\Send'
]);

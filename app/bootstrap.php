<?php
/**
 * Application bootstrap file.
 * @package Zettacast
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @license MIT License
 * @copyright 2015-2018 Rodrigo Siqueira
 */
use Zettacast\Facade\Autoload;

/*
 * This allows you to add your application's or any other classes to the
 * autoloader object and easily access these classes from anywhere.
 */
#Autoload::register('object', [
	// Add here the classes you want to add. Example:
	//'Math' => APP.'/class/math.php',
#]);

/*
 * This allows you to add your application's or any other namespaces to the
 * autoloader object and easily access these namespaces from anywhere.
 */
#Autoload::register('namespace', [
	// Add here the namespaces you want to add. Example:
	//'Mail' => APP.'/class/mail',
#]);

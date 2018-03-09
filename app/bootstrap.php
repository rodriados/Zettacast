<?php
/**
 * Application bootstrap file.
 * This file is responsible for booting the application up and starting all
 * basic code needed for a perfectly functional application execution.
 * @package Zettacast
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @license MIT License
 * @copyright 2017 Rodrigo Siqueira
 */
use Zettacast\Facade\Autoload;
use Zettacast\Autoload\Loader\ObjectLoader;
use Zettacast\Autoload\Loader\NamespaceLoader;

/*
 * This allows you to add your application's or any other classes to the
 * autoloader object and easily access these classes from anywhere.
 */
($objects = [
	// Add here the classes you want to add. Example:
	//'Object' => APP.'/vendor/Object.php',
]) and Autoload::register(new ObjectLoader($objects));
/*
 * This allows you to add your application's or any other namespaces to the
 * autoloader object and easily access these namespaces from anywhere.
 */
($namespaces = [
	// Add here the namespaces you want to add. Example:
	//'Mathr' => APP.'/vendor/Mathr',
]) and Autoload::register(new NamespaceLoader($namespaces));

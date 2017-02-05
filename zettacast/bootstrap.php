<?php
/**
 * Bootstrap class file.
 * @package Zettacast
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @license MIT License
 * @copyright 2015-2017 Rodrigo Siqueira
 */

/*
 * Gets the name, start time and memory of the framework. These information
 * can be used later anywhere in the framework.
 */
defined('ZETTACAST') or define('ZETTACAST', 'Zettacast');
defined('ZBOOTTIME') or define('ZBOOTTIME', microtime(true));
defined('ZBOOTMEMO') or define('ZBOOTMEMO', memory_get_usage());

if($_SERVER['REQUEST_URI'] == '/') {
	include APPPATH."/url/index/index.php";
} elseif(file_exists(APPPATH."/url/".$_SERVER['REQUEST_URI']."/index.php")) {
	$name = APPPATH."/url/".$_SERVER['REQUEST_URI']."/index.php";
	include $name;
}

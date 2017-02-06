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

/**
 * Boots framework and starts its main classes and modules, allowing its
 * correct usage and execution.
 * @version 1.0
 */
final class Zettacast {
	
	/**
	 * Stores this class' singleton instance and helps checking whether the
	 * framework has already been booted or not.
	 * @var Zettacast Framework singleton instance.
	 */
	private static $i = null;
	
	/**
	 * Boots framework's basic functions and modules up. It also sets all
	 * needed handlers and callbacks for error and shutdown.
	 * @todo Uncomment handlers after Err class is fully implemented.
	 * @throws Exception
	 */
	public static function boot() {
		
		if(isset(self::$i))
			throw new Exception('Zettacast cannot be booted more than once!');
		
		require FWORKPATH.'/autoload/autoload.php';
		class_alias('Zettacast\\Autoload', 'Autoload');
		Autoload::register();
		
		register_shutdown_function([self::class, 'shutdown']);
		#set_exception_handler([Err::class, 'exception']);
		#set_error_handler(Err::class, 'error');

		self::$i = new self;
		
	}
	
	/**
	 * Successfully finishes framework's execution and objects. Shutdown event
	 * is triggered so that any final application function can run.
	 * @todo Uncomment method calls after Event and Err classes are working.
	 */
	public static function shutdown() {
		
		Autoload::reset();
		#Event::post('shutdown');
		#Err::shutdown();
		
	}
	
	/**
	 * Aborts the framework execution. This method is always called explicitly,
	 * never by any automatic PHP feature.
	 * @todo Uncomment method calls after Event and Err classes are working.
	 */
	public static function abort() {
		
		Autoload::reset();
		#Event::post('abort');
		#Err::abort();
		
		exit;
		
	}
	
}

if($_SERVER['REQUEST_URI'] == '/') {
	include APPPATH."/url/index/index.php";
} elseif(file_exists(APPPATH."/url/".$_SERVER['REQUEST_URI']."/index.php")) {
	$name = APPPATH."/url/".$_SERVER['REQUEST_URI']."/index.php";
	include $name;
}


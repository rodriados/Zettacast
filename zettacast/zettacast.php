<?php
/**
 * Zettacast class file.
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
	 * Input mode currently in use with the framework.
	 * @var int Mode used in the framework.
	 */
	private static $mode;
	
	/**
	 * Environment in which framework is being executed.
	 * @var string Current environment mode.
	 */
	private static $env;

	/**
	 * Stores this class' singleton instance and helps checking whether the
	 * framework has already been booted or not.
	 * @var Zettacast Framework singleton instance.
	 */
	private static $i = null;
	
	/**#@+
	 * Environment mode constants. These constants determine in which mode
	 * Zettacast should execute. In some occasions, different actions are taken
	 * depending on the environment framework is executing.
	 * @var string Execution mode constants.
	 */
	const DEVELOPMENT   = 0x001;
	const PRODUCTION    = 0x002;
	/**#@-*/
	
	/**#@+
	 * Input mode constants. These constants inform whether the framework is
	 * being executed by command line or by an user via a browser, or even if
	 * it's an asynchronous request, such as AJAX.
	 * @var int Input mode constants.
	 */
	const APPLICATION   = 0x100;
	const COMMANDLINE   = 0x200;
	const ASYNCHRONOUS  = 0x400;
	/**#@-*/
	
	/**
	 * This method is responsible for setting the minimal configuration and
	 * gathering information about the environment so the framework can work
	 * correctly and execute the application as expected.
	 * @todo Uncomment lines after Request and Config are fully implemented.
	 */
	protected function __construct() {
		
		self::$env = $_SERVER['env'] ?? self::PRODUCTION;
		#self::$env = Config::get('environment', Request::server('env'))
		#   ?: self::PRODUCTION;
		
		date_default_timezone_set('America/Sao_Paulo');
		#date_default_timezone_set(Config::get('timezone'));
		
	}
	
	/**
	 * Application execution call. This method is responsible for executing and
	 * rendering the application, and thus producing an answer for an user.
	 */
	public function app() {
		
		if(self::$env == self::PRODUCTION) {
			ini_set('display_errors', 0);
			error_reporting(0);
		}
		
		self::$mode = self::APPLICATION;
		require APPPATH.'/bootstrap.php';
		
		#Request::exec();
		
		if($_SERVER['REQUEST_URI'] == '/') {
			include APPPATH."/http/index/index.php";
		} elseif(file_exists(APPPATH."/http/".$_SERVER['REQUEST_URI']."/index.php")) {
			$name = APPPATH."/http/".$_SERVER['REQUEST_URI']."/index.php";
			include $name;
		}
		
	}
	
	/**
	 * Command line execution call. This method is responsible for executing a
	 * command sent by a terminal or similar.
	 */
	public function cli() {
		
		self::$mode = self::COMMANDLINE;
		print "Hello, Command Line!\n";
		
	}
	
	/**
	 * Boots framework's basic functions and modules up. It also sets all
	 * needed handlers and callbacks for error and shutdown.
	 * @todo Uncomment handlers after Err class is fully implemented.
	 * @return Zettacast Booted framework instance.
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

		return self::$i = new self;
		
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
	
	/**
	 * Informs what is the environment currently used. This method's return
	 * value is one of the most important values throughout the framework's
	 * execution as many decisions are made based on it.
	 * @param int $value Value to be checked if currently active.
	 * @return int|bool Current environment.
	 */
	public static function env(int $value = null) {
		
		return is_null($value)
			? self::$env
			: (bool)(self::$env & $value)
		;
		
	}
	
	/**
	 * Informs what is the input mode used. This information is important
	 * because many resources can have their behavior changed depending on the
	 * input mode used with the framework.
	 * @param int $value Value to be checked if currently active.
	 * @return int|bool Current input mode.
	 */
	public static function mode(int $value = null) {
		
		return is_null($value)
			? self::$mode
			: (bool)(self::$mode & $value)
		;
		
	}
	
}

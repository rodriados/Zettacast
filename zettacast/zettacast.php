<?php
/**
 * Zettacast class file.
 * @package Zettacast
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @license MIT License
 * @copyright 2015-2017 Rodrigo Siqueira
 */

use Zettacast\Injector\Injector;

/**
 * Boots framework and starts its main classes and modules, allowing its
 * correct usage and execution.
 * @version 1.0
 */
final class Zettacast extends Injector {
	
	/**
	 * Stores this class' singleton instance and helps checking whether the
	 * framework has already been booted or not.
	 * @var Zettacast Framework singleton instance.
	 */
	private static $instance = null;
	
	/**#@+
	 * Environment mode constants. These constants determine in which mode
	 * Zettacast should execute. In some occasions, different actions are taken
	 * depending on the environment framework is executing.
	 * @var string Execution mode constants.
	 */
	const LOCAL         = 0x001;
	const TESTING       = 0x002;
	const DEVELOPMENT   = 0x004;
	const PRODUCTION    = 0x008;
	/**#@-*/
	
	/**#@+
	 * Input mode constants. These constants inform whether the framework is
	 * being executed by command line or by an user via a browser, or even if
	 * it's an asynchronous request, such as AJAX.
	 * @var int Input mode constants.
	 */
	const APPLICATION   = 0x010;
	const COMMANDLINE   = 0x020;
	const ASYNCHRONOUS  = 0x040;
	/**#@-*/
	
	/**
	 * This method is responsible for setting the minimal configuration and
	 * gathering information about the environment so the framework can work
	 * correctly and execute the application as expected.
	 * @param string $root Document root directory path.
	 */
	public function __construct(string $root) {
		parent::__construct();
		$root = rtrim($root, '\/');
		
		$this->share('path', $root);
		$this->share('path.app', $root.'/app');
		$this->share('path.public', $root.'/public');
		$this->share('path.fwork', $root.'/zettacast');
		
		$this->share(self::class, $this);
		$this->share(Injector::class, $this);
		
	}
	
	/**
	 * Singleton instance discovery. This method gives access to the singleton
	 * or creates it if it's not yet created.
	 * @param Zettacast $instance Instance to be used as singleton.
	 * @return static Singleton instance.
	 */
	public static function instance(Zettacast $instance = null) {
		
		if(is_null(self::$instance) and !is_null($instance))
			self::$instance = $instance;
		
		return self::$instance;
		
	}
	
}

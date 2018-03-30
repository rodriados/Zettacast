<?php
/**
 * Zettacast\Zettacast class file.
 * @package Zettacast
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @license MIT License
 * @copyright 2015-2018 Rodrigo Siqueira
 */
namespace Zettacast;

use Zettacast\Injector\Binder;
use Zettacast\Injector\Injector;
use Zettacast\Support\SingletonTrait;

/**
 * The Zettacast class boots the framework and starts its main classes and
 * modules, allowing its correct usage and execution.
 * @version 1.0
 */
final class Zettacast extends Injector
{
	use SingletonTrait;
	
	/**
	 * Informs Zettacast current version.
	 * @var string Zettacast version.
	 */
	const VERSION = '1.0';
	
	/**#@+
	 * Input mode constants. These constants inform whether the framework is
	 * being executed by command line or by an user via a browser, or even if
	 * it's an asynchronous request, such as AJAX.
	 * @var int Input mode constants.
	 */
	const APP   = 0x010;
	const CLI   = 0x020;
	/**#@-*/
	
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
	
	/**
	 * Zettacast constructor.
	 * This method is responsible for setting the minimal configuration and
	 * gathering information about the environment so the framework can work
	 * correctly and execute the application as expected.
	 */
	public function __construct()
	{
		$binder = $this->helpers();
		parent::__construct($binder);
		
		$this->set('path', APPPATH);
		$this->set('path.base', ROOTPATH);
		$this->set('path.public', WWWPATH);
		$this->set('path.zetta', SRCPATH);
		
		$this->set(self::class, $this);
		$this->set(Injector::class, $this);
		
		#set_error_handler([Handler::class, 'error']);
		#set_exception_handler([Handler::class, 'exception']);
		#register_shutdown_function([Handler::class, 'shutdown']);
	}
	
	/**
	 * Calls essential functions for the correct working of the framework as
	 * intended. By doing this, kernels will only have to deal with
	 * bootstrapping specific methods for their work.
	 */
	public function bootstrap()
	{
		#setlocale(LC_ALL, Config::get('app.locale', 'en_US'));
		#mb_internal_encoding(Config::get('app.charset', 'UTF-8'));
		#date_default_timezone_set(Config::get('app.timezone', 'UTC'));
		
		$this->set('mode', isset($_SERVER['argv']) ? self::CLI : self::APP);
	}
	
	/**
	 * Creates all shortcut aliases given by framework.
	 * @return Binder The injector binder ready for usage.
	 */
	private function helpers(): Binder
	{
		$binder = new Binder;
		$helpers = require SRCPATH.'/bindings.php';
		
		foreach($helpers as $binding)
			$binder->bind(...$binding);
		
		return $binder;
	}
}

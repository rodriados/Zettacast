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
use Zettacast\Helper\SingletonTrait;

final class Zettacast extends Injector
{
	use SingletonTrait;
	
	/**
	 * Inform Zettacast current version.
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
	 * @param string $root Document root directory path.
	 */
	public function __construct(string $root = ROOTPATH)
	{
		$binder = $this->helpers();
		parent::__construct($binder);
		
		$this->set('path', $root.'/app');
		$this->set('path.base', $root);
		$this->set('path.public', WWWPATH);
		$this->set('path.zetta', $root.'/src');
		
		$this->set(self::class, $this);
		$this->set(Injector::class, $this);
		
		#set_error_handler([Handler::class, 'handleError']);
		#set_exception_handler([Handler::class, 'handleException']);
		#register_shutdown_function([Handler::class, 'handleShutdown']);
	}
	
	/**
	 * Call essential functions for the correct working of the framework as
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
		return $binder;
	}
}

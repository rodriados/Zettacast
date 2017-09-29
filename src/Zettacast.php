<?php
/**
 * Zettacast\Zettacast class file.
 * @package Zettacast
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @license MIT License
 * @copyright 2015-2017 Rodrigo Siqueira
 */
namespace Zettacast;

use Zettacast\Facade\Config;
use Zettacast\Exception\Handler;
use Zettacast\Injector\Injector;
use Zettacast\Injector\Binder\DefaultBinder;
use Zettacast\Contract\Injector\BinderInterface;
use Zettacast\Contract\SingletonTrait;

/**
 * Boots framework and starts its main classes and modules, allowing its
 * correct usage and execution.
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
	 * This method is responsible for setting the minimal configuration and
	 * gathering information about the environment so the framework can work
	 * correctly and execute the application as expected.
	 * @param string $root Document root directory path.
	 */
	public function __construct(string $root = DOCROOT)
	{
		$binder = $this->bindInternal();
		parent::__construct($binder);
		
		$this->set('path', $root.'/app');
		$this->set('path.base', $root);
		$this->set('path.public', $root.'/public');
		$this->set('path.zetta', $root.'/src');
		
		$this->set(self::class, $this);
		$this->set(Injector::class, $this);
		
		set_error_handler([Handler::class, 'handleError']);
		set_exception_handler([Handler::class, 'handleException']);
		register_shutdown_function([Handler::class, 'handleShutdown']);
	}
	
	/**
	 * Calls essential functions for the correct working of the framework as
	 * intended. By doing this, kernels will only have to deal with
	 * bootstrapping specific methods for their work.
	 */
	public function bootstrap()
	{
		setlocale(LC_ALL, Config::get('app.locale', 'en_US'));
		mb_internal_encoding(Config::get('app.charset', 'UTF-8'));
		date_default_timezone_set(Config::get('app.timezone', 'UTC'));
		
		$this->set('mode', isset($_SERVER['argv']) ? self::CLI : self::APP);
	}
	
	/**
	 * Tries to load the framework bindings from the cache. If not possible,
	 * the bindings are loaded from the PHP file.
	 * @return BinderInterface The injector binder ready for usage.
	 */
	private function bindInternal(): BinderInterface
	{
		if(file_exists($cache = CACHEPATH.'/bindings.cache'))
			if(filemtime($cache) > filemtime(FWORKPATH.'/bindings.php'))
				return unserialize(file_get_contents($cache));
		
		$binder = new DefaultBinder;
		$data = require FWORKPATH.'/bindings.php';
		
		foreach($data as $binding)
			$binder->bind(...$binding);
		
		file_put_contents($cache, serialize($binder));
		return $binder;
	}
	
}

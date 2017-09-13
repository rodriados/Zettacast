<?php
/**
 * Zettacast\HTTP\Kernel class file.
 * @package Zettacast
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @license MIT License
 * @copyright 2015-2017 Rodrigo Siqueira
 */
namespace Zettacast\HTTP;

use Zettacast\Zettacast;
use Zettacast\Contract\HTTP\Request;
use Zettacast\Contract\HTTP\Response;
use Zettacast\Contract\HTTP\Kernel as KernelContract;

class Kernel
	implements KernelContract
{
	public function __construct(Zettacast $zetta)
	{
		$zetta->bootstrap();
		$zetta->share(self::class, $this);
		$zetta->share(KernelContract::class, $this);
	}
	
	public function handle(Request $request) : Response
	{
		require APPPATH.'/view/index.php';
		return new \Zettacast\HTTP\Response;
	}
	
	public function complete(Request $request, Response $response)
	{
		// TODO: Implement complete() method.
	}
	
}

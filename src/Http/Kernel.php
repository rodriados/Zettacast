<?php
/**
 * Zettacast\HTTP\Kernel class file.
 * @package Zettacast
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @license MIT License
 * @copyright 2015-2017 Rodrigo Siqueira
 */
namespace Zettacast\Http;

use Zettacast\Facade\Request as RequestFacade;
use Zettacast\Contract\Http\Kernel as KernelContract;
use Zettacast\Contract\Http\Request as RequestContract;
use Zettacast\Contract\Http\Response as ResponseContract;

class Kernel
	implements KernelContract
{
	public function __construct()
	{
		zetta()->bootstrap();
		zetta()->share(self::class, $this);
		zetta()->share(KernelContract::class, $this);
	}
	
	public function commit(RequestContract $req, ResponseContract $resp)
	{
		// TODO: Implement complete() method.
	}
	
	public function handle(RequestContract $req) : ResponseContract
	{
		zetta()->share(RequestContract::class, $req);
		RequestFacade::unfacade();
		
		require APPPATH.'/view/index.php';
		$resp = new Response;
		return $resp;
	}
	
}

<?php
/**
 * Zettacast\Contract\Http\Kernel interface file.
 * @package Zettacast
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @license MIT License
 * @copyright 2015-2017 Rodrigo Siqueira
 */
namespace Zettacast\Contract\Http;

interface Kernel
{
	
	public function commit(Request $request, Response $response);
	
	public function handle(Request $request) : Response;
	
}

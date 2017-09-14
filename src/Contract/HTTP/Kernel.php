<?php
/**
 * Zettacast\Contract\HTTP\Kernel interface file.
 * @package Zettacast
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @license MIT License
 * @copyright 2015-2017 Rodrigo Siqueira
 */
namespace Zettacast\Contract\HTTP;

interface Kernel
{
	
	public function commit(Request $request, Response $response);
	
	public function handle(Request $request) : Response;
	
}

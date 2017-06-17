<?php
/**
 * Zettacast\Helper\Connection interface file.
 * @package Zettacast
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @license MIT License
 * @copyright 2015-2017 Rodrigo Siqueira
 */
namespace Zettacast\Helper;

/**
 * This interface describes methods that allow the creation of a connection
 * to a remote server or service.
 * @package Zettacast\Helper
 */
interface Connection {
	
	/**
	 * Checks whether the connection is active.
	 * @return bool Is connection active?
	 */
	public function check() : bool;
	
	/**
	 * Initiates connection and does all needed tasks for it to work properly.
	 * @return mixed Can return anything.
	 */
	public function connect();
	
	/**
	 * Finishes connection and does all needed cleaning after it.
	 * @return mixed Can return anything.
	 */
	public function disconnect();

}

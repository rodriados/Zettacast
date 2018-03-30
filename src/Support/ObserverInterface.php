<?php
/**
 * Zettacast\Support\ObserverInterface interface file.
 * @package Zettacast
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @license MIT License
 * @copyright 2015-2018 Rodrigo Siqueira
 */
namespace Zettacast\Support;

interface ObserverInterface extends \SplObserver
{
	/**
	 * Receives an update from the subject. This method is called whenever any
	 * subject notifies it has changed.
	 * @param \SplSubject $subject The subject notifying of an update.
	 */
	public function update(\SplSubject $subject): void;
}

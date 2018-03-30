<?php
/**
 * Zettacast\Support\SubjectInterface interface file.
 * @package Zettacast
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @license MIT License
 * @copyright 2015-2018 Rodrigo Siqueira
 */
namespace Zettacast\Support;

interface SubjectInterface extends \SplSubject
{
	/**
	 * Attaches an observer, so that it can be notified of changes.
	 * @param \SplObserver $observer The observer to attach.
	 */
	public function attach(\SplObserver $observer): void;
	
	/**
	 * Detaches an observer from subject to no longer notify it of changes.
	 * @param \SplObserver $observer The observer to detach.
	 */
	public function detach(\SplObserver $observer): void;
	
	/**
	 * Notifies all attached observers of changes.
	 */
	public function notify(): void;
}

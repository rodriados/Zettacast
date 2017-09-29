<?php
/**
 * Zettacast bindings file. This file binds all of the framework's modules and
 * abstracions to a concrete implementation, so they can be easily instantiated.
 * @package Zettacast
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @license MIT License
 * @copyright 2015-2017 Rodrigo Siqueira
 */
return [

	# Injector module
	['injector', 'Zettacast\Contract\Injector\InjectorInterface'],
	['Zettacast\Injector\InjectorInterface', 'Zettacast\Injector\Injector'],
	
	# Autoload module
	['autoload', 'Zettacast\Autoload\Autoload'],
	
	# Collection module
	['stack', 'Zettacast\Collection\Stack'],
	['queue', 'Zettacast\Contract\Collection\QueueInterface'],
	['sequence', 'Zettacast\Contract\Collection\SequenceInterface'],
	['collection', 'Zettacast\Contract\Collection\CollectionInterface'],
	['collection.dot', 'Zettacast\Collection\DotCollection'],
	['collection.recursive', 'Zettacast\Collection\RecursiveCollection'],
	['Zettacast\Contract\Collection\QueueInterface', 'Zettacast\Collection\Queue'],
	['Zettacast\Contract\Collection\SequenceInterface', 'Zettacast\Collection\Sequence'],
	['Zettacast\Contract\Collection\CollectionInterface', 'Zettacast\Collection\Collection'],
	
	# Filesystem module
	['info', 'Zettacast\Filesystem\Info'],
	['file', 'Zettacast\Filesystem\File'],
	['filesystem', 'Zettacast\Filesystem\Filesystem'],
	['file.virtual', ['Zettacast\Filesystem\File', 'virtual']],
	['filesystem.zip', 'Zettacast\Filesystem\Driver\ZipDisk'],
	['filesystem.virtual', 'Zettacast\Filesystem\Driver\VirtualDisk'],
	['Zettacast\Contract\Filesystem\DiskInterface', 'Zettacast\Filesystem\Driver\LocalDisk'],
	
	# Stream module
	['filter', 'Zettacast\Contract\Stream\FilterInterface'],
	['stream', 'Zettacast\Contract\Stream\StreamInterface'],
	['stream.context', 'Zettacast\Stream\StreamContext'],
	['stream.virtual', ['Zettacast\Stream\Stream', 'virtual']],
	['filter.closure', 'Zettacast\Stream\Filter\ClosureFilter'],
	['filter.callable', 'Zettacast\Stream\Filter\CallableFilter'],
	['Zettacast\Contract\Stream\StreamInterface', 'Zettacast\Stream\Stream'],
	['Zettacast\Contract\Stream\FilterInterface', 'Zettacast\Stream\Filter'],
	
	# HTTP module
	['url', 'Zettacast\Http\Url'],
	['request', 'Zettacast\Contract\Http\Request'],
	['response', 'Zettacast\Contract\Http\Response'],
	['Zettacast\Contract\Http\Kernel', 'Zettacast\Http\Kernel'],
	['Zettacast\Contract\Http\Request', 'Zettacast\Http\Request'],
	
];

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
	# Autoload module
	['autoload', 'Zettacast\Autoload\Autoload'],
	
	# Miscellaneous objects
	['config', 'Zettacast\Support\Config'],
	
	# Injector module
	['injector', 'Zettacast\Injector\InjectorInterface'],
	['Zettacast\Injector\InjectorInterface', 'Zettacast\Injector\Injector'],
	
	# Collection module
	['stack', 'Zettacast\Collection\Stack'],
	['queue', 'Zettacast\Collection\Queue'],
	['sequence', 'Zettacast\Collection\Sequence'],
	['collection', 'Zettacast\Collection\CollectionInterface'],
	['collection.dot', 'Zettacast\Collection\DotCollection'],
	['collection.recursive', 'Zettacast\Collection\RecursiveCollection'],
	['Zettacast\Collection\QueueInterface', 'Zettacast\Collection\Queue'],
	['Zettacast\Collection\SequenceInterface', 'Zettacast\Collection\Sequence'],
	['Zettacast\Collection\CollectionInterface', 'Zettacast\Collection\Collection'],
	
	# Filesystem module
	['info', 'Zettacast\Filesystem\Info'],
	['file', 'Zettacast\Filesystem\File'],
	['filesystem', 'Zettacast\Filesystem\Filesystem'],
	['file.virtual', ['Zettacast\Filesystem\File', 'virtual']],
	['filesystem.zip', 'Zettacast\Filesystem\Disk\ZipDisk'],
	['filesystem.virtual', 'Zettacast\Filesystem\Disk\VirtualDisk'],
	['Zettacast\Filesystem\DiskInterface', 'Zettacast\Filesystem\Disk\LocalDisk'],
	
	# Stream module
	['uri', 'Zettacast\Stream\Uri'],
	['filter', 'Zettacast\Stream\FilterInterface'],
	['stream', 'Zettacast\Stream\StreamInterface'],
	['stream.context', 'Zettacast\Stream\StreamContext'],
	['stream.virtual', ['Zettacast\Stream\Stream', 'virtual']],
	['filter.closure', 'Zettacast\Stream\Filter\ClosureFilter'],
	['filter.callable', 'Zettacast\Stream\Filter\CallableFilter'],
	['Zettacast\Stream\StreamInterface', 'Zettacast\Stream\Stream'],
	['Zettacast\Stream\FilterInterface', 'Zettacast\Stream\Filter'],
	
	# HTTP module
	#['url', 'Zettacast\Http\Url'],
	#['request', 'Zettacast\Http\Request'],
	#['response', 'Zettacast\Http\Response'],
	#['Zettacast\Http\Kernel', 'Zettacast\Http\Kernel'],
	#['Zettacast\Http\Request', 'Zettacast\Http\Request'],
];

<?php
/**
 * Zettacast bindings file. This file binds all of the framework's modules and
 * abstracions to a concrete implementation, so they can be easily instantiated.
 * @package Zettacast
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @license MIT License
 * @copyright 2015-2017 Rodrigo Siqueira
 */
$zetta = zetta();

$bindings = [
	# Injector module
	'injector' => 'Zettacast\Contract\Injector\InjectorInterface',
	'Zettacast\Injector\InjectorInterface' => 'Zettacast\Injector\Injector',

	# Autoload module
	'autoload' => 'Zettacast\Autoload\Autoload',

	# Collection module
	'stack' => 'Zettacast\Collection\Stack',
	'queue' => 'Zettacast\Contract\Collection\QueueInterface',
	'sequence' => 'Zettacast\Contract\Collection\SequenceInterface',
	'collection' => 'Zettacast\Contract\Collection\CollectionInterface',
	'collection.dot' => 'Zettacast\Collection\DotCollection',
	'collection.recursive' => 'Zettacast\Collection\RecursiveCollection',
	'Zettacast\Contract\Collection\QueueInterface' => 'Zettacast\Collection\Queue',
	'Zettacast\Contract\Collection\SequenceInterface' => 'Zettacast\Collection\Sequence',
	'Zettacast\Contract\Collection\CollectionInterface' => 'Zettacast\Collection\Collection',

	# Config module
	'config' => 'Zettacast\Config\Warehouse',

	# Filesystem module
	'info' => 'Zettacast\Filesystem\Info',
	'file' => 'Zettacast\Filesystem\File',
	'filesystem' => 'Zettacast\Filesystem\Filesystem',
	'file.virtual' => ['Zettacast\Filesystem\File', 'virtual'],
	'filesystem.zip' => 'Zettacast\Filesystem\Driver\ZipDriver',
	'filesystem.virtual' => 'Zettacast\Filesystem\Driver\VirtualDriver',
	'Zettacast\Contract\Filesystem\DriverInterface' => 'Zettacast\Filesystem\Driver\LocalDriver',
	
	# Stream module
	'filter' => 'Zettacast\Contract\Stream\FilterInterface',
	'stream' => 'Zettacast\Contract\Stream\StreamInterface',
	'stream.context' => 'Zettacast\Stream\StreamContext',
	'stream.virtual' => ['Zettacast\Stream\Stream', 'virtual'],
	'filter.closure' => 'Zettacast\Stream\Filter\ClosureFilter',
	'Zettacast\Contract\Stream\StreamInterface' => 'Zettacast\Stream\Stream',
	'Zettacast\Contract\Stream\FilterInterface' => 'Zettacast\Stream\Filter',
];

foreach($bindings as $abstract => $concrete)
	$zetta->bind($abstract, $concrete);

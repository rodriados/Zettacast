<?php
/**
 * Zettacast bindings file. This file binds all of the framework's modules and
 * abstracions to a concrete implementation, so they can be easily instantiated.
 * @package Zettacast
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @license MIT License
 * @copyright 2015-2017 Rodrigo Siqueira
 */
zetta()

# Injector module
->bind('injector', 'Zettacast\Injector\Injector')
->bind('Zettacast\Injector\InjectorInterface', 'Zettacast\Injector\Injector')

# Autoload module
->bind('autoload', 'Zettacast\Autoload\Autoload')

# Collection module
->bind('queue', 'Zettacast\Collection\Queue')
->bind('stack', 'Zettacast\Collection\Stack')
->bind('dot', 'Zettacast\Collection\DotCollection')
->bind('sequence', 'Zettacast\Collection\Sequence')
->bind('collection', 'Zettacast\Collection\Collection')

# Config module
->bind('config', 'Zettacast\Config\Warehouse')

# Filesystem module
->bind('file', 'Zettacast\Filesystem\File')
->bind('info', 'Zettacast\Filesystem\Info')
->bind('stream', 'Zettacast\Filesystem\Stream\Stream')
->bind('filesystem', 'Zettacast\Filesystem\Filesystem')
->bind('Zettacast\Contract\Filesystem\Driver', 'Zettacast\Filesystem\Driver\Local')
->bind('Zettacast\Contract\Filesystem\Stream', 'Zettacast\Filesystem\Stream\Stream')

;

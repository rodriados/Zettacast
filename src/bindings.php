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
->alias('injector', 'Zettacast\Injector\Injector')
->bind('Zettacast\Contract\Injector\Injector', 'Zettacast\Injector\Injector')

# Autoload module
->alias('autoload', 'Zettacast\Autoload\Autoload')

# Collection module
->alias('dot', 'Zettacast\Collection\Dot')
->alias('queue', 'Zettacast\Collection\Queue')
->alias('stack', 'Zettacast\Collection\Stack')
->alias('sequence', 'Zettacast\Collection\Sequence')
->alias('collection', 'Zettacast\Collection\Collection')

# Config module
->alias('config', 'Zettacast\Config\Repository')

# Filesystem module
->alias('file', 'Zettacast\Filesystem\File')
->alias('info', 'Zettacast\Filesystem\Info')
->alias('stream', 'Zettacast\Filesystem\Stream\Stream')
->alias('filesystem', 'Zettacast\Filesystem\Filesystem')
->bind('Zettacast\Contract\Filesystem\Driver', 'Zettacast\Filesystem\Driver\Local')
->bind('Zettacast\Contract\Filesystem\Stream', 'Zettacast\Filesystem\Stream\Stream')

;

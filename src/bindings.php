<?php
/**
 * Zettacast bindings file. This file binds all of the framework's modules and
 * abstracions to a concrete implementation, so they can be easily instantiated.
 * @package Zettacast
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @license MIT License
 * @copyright 2015-2017 Rodrigo Siqueira
 */

# Injector module
zetta()->alias('injector', 'Zettacast\Injector\Injector');
zetta()->bind('Zettacast\Contract\Injector\Injector', 'Zettacast\Injector\Injector');

# Autoload module
zetta()->alias('autoload', 'Zettacast\Autoload\Autoload');

# Collection module
zetta()->alias('dot', 'Zettacast\Collection\Dot');
zetta()->alias('queue', 'Zettacast\Collection\Queue');
zetta()->alias('stack', 'Zettacast\Collection\Stack');
zetta()->alias('sequence', 'Zettacast\Collection\Sequence');
zetta()->alias('collection', 'Zettacast\Collection\Collection');

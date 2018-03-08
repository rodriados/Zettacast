<?php
/**
 * Zettacast bootstrap file.
 * This file is responsible for booting the framework up and starting all basic
 * code needed for a perfectly functional execution.
 * @package Zettacast
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @license MIT License
 * @copyright 2017 Rodrigo Siqueira
 */

/*
 * Include files needed for the correct functioning of the framework. The
 * global functions created by the framework are available from this point.
 */
require SRCPATH.'/functions.php';
require SRCPATH.'/Autoload/Autoload.php';

/*
 * Create an autoloader object. This object will be responsible for loading all
 * requested classes, interfaces or the like for the framework when needed.
 */
$loader = new \Zettacast\Autoload\Autoload;

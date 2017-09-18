<?php
/**
 * Zettacast\Contract\Collection\CollectionInterface interface file.
 * @package Zettacast
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @license MIT License
 * @copyright 2015-2017 Rodrigo Siqueira
 */
namespace Zettacast\Contract\Collection;

use Zettacast\Contract\StorageInterface;
use Zettacast\Contract\ListableInterface;

/**
 * Collection interface. This interface exposes all methods needed for a class
 * to work as a collection.
 * @package Zettacast\Collection
 */
interface CollectionInterface extends ListableInterface, StorageInterface
{}

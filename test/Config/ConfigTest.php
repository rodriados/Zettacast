<?php
/**
 * Zettacast\Test\Config test file.
 * @package Zettacast
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @license MIT License
 * @copyright 2015-2018 Rodrigo Siqueira
 */
namespace Zettacast\Test\Config;

use Zettacast\Config\Config;
use PHPUnit\Framework\TestCase;

final class ConfigTest extends TestCase
{
	public function canInstantiate()
	{
		$cfg = new Config(__DIR__.'/data');
		$this->assertInstanceOf(Config::class, $cfg);
	}
	
	public function testConfig()
	{
		$cfg = new Config(__DIR__.'/data');
		$this->assertEquals($cfg->get('app.name'), 'Zettacast');
		$this->assertEquals($cfg->get('app.url'), 'zettacast.localhost');
		
		$this->assertTrue($cfg->has('mock.data'));
		$this->assertEquals($cfg->get('mock.user.name.first'), 'John');
		$this->assertEquals($cfg->get('mock.user.name.last'), 'Doe');
		
		$this->assertFalse($cfg->has('no.file'));
		$this->assertEquals($cfg->get('no.file', 'defvalue'), 'defvalue');
	}
}

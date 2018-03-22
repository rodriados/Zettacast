<?php

use Zettacast\Support\Config;

final class ConfigTest extends \PHPUnit\Framework\TestCase
{
	public function canInstantiate()
	{
		$cfg = new Config(__DIR__.'/config');
		$this->assertInstanceOf(Config::class, $cfg);
	}
	
	public function testConfig()
	{
		$cfg = new Config(__DIR__.'/config');
		$this->assertEquals($cfg->get('app.name'), 'Zettacast');
		$this->assertEquals($cfg->get('app.url'), 'zettacast.localhost');
		
		$this->assertTrue($cfg->has('mock.data'));
		$this->assertEquals($cfg->get('mock.user.name.first'), 'John');
		$this->assertEquals($cfg->get('mock.user.name.last'), 'Doe');
		
		$this->assertFalse($cfg->has('no.file'));
		$this->assertEquals($cfg->get('no.file', 'defvalue'), 'defvalue');
	}
}

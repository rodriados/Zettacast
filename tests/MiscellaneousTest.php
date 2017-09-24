<?php

use Zettacast\Facade\Config;

final class MiscellaneousTest extends \PHPUnit\Framework\TestCase
{
	public function testFunctionWith()
	{
		$this->assertEquals(859, with(859));
		$this->assertEquals(478, with(function() { return 478; }));
	}
	
	public function testConfig()
	{
		$this->assertInstanceOf(Config::class, Config::i());
		$this->assertTrue(Config::has('app.timezone'));
		$this->assertEquals(Config::get('app.charset'), 'UTF-8');
		$this->assertEquals(Config::get('doesnt.exist', 'Okay!'), 'Okay!');
	}
	
}

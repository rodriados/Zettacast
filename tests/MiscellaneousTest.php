<?php

final class MiscellaneousTest extends \PHPUnit\Framework\TestCase
{
	public function testFunctionWith()
	{
		$this->assertEquals(859, with(859));
		$this->assertEquals(478, with(function() { return 478; }));
	}
	
}

<?php

use Zettacast\Stream\Filter;
use Zettacast\Stream\Stream;

final class StreamTest extends \PHPUnit\Framework\TestCase
{
	public function testFilterStream()
	{
		$filter = zetta('filter', 'filter.closure', function() {
			if(!$data = $this->read())
				return self::SUCCESS;
			
			$data = explode(' ', $data);
			foreach($data as &$value)
				$value = (int)$value * 2;
			$this->write(implode(' ', $data));
			return self::SUCCESS;
		});
		
		$stream = zetta('stream.virtual', '0 1 2 3 4 5 6 7 8 9');
		$this->assertInstanceOf(Stream::class, $stream);
		$this->assertInstanceOf(Filter::class, $filter);
		
		$stream->filter($filter, Filter::READ);
		$this->assertEquals($stream->read(), '0 2 4 6 8 10 12 14 16 18');
	}
	
}

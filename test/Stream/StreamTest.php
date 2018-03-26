<?php
/**
 * Zettacast\Test\Stream test file.
 * @package Zettacast
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @license MIT License
 * @copyright 2015-2018 Rodrigo Siqueira
 */
namespace Zettacast\Test\Stream;

use Zettacast\Stream\Filter;
use Zettacast\Stream\Stream;
use PHPUnit\Framework\TestCase;

final class StreamTest extends TestCase
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
		$this->assertTrue($stream->eof());
	}
	
	public function testStream()
	{
		$original = zetta('stream.virtual', "original stream");
		$stream = zetta('stream.virtual');

		$stream->writefrom($original);
		$original->seek(0);
		$stream->seek(0);
		
		$this->assertEquals($original->tell(), 0);
		$this->assertEquals($stream->tell(), 0);
		$this->expectOutputString("original streamoriginal stream");
		$this->assertEquals($original->passthru(), $stream->passthru());
		
		$original->offset(-15);
		$stream->offset(-15);
		$this->assertEquals($original->tell(), 0);
		$this->assertEquals($stream->tell(), 0);
		$this->assertEquals($original->read(), $stream->readline());
		
		$original->seek(0);
		$stream->seek(0);
		$this->assertTrue($original->truncate(1));
		$this->assertEquals('o', $original->read());
		
		$original->seek(0);
		$stream->readto($original);
		$this->assertEquals($original->readline(), $stream->readline());
	}
}

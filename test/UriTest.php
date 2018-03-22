<?php

use Zettacast\Support\Uri;
use Zettacast\Support\UriException;

final class UriTest extends \PHPUnit\Framework\TestCase
{
	public function canInstantiate()
	{
		$uri = new Uri('scheme://user@host:1234/path?query=true#frag');
		$this->assertInstanceOf(Uri::class, $uri);
		$this->assertEquals($uri->scheme, 'scheme');
		$this->assertEquals($uri->userinfo, 'user');
		$this->assertEquals($uri->host, 'host');
		$this->assertEquals($uri->port, 1234);
		$this->assertEquals($uri->path, 'path');
		$this->assertEquals($uri->query['query'], 'true');
		$this->assertEquals($uri->fragment, 'frag');
		$this->assertEquals((string)$uri, 'scheme://user@host:1234/path?query=true#frag');
	}
	
	public function testReference()
	{
		# These are the tests given in RFC3986
		$u = new Uri('http://a/b/c/d;p?q');
		$this->assertEquals($u->reference("g:h"), "g:h");
		$this->assertEquals($u->reference("g"), "http://a/b/c/g");
		$this->assertEquals($u->reference("./g"), "http://a/b/c/g");
		$this->assertEquals($u->reference("g/"), "http://a/b/c/g/");
		$this->assertEquals($u->reference("/g"), "http://a/g");
		$this->assertEquals($u->reference("//g"), "http://g");
		$this->assertEquals($u->reference("?y"), "http://a/b/c/d;p?y");
		$this->assertEquals($u->reference("g?y"), "http://a/b/c/g?y");
		$this->assertEquals($u->reference("#s"), "http://a/b/c/d;p?q#s");
		$this->assertEquals($u->reference("g#s"), "http://a/b/c/g#s");
		$this->assertEquals($u->reference("g?y#s"), "http://a/b/c/g?y#s");
		$this->assertEquals($u->reference(";x"), "http://a/b/c/;x");
		$this->assertEquals($u->reference("g;x"), "http://a/b/c/g;x");
		$this->assertEquals($u->reference("g;x?y#s"), "http://a/b/c/g;x?y#s");
		$this->assertEquals($u->reference(""), "http://a/b/c/d;p?q");
		$this->assertEquals($u->reference("."), "http://a/b/c/");
		$this->assertEquals($u->reference("./"), "http://a/b/c/");
		$this->assertEquals($u->reference(".."), "http://a/b/");
		$this->assertEquals($u->reference("../"), "http://a/b/");
		$this->assertEquals($u->reference("../g"), "http://a/b/g");
		$this->assertEquals($u->reference("../.."), "http://a/");
		$this->assertEquals($u->reference("../../"), "http://a/");
		$this->assertEquals($u->reference("../../g"), "http://a/g");
		$this->assertEquals($u->reference("../../../g"), "http://a/g");
		$this->assertEquals($u->reference("../../../../g"), "http://a/g");
		$this->assertEquals($u->reference("/./g"), "http://a/g");
		$this->assertEquals($u->reference("/../g"), "http://a/g");
		$this->assertEquals($u->reference("g."), "http://a/b/c/g.");
		$this->assertEquals($u->reference(".g"), "http://a/b/c/.g");
		$this->assertEquals($u->reference("g.."), "http://a/b/c/g..");
		$this->assertEquals($u->reference("..g"), "http://a/b/c/..g");
		$this->assertEquals($u->reference("./../g"), "http://a/b/g");
		$this->assertEquals($u->reference("./g/."), "http://a/b/c/g/");
		$this->assertEquals($u->reference("g/./h"), "http://a/b/c/g/h");
		$this->assertEquals($u->reference("g/../h"), "http://a/b/c/h");
		$this->assertEquals($u->reference("g;x=1/./y"), "http://a/b/c/g;x=1/y");
		$this->assertEquals($u->reference("g;x=1/../y"), "http://a/b/c/y");
		$this->assertEquals($u->reference("g#s/./x"), "http://a/b/c/g#s/./x");
		$this->assertEquals($u->reference("g#s/../x"), "http://a/b/c/g#s/../x");
		$this->assertEquals($u->reference("http:g"), "http:g");
	}
	
	public function testAbsolute()
	{
		$uri[] = new Uri('http://scheme.absolute');
		$uri[] = new Uri('/path/absolute');
		$this->assertTrue($uri[0]->absolute());
		$this->assertTrue($uri[1]->absolute());
	}
	
	public function testRelative()
	{
		$uri[] = new Uri('//scheme.relative');
		$uri[] = new Uri('path/relative');
		$uri[] = new Uri('?query=string');
		$uri[] = new Uri('#fragment');
		$this->assertTrue($uri[0]->relative());
		$this->assertTrue($uri[1]->relative());
		$this->assertTrue($uri[2]->relative());
		$this->assertTrue($uri[3]->relative());
	}
	
	public function testSegments()
	{
		$uri = new Uri('/absolute/path/is/de/wae');
		$this->assertEquals($uri->segment(1), 'absolute');
		$this->assertEquals($uri->segment(2), 'path');
		$this->assertEquals($uri->segment(3), 'is');
		$this->assertEquals($uri->segment(4), 'de');
		$this->assertEquals($uri->segment(5), 'wae');
	}
	
	public function testInvalid1()
	{
		$this->expectException(UriException::class);
		new Uri('scheme:');
	}
	
	public function testInvalid2()
	{
		$this->expectException(UriException::class);
		new Uri('http://');
	}
	
	public function testInvalid3()
	{
		$this->expectException(UriException::class);
		new Uri('http://user@');
	}
}

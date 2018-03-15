<?php

use Zettacast\Support\Uri;

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
	}
	
	public function testReference()
	{
		$uri = new Uri('http://a/b/c/d;p?q');
		
		$tests = [
			"g:h"           =>  "g:h",
			"g"             =>  "http://a/b/c/g",
			"./g"           =>  "http://a/b/c/g",
			"g/"            =>  "http://a/b/c/g/",
			"/g"            =>  "http://a/g",
			"//g"           =>  "http://g",
			"?y"            =>  "http://a/b/c/d;p?y",
			"g?y"           =>  "http://a/b/c/g?y",
			"#s"            =>  "http://a/b/c/d;p?q#s",
			"g#s"           =>  "http://a/b/c/g#s",
			"g?y#s"         =>  "http://a/b/c/g?y#s",
			";x"            =>  "http://a/b/c/;x",
			"g;x"           =>  "http://a/b/c/g;x",
			"g;x?y#s"       =>  "http://a/b/c/g;x?y#s",
			""              =>  "http://a/b/c/d;p?q",
			"."             =>  "http://a/b/c/",
			"./"            =>  "http://a/b/c/",
			".."            =>  "http://a/b/",
			"../"           =>  "http://a/b/",
			"../g"          =>  "http://a/b/g",
			"../.."         =>  "http://a/",
			"../../"        =>  "http://a/",
			"../../g"       =>  "http://a/g",
			"../../../g"    =>  "http://a/g",
			"../../../../g" =>  "http://a/g",
			"/./g"          =>  "http://a/g",
			"/../g"         =>  "http://a/g",
			"g."            =>  "http://a/b/c/g.",
			".g"            =>  "http://a/b/c/.g",
			"g.."           =>  "http://a/b/c/g..",
			"..g"           =>  "http://a/b/c/..g",
			"./../g"        =>  "http://a/b/g",
			"./g/."         =>  "http://a/b/c/g/",
			"g/./h"         =>  "http://a/b/c/g/h",
			"g/../h"        =>  "http://a/b/c/h",
			"g;x=1/./y"     =>  "http://a/b/c/g;x=1/y",
			"g;x=1/../y"    =>  "http://a/b/c/y",
			"g#s/./x"       =>  "http://a/b/c/g#s/./x",
			"g#s/../x"      =>  "http://a/b/c/g#s/../x",
			"http:g"        =>  "http:g",
		];
		
		foreach($tests as $case => $expect)
			$this->assertEquals($uri->reference($case)->full, $expect);
	}
}

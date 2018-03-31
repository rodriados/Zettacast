<?php
/**
 * Zettacast\Test\Uri test file.
 * @package Zettacast
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @license MIT License
 * @copyright 2015-2018 Rodrigo Siqueira
 */
namespace Zettacast\Test\Uri;

use Zettacast\Uri\Uri;
use Zettacast\Uri\UriException;
use PHPUnit\Framework\TestCase;

final class UriTest extends TestCase
{
	public function validUriProvider()
	{
		return [
			'complete URI' => [
				'scheme://user:pass@host:81/path?query#fragment',
				[
					'scheme' => 'scheme',
					'userinfo' => 'user:pass',
					'host' => 'host',
					'port' => 81,
					'path' => '/path',
					'query' => 'query',
					'fragment' => 'fragment',
				],
			],
			'URI is not normalized' => [
				'ScheMe://user:pass@HoSt:81/path?query#fragment',
				[
					'scheme' => 'ScheMe',
					'userinfo' => 'user:pass',
					'host' => 'HoSt',
					'port' => 81,
					'path' => '/path',
					'query' => 'query',
					'fragment' => 'fragment',
				],
			],
			'URI without scheme' => [
				'//user:pass@HoSt:81/path?query#fragment',
				[
					'scheme' => null,
					'userinfo' => 'user:pass',
					'host' => 'HoSt',
					'port' => 81,
					'path' => '/path',
					'query' => 'query',
					'fragment' => 'fragment',
				],
			],
			'URI without userinfo' => [
				'scheme://HoSt:81/path?query#fragment',
				[
					'scheme' => 'scheme',
					'userinfo' => null,
					'host' => 'HoSt',
					'port' => 81,
					'path' => '/path',
					'query' => 'query',
					'fragment' => 'fragment',
				],
			],
			'URI with empty userinfo' => [
				'scheme://@HoSt:81/path?query#fragment',
				[
					'scheme' => 'scheme',
					'userinfo' => '',
					'host' => 'HoSt',
					'port' => 81,
					'path' => '/path',
					'query' => 'query',
					'fragment' => 'fragment',
				],
			],
			'URI without port' => [
				'scheme://user:pass@host/path?query#fragment',
				[
					'scheme' => 'scheme',
					'userinfo' => 'user:pass',
					'host' => 'host',
					'port' => null,
					'path' => '/path',
					'query' => 'query',
					'fragment' => 'fragment',
				],
			],
			'URI with an empty port' => [
				'scheme://user:pass@host:/path?query#fragment',
				[
					'scheme' => 'scheme',
					'userinfo' => 'user:pass',
					'host' => 'host',
					'port' => null,
					'path' => '/path',
					'query' => 'query',
					'fragment' => 'fragment',
				],
			],
			'URI without user info and port' => [
				'scheme://host/path?query#fragment',
				[
					'scheme' => 'scheme',
					'userinfo' => null,
					'host' => 'host',
					'port' => null,
					'path' => '/path',
					'query' => 'query',
					'fragment' => 'fragment',
				],
			],
			'URI with host IP' => [
				'scheme://10.0.0.2/p?q#f',
				[
					'scheme' => 'scheme',
					'userinfo' => null,
					'host' => '10.0.0.2',
					'port' => null,
					'path' => '/p',
					'query' => 'q',
					'fragment' => 'f',
				],
			],
			'URI with IP future' => [
				'scheme://[vAF.1::2::3]/p?q#f',
				[
					'scheme' => 'scheme',
					'userinfo' => null,
					'host' => '[vAF.1::2::3]',
					'port' => null,
					'path' => '/p',
					'query' => 'q',
					'fragment' => 'f',
				],
			],
			'URI without authority' => [
				'scheme:path?query#fragment',
				[
					'scheme' => 'scheme',
					'userinfo' => null,
					'host' => null,
					'port' => null,
					'path' => 'path',
					'query' => 'query',
					'fragment' => 'fragment',
				],
			],
			'URI without authority and scheme' => [
				'/path',
				[
					'scheme' => null,
					'userinfo' => null,
					'host' => null,
					'port' => null,
					'path' => '/path',
					'query' => null,
					'fragment' => null,
				],
			],
			'URI with empty host' => [
				'scheme:///path?query#fragment',
				[
					'scheme' => 'scheme',
					'userinfo' => null,
					'host' => '',
					'port' => null,
					'path' => '/path',
					'query' => 'query',
					'fragment' => 'fragment',
				],
			],
			'URI with empty host and without scheme' => [
				'///path?query#fragment',
				[
					'scheme' => null,
					'userinfo' => null,
					'host' => '',
					'port' => null,
					'path' => '/path',
					'query' => 'query',
					'fragment' => 'fragment',
				],
			],
			'URI without path' => [
				'scheme://[FEDC:BA98:7654:3210:FEDC:BA98:7654:3210]?query#fragment',
				[
					'scheme' => 'scheme',
					'userinfo' => null,
					'host' => '[FEDC:BA98:7654:3210:FEDC:BA98:7654:3210]',
					'port' => null,
					'path' => null,
					'query' => 'query',
					'fragment' => 'fragment',
				],
			],
			'URI without path and scheme' => [
				'//[FEDC:BA98:7654:3210:FEDC:BA98:7654:3210]?query#fragment',
				[
					'scheme' => null,
					'userinfo' => null,
					'host' => '[FEDC:BA98:7654:3210:FEDC:BA98:7654:3210]',
					'port' => null,
					'path' => null,
					'query' => 'query',
					'fragment' => 'fragment',
				],
			],
			'URI without scheme with IPv6 host and port' => [
				'//[FEDC:BA98:7654:3210:FEDC:BA98:7654:3210]:42?query#fragment',
				[
					'scheme' => null,
					'userinfo' => null,
					'host' => '[FEDC:BA98:7654:3210:FEDC:BA98:7654:3210]',
					'port' => 42,
					'path' => null,
					'query' => 'query',
					'fragment' => 'fragment',
				],
			],
			'complete URI without scheme' => [
				'//user@[FEDC:BA98:7654:3210:FEDC:BA98:7654:3210]:42?q#f',
				[
					'scheme' => null,
					'userinfo' => 'user',
					'host' => '[FEDC:BA98:7654:3210:FEDC:BA98:7654:3210]',
					'port' => 42,
					'path' => null,
					'query' => 'q',
					'fragment' => 'f',
				],
			],
			'URI without authority and query' => [
				'scheme:path#fragment',
				[
					'scheme' => 'scheme',
					'userinfo' => null,
					'host' => null,
					'port' => null,
					'path' => 'path',
					'query' => null,
					'fragment' => 'fragment',
				],
			],
			'URI with empty query' => [
				'scheme:path?#fragment',
				[
					'scheme' => 'scheme',
					'userinfo' => null,
					'host' => null,
					'port' => null,
					'path' => 'path',
					'query' => '',
					'fragment' => 'fragment',
				],
			],
			'URI with query only' => [
				'?query',
				[
					'scheme' => null,
					'userinfo' => null,
					'host' => null,
					'port' => null,
					'path' => null,
					'query' => 'query',
					'fragment' => null,
				],
			],
			'URI without fragment' => [
				'tel:05000',
				[
					'scheme' => 'tel',
					'userinfo' => null,
					'host' => null,
					'port' => null,
					'path' => '05000',
					'query' => null,
					'fragment' => null,
				],
			],
			'URI with empty fragment' => [
				'scheme:path#',
				[
					'scheme' => 'scheme',
					'userinfo' => null,
					'host' => null,
					'port' => null,
					'path' => 'path',
					'query' => null,
					'fragment' => '',
				],
			],
			'URI with fragment only' => [
				'#fragment',
				[
					'scheme' => null,
					'userinfo' => null,
					'host' => null,
					'port' => null,
					'path' => null,
					'query' => null,
					'fragment' => 'fragment',
				],
			],
			'URI with empty fragment only' => [
				'#',
				[
					'scheme' => null,
					'userinfo' => null,
					'host' => null,
					'port' => null,
					'path' => null,
					'query' => null,
					'fragment' => '',
				],
			],
			'URI without authority 2' => [
				'path#fragment',
				[
					'scheme' => null,
					'userinfo' => null,
					'host' => null,
					'port' => null,
					'path' => 'path',
					'query' => null,
					'fragment' => 'fragment',
				],
			],
			'URI with empty query and fragment' => [
				'?#',
				[
					'scheme' => null,
					'userinfo' => null,
					'host' => null,
					'port' => null,
					'path' => null,
					'query' => '',
					'fragment' => '',
				],
			],
			'URI with absolute path' => [
				'/?#',
				[
					'scheme' => null,
					'userinfo' => null,
					'host' => null,
					'port' => null,
					'path' => '/',
					'query' => '',
					'fragment' => '',
				],
			],
			'URI with absolute authority' => [
				'https://thephpleague.com./p?#f',
				[
					'scheme' => 'https',
					'userinfo' => null,
					'host' => 'thephpleague.com.',
					'port' => null,
					'path' => '/p',
					'query' => '',
					'fragment' => 'f',
				],
			],
			'URI with absolute path only' => [
				'/',
				[
					'scheme' => null,
					'userinfo' => null,
					'host' => null,
					'port' => null,
					'path' => '/',
					'query' => null,
					'fragment' => null,
				],
			],
			'URI with empty query only' => [
				'?',
				[
					'scheme' => null,
					'userinfo' => null,
					'host' => null,
					'port' => null,
					'path' => null,
					'query' => '',
					'fragment' => null,
				],
			],
			'relative path' => [
				'../relative/path',
				[
					'scheme' => null,
					'userinfo' => null,
					'host' => null,
					'port' => null,
					'path' => '../relative/path',
					'query' => null,
					'fragment' => null,
				],
			],
			'complex authority' => [
				'http://a_.!~*\'(-)n0123Di%25%26:pass;:&=+$,word@www.zend.com',
				[
					'scheme' => 'http',
					'userinfo' => 'a_.!~*\'(-)n0123Di%25%26:pass;:&=+$,word',
					'host' => 'www.zend.com',
					'port' => null,
					'path' => null,
					'query' => null,
					'fragment' => null,
				],
			],
			'complex authority without scheme' => [
				'//a_.!~*\'(-)n0123Di%25%26:pass;:&=+$,word@www.zend.com',
				[
					'scheme' => null,
					'userinfo' => 'a_.!~*\'(-)n0123Di%25%26:pass;:&=+$,word',
					'host' => 'www.zend.com',
					'port' => null,
					'path' => null,
					'query' => null,
					'fragment' => null,
				],
			],
			'single word is a path' => [
				'http',
				[
					'scheme' => null,
					'userinfo' => null,
					'host' => null,
					'port' => null,
					'path' => 'http',
					'query' => null,
					'fragment' => null,
				],
			],
			'fragment with pseudo segment' => [
				'http://example.com#foo=1/bar=2',
				[
					'scheme' => 'http',
					'userinfo' => null,
					'host' => 'example.com',
					'port' => null,
					'path' => null,
					'query' => null,
					'fragment' => 'foo=1/bar=2',
				],
			],
			'empty string' => [
				'',
				[
					'scheme' => null,
					'userinfo' => null,
					'host' => null,
					'port' => null,
					'path' => null,
					'query' => null,
					'fragment' => null,
				],
			],
			'complex URI' => [
				'htà+d/s:totot',
				[
					'scheme' => null,
					'userinfo' => null,
					'host' => null,
					'port' => null,
					'path' => 'htà+d/s:totot',
					'query' => null,
					'fragment' => null,
				],
			],
			'RFC3986 LDAP example' => [
				'ldap://[2001:db8::7]/c=GB?objectClass?one',
				[
					'scheme' => 'ldap',
					'userinfo' => null,
					'host' => '[2001:db8::7]',
					'port' => null,
					'path' => '/c=GB',
					'query' => 'objectClass?one',
					'fragment' => null,
				],
			],
			'RFC3987 example' => [
				'http://bébé.bé./有词法别名.zh',
				[
					'scheme' => 'http',
					'userinfo' => null,
					'host' => 'bébé.bé.',
					'port' => null,
					'path' => '/有词法别名.zh',
					'query' => null,
					'fragment' => null,
				],
			],
			'colon detection respect RFC3986 (1)' => [
				'http://example.org/hello:12?foo=bar#test',
				[
					'scheme' => 'http',
					'userinfo' => null,
					'host' => 'example.org',
					'port' => null,
					'path' => '/hello:12',
					'query' => 'foo=bar',
					'fragment' => 'test',
				],
			],
			'colon detection respect RFC3986 (2)' => [
				'/path/to/colon:34',
				[
					'scheme' => null,
					'userinfo' => null,
					'host' => null,
					'port' => null,
					'path' => '/path/to/colon:34',
					'query' => null,
					'fragment' => null,
				],
			],
			'scheme with hyphen' => [
				'android-app://org.wikipedia/http/en.m.wikipedia.org/wiki/The_Hitchhiker%27s_Guide_to_the_Galaxy',
				[
					'scheme' => 'android-app',
					'userinfo' => null,
					'host' => 'org.wikipedia',
					'port' => null,
					'path' => '/http/en.m.wikipedia.org/wiki/The_Hitchhiker%27s_Guide_to_the_Galaxy',
					'query' => null,
					'fragment' => null,
				],
			],
		];
	}
	
	public function referenceUriProvider(): array
	{
		# These are the tests given in RFC3986
		return [
		    'reference (1)' => ['g:h', 'g:h'],
		    'reference (2)' => ['g', 'http://a/b/c/g'],
		    'reference (3)' => ['./g', 'http://a/b/c/g'],
		    'reference (4)' => ['g/', 'http://a/b/c/g/'],
		    'reference (5)' => ['/g', 'http://a/g'],
		    'reference (6)' => ['//g', 'http://g'],
		    'reference (7)' => ['?y', 'http://a/b/c/d;p?y'],
		    'reference (8)' => ['g?y', 'http://a/b/c/g?y'],
		    'reference (9)' => ['#s', 'http://a/b/c/d;p?q#s'],
		    'reference (10)' => ['g#s', 'http://a/b/c/g#s'],
		    'reference (11)' => ['g?y#s', 'http://a/b/c/g?y#s'],
		    'reference (12)' => [';x', 'http://a/b/c/;x'],
		    'reference (13)' => ['g;x', 'http://a/b/c/g;x'],
		    'reference (14)' => ['g;x?y#s', 'http://a/b/c/g;x?y#s'],
		    'reference (15)' => ['', 'http://a/b/c/d;p?q'],
		    'reference (16)' => ['.', 'http://a/b/c/'],
		    'reference (17)' => ['./', 'http://a/b/c/'],
		    'reference (18)' => ['..', 'http://a/b/'],
		    'reference (19)' => ['../', 'http://a/b/'],
		    'reference (20)' => ['../g', 'http://a/b/g'],
		    'reference (21)' => ['../..', 'http://a/'],
		    'reference (22)' => ['../../', 'http://a/'],
		    'reference (23)' => ['../../g', 'http://a/g'],
		    'reference (24)' => ['../../../g', 'http://a/g'],
		    'reference (25)' => ['../../../../g', 'http://a/g'],
		    'reference (26)' => ['/./g', 'http://a/g'],
		    'reference (27)' => ['/../g', 'http://a/g'],
		    'reference (28)' => ['g.', 'http://a/b/c/g.'],
		    'reference (29)' => ['.g', 'http://a/b/c/.g'],
		    'reference (30)' => ['g..', 'http://a/b/c/g..'],
		    'reference (31)' => ['..g', 'http://a/b/c/..g'],
		    'reference (32)' => ['./../g', 'http://a/b/g'],
		    'reference (33)' => ['./g/.', 'http://a/b/c/g/'],
		    'reference (34)' => ['g/./h', 'http://a/b/c/g/h'],
		    'reference (35)' => ['g/../h', 'http://a/b/c/h'],
		    'reference (36)' => ['g;x=1/./y', 'http://a/b/c/g;x=1/y'],
		    'reference (37)' => ['g;x=1/../y', 'http://a/b/c/y'],
		    'reference (38)' => ['g?y/./x', 'http://a/b/c/g?y/./x'],
		    'reference (39)' => ['g?y/../x', 'http://a/b/c/g?y/../x'],
		    'reference (40)' => ['g#s/./x', 'http://a/b/c/g#s/./x'],
		    'reference (41)' => ['g#s/../x', 'http://a/b/c/g#s/../x'],
		    'reference (42)' => ['http:g', 'http:g'],
		];
	}
	
	public function relativeUriProvider(): array
	{
		return [
			'absolute (1)' => ['http://scheme.absolute', false],
			'absolute (2)' => ['/path/absolute', false],
			'relative (1)' => ['//scheme.relative', true],
			'relative (2)' => ['path/relative', true],
			'relative (3)' => ['?query=relative', true],
			'relative (4)' => ['#fragment', true],
		];
	}
	
	public function invalidUriProvider(): array
	{
		return [
			'invalid (1)' => ['scheme:'],
			'invalid (2)' => ['scheme://'],
			'invalid (3)' => ['scheme://user@'],
			'invalid scheme (1)' => ['0scheme://host/path?query#fragment'],
			'invalid scheme (2)' => ['://host:80/p?q#f'],
			'invalid port (1)' => ['//host:port/path?query#fragment'],
			'invalid port (2)' => ['//host:-892358/path?query#fragment'],
			'invalid ipv6 host (1)' => ['scheme://]::1[/path?query#fragment'],
			'invalid ipv6 host (2)' => ['scheme://[::1|/path?query#fragment'],
			'invalid ipv6 host (3)' => ['scheme://|::1]/path?query#fragment'],
			'invalid ipv6 host (4)' => ['scheme://[::1]./path?query#fragment'],
			'invalid ipv6 host (5)' => ['scheme://[[::1]]:80/path?query#fragment'],
			'invalid ipv6 scoped (1)' => ['scheme://[::1%25%23]/path?query#fragment'],
			'invalid ipv6 scoped (2)' => ['scheme://[fe80::1234::%251]/path?query#fragment'],
			'invalid path only URI' => ['2620:0:1cfe:face:b00c::3'],
			'invalid path PHP bug #72811' => ['[::1]:80'],
		];
	}
	
	/**
	 * @dataProvider validUriProvider
	 * @param string $uri
	 * @param array $data
	 */
	public function testValidUri(string $uri, array $data)
	{
		$uri = new Uri($uri);
		$this->assertInstanceOf(Uri::class, $uri);
		$this->assertEquals($uri->scheme(), $data['scheme']);
		$this->assertEquals($uri->userinfo(), $data['userinfo']);
		$this->assertEquals($uri->host(), $data['host']);
		$this->assertEquals($uri->port(), $data['port']);
		$this->assertEquals($uri->path(), $data['path']);
		$this->assertEquals($uri->querystr(), $data['query']);
		$this->assertEquals($uri->fragment(), $data['fragment']);
		$this->assertEquals((string)$uri, $uri);
	}
	
	/**
	 * @dataProvider referenceUriProvider
	 * @param string $ref
	 * @param string $expected
	 */
	public function testReference(string $ref, string $expected)
	{
		$u = new Uri('http://a/b/c/d;p?q');
		$this->assertEquals($expected, $u->reference($ref));
	}
	
	/**
	 * @dataProvider relativeUriProvider
	 * @param string $uri
	 * @param bool $relative
	 */
	public function testRelative(string $uri, bool $relative)
	{
		$u = new Uri($uri);
		$this->assertSame($relative, $u->relative());
	}
	
	/**
	 * @dataProvider invalidUriProvider
	 * @param string $uri
	 */
	public function testParseFailed(string $uri)
	{
		$this->expectException(UriException::class);
		new Uri($uri);
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
}

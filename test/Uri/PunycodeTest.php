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
use Zettacast\Uri\Punycode;
use PHPUnit\Framework\TestCase;

final class PunycodeTest extends TestCase
{
	public function punyEncodeProvider(): array
	{
		return [
			'basic' => ['Bach', 'Bach-'],
			'non-ascii (1)' => ['ü', 'tda'],
			'non-ascii (2)' => ['üëäö♥', '4can8av2009b'],
			'mixed ascii (1)' => ['bücher', 'bcher-kva'],
			'mixed ascii (2)' => [
				'Willst du die Blüthe des frühen, die Früchte des späteren Jahres',
				'Willst du die Blthe des frhen, die Frchte des spteren Jahres-x9e96lkal'
			],
			'arabic' => ['ليهمابتكلموشعربي؟', 'egbpdaj6bu4bxfgehfvwxn'],
			'chinese (1)' => ['他们为什么不说中文', 'ihqwcrb4cv8a8dqg056pqjye'],
			'chinese (2)' => ['他們爲什麽不說中文', 'ihqwctvzc91f659drss3x8bo0yb'],
			'czech' => ['Pročprostěnemluvíčesky', 'Proprostnemluvesky-uyb24dma41a'],
			'hebrew' => ['למההםפשוטלאמדבריםעברית', '4dbcagdahymbxekheh6e0a7fei0b'],
			'hindi' => ['यहलोगहिन्दीक्योंनहींबोलसकतेहैं', 'i1baa7eci9glrd9b2ae1bj0hfcgg6iyaf8o0a1dig0cd'],
			'japanese' => ['なぜみんな日本語を話してくれないのか', 'n8jok5ay5dzabd5bym9f0cm5685rrjetr6pdxa'],
			'korean' => [
				'세계의모든사람들이한국어를이해한다면얼마나좋을까',
				'989aomsvi5e83db1d2a355cv1e0vak1dwrv93d5xbh15a0dt30a5jpsd879ccm6fea98c'
			],
			'cyrillic' => ['почемужеонинеговорятпорусски', 'b1abfaaepdrnnbgefbadotcwatmq2g4l'],
			'spanish' => ['PorquénopuedensimplementehablarenEspañol', 'PorqunopuedensimplementehablarenEspaol-fmd56a'],
			'vietnamese' => ['TạisaohọkhôngthểchỉnóitiếngViệt', 'TisaohkhngthchnitingVit-kjcr8268qyxafd2f1b9g'],
			'random (1)' => ['3年B組金八先生', '3B-ww4c5e180e575a65lsy2b'],
			'random (2)' => ['安室奈美恵-with-SUPER-MONKEYS', '-with-SUPER-MONKEYS-pc58ag80a8qai00g7n9n'],
			'random (3)' => ['Hello-Another-Way-それぞれの場所', 'Hello-Another-Way--fc4qua05auwb3674vfr0b'],
			'random (4)' => ['ひとつ屋根の下2', '2-u9tlzr9756bt3uc0v'],
			'random (5)' => ['MajiでKoiする5秒前', 'MajiKoi5-783gue6qz075azm5e'],
			'random (6)' => ['パフィーdeルンバ', 'de-jg4avhby1noc0d'],
			'random (7)' => ['そのスピードで', 'd9juau41awczczp'],
		];
	}
	
	public function domainEncodeProvider(): array
	{
		return [
			'domain (0)' => ['mañana.com', 'xn--maana-pta.com'],
			'domain (1)' => ['example.com.', 'example.com.'],
			'domain (2)' => ['bücher.com', 'xn--bcher-kva.com'],
			'domain (3)' => ['café.com', 'xn--caf-dma.com'],
			'domain (4)' => ['☃-⌘.com', 'xn----dqo34k.com'],
			'domain (5)' => ['퐀☃-⌘.com', 'xn----dqo34kn65z.com'],
			'domain (6)' => ['💩.la', 'xn--ls8h.la'],
			'domain (7)' => ['джpумлатест.bрфa', 'xn--p-8sbkgc5ag7bhce.xn--ba-lmcq'],
			'domain (8)' => ['見.香港', 'xn--nw2a.xn--j6w193g']
		];
	}
	
	/**
	 * @dataProvider punyEncodeProvider
	 * @param string $unicode
	 * @param string $ascii
	 */
	public function testPunycodeEncode(string $unicode, string $ascii)
	{
		$this->assertSame($ascii, Punycode::encode($unicode));
	}
	
	/**
	 * @dataProvider punyEncodeProvider
	 * @param string $unicode
	 * @param string $ascii
	 */
	public function testPunycodeDecode(string $unicode, string $ascii)
	{
		$this->assertSame($unicode, Punycode::decode($ascii));
	}
	
	/**
	 * @dataProvider domainEncodeProvider
	 * @param string $unicode
	 * @param string $ascii
	 */
	public function testDomainEncode(string $unicode, string $ascii)
	{
		$u = new Uri('//'.$unicode);
		$this->assertSame($ascii, $u->punyencode()->host());
	}
	
	/**
	 * @dataProvider domainEncodeProvider
	 * @param string $unicode
	 * @param string $ascii
	 */
	public function testDomainDecode(string $unicode, string $ascii)
	{
		$u = new Uri('//'.$ascii);
		$this->assertSame($unicode, $u->punydecode()->host());
	}
	
}

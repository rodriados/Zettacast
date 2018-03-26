<?php
/**
 * Zettacast\Test\Autoload test file.
 * @package Zettacast
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @license MIT License
 * @copyright 2015-2018 Rodrigo Siqueira
 */
namespace Zettacast\Test\Autoload;

use Zettacast\Facade\Autoload;
use Zettacast\Autoload\Loader\ObjectLoader;
use Zettacast\Autoload\Loader\NamespaceLoader;
use Zettacast\Autoload\Autoload as Autoloader;
use PHPUnit\Framework\TestCase;

final class AutoloadTest extends TestCase
{
	public function testCanUseAlias()
	{
		Autoload::alias([
			'Col' => \Zettacast\Collection\Collection::class,
			'Stk' => \Zettacast\Collection\Stack::class,
			'Del' => \Zettacast\Collection\Collection::class,
		]);
		
		$col = new \Col;
		$stk = new \Stk;
		
		$this->assertInstanceOf(\Zettacast\Collection\Collection::class, $col);
		$this->assertInstanceOf(\Zettacast\Collection\Stack::class, $stk);
		
		Autoload::unalias('Del');
		
		$this->expectException(\Error::class);
		new \Del;
	}
	
	public function testCanUseObjectLoader()
	{
		Autoload::register('object', [
			__NAMESPACE__.'\\CarelessObject' => __DIR__.'/assets/CarelessObject.php',
			__NAMESPACE__.'\\CodelessObject' => __DIR__.'/assets/CodelessObject.php',
			__NAMESPACE__.'\\HopelessObject' => __DIR__.'/assets/HopelessObject.php',
			__NAMESPACE__.'\\UselessObject'  => __DIR__.'/assets/UselessObject.php',
		]);
		
		$useless = new UselessObject;
		$careless = new CarelessObject;
		$codeless = new CodelessObject;
		
		$this->assertInstanceOf(UselessObject::class, $useless);
		$this->assertInstanceOf(CarelessObject::class, $careless);
		$this->assertInstanceOf(CodelessObject::class, $codeless);
		
		Autoload::get('object')->del('HopelessObject');
		
		$this->expectException(\Error::class);
		new HopelessObject;
	}
	
	public function testCanUseNamespaceLoader()
	{
		Autoload::register('namespace', [
			'EmptyNamespace' => __DIR__.'/assets',
			'VoidNamespace'  => __DIR__.'/assets',
		]);
		
		$n1 = new \EmptyNamespace\Empty1;
		$n2 = new \EmptyNamespace\Empty2;
		
		$this->assertInstanceOf(\EmptyNamespace\Empty1::class, $n1);
		$this->assertInstanceOf(\EmptyNamespace\Empty2::class, $n2);
		
		Autoload::get('namespace')->del('VoidNamespace');
		
		$this->expectException(\Error::class);
		new \VoidNamespace\Void1;
	}
	
	public function testCanUseAllLoaderTogether()
	{
		$autoload = new Autoloader;
		$obj = new ObjectLoader;
		$space = new NamespaceLoader;
		
		$autoload->register('object', $obj);
		$autoload->register('namespace', $space);
		$this->assertTrue(
			$autoload->has('object') &&
			$autoload->has('namespace')
		);
		
		$this->assertInstanceOf(ObjectLoader::class, $autoload->get('object'));
		$this->assertInstanceOf(NamespaceLoader::class, $autoload->get('namespace'));
		
		$autoload->unregister('namespace');
		$this->assertFalse($autoload->has('namespace'));
		$autoload->register('namespace', $space);
		
		$obj->set('CarelessObject', __DIR__.'/assets/CarelessObject.php');
		$obj->set('CodelessObject', __DIR__.'/assets/CodelessObject.php');
		$obj->set('HopelessObject', __DIR__.'/assets/HopelessObject.php');
		$obj->set('UselessObject',  __DIR__.'/assets/UselessObject.php');
		
		$space->set('EmptyNamespace', __DIR__.'/assets');
		$space->set('VoidNamespace', __DIR__.'/assets');
		
		$autoload->alias('N1', \EmptyNamespace\Empty1::class);
		$autoload->alias('N2', \EmptyNamespace\Empty2::class);
		$autoload->alias('Useless', UselessObject::class);
		$autoload->alias('Codeless', CodelessObject::class);
		
		$this->assertInstanceOf(UselessObject::class, new \Useless);
		$this->assertInstanceOf(CodelessObject::class, new \Codeless);
		$this->assertInstanceOf(\EmptyNamespace\Empty1::class, new \N1);
		$this->assertInstanceOf(\EmptyNamespace\Empty2::class, new \N2);
	}
}

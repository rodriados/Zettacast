<?php

use Zettacast\Facade\Autoload;
use Zettacast\Autoload\Loader\AliasLoader;
use Zettacast\Autoload\Loader\ObjectLoader;
use Zettacast\Autoload\Loader\NamespaceLoader;
use Zettacast\Autoload\Autoload as Autoloader;

final class AutoloadTest extends \PHPUnit\Framework\TestCase
{
	public function testCanUseAlias()
	{
		Autoload::addAlias([
			'Col' => \Zettacast\Collection\Collection::class,
			'Stk' => \Zettacast\Collection\Stack::class,
			'Del' => \Zettacast\Collection\Collection::class,
		]);
		
		$col = new Col;
		$stk = new Stk;
		
		$this->assertInstanceOf(\Zettacast\Collection\Collection::class, $col);
		$this->assertInstanceOf(\Zettacast\Collection\Stack::class, $stk);
		
		Autoload::delAlias('Del');
		
		$this->expectException(Error::class);
		new Del;
	}
	
	public function testCanUseObjectLoader()
	{
		Autoload::addClass([
			'UselessObject' => __DIR__.'/assets/class1.php',
			'CarelessObject' => __DIR__.'/assets/class2.php',
			'CodelessObject' => __DIR__.'/assets/class3.php',
			'HopelessObject' => __DIR__.'/assets/class4.php',
		]);
		
		$useless = new UselessObject;
		$careless = new CarelessObject;
		$codeless = new CodelessObject;
		
		$this->assertInstanceOf(UselessObject::class, $useless);
		$this->assertInstanceOf(CarelessObject::class, $careless);
		$this->assertInstanceOf(CodelessObject::class, $codeless);
		
		Autoload::delClass('HopelessObject');
		
		$this->expectException(Error::class);
		new HopelessObject;
	}
	
	public function testCanUseNamespaceLoader()
	{
		Autoload::addNamespace([
			'EmptyNamespace' => __DIR__.'/assets',
			'VoidNamespace' => __DIR__.'/assets',
		]);
		
		$n1 = new EmptyNamespace\namespace1;
		$n2 = new EmptyNamespace\namespace2;
		
		$this->assertInstanceOf(EmptyNamespace\namespace1::class, $n1);
		$this->assertInstanceOf(EmptyNamespace\namespace2::class, $n2);
		
		Autoload::delNamespace('VoidNamespace');
		
		$this->expectException(Error::class);
		new VoidNamespace\namespace3;
	}
	
	
	public function testCanUseAllLoaderTogether()
	{
		$autoload = new Autoloader;
		$alias = new AliasLoader;
		$obj = new ObjectLoader;
		$space = new NamespaceLoader;
		
		$autoload->register($alias);
		$autoload->register($obj);
		$autoload->register($space);
		$this->assertTrue(
			$autoload->isRegistered($alias) &&
			$autoload->isRegistered($obj) &&
			$autoload->isRegistered($space)
		);
		
		$autoload->unregister($space);
		$this->assertFalse($autoload->isRegistered($space));
		$autoload->register($space);
		
		$obj->set('UselessObject', __DIR__.'/assets/class1.php')
			->set('CarelessObject', __DIR__.'/assets/class2.php')
			->set('CodelessObject', __DIR__.'/assets/class3.php')
			->set('HopelessObject', __DIR__.'/assets/class4.php');
		
		$space->set('EmptyNamespace', __DIR__.'/assets')
			->set('VoidNamespace', __DIR__.'/assets');
		
		$alias->set('N1', EmptyNamespace\namespace1::class)
			->set('N2', EmptyNamespace\namespace2::class)
			->set('Useless', UselessObject::class)
			->set('Codeless', CodelessObject::class);
		
		$this->assertInstanceOf(UselessObject::class, new Useless);
		$this->assertInstanceOf(CodelessObject::class, new Codeless);
		$this->assertInstanceOf(\EmptyNamespace\namespace1::class, new N1);
		$this->assertInstanceOf(\EmptyNamespace\namespace2::class, new N2);
	}

}

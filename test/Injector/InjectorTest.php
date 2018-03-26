<?php
/**
 * Zettacast\Test\Injector test file.
 * @package Zettacast
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @license MIT License
 * @copyright 2015-2018 Rodrigo Siqueira
 */
namespace Zettacast\Test\Injector;

use Zettacast\Injector\Injector;
use Zettacast\Injector\InjectorException;
use PHPUnit\Framework\TestCase;

require __DIR__.'/assets/injector.php';

final class InjectorTest extends TestCase
{
	public function testCanInstantiate()
	{
		$injector = new Injector;
		$this->assertInstanceOf(Injector::class, $injector);
		$this->assertInstanceOf(Injector::class, zetta());
	}
	
	public function testCannotInstantiate()
	{
		$this->expectException(InjectorException::class);
		zetta(DInterface::class);
	}
	
	public function testCanBind()
	{
		zetta()->bind(AInterface::class, A::class);
		zetta()->bind(BInterface::class, B::class);
		zetta()->bind(CInterface::class, C::class);
		zetta()->bind(DInterface::class, D::class);
		zetta()->bind('testA', AInterface::class);
		zetta()->bind('testD', DInterface::class);
		$this->assertInstanceOf(A::class, $a = zetta()->make('testA'));
		$this->assertInstanceOf(C::class, zetta()->make(CInterface::class));
		
		zetta()->set(AInterface::class, $a);
		$this->assertEquals(zetta()->make('testA'), $a);
		
		zetta()->when(D::class)->bind(BInterface::class, C::class);
		$this->assertInstanceOf(D::class, $d = zetta()->make('testD'));
		$this->assertInstanceOf(C::class, $d->b);
		
		zetta()->when(D::class)->unbind(BInterface::class);
		$this->assertInstanceOf(D::class, $d = zetta()->make('testD'));
		$this->assertInstanceOf(B::class, $d->b);
		$this->assertTrue(zetta()->knows('testA'));
		
		zetta()->del(AInterface::class);
		$this->assertFalse(zetta()->has(AInterface::class));
		
		zetta()->drop('testD');
		$this->expectException(InjectorException::class);
		zetta('testD');
	}
	
	public function testCanBindClosure()
	{
		zetta()->bind(AInterface::class, function() {
			return new A;
		});
		
		zetta()->bind(BInterface::class, function() {
			return zetta(B::class);
		});
		
		zetta()->bind(CInterface::class, C::class);
		zetta()->bind(DInterface::class, D::class);
		
		$this->assertInstanceOf(A::class, zetta(AInterface::class));
		$this->assertInstanceOf(B::class, zetta(BInterface::class));
	}
	
	public function testCanWrap()
	{
		zetta()->bind(AInterface::class, A::class);
		zetta()->bind(BInterface::class, B::class);
		zetta()->bind(CInterface::class, C::class);
		zetta()->bind(DInterface::class, D::class);
		
		$d = zetta()->factory(DInterface::class);
		$this->assertInstanceOf(D::class, $d());
		
		$f = zetta()->wrap(__NAMESPACE__.'\\f');
		$this->assertInstanceOf(D::class, $f());
		$this->assertInstanceOf(D::class, zetta()->call(__NAMESPACE__.'\\f'));
		
		$f = zetta()->wrap([D::class, 'staticF']);
		$r = $f([1089]);
		$this->assertInstanceOf(A::class, $r[0]);
		$this->assertEquals($r[1], 1089);
		
		$this->expectException(InjectorException::class);
		$f();
		
	}
	
	public function testCanWrapInstanceMethod()
	{
		zetta()->bind(AInterface::class, A::class);
		zetta()->bind(BInterface::class, B::class);
		zetta()->bind(CInterface::class, C::class);
		zetta()->bind(DInterface::class, D::class);
		
		$f = zetta()->wrap([zetta()->make(D::class), 'instanceF'], [57.48]);
		$r = $f();
		$this->assertInstanceOf(A::class, $r[0]);
		$this->assertEquals($r[1], 57.48);
		$this->assertEquals($f([100.18])[1], 100.18);
	}
	
	public function testCanShareBoundInstance()
	{
		zetta()->bind(AInterface::class, A::class, true);
		zetta()->bind(BInterface::class, B::class, true);
		zetta()->bind(CInterface::class, C::class, true);
		zetta()->bind(DInterface::class, D::class, true);
		$i1 = zetta(DInterface::class);
		$i2 = zetta(DInterface::class);
		
		$this->assertEquals(spl_object_hash($i1), spl_object_hash($i2));
	}
	
	public function testExceptionUninstantiable()
	{
		$this->expectException(InjectorException::class);
		zetta()->make(EInterface::class);
	}
}

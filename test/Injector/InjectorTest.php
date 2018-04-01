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
		$i = new Injector;
		$this->assertInstanceOf(Injector::class, $i);
		$this->assertInstanceOf(Injector::class, zetta());
		$this->assertInstanceOf(Injector::class, zetta('injector'));
	}
	
	public function testCannotInstantiate()
	{
		$i = new Injector;

		$this->expectException(InjectorException::class);
		$i->make(DInterface::class);
	}
	
	public function testCanBind()
	{
		$i = new Injector;
		$i->bind(AInterface::class, A::class);
		$i->bind(BInterface::class, B::class);
		$i->bind(CInterface::class, C::class);
		$i->bind(DInterface::class, D::class);
		$i->bind('testA', AInterface::class);
		$i->bind('testD', DInterface::class);
		$this->assertInstanceOf(A::class, $a = $i->make('testA'));
		$this->assertInstanceOf(C::class, $i->make(CInterface::class));
		
		$i->set(AInterface::class, $a);
		$this->assertEquals($i->make('testA'), $a);
		
		$i->when(D::class)->bind(BInterface::class, C::class);
		$this->assertInstanceOf(D::class, $d = $i->make('testD'));
		$this->assertInstanceOf(C::class, $d->b);
		
		$i->when(D::class)->unbind(BInterface::class);
		$this->assertInstanceOf(D::class, $d = $i->make('testD'));
		$this->assertInstanceOf(B::class, $d->b);
		$this->assertTrue($i->knows('testA'));
		
		$i->del(AInterface::class);
		$this->assertFalse($i->has(AInterface::class));
		
		$i->drop('testD');
		
		$this->expectException(InjectorException::class);
		$i->make('testD');
	}
	
	public function testCanBindClosure()
	{
		$i = new Injector;
		
		$i->bind(AInterface::class, function() {
			return new A;
		});
		
		$i->bind(BInterface::class, function() use($i) {
			return $i->make(B::class);
		});
		
		$i->bind(CInterface::class, C::class);
		$i->bind(DInterface::class, D::class);
		
		$this->assertInstanceOf(A::class, $i->make(AInterface::class));
		$this->assertInstanceOf(B::class, $i->make(BInterface::class));
	}
	
	public function testCanWrap()
	{
		$i = new Injector;
		
		$i->bind(AInterface::class, A::class);
		$i->bind(BInterface::class, B::class);
		$i->bind(CInterface::class, C::class);
		$i->bind(DInterface::class, D::class);
		
		$d = $i->factory(DInterface::class);
		$this->assertInstanceOf(D::class, $d());
		
		$f = $i->wrap(__NAMESPACE__.'\\f');
		$this->assertInstanceOf(D::class, $f());
		$this->assertInstanceOf(D::class, $i->call(__NAMESPACE__.'\\f'));
		
		$f = $i->wrap([D::class, 'staticF']);
		$r = $f([1089]);
		$this->assertInstanceOf(A::class, $r[0]);
		$this->assertEquals($r[1], 1089);
		
		$this->expectException(InjectorException::class);
		$f();
		
	}
	
	public function testCanWrapInstanceMethod()
	{
		$i = new Injector;
		
		$i->bind(AInterface::class, A::class);
		$i->bind(BInterface::class, B::class);
		$i->bind(CInterface::class, C::class);
		$i->bind(DInterface::class, D::class);
		
		$f = $i->wrap([$i->make(D::class), 'instanceF'], [57.48]);
		$r = $f();
		$this->assertInstanceOf(A::class, $r[0]);
		$this->assertEquals($r[1], 57.48);
		$this->assertEquals($f([100.18])[1], 100.18);
	}
	
	public function testCanShareBoundInstance()
	{
		$i = new Injector;
		
		$i->bind(AInterface::class, A::class, true);
		$i->bind(BInterface::class, B::class, true);
		$i->bind(CInterface::class, C::class, true);
		$i->bind(DInterface::class, D::class, true);
		$i1 = $i->make(DInterface::class);
		$i2 = $i->make(DInterface::class);
		
		$this->assertEquals(spl_object_hash($i1), spl_object_hash($i2));
	}
	
	public function testExceptionUninstantiable()
	{
		$this->expectException(InjectorException::class);
		zetta()->make(EInterface::class);
	}
}

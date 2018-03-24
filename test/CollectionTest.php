<?php

use Zettacast\Collection\Stack;
use Zettacast\Collection\Queue;
use Zettacast\Collection\Sequence;
use Zettacast\Collection\Collection;
use Zettacast\Collection\DotCollection;
use Zettacast\Collection\RecursiveCollection;

final class CollectionTest extends \PHPUnit\Framework\TestCase
{
	public function testCanInstantiate()
	{
		$c1 = new Collection;
		$c2 = new Collection(['a' => 1, 'b' => 2]);
		
		$this->assertInstanceOf(Collection::class, $c1);
		$this->assertInstanceOf(Collection::class, $c2);
		$this->assertEquals($c2->raw(), ['a' => 1, 'b' => 2]);
		$this->assertEquals($c2['b'], 2);
		$this->assertTrue(isset($c2['a']));
		$this->assertFalse(isset($c2['c']));
		$this->assertTrue($c1->empty());
		
		$this->assertEquals(1, $c2->rewind());
		$this->assertEquals(1, $c2->current());
		$this->assertEquals(2, $c2->next());
		$this->assertEquals(1, $c2->prev());
		$this->assertEquals('a', $c2->key());
		$this->assertEquals([new Collection(['a','b']),new Collection([1,2])], $c2->divide());
		$this->assertTrue($c2->every());
		$this->assertFalse($c2->every(function($v) {return $v == 1;}));
		$this->assertEquals(new Collection(['b' => 2]), $c2->except('a'));
		$this->assertEquals(new Collection(['b' => 2]), $c2->only('b'));
		$this->assertEquals(new Collection(['a'=>2,'b'=>4]), $c2->map(function($v){return $v * 2;}));
		$this->assertEquals(3, $c2->reduce(function($c, $v) {return $c + $v;}));
		$this->assertEquals('b', $c2->search(2));
		
		$this->assertEquals(['a' => 1, 'b' => 2], $c2->clear());
		$this->assertTrue($c2->empty());
	}
	
	public function testCollection()
	{
		$col = new Collection([.5,1,1.5,2,2.5,3,3.5,4,4.5,5]);
		$this->assertEquals($col[0], .5);
		$this->assertEquals($col->get(0), .5);
		$this->assertTrue($col->has(8));
		$this->assertEquals($col->raw(), with(clone $col)->raw());
		$this->assertEquals($col->count(), 10);
		$this->assertEquals($col->apply(function($value): int {
			return (int)($value * 2);
		})->raw(), [1,2,3,4,5,6,7,8,9,10]);
		
		$col->add([10 => 11, 11 => 12]);
		$chunk = $col->chunk(3);
		$this->assertEquals([], $col->chunk(0));
		$this->assertEquals([9=>10,10=>11,11=>12], $chunk[3]->raw());
	}
	
	public function testRecursiveCollection()
	{
		$rec = new RecursiveCollection([[1,2,3],[[4,5,6]],[7,8,9]]);
		$this->assertInstanceOf(RecursiveCollection::class, $rec);
		$this->assertFalse($rec->empty());
		
		$this->assertEquals($rec->collapse()->raw(), [1,2,3,[4,5,6],7,8,9]);
		$this->assertEquals($rec->flatten()->raw(), [1,2,3,4,5,6,7,8,9]);
		$this->assertEquals($rec->get(0)->get(2), 3);
		$this->assertEquals($rec->get(7, "default"), "default");
		
		$rec->apply(function($v){return $v * 2;});
		$this->assertEquals([2,4,6], $rec->get(0)->raw());
	}
	
	public function testDotCollection()
	{
		$dot = new DotCollection(['user' => [
			'pass' => 123, 'email' => '@', 'date' => time(), 'del' => true
		]]);
		
		$this->assertInstanceOf(DotCollection::class, $dot);
		$this->assertEquals($dot['user.pass'], 123);
		$this->assertEquals($dot['user.email'], '@');
		$this->assertTrue(isset($dot['user.date']));
		
		unset($dot['user.del']);
		$this->assertFalse(isset($dot['user.del']));
		
		$dot['user.data.value'] = 23498;
		$this->assertEquals($dot['user.data.value'], 23498);
		
	}
	
	public function testDotCollectionPlucking()
	{
		$dot = new DotCollection([
			['name' => 'Catarina', 'year' => 2004],
			['name' => 'Katrina', 'year' => 2006],
			['name' => 'Irma', 'year' => 2017],
        ]);
		
		$plucked = $dot->pluck('name');
		$this->assertEquals($plucked->raw(), ['Catarina', 'Katrina', 'Irma']);
		
		$plucked = $dot->pluck('name', 'year');
		$this->assertEquals($plucked->raw(), [2004 => 'Catarina', 2006 => 'Katrina', 2017 => 'Irma']);
		
		$filtered = $dot->filter(function($value): bool {
			return is_string($value) && strpos($value, 't') !== false || is_array($value);
		});
		$this->assertEquals($filtered[0]->raw(), ['name' => 'Catarina']);
	}
	
	public function testQueue()
	{
		$queue = new Queue;
		$this->assertInstanceOf(Queue::class, $queue);
		$this->assertTrue($queue->empty());
		
		$queue->push(0);
		$queue->push(1);
		$queue->push(2);
		$queue->push(3);
		$queue->push(4);
		$queue->push(5);
		
		$this->assertFalse($queue->empty());
		$this->assertEquals($queue->peek(), 0);
		$this->assertEquals($queue->pop(), 0);
		$this->assertEquals($queue->peek(), 1);
		$this->assertEquals($queue->count(), 5);
		
		foreach($queue as $key => $value)
			$this->assertEquals($key + 1, $value);
		
		$this->assertEquals($queue->clear(), [1,2,3,4,5]);
		$this->assertTrue($queue->empty());
	}
	
	public function testStack()
	{
		$queue = new Stack;
		$this->assertInstanceOf(Stack::class, $queue);
		$this->assertTrue($queue->empty());
		
		$queue->push(5);
		$queue->push(4);
		$queue->push(3);
		$queue->push(2);
		$queue->push(1);
		$queue->push(0);
		
		$this->assertFalse($queue->empty());
		$this->assertEquals($queue->peek(), 0);
		$this->assertEquals($queue->pop(), 0);
		$this->assertEquals($queue->peek(), 1);
		$this->assertEquals($queue->count(), 5);
		
		$this->assertEquals($queue->clear(), [5,4,3,2,1]);
		$this->assertTrue($queue->empty());
	}
	
	public function testSequence()
	{
		$seq = new Sequence([0,1,2,3,4,5,6]);
		$this->assertInstanceOf(Sequence::class, $seq);
		$this->assertFalse($seq->empty());
		
		$this->assertEquals($seq->pop(), 6);
		$this->assertEquals($seq->count(), 6);
		$seq->push(6);
		
		$seq->apply(function($value): int {
			return 2 * $value;
		});
		
		$this->assertEquals($seq->raw(), [0,2,4,6,8,10,12]);
		$this->assertEquals($seq->first(), 0);
		$this->assertEquals($seq->last(), 12);
		
		$seq[6] = 7;
		$this->assertTrue(isset($seq[6]));
		$this->assertEquals($seq[6], 7);
		unset($seq[6]);
	}
}

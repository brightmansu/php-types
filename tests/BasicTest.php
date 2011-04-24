<?php
require_once 'PHPUnit/Autoload.php';
require_once 'Types/__autoload.php';

class phptypesBasicTest extends PHPUnit_Framework_TestCase {
	function testArray() {
		$a = &a(1,2,3);
		$this->assertArrayHasKey(2, $a->toArrayNative());
		$this->assertInstanceOf('Types\\ArrayObject', $a);

		$a->append( array(4,5) );
		$this->assertArrayHasKey(4, $a->toArrayNative());

		$a->push(6);
		$this->assertArrayHasKey(5, $a->toArrayNative());

		$this->assertArrayHasKey(5, $a->toArrayNative());;
	}

	public function testHash()
	{
		$aa = &aa(
			'foo1', 'bar1',
			'foo2', 'bar2',
			'foo3', 'bar3'
		);

		$this->assertEquals($aa->toArrayNative(), $this->getTestArray());
	}

	function testMerge() {
		$aa = &ao($this->getTestArray());
		$aa = $aa->merge(aa('foo4', 'bar13'));
		$this->assertEquals($aa->toArrayNative(), array(
			'foo1' => 'bar1',
			'foo2' => 'bar2',
			'foo3' => 'bar3',
			'foo4' => 'bar13'
		));
	}

	function testFilter() {
		$test = $this->getTestArray();
		$test['foo4'] = 'bar13';
		$aa = &ao($test);

		// filter
		$aa = $aa->filter(function($v)
			{
				return string($v)->matches('/([3-9]|\d\d)$/');
			});
		
		$this->assertEquals($aa->toArrayNative(), array(
			0 => 'bar3',
			1 => 'bar13'
		));
	}

	function testMap() {
		$aa = &ao($this->getTestArray());

		// map
		$aa = $aa->map(function($v, $k)
			{
				return $k . $v;
			});

		$this->assertEquals($aa->toArrayNative(), array(
			'foo1' => 'foo1bar1',
			'foo2' => 'foo2bar2',
			'foo3' => 'foo3bar3'
		));
	}

	function testArrayAccess() {
		$aa = &ao($this->getTestArray());

		$aa['foo4'] = 'bar4';
		$aa['foo2'] = 'new';

		$this->assertEquals($aa->toArrayNative(), array(
			'foo1' => 'bar1',
			'foo2' => 'new',
			'foo3' => 'bar3',
			'foo4' => 'bar4'
		));
	}

	function testIterable() {

		$test = $this->getTestArray();
		$keys = array_keys($test);
		$aa = &ao($test);
		$i = 0;
		foreach($aa as $k => $v) {
			$this->assertEquals($keys[$i], $k);
			$this->assertEquals($test[$keys[$i]], $v);
			$i++;
		}
	}

	protected function getTestArray() {
		return array(
			'foo1' => 'bar1',
			'foo2' => 'bar2',
			'foo3' => 'bar3'
		);
	}
}

//$t = new phptypesBasicTest();
//$t->testArray();
//$t->testAssociativeArray();
//$t->testAaMap();
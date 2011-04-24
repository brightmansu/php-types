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

		$this->assertArrayHasKey(5, $a->toArrayNative());

		$aa = &aa(
			'foo1', 'bar1',
			'foo2', 'bar2',
			'foo3', 'bar3'
		);

		$this->assertEquals($aa->toArrayNative(), $this->getTestArray());

		// map
		$aa = $aa->map(function($v, $k) {
			return $k.$v;
		});

		$this->assertEquals($aa->toArrayNative(), array(
			'foo1' => 'foo1bar1',
			'foo2' => 'foo2bar2',
			'foo3' => 'foo3bar3'
		));

		$aa = &aa($this->getTestArray());
		$aa->append(aa('foo4', 'bar13'));

		// filter
		$aa->filter(function($v){
			return string($v)->matches('/([3-9]|\d\d)$/');
		});

		$this->assertEquals($aa->toArrayNative(), array(
			'foo3' => 'foo3bar3',
			'foo2' => 'foo2bar13'
		));
	}

	function getTestArray() {
		return array(
			'foo1' => 'bar1',
			'foo2' => 'bar2',
			'foo3' => 'bar3'
		);
	}
}

$t = new phptypesBasicTest();
$t->testArray();
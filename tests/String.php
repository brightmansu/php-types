<?php

require_once 'PHPUnit/Autoload.php';
require_once 'Types/__autoload.php';

class phptypesBasicTest extends PHPUnit_Framework_TestCase {
	function testCreation() {
		$s = 'foo bar baz';
		$a = string($s);
		
		$this->assertEquals($s, $a->toPrimitiveType());
		$this->assertEquals($s, (string)$a);
		$this->assertEquals($s, ''.$a);
	}
	function testRegex() {
		$s = 'foo bar baz';
		$a = string($s)->replaceRegex('!\b\war\b!', 'www');

		$s = 'foo www baz';
		$this->assertEquals($s, $a.'');
	}
	function testBasics() {
		$s = 'foo BAR baz';
		$a = string($s);

		$this->assertTrue($a->startsWith('foo'));
		$this->assertFalse($a->startsWith('bar'));
		
		$this->assertEquals(
			'foo bar baz',
			$a->toLowerCase().''
		);
		$this->assertEquals(
			'FOO BAR BAZ',
			$a->toUpperCase().''
		);
	}
}
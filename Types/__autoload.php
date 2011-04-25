<?php
namespace Types;

function autoload($class) {
	$name = explode('\\', $class);

	if ( $name[0] == 'Types' )
		require srcDir().'/'.implode('/', array_slice($name, 1)) . '.php';
}

function srcDir() {
	static $dir = null;

	if (!$dir)
		$dir = realpath(rtrim(dirname(__FILE__), '/'));

	return $dir;
}

spl_autoload_register('Types\\autoload');

function &wrap($var) {
	if ( is_object($var) )
		return $var;

	switch(gettype($var)) {
		case 'integer':
		case 'float':
		case 'double':
			return integer($var);
			break;
		case 'string':
			return string($var);
			break;
		case 'array':
			$r = new Types\ArrayObject($var);
			return $r;
			break;
	}

	return $var;
}

require 'globals.php';

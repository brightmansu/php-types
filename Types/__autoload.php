<?php
namespace Types;

function autoload($class) {
	$name = explode('\\', $class);
	if ( $name[0] == 'Types' )
		require srcDir().'/'.$name[ count($name)-1 ] . '.php';
}

function srcDir() {
	static $dir = null;

	if (!$dir)
		$dir = realpath(rtrim(dirname(__FILE__), '/'));

	return $dir;
}

spl_autoload_register('Types\\autoload');

require 'globals.php';

<?php


/**
 * Initializes a newly created String object.
 * @param value string
 * @return String created String object
 */

function & string($value = null) {
	$x = & VariablesManager::getNewPointer(new String($value));
	return $x;
}



/**
 * Create new indexed array object.
 *
 * @return ArrayObject
 */
function &a() {
	$args = func_get_args();
	$array = array();
	foreach($args as $v) {
		$array[] = $v;
	}
	$r = new Types\ArrayObject($array);
	return $r;
}
/**
 * Create new associative array object.
 *
 * @return ArrayObject
 */
function &aa() {
	$args = func_get_args();
	$array = array();
	$lastKey = null;
	foreach($args as $v) {
		if ( $lastKey === null ){
			xdebug_break();
			$array[$v] = null;
			$lastKey = $v;
		} else {
			$array[$lastKey] = $v;
			$lastKey = null;
		}
	}
		$r = new Types\ArrayObject($array);
	return $r;
}

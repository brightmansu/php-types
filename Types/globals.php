<?php


/**
 * Initializes a newly created String object.
 * @param value string
 * @return String created String object
 */

function & string($value = null) {
//	$x = & Types\VariablesManager::getNewPointer(new String($value));
	$x = new Types\String($value);
	return $x;
}


/**
* Initializes a newly created Integer object.
* @return Integer created String object
*/
function & integer($value = null) {
//    $x = & VariablesManager::getNewPointer(new Integer($value));
	$r = new Types\Integer($value);
    return $r;
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

function &wrap($var) {
	return Types\wrap($var);
}

function &arrayObject($var) {
	$r = new Types\ArrayObject($var);
	return $r;
}

/**
 * @param  $var
 * @return Types\ArrayObject
 */
function &ao($var) {
	return arrayObject($var);
}
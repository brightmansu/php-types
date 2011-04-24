<?php
/**
 * Created by IntelliJ IDEA.
 * User: bob
 * Date: 4/23/11
 * Time: 1:39 PM
 * To change this template use File | Settings | File Templates.
 */

// TODO
function __autoload($class_name) {
    $name = explode('\\', $class_name);
    require '../src/'.$name[ count($name)-1 ] . '.php';
}

class testInline {
    function test1($string, $characters = null) {

        if ($characters === null) {
            return strcmp($this->data, (string)$string);
        }
        return strncmp($this->data, (string)$string, (int)$characters);
    }
    function test2($string, $characters = null) {

        if ($characters === null) {
            return strcmp($this->data, (string)$string);
        }
        return strncmp($this->data, (string)$string, (int)$characters);
    }
    function test3($string, $characters = null) {

        if ($characters === null) {
            return strcmp($this->data, (string)$string);
        }
        return strncmp($this->data, (string)$string, (int)$characters);
    }
    function test4($string, $characters = null) {

        if ($characters === null) {
            return strcmp($this->data, (string)$string);
        }
        return strncmp($this->data, (string)$string, (int)$characters);
    }
}

class testStaticBackend {
    static function test1($data, $string, $characters = null) {

        if ($characters === null) {
            return strcmp($data, (string)$string);
        }
        return strncmp($data, (string)$string, (int)$characters);
    }
    static function test2($data, $string, $characters = null) {

        if ($characters === null) {
            return strcmp($data, (string)$string);
        }
        return strncmp($data, (string)$string, (int)$characters);
    }
    static function test3($data, $string, $characters = null) {

        if ($characters === null) {
            return strcmp($data, (string)$string);
        }
        return strncmp($data, (string)$string, (int)$characters);
    }
    static function test4($data, $string, $characters = null) {

        if ($characters === null) {
            return strcmp($data, (string)$string);
        }
        return strncmp($data, (string)$string, (int)$characters);
    }
}
class testStatic {
    function test1($string, $characters = null) {
        return testStaticBackend::test1($string, $characters);
    }
    function test2($string, $characters = null) {
        return testStaticBackend::test2($string, $characters);
    }
    function test3($string, $characters = null) {
        return testStaticBackend::test3($string, $characters);
    }
    function test4($string, $characters = null) {
        return testStaticBackend::test4($string, $characters);
    }
}
class testStaticCall {
    function __call($method, $params) {
        return call_user_func_array(
            array('testStaticBackend', $method), $params
        );
    }
}


$start = memory_get_usage(true);
$mem = array();
//var_dump($start);

//for($i = 0; $i < 10000; $i++) {
//    $mem[] = &Type\string($string, $characters = null);
//    $mem[ count($mem)-1 ] = 'foo bar foo';
//}
for($i = 0; $i < 100000; $i++)
    // NO DIFFERENCE!!!
    $mem[] = new testInline();
//    $mem[] = new testInline();
//    $mem[] = new testStaticCall();

$end = memory_get_usage(true);
//var_dump($end);
print number_format($end-$start)."\n";
//print $foo->replaceRegex('!foo$!', 'baz');

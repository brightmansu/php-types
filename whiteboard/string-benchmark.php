<?php
/**
 * Created by IntelliJ IDEA.
 * User: bob
 * Date: 4/23/11
 * Time: 1:39 PM
 * To change this template use File | Settings | File Templates.
 */

require '__autoload.php';
require '../src/String.php';
require '../src/ArrayObject.php';

use Types;

$start = memory_get_usage(true);
$mem = array();
//var_dump($start);

//for($i = 0; $i < 10000; $i++) {
//    $mem[] = &Type\string();
//    $mem[ count($mem)-1 ] = 'foo bar foo';
//}
for($i = 0; $i < 10000; $i++)
    $mem[] = 'foo bar foo';

$end = memory_get_usage(true);
//var_dump($end);
print number_format($end-$start)."\n";
//print $foo->replaceRegex('!foo$!', 'baz');

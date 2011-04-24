<?php

require '__autoload.php';
require '../src/String.php';
require '../src/ArrayObject.php';

//use Type;

$a = a(1,2,3);
$a->append( a(4,5) );
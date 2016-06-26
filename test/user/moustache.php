<?php
require_once __DIR__ . '/../../vendor/autoload.php';

$m = new \Mustache_Engine();
$oContent = new stdClass();
$oContent->a = 1;
$oContent->b = 2;

$oContent1 = new stdClass();
$oContent1->a = 11;
$oContent1->b = 22;
var_dump($m->render('{{# list }} <li> {{.}}</li> {{/ list}} {{# planet}} Hello,  {{a}} {{/ planet}}!',
    array('planet' => [$oContent, $oContent1], 'list' => ['red', 'white']))); // "Hello, World!"
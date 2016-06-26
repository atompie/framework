<?php
require_once __DIR__ . '/../../test/unit/Config.php';

// Add Cookie to response

use AtomPie\Web\Connection\Http as Http;

$o1 = \AtomPie\Web\CookieJar::getInstance();
$o1->add(new \AtomPie\Web\Cookie('1dfgfd', '2'));
$o1->add(new \AtomPie\Web\Cookie('2dfgdsfg', '3'));
$o1->add(new \AtomPie\Web\Cookie('risto', '3', -1, '/'));

// $oRequest = new \Web\Connection\Http\Response();
// $oRequest->addCookie(new \Web\Cookie('a','b'));
// $oRequest->appendCookieJar($o1);

// $oRequest->send();
// exit;
// Add cookie to request;

// $a1 = new \Web\Connection\Http\Request();
// $a1->addCookie(new \Web\Cookie('a','b'));
// var_dump($a1->send('http://www.onet.pl'));
// var_dump($a1->getCookies());
$oRequest = new Http\Request();
$oRequest->appendCookieJar($o1);
$oRequest->load();
$oRequest->setMethod('get');

// var_dump($oRequest);

// $oRes = ($oRequest->send('http://localhost/framework/trunk/___Tests/__Tmp/test.php'));
$oRes = ($oRequest->send('http://www.onet.pl'));
//var_dump($oRes);
// echo $oRes->getContent();
$oRes->send();
$oRes = new Http\Response();
// $oRequest->addCookie(new \Web\Cookie('a','b'));
$oRes->appendCookieJar($oRequest->getCookies());
$oRes->setContent(new Http\Content('content'));
$oRes->send();
// var_dump($oRequest->getCookies());

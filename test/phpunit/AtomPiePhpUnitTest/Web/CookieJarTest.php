<?php
namespace AtomPiePhpUnitTest\Web;

use AtomPie\Web\Cookie;
use AtomPie\Web\CookieJar;

class CookieJarTest extends \PHPUnit_Framework_TestCase
{
    public function testCookieJar()
    {
        CookieJar::destroyInstance();
        $oCookieJar = CookieJar::getInstance();
        $this->assertTrue($oCookieJar->isEmpty());
        $oCookieJar->add(new Cookie('Name1', 'Value1', 100, 'http://url.pl', true, true));
        $oCookieJar->add(new Cookie('Name2', 'Value2'));
        $this->assertTrue($oCookieJar->count() == 2);
        $this->assertTrue($oCookieJar->has('Name1'));
        $this->assertTrue($oCookieJar->has('Name2'));
        $this->assertFalse($oCookieJar->has('Name3'));
        $this->assertFalse($oCookieJar->isEmpty());
        $oCookieJar->remove('Name2');
        $this->assertTrue($oCookieJar->count() == 1);
        CookieJar::destroyInstance();

    }
}

<?php
namespace AtomPiePhpUnitTest\Web;

use AtomPie\Web\Cookie;

class CookieTest extends \PHPUnit_Framework_TestCase
{
    public function testEmptyCookie()
    {
        $oCookie = new Cookie('test');
        $this->assertTrue($oCookie == 'test=; Path=/');
    }

    public function testFullCookie()
    {
        $oCookie = new Cookie('name', 'value', 200, 'path', 'domain.pl', true, true);
        $sOutput = $oCookie->__toString();
        $this->assertTrue(false !== strstr($sOutput, '; Domain=domain.pl; Secure; HttpOnly'));
        $this->assertTrue(false !== strstr($sOutput, 'name=value; Path=path; Expires='));
    }

    public function testNotEmptyCookie()
    {
        $oCookie = new Cookie('test', 'value');
        $this->assertTrue($oCookie == 'test=value; Path=/');
    }

}

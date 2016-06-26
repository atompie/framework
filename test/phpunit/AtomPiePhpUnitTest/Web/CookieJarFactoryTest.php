<?php

namespace AtomPiePhpUnitTest\Web;

use AtomPie\Web\Cookie;
use AtomPie\Web\CookieJarFactory;

class CookieJarFactoryTest extends \PHPUnit_Framework_TestCase
{

    /**
     * Prepares the environment before running a test.
     */
    protected function setUp()
    {
        parent::setUp();
    }

    /**
     * Cleans up the environment after running a test.
     */
    protected function tearDown()
    {
        parent::tearDown();
    }

    /**
     * Constructs the test case.
     */
    public function testCreateCookieJar()
    {

        $sHeader = "Cache-Control:no-store, no-cache, must-revalidate, post-check=0, pre-check=0\n" .
            "Connection:Keep-Alive\n" .
            "Content-Encoding:gzip\n" .
            "Content-Length:1589\n" .
            "Content-Type:text/html; charset=utf-8\n" .
            "Date:Sun, 16 Mar 2014 16:48:15 GMT\n" .
            "Expires:Thu, 19 Nov 1981 08:52:00 GMT\n" .
            "Keep-Alive:timeout=5, max=100\n" .
            "Pragma:no-cache\n" .
            "Server:Apache/2.2.22 (Ubuntu)\n" .
            "Set-Cookie:ZDEDebuggerPresent=php,phtml,php3; path=/\n" .
            "Set-Cookie: session = 12345; path=/; expires=2014/01/01 00:00:00; secure=true\n" .
            "Vary:Accept-Encoding\n" .
            "X-Powered-By:PHP/5.3.27 ZendServer/6.2.0";
        $oCookieJar = CookieJarFactory::create($sHeader);
        /** @var Cookie $oCookieForSession */
        $oCookieForSession = $oCookieJar->get('session');
        $this->assertTrue($oCookieForSession->getExpire() == @strtotime('2014/01/01 00:00:00'));
        $this->assertTrue($oCookieForSession->getValue() == '12345');
        $this->assertTrue($oCookieForSession->getDomainPath() == '/');
        $this->assertTrue($oCookieForSession->getSecure());

        $oFactory = new CookieJarFactory($oCookieJar);
        $this->assertTrue('ZDEDebuggerPresent=php,phtml,php3; session=12345' == $oFactory->getMergedAllCookiesIntoOne());

    }

}

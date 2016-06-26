<?php
namespace AtomPiePhpUnitTest\Web;

use AtomPie\Web\Connection\Http\Content;
use AtomPie\Web\Connection\Http\Header;
use AtomPie\Web\Connection\Http\Response;
use AtomPie\Web\Cookie;
use AtomPie\Web\CookieJar;

class ResponseTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @test
     */
    public function shouldHaveStatus()
    {
        $oResponse = new Response(new Header\Status(406));
        $this->assertTrue($oResponse->getStatus()->is(406));
        $this->assertTrue(false !== strstr($oResponse->getStatus()->__toString(), 'HTTP/1.1 406 Not Acceptable'));

        $oResponse = new Response();
        $this->assertTrue($oResponse->getStatus()->is(Header\Status::OK));
        $oResponse->setStatus(new Header\Status(406));
        $this->assertTrue($oResponse->getStatus()->is(406));
        $this->assertTrue(false !== strstr($oResponse->getStatus(), 'HTTP/1.1 406 Not Acceptable'));
    }

    /**
     * @test
     * @throws \AtomPie\Web\Exception
     */
    public function shouldHaveCookies()
    {
        $oResponse = new Response(new Header\Status(406));
        $oResponse->addCookie(new Cookie('Key', 'Value'));
        $oCookies = $oResponse->getCookies();
        $this->assertTrue($oCookies->get('Key')->getValue() == 'Value');

        $oCookieJar = new CookieJar();
        $oCookieJar->add(new Cookie('Name1', 'Value1', 100, 'http://url.pl', true, true));
        $oCookieJar->add(new Cookie('Name2', 'Value2'));
        $oResponse->appendCookieJar($oCookieJar);

        $oCookies = $oResponse->getCookies();
        $this->assertTrue($oCookies->get('Name1')->getValue() == 'Value1');
        $this->assertTrue($oCookies->get('Name2')->getValue() == 'Value2');

    }

    /**
     * @test
     */
    public function shouldHaveHeaders()
    {
        $oResponse = new Response(new Header\Status(200));
        $oResponse->addHeader('name', 'value');
        $this->assertTrue($oResponse->hasHeader('name'));
        $this->assertTrue($oResponse->getHeader('name')->getValue() == 'value');
        $oResponse->resetHeaders();
        $this->assertFalse($oResponse->hasHeader('name'));
        $this->assertFalse($oResponse->getHeader('name'));
        $oResponse->addHeader('name1', 'value1');
        $oResponse->removeHeader('name1');
        $this->assertFalse($oResponse->hasHeader('name1'));
    }

    /**
     * @test
     */
    public function shouldHaveContent()
    {

        $oResponse = new Response(new Header\Status(200));
        $oResponse->setContent(new Content('abc', new Header\ContentType(Header\ContentType::HTML)));
        $sMessage = $oResponse->getContent()->get();
        $this->assertTrue(false !== strstr($sMessage, 'abc'));
        $this->assertTrue($oResponse->getContent()->getContentType()->isHtml());

        $oResponse = new Response(new Header\Status(200));
        $oResponse->setContent(new Content(json_encode(['a' => 1, 'b' => 2]),
            new Header\ContentType(Header\ContentType::JSON)));
        $this->assertTrue($oResponse->getContent()->getContentType()->isJson());
        $aData = $oResponse->getContent()->decodeAsJson(true);
        $this->assertTrue(isset($aData['a']) && $aData['a'] == 1);
        $this->assertTrue(isset($aData['b']) && $aData['b'] == 2);
        $this->assertTrue(count($aData) == 2);

    }
}

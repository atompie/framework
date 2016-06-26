<?php
namespace AtomPiePhpUnitTest\Web;

use AtomPie\Web\Environment;
use AtomPie\Web\Connection\Http\Header;

class EnvironmentTest extends \PHPUnit_Framework_TestCase
{

    /**
     * Prepares the environment before running a test.
     */
    protected function setUp()
    {
        Environment::destroyInstance();
        parent::setUp();
    }

    /**
     * Cleans up the environment after running a test.
     */
    protected function tearDown()
    {
        Environment::destroyInstance();
        parent::tearDown();
    }

    public function testEnv()
    {
        putenv('test=1');
        $oEnv = Environment::getInstance();
        $this->assertTrue($oEnv->getEnv()->get('test') == 1);
    }

    public function testAutoRequestContentTypeSetting_XML()
    {

        $_SERVER['HTTP_ACCEPT'] = "*/*;q=0.8,application/json;q=0.6,application/xml;q=0.9";
        $oEnv = Environment::getInstance();
        $oRequest = $oEnv->getRequest();
        $oAccept = $oRequest->getHeader(Header::ACCEPT);
        $this->assertTrue($oAccept->getValue() == $_SERVER['HTTP_ACCEPT']);
        $this->assertTrue($oEnv->getResponse()->getContent()->getContentType()->getValue() == Header\ContentType::XML);

    }

    public function testAutoRequestContentTypeSetting_HTML()
    {

        $_SERVER['HTTP_ACCEPT'] = "text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8";
        $oEnv = Environment::getInstance();
        $oRequest = $oEnv->getRequest();
        $oAccept = $oRequest->getHeader(Header::ACCEPT);
        $this->assertTrue($oAccept->getValue() == $_SERVER['HTTP_ACCEPT']);
        $this->assertTrue($oEnv->getResponse()->getContent()->getContentType()->getValue() == Header\ContentType::HTML);

    }

    public function testAutoRequestContentTypeSetting_XHTML()
    {

        $_SERVER['HTTP_ACCEPT'] = "application/xhtml+xml";
        $oEnv = Environment::getInstance();
        $oRequest = $oEnv->getRequest();
        $oAccept = $oRequest->getHeader(Header::ACCEPT);
        $this->assertTrue($oAccept->getValue() == $_SERVER['HTTP_ACCEPT']);
        $this->assertTrue($oEnv->getResponse()->hasHeader(Header::CONTENT_TYPE));
        $this->assertTrue($oEnv->getResponse()->getContent()->getContentType()->getValue() == Header\ContentType::HTML);

    }

    public function testAutoRequestContentTypeSetting_None()
    {

        $_SERVER['HTTP_ACCEPT'] = "*/*;q=0.8";
        $oEnv = Environment::getInstance();
        $oRequest = $oEnv->getRequest();
        $oAccept = $oRequest->getHeader(Header::ACCEPT);
        $this->assertTrue($oAccept->getValue() == $_SERVER['HTTP_ACCEPT']);
        $this->assertTrue($oEnv->getResponse()->getContent()->getContentType()->getValue() == Header\ContentType::HTML);

    }


}

<?php
namespace WorkshopTest;

use AtomPie\System\Error\DefaultErrorHandler;
use AtomPie\Web\Environment;
use AtomPie\Web\Connection\Http\Header\ContentType;

require_once __DIR__ . '/../Config.php';

class ExceptionHandlerTest extends \PHPUnit_Framework_TestCase
{

    /**
     * Prepares the environment before running a test.
     */
    protected function setUp()
    {
        Environment::destroyInstance();
        $_REQUEST = array();
        parent::setUp();
    }

    /**
     * Cleans up the environment runAfter running a test.
     */
    protected function tearDown()
    {
        Environment::destroyInstance();
        $_REQUEST = array();
        parent::tearDown();
    }

    public function testException_Html()
    {

        $oErrorHandler = new DefaultErrorHandler();
        $sHtml = $oErrorHandler->handleException(
            new \Exception('Test exception'),
            new ContentType(ContentType::HTML)
        );

        $this->assertTrue(strstr($sHtml, 'Test exception') !== false);
        $this->assertTrue(strstr($sHtml, 'Line') !== false);
        $this->assertTrue(strstr($sHtml, 'File') !== false);
        $this->assertTrue(strstr($sHtml, 'StackTrace') !== false);
    }

    public function testException_Xml()
    {

        $oErrorHandler = new DefaultErrorHandler();
        $sXml = $oErrorHandler->handleException(
            new \Exception('Test exception'),
            new ContentType(ContentType::XML)
        );

        $oXml = @new \SimpleXMLElement($sXml);

        $this->assertTrue(isset($oXml->ErrorMessage) && $oXml->ErrorMessage == 'Test exception');
        $this->assertTrue(isset($oXml->Line));
        $this->assertTrue(isset($oXml->File));
        $this->assertTrue(isset($oXml->StackTrace));

    }

    public function testException_Json()
    {
        $oErrorHandler = new DefaultErrorHandler();
        $sJson = $oErrorHandler->handleException(
            new \Exception('Test exception'),
            new ContentType(ContentType::JSON)
        );

        $oJson = json_decode($sJson);

        $this->assertTrue(isset($oJson->ErrorMessage) && $oJson->ErrorMessage == 'Test exception');
        $this->assertTrue(isset($oJson->Line));
        $this->assertTrue(isset($oJson->File));
        $this->assertTrue(isset($oJson->StackTrace));
    }
}

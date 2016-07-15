<?php
namespace WorkshopTest;

require_once __DIR__ . '/../Config.php';

use AtomPie\System\Dispatch\ClientAnnotationValidator;
use AtomPie\System\Dispatch\DispatchException;
use AtomPie\AnnotationTag\Client;
use AtomPie\Core\Service\AuthorizeAnnotationService;
use AtomPie\System\Dispatch\DispatchAnnotationFetcher;
use AtomPie\AnnotationTag\Authorize;
use AtomPie\AnnotationTag\Header;
use AtomPie\Web\Connection\Http\Content;
use AtomPie\Web\Connection\Http\Header\ContentType;
use AtomPie\Web\Connection\Http\Request;
use AtomPie\Web\Connection\Http\Request\Method;
use Generi\Exception;

/**
 * @Authorize(ResourceIndex="WWWAnnotated",AuthType="Basic",AuthToken="class:class")
 */
class WWWAnnotated
{
    /**
     * @Header(Server="MyServer1")
     * @Authorize(ResourceIndex="WWWAnnotated.annotated",AuthType="Basic",AuthToken="method:method")
     */
    public function annotated()
    {

    }

    /**
     * @Authorize(ResourceIndex="WWWAnnotated.PrivateAccess",AuthType="Static",AuthToken="private")
     */
    public function privateAccess()
    {

    }

    /**
     * @Authorize(ResourceIndex="WWWAnnotated.PublicAccess",AuthType="Static",AuthToken="public")
     */
    public function publicAccess()
    {

    }

    /**
     * @Authorize(ResourceIndex="WWWAnnotated.AccessTrue",AuthType="Method",AuthToken="this.LogicTrue")
     */
    public function accessTrue()
    {

    }

    /**
     * @Authorize(ResourceIndex="WWWAnnotated.AccessFalse",AuthType="Method",AuthToken="this.LogicFalse")
     */
    public function accessFalse()
    {

    }

    /**
     * @Authorize(ResourceIndex="WWWAnnotated.AccessNull",AuthType="Method",AuthToken="this.LogicNull")
     */
    public function accessNull()
    {

    }

    /**
     * @Authorize(ResourceIndex="WWWAnnotated.AccessStatic",AuthType="Method",AuthToken="\WorkshopTest\WWWAnnotated.LogicStaticTrue")
     */
    public function accessStatic()
    {

    }

    /**
     * @Client(Method="GET")
     */
    public function webMethodGet()
    {

    }

    /**
     * @Client(Method="POST")
     */
    public function webMethodPost()
    {

    }

    /**
     * @Client(Method="POST,GET")
     */
    public function webMethodPostAndGet()
    {

    }

    /**
     * @Client(Method="POST",ContentType="application/json")
     */
    public function webMethodPostJson()
    {

    }

    public function webMethodNotAnnotated()
    {

    }


    public static function LogicStaticTrue()
    {
        return true;
    }

    public function LogicTrue()
    {
        return true;
    }

    public function LogicFalse()
    {
        return false;
    }

    public function LogicNull()
    {
        return null;
    }
}

class AnnotationHandlerTest extends \PHPUnit_Framework_TestCase
{

    /**
     * Prepares the environment before running a test.
     */
    protected function setUp()
    {

        $oConfig = Resource\Config\Config::get();
        Boot::up($oConfig);
        parent::setUp();
        unset($_SERVER['PHP_AUTH_USER']);
        unset($_SERVER['PHP_AUTH_PW']);
    }

    /**
     * Cleans up the environment runAfter running a test.
     */
    protected function tearDown()
    {
        parent::tearDown();
        unset($_SERVER['PHP_AUTH_USER']);
        unset($_SERVER['PHP_AUTH_PW']);
    }

    public function testAnnotationHandler_PhpDocFromClass()
    {

        $oHandler = new AuthorizeAnnotationService();
        /** @noinspection PhpUnusedLocalVariableInspection */
        list($bResult, $sMessage) = $oHandler->invokeAuthorizeAnnotation(new WWWAnnotated());
        $this->assertFalse($bResult);
        $_SERVER['PHP_AUTH_USER'] = 'class';
        $_SERVER['PHP_AUTH_PW'] = 'class';
        /** @noinspection PhpUnusedLocalVariableInspection */
        list($bResult, $sMessage) = $oHandler->invokeAuthorizeAnnotation(new WWWAnnotated());
        $this->assertTrue($bResult);
        $_SERVER['PHP_AUTH_USER'] = 'method';
        $_SERVER['PHP_AUTH_PW'] = 'method';
        /** @noinspection PhpUnusedLocalVariableInspection */
        list($bResult, $sMessage) = $oHandler->invokeAuthorizeAnnotation(new WWWAnnotated());
        $this->assertFalse($bResult);
        $_SERVER['PHP_AUTH_USER'] = 'method';
        $_SERVER['PHP_AUTH_PW'] = 'method';
    }

    public function testAnnotationHandler_PhpDocFromMethod()
    {
        $oHandler = new AuthorizeAnnotationService();
        /** @noinspection PhpUnusedLocalVariableInspection */
        list($bResult, $sMessage) = $oHandler->invokeAuthorizeAnnotation(
            new WWWAnnotated(), 'privateAccess');
        $this->assertFalse($bResult);
        $oHandler = new AuthorizeAnnotationService();
        /** @noinspection PhpUnusedLocalVariableInspection */
        list($bResult, $sMessage) = $oHandler->invokeAuthorizeAnnotation(
            new WWWAnnotated(), 'publicAccess');
        $this->assertTrue($bResult);
        $oHandler = new AuthorizeAnnotationService();
        /** @noinspection PhpUnusedLocalVariableInspection */
        list($bResult, $sMessage) = $oHandler->invokeAuthorizeAnnotation(
            new WWWAnnotated(), 'accessTrue');
        $this->assertTrue($bResult);
        $oHandler = new AuthorizeAnnotationService();
        /** @noinspection PhpUnusedLocalVariableInspection */
        list($bResult, $sMessage) = $oHandler->invokeAuthorizeAnnotation(
            new WWWAnnotated(), 'accessFalse');
        $this->assertFalse($bResult);
    }

    public function testAnnotationHandler_PhpDocFromMethod_WithStrategy()
    {

        $aStrategy = array(
            'basic' => function () {
                return 'basic';
            },
            'method' => function () {
                return 'method';
            },
            'static' => function () {
                return 'static';
            }
        );

        $oHandler = new AuthorizeAnnotationService();
        /** @noinspection PhpUnusedLocalVariableInspection */
        list($bResult, $sMessage) = $oHandler->invokeAuthorizeAnnotation(
            new WWWAnnotated(), 'privateAccess', $aStrategy);
        $this->assertTrue($bResult == 'static');
        $oHandler = new AuthorizeAnnotationService();
        /** @noinspection PhpUnusedLocalVariableInspection */
        list($bResult, $sMessage) = $oHandler->invokeAuthorizeAnnotation(
            new WWWAnnotated(), 'accessFalse', $aStrategy);
        $this->assertTrue($bResult == 'method');
    }

    public function testAnnotationHandler_PhpDocFromMethod_WithStrategy_WithException()
    {

        $aStrategy = array(
            'basic' => function () {
                return 'basic';
            },
            'method' => function () {
                return 'method';
            },
            'static' => function () {
                return 'static';
            }
        );

        $oHandler = new AuthorizeAnnotationService();
        /** @noinspection PhpUnusedLocalVariableInspection */
        list($bResult, $sMessage) = $oHandler->invokeAuthorizeAnnotation(
            new WWWAnnotated(), 'publicAccess', $aStrategy);
        $this->assertTrue($bResult);
    }

    public function testAnnotationHandler_PhpDocFromMethod_Exception_NotBool()
    {
        $this->expectException(Exception::class);
        $oHandler = new AuthorizeAnnotationService();
        /** @noinspection PhpUnusedLocalVariableInspection */
        list($bResult, $sMessage) = $oHandler->invokeAuthorizeAnnotation(
            new WWWAnnotated(), 'accessNull');
        $this->assertFalse($bResult);
    }

    public function testAnnotationHandler_PhpDocFromMethod_Exception_NoMethod()
    {
        $this->expectException(\ReflectionException::class);
        $oHandler = new AuthorizeAnnotationService();
        /** @noinspection PhpUnusedLocalVariableInspection */
        list($bResult, $sMessage) = $oHandler->invokeAuthorizeAnnotation(
            new WWWAnnotated(), 'accessNONE');
        $this->assertFalse($bResult);
    }

    public function testAnnotationHandler_PhpDocFromMethod_Static_Method()
    {
        $oHandler = new AuthorizeAnnotationService();
        /** @noinspection PhpUnusedLocalVariableInspection */
        list($bResult, $sMessage) = $oHandler->invokeAuthorizeAnnotation(
            new WWWAnnotated(), 'accessStatic');
        $this->assertTrue($bResult);
    }

    public function testWebExpose_GET()
    {
        $oRequest = Boot::getEnv()->getRequest();
        $oHandler = new DispatchAnnotationFetcher();
        $oClient = $oHandler
            ->getClientAnnotation(new WWWAnnotated(), 'webMethodGet');
        $bResult = (new ClientAnnotationValidator($oClient))->validateMethod($oRequest);

        $this->assertTrue($bResult);
    }

    public function testWebExpose_POST()
    {
        $oRequest = Boot::getEnv()->getRequest();
        $oHandler = new DispatchAnnotationFetcher();
        $this->expectException(DispatchException::class);
        $oClient = $oHandler->getClientAnnotation(new WWWAnnotated(), 'webMethodPost');
        (new ClientAnnotationValidator($oClient))->validateMethod($oRequest);
    }

    public function testWebExpose_POST_JSON()
    {
        $oRequest = Boot::getEnv()->getRequest();
        $oRequest->setMethod(Method::POST);
        $oRequest->setContent(new Content('', new ContentType('application/json')));
        $oHandler = new DispatchAnnotationFetcher();
        $oClientAnnotation1 = $oHandler
            ->getClientAnnotation(new WWWAnnotated(), 'webMethodPostJson');
        $oClientAnnotation2 = $oHandler
            ->getClientAnnotation(new WWWAnnotated(), 'webMethodPostJson');
        $bResult =
            (new ClientAnnotationValidator($oClientAnnotation1))->validateMethod($oRequest)
            &&
            (new ClientAnnotationValidator($oClientAnnotation2))->validateContentType($oRequest);

        $this->assertTrue($bResult);
    }

    public function testWebExpose_POST_GET1()
    {
        $oRequest = Boot::getEnv()->getRequest();
        $oRequest->setMethod(Method::POST);
        $oRequest->setContent(new Content('', new ContentType('application/json')));
        $oHandler = new DispatchAnnotationFetcher();
        $oClient = $oHandler->getClientAnnotation(new WWWAnnotated(), 'webMethodPostAndGet');
        $bResult = (new ClientAnnotationValidator($oClient))->validateMethod($oRequest);
        $this->assertTrue($bResult);
    }

    public function testWebExpose_POST_GET2()
    {
        $oRequest = Boot::getEnv()->getRequest();
        $oRequest->setMethod(Method::GET);
        $oRequest->setContent(new Content('', new ContentType('application/json')));
        $oHandler = new DispatchAnnotationFetcher();
        $oClient = $oHandler
            ->getClientAnnotation(new WWWAnnotated(), 'webMethodPostAndGet');
        $bResult = (new ClientAnnotationValidator($oClient))->validateMethod($oRequest);
        $this->assertTrue($bResult);
    }

    public function testWebExpose_POST_GET3()
    {
        $oRequest = Boot::getEnv()->getRequest();
        $oRequest->setMethod(Method::PUT);
        $oRequest->setContent(new Content('', new ContentType('application/json')));
        $oHandler = new DispatchAnnotationFetcher();
        $this->expectException(DispatchException::class);
        $oClient = $oHandler->getClientAnnotation(new WWWAnnotated(), 'webMethodPostAndGet');
        $bResult = (new ClientAnnotationValidator($oClient))->validateMethod($oRequest);
        $this->assertTrue($bResult);
    }

    public function testWebExpose_POST_JSON_Fail_WrongContentType()
    {
        $oRequest = Boot::getEnv()->getRequest();
        $oRequest->setMethod(Method::POST);
        $oRequest->setContent(new Content('', new ContentType('text/html')));
        $oHandler = new DispatchAnnotationFetcher();

        $this->expectException(DispatchException::class);
        $oClient1 = $oHandler
            ->getClientAnnotation(new WWWAnnotated(), 'webMethodPostJson');
        $oClient2 = $oHandler
            ->getClientAnnotation(new WWWAnnotated(), 'webMethodPostJson');

        (new ClientAnnotationValidator($oClient1))->validateMethod($oRequest)
        &&
        (new ClientAnnotationValidator($oClient2))->validateContentType($oRequest);
    }

    public function testWebExpose_POST_JSON_Fail_NotAnnotated()
    {
        $oHandler = new DispatchAnnotationFetcher();
        $bResult = $oHandler->getEndPointAnnotation(new WWWAnnotated(), 'webMethodNotAnnotated');
        $this->assertNull($bResult);
    }
}

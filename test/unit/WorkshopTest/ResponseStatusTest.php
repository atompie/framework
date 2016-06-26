<?php
namespace unit\WorkshopTest;

use AtomPie\Core\Dispatch\QueryString;
use AtomPie\Web\Connection\Http\Content;
use AtomPie\Web\Connection\Http\Header\ContentType;
use AtomPie\Web\Connection\Http\Header\Status;
use AtomPie\Web\Connection\Http\Request\Method;
use AtomPie\Web\Environment;
use WorkshopTest\Boot;
use WorkshopTest\RequestFactory;
use WorkshopTest\Resource\Component\MockComponent1;
use WorkshopTest\Resource\Component\MockComponent7;
use WorkshopTest\Resource\Component\MockComponent8;
use WorkshopTest\Resource\Config\Config;

class ResponseStatusTest extends \PHPUnit_Framework_TestCase
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

    public function testNoFoundError()
    {
        RequestFactory::produce('NoClass.index');
        $oResponse = Boot::run(Boot::getEnv(), Config::get());
        // Could not resolve class [WorkshopTest\Resource\EndPoint\NoClass] as EndPoint.
        $this->assertTrue($oResponse->getStatus()->is(Status::NOT_FOUND));
    }

    public function testNoEndPointAnnotationError()
    {
        RequestFactory::produce(QueryString::urlEscape(MockComponent1::class) . '.test');
        $oResponse = Boot::run(Boot::getEnv(), Config::get());
        $this->assertTrue($oResponse->getStatus()->is(Status::NOT_FOUND));
    }

    public function testMissingEndPointParamError()
    {
        RequestFactory::produce(QueryString::urlEscape(MockComponent7::class) . '.EndPoint');
        $oResponse = Boot::run(Boot::getEnv(), Config::get());
        $this->assertTrue($oResponse->getStatus()->is(Status::BAD_REQUEST));
    }

    public function testRequireJsonError()
    {
        // Client accepts text/html
        $_SERVER['HTTP_ACCEPT'] = 'text/html';
        RequestFactory::produce(
            QueryString::urlEscape(MockComponent8::class) . '.requireJsonContent');
        $oResponse = Boot::run(Boot::getEnv(), Config::get());
        // BUT EndPoint requires that client explicitly accepts application\/json media-type.
        $this->assertTrue($oResponse->getStatus()->is(Status::NOT_ACCEPTABLE));
    }

    public function testIncorrectRequestContentTypeError_JSON()
    {
        $oRequest = RequestFactory::produce(
            QueryString::urlEscape(MockComponent8::class) . '.requireJsonParam');
        // Sets incompatible content to content-type, one is url-encoded string
        // and content-type is json
        $oRequest->setContent(new Content('test=1', new ContentType('application/json')));
        $oResponse = Boot::run(Boot::getEnv(), Config::get());
        // BUT EndPoint requires that client explicitly accepts application\/json media-type.
        $this->assertTrue($oResponse->getStatus()->is(Status::UNPROCESSABLE_ENTITY));
    }

    public function testIncorrectRequestContentTypeError_XML()
    {
        $oRequest = RequestFactory::produce(
            QueryString::urlEscape(MockComponent8::class) . '.requireXmlParam');

        // Sets compatible content and content-type, both are json
        $oRequest->setContent(new Content('NOT-XML', new ContentType('application/xml')));
        $oResponse = Boot::run(Boot::getEnv(), Config::get());
        // BUT EndPoint requires that client explicitly accepts application\/json media-type.
        $this->assertTrue($oResponse->getStatus()->is(Status::UNPROCESSABLE_ENTITY));
    }

    public function testCorrectRequestContentType_Json()
    {
        $oRequest = RequestFactory::produce(
            QueryString::urlEscape(MockComponent8::class) . '.requireJsonParam');

        // Sets compatible content and content-type, both are json
        $oRequest->setContent(new Content('{"test":1}', new ContentType('application/json')));
        $oResponse = Boot::run(Boot::getEnv(), Config::get());
        // BUT EndPoint requires that client explicitly accepts application\/json media-type.
        $this->assertTrue($oResponse->getStatus()->is(Status::OK));
    }

    public function testCorrectRequestContentType_Xml()
    {
        $oRequest = RequestFactory::produce(
            QueryString::urlEscape(MockComponent8::class) . '.requireXmlParam'
        );

        // Sets compatible content and content-type, both are json
        $oRequest->setContent(new Content(/** @lang XML */
            '<data><test><b>1</b><c>2</c></test><a>2</a></data>', new ContentType('application/xml')));
        $oResponse = Boot::run(Boot::getEnv(), Config::get());
        // BUT EndPoint requires that client explicitly accepts application\/json media-type.
        $this->assertTrue($oResponse->getStatus()->is(Status::OK));
    }

    public function testClientJsonContentType()
    {
        $oRequest = RequestFactory::produce(
            QueryString::urlEscape(MockComponent8::class) . '.requireJsonContentType'
        );

        // Sets compatible content and content-type, both are json
        $oRequest->setContent(new Content('', new ContentType('application/xml')));
        $oRequest->setMethod(Method::POST);
        $oResponse = Boot::run(Boot::getEnv(), Config::get());
        // BUT EndPoint requires that client explicitly accepts application\/json media-type.
        $this->assertTrue($oResponse->getStatus()->is(Status::UNSUPPORTED_MEDIA_TYPE));
    }

    public function testClientPutJsonContentType()
    {
        $oRequest = RequestFactory::produce(
            QueryString::urlEscape(MockComponent8::class) . '.requireJsonPutContentType'
        );

        // Sets compatible content and content-type, both are json
        $oRequest->setContent(new Content('', new ContentType('application/xml')));
        $oRequest->setMethod(Method::POST);  // Expects PUT
        $oResponse = Boot::run(Boot::getEnv(), Config::get());
        // BUT EndPoint requires that client explicitly accepts application\/json media-type.
        $this->assertTrue($oResponse->getStatus()->is(Status::METHOD_NOT_ALLOWED));
    }

    public function testClientAjaxJsonContentType()
    {
        $oRequest = RequestFactory::produce(
            QueryString::urlEscape(MockComponent8::class) . '.requireJsonAjaxContentType'
        );

        // Sets compatible content and content-type, both are json
        $oRequest->setContent(new Content('', new ContentType('application/xml')));
        $oRequest->setMethod(Method::POST);
        $oResponse = Boot::run(Boot::getEnv(), Config::get());
        // BUT EndPoint requires that client explicitly accepts application\/json media-type.
        $this->assertTrue($oResponse->getStatus()->is(Status::OK));
    }


}

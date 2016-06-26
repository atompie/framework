<?php
namespace AtomPiePhpUnitTest\System;

use AtomPieTestAssets\Middleware\TestAfterMiddleWare;
use AtomPieTestAssets\Middleware\TestBeforeMiddleWare;
use AtomPieTestAssets\Middleware\TestBeforeSetContentTypeToJson;
use AtomPie\File\FileProcessorProvider;
use AtomPie\Core\Dispatch\DispatchManifest;
use AtomPiePhpUnitTest\FrameworkTest;
use AtomPie\System\IO\File;
use AtomPie\Web\Connection\Http\Header\Status;
use AtomPie\Web\Environment;

class KernelTest extends FrameworkTest
{

    /**
     * @test
     */
    public function shouldBootAndReturnErrorThatMethodDoesNotExist()
    {

        $oConfig = $this->getDefaultConfig('MockEndPoint.doNotExists');
        $oResponse = $this->bootKernel($oConfig, [], []);
        $this->assertTrue($oResponse->getStatus()->is(Status::NOT_FOUND));

    }

    /**
     * @test
     */
    public function shouldBootAndReturnResponseWithStatusOK()
    {
        $oConfig = $this->getDefaultConfig('MockEndPoint.index');
        $oResponse = $this->bootKernel($oConfig, [], []);

        $this->assertTrue($oResponse->getStatus()->isOk());
        $this->assertTrue($oResponse->getContent()->get() == '1');
    }

    /**
     * @test
     */
    public function shouldBootAndReturnResponseWithStatusOKAndDependentClassLoaded()
    {
        $oConfig = $this->getDefaultConfig('MockEndPoint.indexWithDependencyInjection');
        $oResponse = $this->bootKernel($oConfig, [], []);
        $this->assertTrue($oResponse->getStatus()->isOk());
        $this->assertTrue($oResponse->getContent()->get() == 'Dependency-Injection-Container-Exists');
    }

    /**
     * @test
     */
    public function shouldBootWithDependencyInsideDependency()
    {
        $sDefaultEndPoint = 'MockEndPoint.indexDependencyInsideDependency';
        $oConfig = $this->getDefaultConfig($sDefaultEndPoint);
        $oResponse = $this->bootKernel($oConfig, [], []);
        $this->assertTrue($oResponse->getStatus()->isOk());
        // Returns configs default endpoint.
        $this->assertTrue($oResponse->getContent()->get() == $sDefaultEndPoint);
    }



    /**
     * @test
     */
    public function shouldBootAndReturnResponseWithStatusOKAndDependentClassLoadedAsFactoryMethod()
    {
        $oConfig = $this->getDefaultConfig('MockEndPoint.indexWithFactoryMethodDI');
        $oResponse = $this->bootKernel($oConfig, [], []);
        echo $oResponse;

        $this->assertTrue($oResponse->getStatus()->isOk());
        $this->assertTrue($oResponse->getContent()->get() == 'Factory-Method-Exists');
    }

    /**
     * @test
     */
    public function shouldNotBootAsNoFactoryMethodIsDefinedInInjectedParam()
    {
        $oConfig = $this->getDefaultConfig('MockEndPoint.indexWithoutFactoryMethodDI');

        $oResponse = $this->bootKernel($oConfig, [], []);
        $this->assertTrue($oResponse->getStatus()->is(Status::INTERNAL_SERVER_ERROR));
    }

    /**
     * @test
     */
    public function shouldBootAndReturnResponseProcessedByFileProcessor()
    {
        $oConfig = $this->getDefaultConfig('MockEndPoint.getFile');
        $oResponse = $this->bootKernel($oConfig, [
            new FileProcessorProvider()
        ], []);

        $this->assertTrue($oResponse->getStatus()->isOk());
        $this->assertTrue($oResponse->getContent()->get() == 'test');
    }

    /**
     * @test
     */
    public function shouldBootWithMiddleAfterWareAndChangeResponse()
    {

        $oConfig = $this->getDefaultConfig('MockEndPoint.index');
        $oResponse = $this->bootKernel($oConfig, [], [
            new TestAfterMiddleWare()
        ]);

        $this->assertTrue($oResponse->getContent()->get() == TestAfterMiddleWare::class);
    }

    /**
     * @test
     */
    public function shouldBootWithBeforeMiddleWareAndChangeRequest()
    {
        $oConfig = $this->getDefaultConfig('MockEndPoint.index');
        $oResponse = $this->bootKernel($oConfig, [
            new FileProcessorProvider()
        ], [
            new TestBeforeMiddleWare()  // This middleware replace MockEndPoint.index
            // into MockEndPoint.getFile
        ]);

        $oRequest = Environment::getInstance()->getRequest();

        // Dispatch params are removed from request (see: Dispatcher line: 277)
        $this->assertTrue($oRequest->getParam(DispatchManifest::END_POINT_QUERY) == null);
        $this->assertTrue($oResponse->getContent()->get() == 'test');
    }

    /**
     * @test
     */
    public function shouldNotBootWithWithoutParams()
    {
        $oConfig = $this->getDefaultConfig('MockEndPoint.getWithParams');
        $oResponse = $this->bootKernel($oConfig, [], [
            new TestBeforeSetContentTypeToJson()
        ]);
        $oJson = $oResponse->getContent()->decodeAsJson();

        $this->assertTrue($oJson->ErrorMessage == 'Missing required parameter [param1].');
        $this->assertTrue($oResponse->getStatus()->is(Status::BAD_REQUEST));
    }

    /**
     * @test
     */
    public function shouldBootWithWithoutParams()
    {
        $oConfig = $this->getDefaultConfig('MockEndPoint.getWithNotRequiredParams');
        $oResponse = $this->bootKernel($oConfig, [], []);
        $this->assertTrue($oResponse->getStatus()->isOk());
        $this->assertTrue($oResponse->getContent()->get() == true);
    }

    /**
     * @test
     */
    public function shouldBootWithParams()
    {
        $_REQUEST['param1'] = 'valueOfParam1';

        $oConfig = $this->getDefaultConfig('MockEndPoint.getWithParams');
        $oResponse = $this->bootKernel($oConfig, [], [
            new TestBeforeSetContentTypeToJson()
        ]);

        $oJson = $oResponse->getContent()->decodeAsJson();
        $this->assertTrue($oResponse->getStatus()->isOk());
        $this->assertTrue($oJson == 'valueOfParam1');
    }

    /**
     * @test
     */
    public function shouldBootWithException()
    {
        $oConfig = $this->getDefaultConfig('MockEndPoint.error');
        $oResponse = $this->bootKernel($oConfig, [], []);
        $oJson = $oResponse->getContent()->decodeAsJson();

        $this->assertTrue($oResponse->getStatus()->is(Status::INTERNAL_SERVER_ERROR));
        $this->assertTrue('TestException' == $oJson->ErrorMessage);
    }

    /**
     * @test
     */
    public function shouldBootWithExceptionAnd404Status()
    {
        $oConfig = $this->getDefaultConfig('MockEndPoint.errorUnAuthorized');
        $oResponse = $this->bootKernel($oConfig, [], []);
        $oJson = $oResponse->getContent()->decodeAsJson();
        $this->assertTrue($oResponse->getStatus()->is(Status::UNAUTHORIZED));
        $this->assertTrue($oResponse->hasHeader('WWW-Authenticate'));
        $this->assertTrue('TestException' == $oJson->ErrorMessage);
    }

    /**
     * @test
     */
    public function shouldBootWithCustomDi()
    {

    }
}
